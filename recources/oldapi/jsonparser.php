<?php
    //Reads RAW POST-data
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input["requesttype"] == "upload"){
        $requestattachment = $input["requestattachment"];

        $myfile = fopen($requestattachment["levelid"] . ".json" , "w");

        fwrite($myfile, json_encode($requestattachment["leveldata"]));

        fclose($myfile);

        echo json_encode(["errormessage" => "upload completed"]);
    } else {
        echo json_encode($input);
    }
?>

<?php

    apiDecoder(){
        return json_decode(file_get_contents('php://input'), true);
    }
    apiEncoder($input){
        echo json_encode($input);
    }

    capSelector(){ //Choose which caps should be send to the client 
        return;
    }
    calGrapper(){ //Grabs the items capSelector choose, and returns a list of json-data with the cap-data
        return;
    }


?>