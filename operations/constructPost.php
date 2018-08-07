<?php

    include '../operations/stringOps.php';

    //Shorten the name of the URL to find the name of the current file.
    $siteName = $_SERVER['PHP_SELF'];
    $siteName = removeString($siteName, "/posts/");
    
    //search the array for the post number or search the db for which row.

    
?>