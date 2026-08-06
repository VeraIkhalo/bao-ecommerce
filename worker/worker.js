const db = require("./db");

async function processQueue() {

    const [rows] = await db.execute(`
        SELECT *
        FROM order_queue
        WHERE status='pending'
        AND available_at <= NOW()
        LIMIT 1
    `);

    if (rows.length === 0) {
        return;
    }

    const queue = rows[0];

    await db.execute(
        `
        UPDATE order_queue
        SET status='processing',
            locked_by='worker-1',
            locked_at=NOW()
        WHERE id=?
    `,
        [queue.id]
    );

    try {

        await db.execute(
            `
            UPDATE orders
            SET status='completed'
            WHERE id=?
        `,
            [queue.order_id]
        );

        const [orders] = await db.execute(
            `
            SELECT user_id
            FROM orders
            WHERE id=?
        `,
            [queue.order_id]
        );

        const userId = orders[0].user_id;

        await db.execute(
            `
            INSERT INTO notifications(user_id,message,is_read,created_at)
            VALUES(?,?,0,NOW())
        `,
            [userId, "Order processed successfully"]
        );

        await db.execute(
            `
            UPDATE order_queue
            SET status='completed'
            WHERE id=?
        `,
            [queue.id]
        );

        console.log("Processed Order:", queue.order_id);

    } catch (err) {

        const attempts = queue.attempts + 1;

        if (attempts >= 3) {

            await db.execute(
                `
                UPDATE order_queue
                SET status='failed',
                    attempts=?
                WHERE id=?
            `,
                [attempts, queue.id]
            );

            await db.execute(
                `
                UPDATE orders
                SET status='failed'
                WHERE id=?
            `,
                [queue.order_id]
            );

        } else {

            await db.execute(
                `
                UPDATE order_queue
                SET attempts=?,
                    status='pending'
                WHERE id=?
            `,
                [attempts, queue.id]
            );
        }

        console.log(err);
    }

}

setInterval(processQueue, 5000);