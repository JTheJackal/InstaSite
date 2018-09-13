<?php

    include("../database/conn.php");
    include("../")

    // Create connection and select database.
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

    //Construct the SQL code for getting the info from the DB.
    $postsql = "SELECT image, description, short_description, title FROM posts";
    $usersql = "SELECT username, displayname, password, bio FROM users";

    //Query the DB using the SQL, and store the returned object in a variable.
    $postResult = $connection->query($postsql);
    $usersResult = $connection->query($usersql);

    switch(true){
            
        case isset($_POST['saveUser']):
            
            editBio();
            break;
            
        case isset($_POST['saveAllPosts']):
            
            editPosts();
            break;
    }

    function editPosts(){

        global $postResult;
        global $connection;
        
        for($i = 0; $i < $postResult->num_rows; $i++){
            
            $tempDescription = $_POST['descriptionBox'.$i];
            $tempImage = $_POST['imageBox'.$i];
            $tempShortDescription = shortenDescription($tempDescription);
            $tempTitle = createTitle($tempShortDescription);
            
            $tempSQL1 = "UPDATE posts SET description = '" . $tempDescription . "' WHERE image = '" . $tempImage . "'";
            $tempSQL2 = "UPDATE posts SET short_description = '" . $tempShortDescription . "' WHERE image = '" . $tempImage . "'";
            $tempSQL3 = "UPDATE posts SET title = '" . $tempTitle . "' WHERE image = '" . $tempImage . "'";
            
            $connection->query($tempSQL1);
            $connection->query($tempSQL2);
            $connection->query($tempSQL3);
        }
        
        //$connection->query($SQL);
        
    }
    
    function editBio(){
        
        global $connection;
        
        $newBio = $_POST['userBio'];
        
        if(isset($_POST['saveUser'])){

            $SQL = "UPDATE users SET bio = '" . $newBio . "' WHERE id = 1";
        }
        
        $connection->query($SQL);
    }

    function editTitle(){
        
        
    }
?>