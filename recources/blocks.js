/*
Our API-fetch-game is block-based:
Every block consist of a single API-call, with multiple caps included.

block_gameoptions:
block_loadAhead: amount of blocks the game load ahead, from current block.
block_remember: amount of blocks the game remembers, from the current block. Together with loadAhead it makes up the total blocks in the array.
block_array: Array countaining all the old block (As far as the game remembers) and all the new blocks to be used. 

block_properies:
newest_block - the index of the newest block which has been pointed to.
last_block - the index of the last block in the blockarray.

block functions:
get_nextCap - Points the curser one step forward, if the block is new, and returns the next block.
get_previusCap - returns the previus cap.
fetch_newBlock - Calls the API, and stores the new block in the block_array.
*/

class fetcher{

    static requestData = {};

    static setRequestData(input){
        fetcher.requestData = input;
    }
    
    static setRequestOptions(){
        return ({
            method: 'GET',
            cache: 'no-cache',
            //body: JSON.stringify(requestObject)
        });
    }

    static async apiCallJson(){
        
        const response = await fetch('recources/capdataapi.php', fetcher.setRequestOptions());
        const responseText = await response.text();
        //console.log(responseText);
        return JSON.parse(responseText);
        //return await response.json(); //extract JSON from the http response
    }

    static fetchHandler(callBack = () => {return}){
        let timeForAPIRefresh = 10000; //default, wait 10 seconds between each request
        fetcher.apiCallJson()
        .then(function (newBlock) {

            //Checks for API Errors
            if(newBlock.hasOwnProperty('APIerror')){
                const APIerror = newBlock['APIerror'];

                if(APIerror['errorcode'] == 'NOMORECALLS'){
                    timeForAPIRefresh = APIerror['errorAttachment']['datetime'] * 1000;
                }

                throw new Error(APIerror['errormessage']);

            } else {
                if(blockHandler.block_array.length < (blockHandler.block_loadAhead + blockHandler.block_remember + 1)){
                    blockHandler.block_array.push(newBlock);
                    blockHandler.last_block = blockHandler.block_array.length -1;
                } else {
                    blockHandler.last_block += 1;
                    blockHandler.block_array[blockHandler.last_block % blockHandler.block_array.length] = newBlock;
                }
                callBack();
                //blockplace();
            }
        })
        .catch(function(error){
            console.error(error);
            setTimeout(fetcher.fetchHandler, timeForAPIRefresh);
            console.log('Time left until your next call: ' + timeForAPIRefresh);
        });
    }
}

class blockHandler{
    static block_loadAhead = 1;
    static block_remember = 1;
    static block_array = [];
    static activeBlock;
    static activeCapIndex;

    static last_block;
    static current_block = 0;

    static initialise(){
        for(let i = 0; i < blockHandler.block_loadAhead + 1; i++){
            if(i == 0){
                //Initialize activeBlock and activeCapIndex after the first fetch.
                fetcher.fetchHandler(() => {
                    blockHandler.activeBlock = blockHandler.block_array[0];
                    blockHandler.activeCapIndex = 0;
                });
            } else {
                fetcher.fetchHandler();
            }
        }
    }

    static get_nextBlock(){
        if(blockHandler.last_block == blockHandler.current_block){
            blockplace();
            throw new Error("Something went wrong with the fetch. There are no new blocks");
        } else {
            blockHandler.current_block += 1;
            if(blockHandler.last_block - blockHandler.current_block < blockHandler.block_loadAhead){
                queueMicrotask(fetcher.fetchHandler);
            }
            return blockHandler.block_array[blockHandler.current_block % blockHandler.block_array.length];  
        }
    }
    static get_previusBlock(){
        if (blockHandler.current_block != 0){
            if ((blockHandler.current_block - 1) % blockHandler.block_array.length != blockHandler.last_block % blockHandler.block_array.length){
                blockHandler.current_block -= 1;
                return blockHandler.block_array[blockHandler.current_block % blockHandler.block_array.length];
            } else {
                throw new Error("The block you're looking for has been overwritten");
            }
        } else {
            throw new Error("There are no previus block");
        }
    }
    static getNextCap(){
        if(blockHandler.activeBlock['caps'].length == blockHandler.activeCapIndex + 1){
            try{
                blockHandler.activeBlock = blockHandler.get_nextBlock();
                blockHandler.activeCapIndex = 0;
            }
            catch(fetcherror){
                throw new Error(fetcherror);
            }
        } else {
            blockHandler.activeCapIndex += 1;
        }
        console.log('CapIndex : ' + blockHandler.activeCapIndex);
        return blockHandler.activeBlock['caps'][blockHandler.activeCapIndex];
    }
    static getPreviusCap(){
        if(blockHandler.activeCapIndex == 0){
            try{
                blockHandler.activeBlock = blockHandler.get_previusBlock();
                blockHandler.activeCapIndex = blockHandler.activeBlock['caps'].length - 1;
            }
            catch(memoryError){
                throw new Error(memoryError);
            }
        } else {
            blockHandler.activeCapIndex -= 1;
        }
        console.log('CapIndex : ' + blockHandler.activeCapIndex);
        return blockHandler.activeBlock['caps'][blockHandler.activeCapIndex];
    }
    static getCurrentCap(){
        console.log('CapIndex : ' + blockHandler.activeCapIndex);
        return blockHandler.activeBlock['caps'][blockHandler.activeCapIndex];
    }
}

// fetcher.setRequestData({
//     options : {
//         shockcategory : []
//     }
// });

function play(){
    blockHandler.initialise();
}
play();

function up(){
    blockHandler.get_nextBlock();
}
function down(){
    blockHandler.get_previusBlock();
    blockplace();
}
function ar(){
    console.log(blockHandler.block_array);
    blockplace();
    //console.log(JSON.stringify(blockHandler.block_array));
}
function blockplace(){
    console.log("lastblock: " + blockHandler.last_block + ". currentblock: " + blockHandler.current_block);
}
function feetch(){
    fetcher.fetchHandler();
    JSON.stringify(blockHandler.block_array);
}
function nxcap(){
    return blockHandler.getNextCap();
}
function prcap(){
    return blockHandler.getPreviusCap();
}
function cucap(){
    return blockHandler.getCurrentCap();
}

//console.log(JSON.stringify(fetcher.fetchHandler()));

