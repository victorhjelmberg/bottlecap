<?php

include $_SERVER['DOCUMENT_ROOT'].'/ølkapsel/recources/gameclasses.php';

class SQL {

    private static $conn = null;

    public static function connect(){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "kapselapi";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }
        SQL::$conn = $conn;
    }

    public static function disconnect(){
        SQL::$conn->close();
    }

    /*
    public static function getconn(){
        return SQL::$conn;
    }
    */

    public static function sqlrequest($sql){
        $SQLresult = SQL::$conn->query($sql);

        if ($SQLresult === FALSE) {
            echo "Error: " . $sql . "<br>" . SQL::$conn->error;
            return;
        } else {
            return $SQLresult;
        }
    }

    //This function is untested and untrusted!!
    public static function antiSQLattack($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

class API{
    public static $MAX_CAPS_TO_RETURN = 30;
    public static $MAX_API_CALLS = 30;
    public static $API_REFRESH_RATE = 60 * 60 * 24; //24 hours between every refresh

    private static function apiRequestUpdater($input){
        $tabelID = $input["tabelID"];
        $reqleft = $input["reqLeft"];
        $dateTimeobj = $input["dateTimeobj"];

        if($reqleft != 0){
            $reqleft = $reqleft - 1;
        } else {
            $temp_stored_datetime = strtotime($input["dateTimeobj"]);
            $temp_current_datetime = strtotime(date('Y-m-d H:i:s'));

            if($temp_current_datetime - $temp_stored_datetime >= API::$API_REFRESH_RATE){
                $reqleft = API::$MAX_API_CALLS;
                $dateTimeobj = date('Y-m-d H:i:s');
            } else {
                //ERROR NOT ENOUGH API CALLS LEFT
                echo "You're all out of calls";
            }
        }
        SQL::sqlrequest("UPDATE apikey SET reqLeft = '".$reqleft."', dateTimeobj = '".$dateTimeobj."' WHERE tabelID = ".$tabelID);
    }

    public static function keyvalidation(){
        #Checks for IP match in database, and run apiRequestUpdater() on SQL-row where there is a match, or creates new data in the database, if user doesn't excist.

        // The real key uppon publishing: $key = $_SERVER['REMOTE_ADDR'];
        $key = "2a05:f6c4:6462:0:49f6:9369:f1fb:a6aa";

        $SQLresponse = SQL::sqlrequest("SELECT * FROM apikey");

        if ($SQLresponse->num_rows > 0) {
            while($row = $SQLresponse->fetch_assoc()) {
                if(password_verify($key, $row["identifier"])){
                    //User has been found
                    API::apiRequestUpdater($row);
                    return;
                }
            }
        }
        //User has not been found
        $key = password_hash($key,PASSWORD_DEFAULT);
        $temp_current_datetime = date('Y-m-d H:i:s');
        SQL::sqlrequest("INSERT INTO apikey (identifier, reqLeft, dateTimeobj) VALUES ('".$key."','".(API::$MAX_API_CALLS - 1)."','".$temp_current_datetime."')");

        return;
    }
}

SQL::connect();
//API::keyvalidation();

test();

SQL::disconnect();

?>