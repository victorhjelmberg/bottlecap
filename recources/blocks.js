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

For testing purpose: 
blockarray: [block1,block2,block3]
fetchable_blocks : block4

block1 = [10,15,13,19]
block2 = [29,22,21,20]
block3 = [33,38,39,32]


Errors?
Missing block, because of bad html-request.


*/
testing_fetchablearray = [[10,15,13,19],[29,22,21,20],[33,38,39,32]]
function testing_fetchblock(){
    return testing_fetchablearray.shift();
}

class blockHandler{
    static block_loadAhead = 1;
    static block_remember = 1;
    static block_array = [];

    static last_block;

    static get_nextCap(){
        return;
    }
    static get_previusCap(){
        return;
    }
    static fetch_newBlock(){
        let newBlock = testing_fetchblock();

        //checks if array isn't filled
        if(blockHandler.block_array.length < (blockHandler.block_loadAhead + blockHandler.block_remember + 1)){
            blockHandler.block_array.push(newBlock);
        } else {
            //Checks if index will be out of bouns.
            if(blockHandler.last_block + 1 > blockHandler.block_array.length){
                blockHandler.last_block = 0;
            } else {
                blockHandler.last_block += 1;
            }
        }
        return;
    }
}

function play(){
    blockHandler.fetch_newBlock();
    console.log(JSON.stringify(blockHandler.block_array));
    blockHandler.fetch_newBlock();
    console.log(JSON.stringify(blockHandler.block_array));
    blockHandler.fetch_newBlock();
    console.log(JSON.stringify(blockHandler.block_array));
}
play();