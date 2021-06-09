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
            $temp_SQL_CONDITIONS
            $capIDResponse = SQL::sqlrequest('SELECT capID FROM cap WHERE gameclassID =' . $row['gameclassID']);
            if ($capIDResponse->num_rows > 0) {
                while($row = $capIDResponse->fetch_assoc()) {
                    $capIDarray[] = $row['capID'];
                }
            }
            $this->capIDs = $capIDarray;
            //


        }
        function capindex($index){
            return new cap (SQL::sqlrequest('SELECT * FROM cap WHERE capID = '.$index));
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

    function categoryinit($options = []){
        $categorylist = [];
        $SQLresponse = SQL::sqlrequest("SELECT * FROM gameclass");

        if ($SQLresponse->num_rows > 0) {
            while($row = $SQLresponse->fetch_assoc()) {
                $categorylist[] = new gameCategory($row, $options);
            }
            return $categorylist;
        }
    }
    function play(){

        $options = [
            'shockcategory' => ['shocking', 'nudity']
        ];

        $x = categoryinit($options);

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