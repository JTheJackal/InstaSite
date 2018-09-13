<?php
        
    include "../database/conn.php";

    $infoArray = array();
    $imagesArray = array();
    $descriptionsArray = array();
    $usersResult;
    $postResult;


    // Create connection and select database.
    //$conn = new mysqli($servername, $username, $password);
    $connection = mysqli_connect("localhost", $dbUser, $dbPass);
    mysqli_select_db($connection, $dbName);

    // Check connection
    if(!$connection){

        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }

    //Echo connection status.
    if ($connection->connect_error) {

        die("Connection failed: " . $connection->connect_error);
    }

    $postsql = "SELECT image, description FROM posts";
    $usersql = "SELECT username, displayname, password, bio FROM users";
    $usersResult = $connection->query($usersql);
    $postResult = $connection->query($postsql);
    //$totalPosts = $result->num_rows;

    function getBio(){
        
        global $usersResult;
        
        if ($usersResult->num_rows > 0) {
        // output data of each row
        
            while($row = $usersResult->fetch_assoc()) {

                return $row["bio"];
            }
        }
    }

    function getTable(){
        
        include("../operations/stringOps.php");
        
        global $postResult;
        global $imagesArray;
        global $descriptionsArray;
        
        //$html = '<form action="../operations/removeFromDB.php" method="post">';
        $html = '';
        
        if ($postResult->num_rows > 0) {
        // output data of each row
        
            while($row = $postResult->fetch_assoc()) {

                array_push($imagesArray, $row["image"]);
                array_push($descriptionsArray, $row["description"]);
            }
        }
        
        for($i = 0; $i < sizeof($imagesArray); $i++){
        
            $tempDescription = removeQuotes($descriptionsArray[$i]);
            
            $html = $html . '<div class="tableRow">
                        <input type="text" class="imageBox" value="' . $imagesArray[$i] . '" name="imageBox' . $i . '"/>
                        <input type="text" class="descriptionBox" value="' . $tempDescription . '" name="descriptionBox' . $i . '" />
                        <input type="submit" class="deleteBTN" value="x" name="row' . $i . '" /> 
                    </div>';
        }
        
        //$html = $html . '</form>';
        return $html;
    }
?>