<?php

    require "../database/conn.php";
    //header('Location: ../admin/index.php');

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

    $postsql = "SELECT postid, post, image FROM posts";
    $usersql = "SELECT username, displayname, password, bio FROM users";

    //$usersResult = $connection->query($usersql);
    $postResult = $connection->query($postsql);

    $numOfRows = $postResult->num_rows;

    //For all the posts stored, check which one had the delete button pressed.
    for($i = 0; $i < $numOfRows; $i++){
 
        if(isset($_POST['row' . $i])){
            $numPosts = $postResult->num_rows-1;
            $tempImage = $_POST ['imageBox'.$i];
   
            
            while($row = $postResult->fetch_assoc()) {
                
                if($tempImage == $row['image']){
                    
                    $tempPost = $row['post'];
                    $deletesql = "DELETE FROM posts WHERE image='" . $tempImage . "'";
                    removeLocalFile($tempPost);
                    $deletesql = "DELETE FROM posts WHERE post='" . $tempPost . "'";
                    removeLocalFile($tempImage);
                }
            }
            
            rebuildIndex($connection, $numPosts);
            removePost($tempImage, $connection, $deletesql);
        }
    }

    function removeLocalFile($file){
        
        unlink($file);
    }

    function removePost($tempImage, $connection, $sql){

        $connection->query($sql);
    }

    function rebuildIndex($conn, $numPosts){
        
        //include("../operations/stringOps.php");
        include("../posts/testDescription.php");
        //include("../install/installAction.php");
        include("../operations/constructSite.php");
        //include("../operations/stringOps.php");
        include("../database/conn.php");

        
        $themeNo = 3;
        $urlArray = array();
        
        $sql = "SELECT postid, post, image, description, short_description, postdate, likes, title, source FROM posts";
        $result = $conn->query($sql);
        
        //Collect all short descriptions.
        if ($numPosts > 0) {
        
            while($row = $result->fetch_assoc()) {

                //create a url from the post title and
                //push it to the array.
                $tempURL = createURL($row["title"], $row["postid"], $keyword);
                array_push($urlArray, $tempURL);
            }
        }
        
        rebuild();
        constructIndex($numPosts, "", $themeNo, $urlArray);
    }
?>