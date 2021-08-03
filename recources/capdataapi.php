<?php

include $_SERVER['DOCUMENT_ROOT'].'/ølkapsel/recources/gameclasses.php';
$apiCallTimeToWait = "";

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
    public static $MAX_API_CALLS = 5;
    public static $API_REFRESH_RATE = 60 * 60 * 1 / 60 / 2; //24 hours between every refresh

    private static function apiRequestUpdater($input){
        $tabelID = $input["tabelID"];
        $reqleft = $input["reqLeft"];
        $dateTimeobj = $input["dateTimeobj"];

        if($reqleft - 1 >= 0){
            $reqleft = $reqleft - 1;
        } else {
            $temp_stored_datetime = strtotime($input["dateTimeobj"]);
            $temp_current_datetime = strtotime(date('Y-m-d H:i:s'));

            if($temp_current_datetime - $temp_stored_datetime >= API::$API_REFRESH_RATE){
                if(API::$MAX_API_CALLS - 1 >= 0){
                    $reqleft = API::$MAX_API_CALLS - 1;
                    $dateTimeobj = date('Y-m-d H:i:s');
                } else {
                    return [false];
                }
            } else {
                return [false, strtotime($dateTimeobj) + API::$API_REFRESH_RATE - strtotime(date('Y-m-d H:i:s'))]; //ERROR NOT ENOUGH API CALLS LEFT
            }
        }
        SQL::sqlrequest("UPDATE apikey SET reqLeft = '".$reqleft."', dateTimeobj = '".$dateTimeobj."' WHERE tabelID = ".$tabelID);
        return [true]; //API-key has been validated
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
                    return API::apiRequestUpdater($row); //Key was or was not validated. return true or false
                }
            }
        }
        //User has not been found
        if(API::$MAX_API_CALLS - 1 >= 0){
            $key = password_hash($key,PASSWORD_DEFAULT);
            $temp_current_datetime = date('Y-m-d H:i:s');
            SQL::sqlrequest("INSERT INTO apikey (identifier, reqLeft, dateTimeobj) VALUES ('".$key."','".(API::$MAX_API_CALLS - 1)."','".$temp_current_datetime."')");
            return [true]; //Key was validated. You may proceed
        }
    }

    public static function runTimeUpdate(){
        $runTimeData = SQL::sqlrequest('SELECT * FROM runtime WHERE runtime.runTimeID = 1');

        while($row = $runTimeData->fetch_assoc()) {
            $runTimeAmunt = $row['runAmount'];
            SQL::sqlrequest("UPDATE runtime SET runtime.runAmount = " . $runTimeAmunt + 1 . " WHERE runtime.runTimeID = 1");
        }
    }
}

class apiError{
    private $errorobject;

    function __construct($errormessage, $errorcode, $errorAttachment){
        $this->errorobject = ['APIerror' => [
            'errormessage' => $errormessage,
            'errorcode' => $errorcode,
            'errorAttachment' => $errorAttachment
        ]];
    }
    function jsonError(){
        return $this->errorobject;
    }
}

$requestData = json_decode($_COOKIE['requestData']);

SQL::connect();

$APISucces = API::keyvalidation();

if ($APISucces[0]){
    API::runTimeUpdate();
    test();
} else {
    $outOfAPICallsError = new apiError('No more API-calls', 'NOMORECALLS', ['datetime' => $APISucces[1]]);
    /*
    echo json_encode(
        ['error' => "No more API-calls", 'datetime' => $APISucces[1]]
    ); 
    */
    echo json_encode($outOfAPICallsError->jsonError());
}

SQL::disconnect();

// SQL::connect();

// $APISucces = API::keyvalidation();

// if ($APISucces[0]){
//     API::runTimeUpdate();
//     test();
// } else {
//     echo json_encode(['error' => "No more API-calls", 'datetime' => $APISucces[1]]); 
// }

// SQL::disconnect();

?>