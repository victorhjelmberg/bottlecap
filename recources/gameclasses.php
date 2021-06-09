<?php

    class gameCategory{
        function __construct($row, $options) {
            $this->gameclassID = $row['gameclassID'];
            $this->name = $row['name'];
            $this->color = $row['color'];
            $this->discription = $row['discription'];

            //Creates array with CapID's
            $capIDarray = [];

            //SQL REQUEST COMBINER
            $capIDResponse = SQL::sqlrequest('SELECT capID FROM cap WHERE gameclassID =' . $row['gameclassID']);
            if ($capIDResponse->num_rows > 0) {
                while($row = $capIDResponse->fetch_assoc()) {
                    $capIDarray[] = $row['capID'];
                }
            }
            $this->capIDs = $capIDarray;
            //


        }
    }

    class cap{
        function __construct($SQLresponse){
            if ($SQLresponse->num_rows > 0) {
                while($row = $SQLresponse->fetch_assoc()) {
                    $this->unformatedText = $row['unformatedText'];
                    $this->drinkAmount = $row['drinkAmount'];
                    $this->difficulty = $row['difficulty'];
                    $this->keywordsarray = $row['keywordsarray'];
                    $this->shockcategoryarray = $row['shockcategoryarray'];
                }
            }
        }
    }

    class randomCapSelector{
        static $categories;
        static $capIDs;
        
        static function setClassArray(){
            $gameClasses = [];
            $temp_getClassesSQL = SQL::sqlrequest('SELECT gameclass.gameclassID FROM gameclass');
            if ($temp_getClassesSQL->num_rows > 0) {
                while($row = $temp_getClassesSQL->fetch_assoc()) {
                    $gameClasses[] = $row['gameclassID'];
                }
            }
            randomCapSelector::$categories = $gameClasses;
        }
        static function setCapArray($options = []){
            $capIDs = [];

            for ($i = 0; $i < sizeof(randomCapSelector::$categories); $i++){
                $capIDs[randomCapSelector::$categories[$i]] = [];
            }

            //Some ugly code, to manipulate the SQL-Request
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
            $capsFromOptionsSQL = SQL::sqlrequest($capsFromOptionsSQL);
            if ($capsFromOptionsSQL->num_rows > 0) {
                while($row = $capsFromOptionsSQL->fetch_assoc()) {
                    $capIDs[$row['gameclassID']][] = $row['capID'];
                }
            }
            randomCapSelector::$capIDs = $capIDs;
        }
        static function capindex($index){
            return new cap (SQL::sqlrequest('SELECT * FROM cap WHERE capID = '.$index));
        }
        static function randomSelector(){

            $categories = array_keys(randomCapSelector::$capIDs);
            $selectedCapsArray = [];
            $selectedCaps = [];

            //Creates new array selectedCapsArray from capIDs
            for ($i = 0; $i < $categories;$i++){
                $selectedCapsArray[] = $categories[$i];
                if (sizeof($categories[$i]) < API::$MAX_CAPS_TO_RETURN){
                    $selectedCapsArray[$categories[$i]] = array_rand(randomCapSelector::$categories,sizeof($categories[$i]));
                } else {
                    $selectedCapsArray[$categories[$i]] = array_rand(randomCapSelector::$categories,API::$MAX_CAPS_TO_RETURN);
                }
            }

            for($i = 0; $i < API::$MAX_CAPS_TO_RETURN; $i++){
                $last_category = "";
                do {
                    $new_category = array_rand(array_keys($selectedCapsArray));
                } while ($new_category == $last_category and sizeof($selectedCapsArray[$new_category]) != 0);

                $capToAdd = array_rand($selectedCapsArray[$new_category]);
                $selectedCaps[] = $selectedCapsArray[$new_category][$capToAdd];
                array_splice($selectedCapsArray[$new_category],$capToAdd,1); //Removes element to simulate that it has been selected
            }

            echo "completion";
            echo json_encode($selectedCaps);



            //$randomSelectedCategory = array_rand(randomCapSelector::$categories);

        }
        
    }

    /*function randomCapSelector(){

        //Puts gameclasses into array and selects the category to be displayed

    

        //echo $gameClasses[array_rand($gameClasses)];
    }*/

    function play(){

        $options = [
            //'shockcategory' => ['shocking', 'nudity']
            'shockcategory' => []
        ];

        randomCapSelector::setClassArray();
        randomCapSelector::setCapArray($options);
        randomCapSelector::randomSelector();

        //$x = categoryinit($options);

        //echo $x[0]->capIDs[0]; //Fetch first category with first cap in category.
        //echo $x[0]->capindex($x[0]->capIDs[0])->unformatedText;
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