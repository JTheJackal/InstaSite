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

    //Retrieve required SQL file.
    $sqlSource = file_get_contents('../database/initOps.sql');
    //$sqlSource = file('../database/initOps.sql');
    //$sql_contents = SplitSQL($sqlSource);
        
    //Create required tables in the Database.
    if (mysqli_query($connection, $sqlSource)) {
    //if ($connection->query($sql_contents) === TRUE) {
        echo "Tables created successfully";
    } else {
        echo "Error creating tables: " . $connection->error;
    }

    function SplitSQL($file, $delimiter = ';'){

        $lines = file($file);
        // Loop through each line
        foreach ($lines as $line) {
        // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            // Add this line to the current segment
            $templine .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                mysql_query($templine);
                // Reset temp variable to empty
                $templine = '';
            }
        }
    }
?>

    