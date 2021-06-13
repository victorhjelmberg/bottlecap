<?php

    class gameCategory{
        function __construct($gameclassID) {

            $sql = "SELECT * FROM gameclass WHERE gameclass.gameclassID = " . $gameclassID;
            $SQLresponse = SQL::sqlrequest($sql);

            while($row = $SQLresponse->fetch_assoc()) {
                $this->name = $row['name'];
                $this->color = $row['color'];
                $this->discription = $row['discription'];
            }
        }
    }

    class cap{
        function __construct($SQLresponse){
            if ($SQLresponse->num_rows > 0) {
                while($row = $SQLresponse->fetch_assoc()) {
                    $this->unformatedText = $row['unformatedText'];
                    $this->gameClass = $row['gameclassID'];

                    if($row['drinkAmount'] != null){
                        $this->drinkAmount = $row['drinkAmount'];
                    }
                    if($row['difficulty'] != null){
                        $this->difficulty = $row['difficulty'];
                    }
                    if($row['keywordsarray'] != null){
                        $this->keywordsarray = $row['keywordsarray'];
                    }
                }
            }
        }
    }

    class randomCapSelector{
        
        static function setCapArray($options = []){

            //Some ugly code, to manipulate the SQL-Request. Beautify is heavily needed, but not requried
            $condition = 'WHERE ';
            
            if(array_key_exists('shockcategory',$options)){
                for ($i = 0; $i < sizeof($options['shockcategory']);$i++){
                    $condition .= "shockcategory.shockcategoryText = '" . $options['shockcategory'][$i] . "' OR ";
                }
            }

            if($condition == 'WHERE '){
                $condition = '';
            } else {
                $condition = substr($condition,0,strlen(' OR ')*-1);
            }

            $capsFromOptionsSQL = 
            "
            SELECT cap.capID, cap.gameclassID
            FROM ((cap INNER JOIN screference ON cap.capID = screference.capID) INNER JOIN shockcategory ON screference.shockcategoryID = shockcategory.shockcategoryid)
            ".$condition."
            GROUP BY cap.capID
            ";

            //Request into Array
            $capIDs = [];
            $categories = [];

            $capsFromOptionsSQL = SQL::sqlrequest($capsFromOptionsSQL);
            if ($capsFromOptionsSQL->num_rows > 0) {
                while($row = $capsFromOptionsSQL->fetch_assoc()) {
                    if(!(array_key_exists($row['gameclassID'], $capIDs))){
                        $capIDs[$row['gameclassID']] = [];
                        $categories[$row['gameclassID']] = new gameCategory($row['gameclassID']);
                    }
                    $capIDs[$row['gameclassID']][] = $row['capID'];
                }
            }
            return [
                'capIDs' => $capIDs,
                'categories' => $categories];
        }

        static function capindex($index){
            return new cap (SQL::sqlrequest('SELECT * FROM cap WHERE capID = '.$index));
        }

        static function randomSelector($organisedCaps){

            $selectedCaps = [];
            //$organisedCaps = randomCapSelector::$capIDs; //DICT: Contains all the capIDs organised in categories in key value format:: capIDs = {'category1' => [1,3,5,7], 'category2' => [9,23]}
            $itterationsLeft = API::$MAX_CAPS_TO_RETURN;

            while ((sizeof(array_keys($organisedCaps)) != 0) and ($itterationsLeft > 0)){
                //First step: Select a random category:
                $temp_categories = array_keys($organisedCaps);
                $randomSelectedCategoryKey = $temp_categories[array_rand($temp_categories)];
                $randomSelectedCategoryValue = $organisedCaps[$randomSelectedCategoryKey];

                //Next step: Select a random capID in the randomly selected category:
                $randomSelectedCapIDIndex = array_rand($randomSelectedCategoryValue);
                $randomSelectedCapID = $randomSelectedCategoryValue[$randomSelectedCapIDIndex];
                $selectedCaps[] = $randomSelectedCapID;

                //Third step: Delete the random selected CapID from the array, to away duplicate caps
                array_splice($organisedCaps[$randomSelectedCategoryKey],$randomSelectedCapIDIndex,1);
                if(sizeof($organisedCaps[$randomSelectedCategoryKey]) == 0){
                    unset($organisedCaps[$randomSelectedCategoryKey]);
                }
                $itterationsLeft -= 1;
            }
            return $selectedCaps;
        }
    }

    function play(){

        $options = [
            //'shockcategory' => ['shocking', 'nudity']
            'shockcategory' => ['nudity','shocking','illegal']
        ];

        $capArray = randomCapSelector::setCapArray($options);
        $organisedCaps = $capArray['capIDs'];
        $categories = $capArray['categories'];
        $selectedCaps = randomCapSelector::randomSelector($organisedCaps);
        
        $arrayOfCapObjects = [];
        for($i = 0; $i < sizeof($selectedCaps); $i++){
            $arrayOfCapObjects[] = randomCapSelector::capindex($selectedCaps[$i]);
        }
        $response = ['caps' => $arrayOfCapObjects, 'gameclasses' => $categories];
        echo json_encode($response,JSON_PRETTY_PRINT | JSON_INVALID_UTF8_IGNORE);
        header('Content-type: application/json');


        //echo $x[0]->capIDs[0]; //Fetch first category with first cap in category.
        //echo $x[0]->capindex($x[0]->capIDs[0])->unformatedText;
    }

    function test(){
        $test_repetition = 1;
        $test_displayTime = false;

        $test_timeStart = microtime(true);

        for ($i = 0; $i < $test_repetition; $i++){
            play();
        }

        $test_timeStop = microtime(true);

        if($test_displayTime == true){
            echo "Code execution took: " . round(($test_timeStop - $test_timeStart) * 1000, 2) . " ms";
        }

    }


/*
Class category
category.discription = cateogory discription
category.name = category name
category.length() = return number of caps in category
category.cap(i) = return cap object of index (i)

Class cap
cap.unformatedtext = text
cap.keywords = array of keywords
...
*/

?>