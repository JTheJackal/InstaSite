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
        writeFile($username, $password, $database);
    }

    //Retrieve required SQL file.
    //$sqlSource = file_get_contents('../database/initOps.sql');
    $sqlSource = file_get_contents('db/createUsersTable.sql');
    createTable($connection, $sqlSource);

    $sqlSource = file_get_contents('db/createPostsTable.sql');
    createTable($connection, $sqlSource);
    

    function createTable($connection, $sqlSource){
        
        //Create required tables in the Database.
        if (mysqli_query($connection, $sqlSource)) {
            echo "Tables created successfully";
        } else {
            echo "Error creating tables: " . $connection->error;
        }
    }

    function writeFile($username, $password, $database){
        
        $filename = "../database/conn.php";
        $ourFileName =$filename;
        $ourFileHandle = fopen($ourFileName, 'w');



        $written =  "<?php
        
                     //Database connection details
                     $dbUser = " . $username . "
                     $dbName = " . $database . "
                     $dbPass = " . $password . ";
                     ?>";

        fwrite($ourFileHandle,$written);

        fclose($ourFileHandle);
    }
?>

    