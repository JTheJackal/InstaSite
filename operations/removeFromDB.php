<?php

    include "../database/conn.php";
    header('Location: ../admin/index.php');

    $infoArray = array();
    $imagesArray = array();
    $descriptionsArray = array();
    $usersResult;
    $postResult;
    $rows = array();

    // Create connection and select database.
    //$conn = new mysqli($servername, $username, $password);
    $connection = mysqli_connect("localhost", $dbUser, $dbPass);
    mysqli_select_db($connection, $dbName);

    $postsql = "SELECT postid, image FROM posts";
    $usersql = "SELECT username, displayname, password, bio FROM users";
    
    $usersResult = $connection->query($usersql);
    $postResult = $connection->query($postsql);

    //For all the posts stored, check which one had the delete button pressed.
    for($i = 0; $i < $postResult->num_rows; $i++){
        
        if(isset($_POST['row' . $i])){
        
            $tempImage = $_POST ['imageBox'.$i];
            $deletesql = "DELETE FROM posts WHERE image='" . $tempImage . "'";
                
            removePost($tempImage, $connection, $deletesql);
        }
    }

    function removePost($tempImage, $connection, $sql){
        
        $connection->query($sql);
        
        echo "post deleted - " . $tempImage;
    }
?>