var testobj = {
    firstname : "Bob",
    secondname : "notbob",
    fullname : function(){
        return this.firstname + " " + this.secondname;
    }
}
jsonstring = JSON.stringify(testobj);
document.getElementById("change").innerHTML = jsonstring;