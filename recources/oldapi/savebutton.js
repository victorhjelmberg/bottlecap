function responshandler(jsonstring){

    console.log(jsonstring);

    responsedata = JSON.parse(jsonstring);

    if (responsedata.hasOwnProperty("errormessage")){
        //An error occured on the client side
        console.log("An error on the clientside occured: " + responsedata["errormessage"]);
    } else {
        //No error occured on the client side
        console.log(responsedata);
    }
}



function myapireqmaker (requesttype, additionalattachments = []){

    let requestframe = {
        requesttype : requesttype
    };
    
    if (requesttype == "upload"){
        requestframe["requestattachment"] = {
            leveldata : additionalattachments[0],
            levelid : additionalattachments[1]
        }
    } else {
        console.log("Error, the requesttype doesn't match anything");
    }

    return JSON.stringify(requestframe);
}




document.getElementById("myBtn").addEventListener("click", function() {

    //temp
    let leveldataobj = {
        hat : "hej",
        bob : "hej2"
    };

    let levelID = "hejsa";

    //Some fetch magic - Needs research!!
    fetch("/mygame/jsonparser.php", {
        method: 'post',
        body: myapireqmaker("upload", [leveldataobj, levelID])
    })
    .then(response => response.text())
    .then((response) => {
        responshandler(response);
    })



});