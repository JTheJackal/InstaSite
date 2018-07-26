<?php 

    //26/07/2018
    //Joshua Styles
    //Handling database and registration details.
    //Confirms success to user.

    $servername = "localhost";
    $username = $_POST['dbUser'];
    $database = $_POST['dbName'];
    $password = $_POST['dbPass'];

    // Create connection and select database.
    //$conn = new mysqli($servername, $username, $password);
    $connection = mysqli_connect($servername, $username, $password);
    mysqli_select_db($connection, $database);

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
    }else{
        echo "Connected successfully";
    }

    //Create required tables in the Database.
    
?>

    