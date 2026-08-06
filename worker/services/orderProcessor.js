async function processOrder(queue){


    /*
        Simulate processing delay
    */


    await new Promise(resolve =>
        setTimeout(resolve,2000)
    );


    /*
        Example failure condition
        Replace with real business logic
    */


    if(queue.order_id % 5 === 0){

        throw new Error(
            "Payment processing failed"
        );

    }


    return true;

}


module.exports={
    processOrder
};