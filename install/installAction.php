<?php 

    //26/07/2018
    //Joshua Styles
    //Handling database and registration details.
    //Confirms success to user.

    require("../operations/stringOps.php");

    $totalPosts = 15;
    $servername = "localhost";
    $username = $_POST['dbUser'];
    $database = $_POST['dbName'];
    $password = $_POST['dbPass'];
    $themeNo = 3;

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

    //Retrieve required SQL files and build tables.
    $sqlSource = file_get_contents('db/createUsersTable.sql');
    createTable($connection, $sqlSource);
    $sqlSource = file_get_contents('db/createPostsTable.sql');
    createTable($connection, $sqlSource);

    //$headerPath = handleUpload("header");

    //Find the hashtags entered by the user and store them seperately in an array.
    $keywords = $_POST['siteKeywords'];

    //Count how many keywords were entered.
    $totalKeywords = findTotalKeywords($keywords);

    //Initialise array.
    $keywordsArray = array();

    if($totalKeywords > 1){
        
        for($i = 0; $i < $totalKeywords; $i++){
            
            //Improve in the future for multiple keyword support.
            
            /*
            if($i == $totalKeywords-1){
                
            
                //Check for the final keyword. If so, no need for searching and trimming.
                array_push($keywordsArray, $keywords);
                
            }else{
                
                //If there is more than one keyword, loop through them all, add
                //them to the array and trim them from the original string.
                $tempKeyword = findKeyword($keywords, "#", " ");
                $tempKeyword = extractKeyword($keywords, $tempKeyword);
                $tempKeyword = removeString($tempKeyword, "#");
                array_push($keywordsArray, $tempKeyword);
                $keywords = removeString($keywords, $tempKeyword);
            }
            */
        }
    }else{

        //Remove the "'" character from string. It breaks the SQL statements later on.
        $keywords = removeString($keywords, "'");
        array_push($keywordsArray, $keywords); 
    }

    //$header = validateImageFile();

    scrapeKeywords($totalPosts, $keywordsArray, $connection);


    //Check how many entries in the Database were made.
    $sql = "SELECT postid, image, description, short_description FROM posts";
    $result = $connection->query($sql);        
    $totalPosts = $result->num_rows;

    //Finally, generate HTML to build the desired site.
    constructSite($totalPosts, $headerPath, $themeNo);

    //Testing
    //testOutput($totalKeywords, $keywords, $keywordsArray);

    function handleUpload($type){
        
        $directory = "../assets/uploads/";
        $uploadOk = 0;

        switch ($type){
                
            case "header":
                
                //Handle the image uploads.
                $target_file = $directory . basename($_FILES["headerUpload"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                $fileName = "header." . pathinfo($_FILES["headerUpload"]["name"], PATHINFO_EXTENSION);
                $target_file = $directory . $fileName;
                
                break;
                
        }
        
        //Make sure the upload is an image.
        $check = getimagesize($_FILES["headerUpload"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["headerUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["headerUpload"]["tmp_name"], $target_file)) {
                echo "The file ". basename( $_FILES["headerUpload"]["name"]). " has been uploaded.";
                return $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    function validateImageFile(){
        
        $directory = "../assets/uploads/";
        
        $target_file = $directory . basename($_FILES["headerPic"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["headerPic"]["tmp_name"]);
            if($check !== false) {
                
                return true;
            } else {
                return false;
            }
        }
    }

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
        $" . "dbUser" . " = '" . $username . "';
        $" . "dbName" . " = '" . $database . "';
        $" . "dbPass" . " = '" . $password . "';
        ?>";

        fwrite($ourFileHandle,$written);

        fclose($ourFileHandle);
    }

    function scrapeKeywords($desiredNumPosts, $keywordsArray, $connection){
        
        require('../operations/scrape.php');
        
        //Run through each keyword in the array and scrape Instagram for it.
        for($j = 0; $j < sizeof($keywordsArray); $j++){
            
            $nodes = accessNodes($keywordsArray[$j]);
        
            for($i = 0; $i < $desiredNumPosts; $i++){

                //Retrieve info from the nodes
                $tempImage = scrapeImage($nodes, $i);
                $tempDescription = scrapeDescription($nodes, $i);
                $tempShortDescription = shortenDescription($tempDescription);
                $tempDate = scrapeDate($nodes, $i);
                $tempPoster = scrapePoster($nodes, $i);
                $tempSaveImage = file_get_contents($tempImage);

                //Store the image locally in case the URL should ever change
                $filepath = "../assets/uploads/image";
                $location = fopen($filepath . $i . ".jpg", "w");
                fwrite($location, $tempSaveImage);
                fclose($location);

                //Add post details to database table.
                $sqlSource = "INSERT INTO `Posts` (`image`, `description`, `short_description`, `postdate`, `postedby`) VALUES ('$filepath" . "$i.jpg', '$tempDescription', '$tempShortDescription', '$tempDate', '$tempPoster')";

                if(mysqli_query($connection, $sqlSource)){

                    //echo "New record created successfully";
                }else{

                    echo "Error creating post: " . $connection->error;
                    
                    //Failed because of the description. Try posting without.
                    $sqlSource = "INSERT INTO `Posts` (`image`, `postdate`, `postedby`) VALUES ('$filepath" . "$i.jpg', '$tempDate', '$tempPoster')";
                }
            }
        }
        
        //Uncomment to see node structure of JSON.
        //var_dump($nodes);
    }

    function constructSite($totalPosts, $headerPath, $themeNo){
        
        
        $filename = "../operations/constructIndex.php";
        $ourFileName =$filename;
        $ourFileHandle = fopen($ourFileName, 'w');
        $generatedVarCode = generateVarsCode($totalPosts, $themeNo);
        $generatedTiles = createTilesHTML($totalPosts, $themeNo);
        $profilePic = "../assets/uploads/avatar.jpg";
        $written = "";

        
        switch($themeNo){
                
            case 1:
                
                //The code for the index page of the website
                $written = '<?php

                include "./posts/testDescription.php";' . $generatedVarCode . '

                $index = \'<div class="header">
                            <div class="row">
                                <div class="col-4 banner"><img src="' . $headerPath . '" class="headerIMG" /></div>
                                <div class="col-5"></div>
                                <div class="col-3 navContainer">
                                    <div class="navBTN"><p>Log In</p></div>
                                    <div class="navBTN"><p>Search</p></div>
                                    <div class="navBTN"><p>Home</p></div>
                                </div>
                            </div>
                        </div>

                        <div class="container">' . $generatedTiles . '</div>

                        <div class="footer">
                            <div class="col-5"></div>
                            <div class="col-2">
                                <div class="navFootBTN"><p>Next</p></div>
                                <div class="navFootBTN"><p>Previous</p></div>
                            </div>
                            <div class="col-5"></div>
                        </div>\'
                    ?>';
                    break;
                
            case 2:
                
                $written = '<?php

                include "./posts/testDescription.php";' . $generatedVarCode . '

                $index = \'<div class="header">
                                <div class="col-6 banner"><img src="" class="headerIMG" /></div>
                                <div class="col-3"></div>
                                <div class="col-3 navContainer">
                                    <div class="navBTN"><img src="assets/graphics/home.png" class="navIMG" /></div>
                                    <div class="navBTN"><img src="assets/graphics/search.png" class="navIMG" /></div>
                                    <div class="navBTN"><img src="assets/graphics/home.png" class="navIMG" /></div>
                                </div>
                            </div>

                        <div class="col-8 featuredContainer">
                            <div class="featureBlock"></div>
                        </div>
                        <div class="col-4 bioContainer"> 
                            <div class="bioBlock">
                                <div class="col-9 welcomeBlock"><h1>Hello...</h1></div>
                                <div class="col-3 avatarBlock"></div>
                                <div class="col-12 welcomeBlockExt">
                                    
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In varius maximus dui, ac bibendum turpis hendrerit ut. Donec lacinia tempus elit ac laoreet. Vivamus rutrum sem sit amet ipsum blandit sodales. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec scelerisque ultricies ultrices.</p>
                                    <br />
                                    <p>Donec mattis lorem eros, pellentesque blandit turpis tristique sit amet. Quisque laoreet, dui sit amet consectetur fringilla, leo nulla pulvinar justo, id feugiat ipsum diam sed augue. Nunc feugiat orci purus, ut efficitur justo tempus sed. </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 reelContainer">
                            <div class="reelBlock"></div>
                        </div>

                        <div class="container">' . $generatedTiles . '</div>

                        <div class="footer">
                            <div class="col-5"></div>
                            <div class="col-2">
                                <div class="navFootBTN"><p>Next</p></div>
                                <div class="navFootBTN"><p>Previous</p></div>
                            </div>
                            <div class="col-5"></div>
                        </div>\'
                    ?>';
                    break;
                
            case 3:
                
                $written = '<?php

                include "./posts/testDescription.php";' . $generatedVarCode . '

                $index = \'<div class="col-12 pageContainer">
                            <div class="col-8 featuredContainer">
                            
                                <div class="container">' . $generatedTiles . '
                                </div>
                            </div>
                            
                          </div>
                        <div class="col-4 bioContainer">
                            <div class="col-8 welcomeBlock">
                                <h1>Hello...</h1>
                            </div>
                            <div class="col-4 avatarBlock">
                                <div class="avatar"></div>
                            </div>
                            <div class="col-12 welcomeBlockExt">
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla fringilla volutpat vulputate. Morbi ullamcorper vehicula ante, vitae ultricies tellus gravida euismod. Aliquam feugiat accumsan odio, eget accumsan nisi posuere sed. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque varius orci nulla, non lacinia risus bibendum pellentesque.</p>
                                <p>Maecenas eleifend tortor id dolor lacinia pretium. Pellentesque id eleifend turpis. Suspendisse nec augue ac ante pharetra mollis. Sed eu mi eu quam egestas placerat eu ut odio. Morbi et est vitae lacus tincidunt mattis. Praesent eu magna ipsum. Aenean a risus non justo sagittis sagittis eget eu justo. Maecenas vulputate ante ac lacinia gravida. Sed non tortor auctor, auctor tortor ac, pharetra elit.</p>
                            </div>
                            <div class="col-12 tagTrendHeader">
                                <p>Trending Tags</p>
                            </div>
                            <div class="col-12 tagTrendContent">
                                <p>#NoMoneyForMods, #Cars</p>
                            </div>
                        </div>
                      </div>\'
                    ?>';
                    break;
        }

        

            fwrite($ourFileHandle,$written);

            fclose($ourFileHandle);
    }

    function generateVarsCode($totalPosts, $themeNo){
        
        $tempVars = "";
        
        //Generate a new variable for each tile until the total requested has been matched.
        for($i = 0; $i < $totalPosts; $i++){
            
            switch($themeNo){
                    
                case 1:
                    
                    if($i == 0){
                
                        $tempVars = '$tile0 = "<div class=\'imgholder\'>
                            <img class=\'aspectIMG\' src=\'$imageArray[0]\' width=\'100%\' height=\'100%\' />
                                <div class=\'textBox\'>
                                <br>
                                <p1>$descriptionArray[0]</p1>
                            </div>
                        </div>";';
                    }else{

                        $tempVars = $tempVars . '$tile'.$i.' = "<div class=\'imgholder\'>
                            <img class=\'aspectIMG\' src=\'$imageArray['.$i.']\' width=\'100%\' height=\'100%\' />
                            <div class=\'textBox\'>
                            <br>
                            <p1>$descriptionArray['.$i.']</p1>
                            </div>
                        </div>";';
                    }
                    break;
                    
                case 2:
                    
                    if($i == 0){
                
                        $tempVars = '$tile0 = "<div class=\'imgholder\'>
                            <img class=\'aspectIMG\' src=\'$imageArray[0]\' width=\'100%\' height=\'100%\' />
                            <div class=\'textBox\'>
                            <br>
                            <p1>$descriptionArray[0]</p1>
                            </div>
                        </div>";';
                    }else{

                        $tempVars = $tempVars . '$tile'.$i.' = "<div class=\'imgholder\'>
                            <img class=\'aspectIMG\' src=\'$imageArray['.$i.']\' width=\'100%\' height=\'100%\' />
                            <div class=\'textBox\'>
                            <br>
                            <p1>$descriptionArray['.$i.']</p1>
                            </div>
                        </div>";';
                    }
                    break;
                    
                case 3:
                    
                    if($i == 0){
                
                        $tempVars = '$tile0 = "<div class=\'imgholder\'>
                            <img class=\'aspectIMG\' src=\'$imageArray[0]\' width=\'100%\' height=\'100%\' />
                            <div class=\'textBox\'>
                            <br>
                            <p1>$descriptionArray[0]</p1>
                            </div>
                        </div>
                        <div class=\'tileCover\' onclick=\'window.location=\"https://www.google.com\"\'>
                        </div>";';
                    }else{

                        $tempVars = $tempVars . '$tile'.$i.' = "<div class=\'imgholder\'>
                        <img class=\'aspectIMG\' src=\'$imageArray['.$i.']\' width=\'100%\' height=\'100%\' />
                        <div class=\'textBox\'>
                        <br>
                        <p1>$descriptionArray['.$i.']</p1>
                        </div>
                    </div>
                    <div class=\'tileCover\' onclick=\'window.location=\"https://www.google.com\"\'>
                    </div>";';
                    }
                    break;
            }
            
        }
        
        return $tempVars;
    }

    function createTilesHTML($totalPosts, $themeNo){
        
        $tempHTML = "";
        
        for($i = 0; $i < $totalPosts; $i++){
            switch($themeNo){

                case 1:

                    if($i == 0){

                        $tempHTML = '<div class="row">
                                <div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>';
                    }else if($i % 3 == 0 && $i > 0){

                        $tempHTML = $tempHTML . '</div>
                                <div class="row">
                                <div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>';
                    }else if($i % 3 == 0 && $i > 0 && $i+1 == $totalPosts){

                        $tempHTML = $tempHTML . '</div>
                                <div class="row">
                                <div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>
                                </div>';
                    }else{

                        $tempHTML = $tempHTML . '<div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>';
                    }
                    break;
                    
                case 2:
                    
                    if($i == 0){

                        $tempHTML = '<div class="row">
                                <div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>';
                    }else if($i % 3 == 0 && $i > 0){

                        $tempHTML = $tempHTML . '</div>
                                <div class="row">
                                <div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>';
                    }else if($i % 3 == 0 && $i > 0 && $i+1 == $totalPosts){

                        $tempHTML = $tempHTML . '</div>
                                <div class="row">
                                <div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>
                                </div>';
                    }else{

                        $tempHTML = $tempHTML . '<div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>';
                    }
                    break;
                    
                case 3:
                    
                    if($i == 0){

                        $tempHTML = '<div class="row">
                                <div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>';
                    }else if($i % 3 == 0 && $i > 0){

                        $tempHTML = $tempHTML . '</div>
                                <div class="row">
                                <div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>';
                    }else if($i % 3 == 0 && $i > 0 && $i+1 == $totalPosts){

                        $tempHTML = $tempHTML . '</div>
                                <div class="row">
                                <div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>
                                </div>';
                    }else{

                        $tempHTML = $tempHTML . '<div class="col-4 tile">\'
                                . $tile'.$i.' .
                                \'</div>';
                    }
                    break;
            } 
        }
        
        return $tempHTML;
        
    }

    function testOutput($total, $words, $output){
        
        
        if($total > 0){
            
            echo "\n Total keywords: $total \n";
            
            for($i = 0; $i < count($output); $i++){

                echo "Array $i $output[$i]";
            }
        }else{
            
            echo "No keywords were entered. Imploding now";
        }
    }

    function findTotalKeywords($keywords){
        
        //Search for how many instances of # are used.
        return substr_count($keywords, "#");
    }
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/stylesheet1.css">
        <title>InstaBuilder</title>
    </head>
<body>
    <div class="row header">
        <div class="col-4 darker fillHeight"></div>
        <div class="col-4 dark fillHeight">
            <h1>InstaSite Installer</h1>
        </div>
        <div class="col-4 darker fillHeight"></div>
    </div>

    <div class="row slim">
        <div class="col-4 darker fillHeight"></div>
        <div class="col-4 infoPanel fillHeight">
            <p>Fill in the information and submit to install the site.</p>
        </div>
        <div class="col-4 darker fillHeight"></div>
    </div>


        <div class="row formTheme">
            <div class="col-4 darker fillHeight"></div>
            <div class="col-4 dark fillHeight">


            </div>
            <div class="col-4 darker fillHeight"></div>
        </div>

        <div class="row formTheme">
            <div class="col-4 darker fillHeight"></div>
            <div class="col-4 dark fillHeight">

            </div>
            <div class="col-4 darker fillHeight"></div>
        </div>
</body>
</html>

    