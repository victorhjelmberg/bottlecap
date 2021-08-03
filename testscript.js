
/*
async function apiCallJson(){
    const response = await fetch('recources/capdataapi.php');
    return await response.json(); //extract JSON from the http response
}

let errorsBeforeTimeout = 10;

function fetchHandler(){
    apiCallJson()
    .then(value => console.log(value))
    .catch(function(){
        errorsBeforeTimeout -= 1; 
        if(errorsBeforeTimeout > 0){
            fetchHandler();
        } else {
            console.log("Your connection was timed out");
            setTimeout(function(){
                errorsBeforeTimeout = 20;
                fetchHandler();
            },5000);
        }
    });
}
fetchHandler();
*/

async function mytest(){
    await fetch('recources/logo.svg');
    console.log("done");
}

queueMicrotask(mytest);
console.log('mitty');