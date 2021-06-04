<?php
    $file = fopen("readme.txt","r");
    echo fread($file,filesize("readme.txt"));

?>