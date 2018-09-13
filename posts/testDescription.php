<?php

    $imageArray = array();
    $descriptionArray = array();
    $connection;

    function initBuild(){
        
        include("database/conn.php");

        global $imageArray;
        global $descriptionArray;
        global $connection;
        
        //Create arrays for the posts information.
        $imageArray = array();
        $descriptionArray = array();
        $counter = 0;

        $connection = mysqli_connect("localhost", $dbUser, $dbPass);
        mysqli_select_db($connection, $dbName);

        $sql = "SELECT postid, image, description, short_description FROM posts";
        $result = $connection->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row

            while($row = $result->fetch_assoc()) {

                $descriptionArray[$counter] = $row["short_description"];
                $imageArray[$counter] = $row["image"];
                $counter = $counter + 1;
            }
        }
    }

    function rebuild(){
        
        include("../database/conn.php");
        
        global $imageArray;
        global $descriptionArray;
        global $connection;
        $counter = 0;

        $connection = mysqli_connect("localhost", $dbUser, $dbPass);
        mysqli_select_db($connection, $dbName);

        $sql = "SELECT postid, image, description, short_description FROM posts";
        $result = $connection->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row

            while($row = $result->fetch_assoc()) {

                $descriptionArray[$counter] = $row["short_description"];
                $imageArray[$counter] = $row["image"];
                $counter = $counter + 1;
            }
        }else{
     
            echo "!! - No rows were found...";
        }    
    }
?>