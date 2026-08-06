const db = require("./db");
const { processOrder } = require("./services/orderProcessor");

const workerId = `worker-${process.pid}`;

let running = false;


/**
 * Pick one available order and lock it
 */
async function claimOrder(connection) {

    const [rows] = await connection.execute(`
        SELECT *
        FROM order_queue
        WHERE status='pending'
        AND available_at <= NOW()
        LIMIT 1
        FOR UPDATE
    `);


    if(rows.length === 0){
        return null;
    }


    const queue = rows[0];


    const [result] = await connection.execute(
        `
        UPDATE order_queue
        SET 
            status='processing',
            locked_by=?,
            locked_at=NOW()
        WHERE id=?
        AND status='pending'
        `,
        [
            workerId,
            queue.id
        ]
    );


    if(result.affectedRows === 0){
        return null;
    }


    return queue;
}



/**
 * Handle failed order
 */
async function handleFailure(connection, queue, error){

    const [attemptResult] = await connection.execute(
        `
        UPDATE order_queue
        SET 
            attempts = attempts + 1,
            updated_at = NOW()
        WHERE id=?
        `,
        [
            queue.id
        ]
    );


    const [attempts] = await connection.execute(
        `
        SELECT attempts
        FROM order_queue
        WHERE id=?
        `,
        [
            queue.id
        ]
    );


    const currentAttempts = attempts[0].attempts;


    if(currentAttempts >= 3){


        await connection.execute(
            `
            UPDATE order_queue
            SET
                status='failed',
                updated_at=NOW()
            WHERE id=?
            `,
            [
                queue.id
            ]
        );


        await connection.execute(
            `
            UPDATE orders
            SET
                status='failed',
                updated_at=NOW()
            WHERE id=?
            `,
            [
                queue.order_id
            ]
        );


        const [order] = await connection.execute(
            `
            SELECT user_id
            FROM orders
            WHERE id=?
            `,
            [
                queue.order_id
            ]
        );


        await connection.execute(
            `
            INSERT INTO notifications
            (
                user_id,
                message,
                is_read,
                created_at
            )
            VALUES(?,?,0,NOW())
            `,
            [
                order[0].user_id,
                "Order processing failed after multiple attempts"
            ]
        );


    }
    else {


        // retry after 30 seconds

        await connection.execute(
            `
            UPDATE order_queue
            SET
                status='pending',
                available_at=DATE_ADD(NOW(), INTERVAL 30 SECOND),
                locked_by=NULL,
                locked_at=NULL
            WHERE id=?
            `,
            [
                queue.id
            ]
        );

    }

}



/**
 * Main worker function
 */
async function runWorker(){

    if(running){
        return;
    }


    running=true;


    let connection;


    try {


        connection = await db.getConnection();


        await connection.beginTransaction();


        const queue = await claimOrder(connection);


        if(!queue){

            await connection.rollback();
            return;

        }


        await connection.commit();



        /*
            Process order outside lock transaction
        */

        try {


            await processOrder(queue);



            await connection.beginTransaction();



            await connection.execute(
                `
                UPDATE orders
                SET
                    status='completed',
                    updated_at=NOW()
                WHERE id=?
                `,
                [
                    queue.order_id
                ]
            );



            const [order] = await connection.execute(
                `
                SELECT user_id
                FROM orders
                WHERE id=?
                `,
                [
                    queue.order_id
                ]
            );



            await connection.execute(
                `
                INSERT INTO notifications
                (
                    user_id,
                    message,
                    is_read,
                    created_at
                )
                VALUES(?,?,0,NOW())
                `,
                [
                    order[0].user_id,
                    "Your order has been processed successfully"
                ]
            );



            await connection.execute(
                `
                UPDATE order_queue
                SET
                    status='completed',
                    updated_at=NOW()
                WHERE id=?
                `,
                [
                    queue.id
                ]
            );


            await connection.commit();



            console.log(
                "Completed order:",
                queue.order_id
            );



        }
        catch(error){


            await connection.beginTransaction();


            await handleFailure(
                connection,
                queue,
                error
            );


            await connection.commit();


            console.log(
                "Order failed:",
                queue.order_id,
                error.message
            );

        }



    }
    catch(error){

        console.log(error);


        if(connection){
            await connection.rollback();
        }

    }
    finally{


        if(connection){
            connection.release();
        }


        running=false;

    }

}


setInterval(runWorker,5000);