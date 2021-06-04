class capcard{
    constructor(jsonQuestion, categoryData){
        this.name = categoryData["name"];
        this.discription = categoryData["discription"];
        this.text = capcard.textformater(jsonQuestion);
    }

    static textformater(jsonQuestion){
        let playerlist = game.playerlist;
        let formatedText = jsonQuestion["text"]["unformat"];
        let keywordsList = jsonQuestion["text"]["keywords"];

        let playerlistleft = playerlist;
        let randomplayerdict = {};

        for (const i in keywordsList){

            if (keywordsList[i].indexOf("randomplayer") != -1){
                if (!(randomplayerdict.hasOwnProperty(keywordsList[i]))){
                    let randomplayerresponse = capcard.gameActionUtilities("randomplayer", null, playerlistleft)();
                    randomplayerdict[keywordsList[i]] = randomplayerresponse[0];
                    playerlistleft = randomplayerresponse[1];
                }
                formatedText = formatedText.replace("{}", randomplayerdict[keywordsList[i]]);
                continue;
            }

            formatedText = formatedText.replace("{}", capcard.gameActionUtilities(keywordsList[i], jsonQuestion));
        }
        return formatedText;
    }

    static gameActionUtilities(keyword, jsonQuestion = {}, playerlist = []){
        let functionslist = {
            "randomplayer" : function(){
                let randomint = Math.floor(Math.random() * playerlist.length);
                let removed = playerlist.splice(randomint,1);
                return [removed, playerlist];},
            "punishment" : function(){return jsonQuestion["text"]["punishment"]}
        }
        return functionslist[keyword];
    }
}


class game{

    static playerlist = ["Victor", "Hans", "Viktor", "Julie", "Allan", "Ælling", "Simmon", "jakob", "mr.jul"];

    static getGameObjects(){
        $.getJSON("recources/gamedataobject.json", function(json) {

            let jsonQuestion = json["Alle prøver at"]["questions"][0]
            let categoryData = {"name" : "Alle prøver at", "discription" : json["Alle prøver at"]["discription"]}
            let mycapcard = new capcard(jsonQuestion, categoryData);

            document.querySelector(".gameobjtitle").innerHTML = mycapcard.name;
            document.querySelector(".gameobjtext").innerHTML = mycapcard.text;
        });
    }
}

class scenehandler {
    constructor(constructiondict) {
        this.unloaddirection = constructiondict["unloaddirection"];
        this.loadid = constructiondict["loadid"];
    }

    sceneload(){
        for (let i in this.loadid){
            $("#" + i).animate(this.loadid[i]);
        }
    }

    sceneunload(){
        let percentvalue;

        if (this.unloaddirection == "right"){
            percentvalue = 100
        } else if (this.unloaddirection == "left"){
            percentvalue = -100
        }

        //jquery declaration
        for (let i in this.loadid){
            $("#" + i).animate({
                left: percentvalue + "%"
              });
        }
    }
}

// Scenes/////////////////////////////////

let startmenu = new scenehandler({
    unloaddirection: "left", 
    loadid : {
        startmenucontainer : {left: '0%'}
    }
});

let gamescene1 = new scenehandler({
    unloaddirection : "right",
    loadid : {
        gameobj1 : {left: '0%'}
    }
})
///////////////////////////////////////////


   
window.addEventListener("load", function(){

    game.getGameObjects();

    startmenu.sceneunload();
    gamescene1.sceneload();

    //document.querySelector("#changingsvg").contentDocument.querySelector(".st0").setAttribute("fill", "green")

    $("#startmenubutton").click(function(){

        startmenu.sceneunload();
        gamescene1.sceneload();

      }); 

    $("#backbutton").click(function(){

        startmenu.sceneload();
        gamescene1.sceneunload();

      }); 

});