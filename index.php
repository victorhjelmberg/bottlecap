<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-16">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="recources/html5reset-1.6.1.css">
    <link rel="stylesheet" href="recources/basicstyle.css">

    <!-- Set Cookie -->
    <script>
        //Set expireyDate
        const d = new Date();
        d.setTime(d.getTime() + (12*60*60*1000)); //Expires in 12 hours
        const expires = "expires="+ d.toUTCString();

        document.cookie = "requestData=options:{shockcategory : []};" + expires;
    </script>

    <!-- API  -->
    <script src="recources/blocks.js"></script>

    <!-- On initialise json-data -->

    <!--
    <script>
        const testFetch; <?php include $_SERVER['DOCUMENT_ROOT'].'/ølkapsel/recources/gameclasses.php'; ?>
    </script>
    -->

    <!-- Display -->
    <script src="recources/jquery-3.6.0.js"></script>
    <script src="recources/javascript.js"></script>

    <title>Ølkapsel</title>
</head>
<body>

    <div id="startmenucontainer" class="menucontainer">
        <img id="startmenulogo" src="recources/logo.svg">
        <button id="startmenubutton" class="button"><b>Spil</b></button>
    </div>

    <div id="gameobj1" class="menucontainer">
        <div class="gamec" id="ani1">
        <!--    <object class="gamecap" data="recources/green-circle.svg" type="image/svg+xml"></object> -->
            <i id="lefti"></i>
            <i id="righti"></i>
            <p class="gameobjtitle">Title</p>
            <p class="gameobjtext">hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj hej hej ehj ehj ehj
            </p>

        </div>

        <button id="backbutton" class="button"><b>Tilbage</b></button>
    </div>

</body>
</html>
