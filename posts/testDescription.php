<?php

    require("./database/conn.php");

    $connection = mysqli_connect("localhost", $dbUser, $dbPass);
    mysqli_select_db($connection, $dbName);

    $sql = "SELECT postid, image, title, description FROM posts";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
            $title1 = $row["title"];
            $description1 = $row["description"];
            $image = $row["image"];
        }
    }
?>