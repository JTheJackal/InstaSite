<?php

    require("./database/conn.php");

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
            //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
            //$description1 = $row["description"];
            //$image = $row["image"];
            
            $descriptionArray[$counter] = $row["short_description"];
            $imageArray[$counter] = $row["image"];
            $counter = $counter + 1;
        }
    }
?>