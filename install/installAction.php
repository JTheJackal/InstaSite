<?php 

    //26/07/2018
    //Joshua Styles
    //Handling database and registration details.
    //Confirms success to user.

    require("../operations/stringOps.php");


    //Set the maximum allowed execution time for this script. 8 minutes currently.
    ini_set('max_execution_time', 480);

    //Continually changing the max_id for the instagram API call will allow the scraper to use as many result as we like instead of just the first batch.
    $maxID = "";
    $totalPosts = 20;
    $foundPosts = 0;
    $failsafe = 500;
    $servername = "localhost";
    $username = $_POST['dbUser'];
    $database = $_POST['dbName'];
    $password = $_POST['dbPass'];
    $themeNo = 3;
    $URLarray = array();
    $postsArray = array();

    

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
        //echo "Connected successfully";
        writeFile($username, $password, $database, $keywordsArray[0]);
    }

    //Retrieve required SQL files and build tables.
    $sqlSource = file_get_contents('db/createUsersTable.sql');
    createTable($connection, $sqlSource);
    $sqlSource = file_get_contents('db/createPostsTable.sql');
    createTable($connection, $sqlSource);

    //$header = validateImageFile();

    while($foundPosts < $totalPosts){
        
        scrapeKeywords($totalPosts, $keywordsArray, $connection);
        $failsafe++;
    }


    //Check how many entries in the Database were made.
    $sql = "SELECT postid, image, description, short_description, title FROM posts";
    $result = $connection->query($sql);        
    $totalPosts = $result->num_rows;

    //Finally, generate HTML to build the desired site.
    //constructSite($totalPosts, $headerPath, $themeNo);
    //constructSite($totalPosts, "", $themeNo);

    constructPostPages($connection, $totalPosts, "", $themeNo, $keywords);
    //constructPosts($totalPosts, $headerPath, $themeNo);

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
                //echo "The file ". basename( $_FILES["headerUpload"]["name"]). " has been uploaded.";
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
            //echo "Tables created successfully";
        } else {
            echo "Error creating tables: " . $connection->error;
        }
    }

    function writeFile($username, $password, $database, $keyword){
        
        $filename = "../database/conn.php";
        $ourFileName =$filename;
        $ourFileHandle = fopen($ourFileName, 'w');



        $written =  "<?php
        //Database connection details
        $" . "dbUser" . " = '" . $username . "';
        $" . "dbName" . " = '" . $database . "';
        $" . "dbPass" . " = '" . $password . "';
        $" . "keyword" . " = '" . $keyword . "';
        ?>";

        fwrite($ourFileHandle,$written);

        fclose($ourFileHandle);
    }

    function scrapeKeywords($desiredNumPosts, $keywordsArray, $connection){
        
        require_once('../operations/scrape.php');
        
        /*
        //Run through each keyword in the array and scrape Instagram for it.
        for($j = 0; $j < sizeof($keywordsArray); $j++){
            
            $nodes = accessNodes($keywordsArray[$j]);
        
            //While condition is true
            for($i = 0; $i < $desiredNumPosts; $i++){

                //If counter < desired{
                
                //else{
                //counter++;
                
                //Retrieve info from the nodes
                $tempImage = scrapeImage($nodes, $i);
                $tempDescription = scrapeDescription($nodes, $i);
                $tempShortDescription = shortenDescription($tempDescription);
                $tempTitle = createTitle($tempShortDescription);
                $tempDate = scrapeDate($nodes, $i);
                $tempPoster = scrapePoster($nodes, $i);
                $tempSaveImage = file_get_contents($tempImage);

                //Store the image locally in case the URL should ever change
                $filepath = "../assets/uploads/image";
                $location = fopen($filepath . $i . ".jpg", "w");
                fwrite($location, $tempSaveImage);
                fclose($location);

                //Add post details to database table.
                $sqlSource = "INSERT INTO `Posts` (`image`, `description`, `short_description`, `postdate`, `postedby`, `title`) VALUES ('$filepath" . "$i.jpg', '$tempDescription', '$tempShortDescription', '$tempDate', '$tempPoster', '$tempTitle')";

                if(mysqli_query($connection, $sqlSource)){

                    //echo "New record created successfully";
                }else{

                    echo "Error creating post: " . $connection->error;
                    
                    //Failed because of the description. Try posting without.
                    $sqlSource = "INSERT INTO `Posts` (`image`, `postdate`, `postedby`) VALUES ('$filepath" . "$i.jpg', '$tempDate', '$tempPoster')";
                }
            }
        }
        */
        
        $i = 0;
        $counter = 0;
        $nodes = null;
        global $foundPosts;
        
        //Run through each keyword in the array and scrape Instagram for it.
        for($j = 0; $j < sizeof($keywordsArray); $j++){
            
            $nodes = accessNodes($keywordsArray[$j]);
            
            //echo "NUMBER OF NODES______";
            //echo sizeof($nodes);
            //echo "NUMBER OF NODS_____";
        
            //Run through every post collected for the keyword and discard if the description is too small.
            while($counter < $desiredNumPosts && $i < sizeof($nodes)){
            //for($i = 0; $i < $desiredNumPosts; $i++){

                //If counter < desired{
                
                //else{
                //counter++;
                
                //Retrieve info from the nodes
                //$tempImage = scrapeImage($nodes, $i);
                $tempImage = scrapeImage($nodes, $i);
                $tempDescription = scrapeDescription($nodes, $i);
                
                //list($width, $height) = getimagesize($tempImage);
                
                //Ensure the description is more than 150 characters long, and that the image is horizontal.
                if(strlen($tempDescription) > 150 && getimagesize($tempImage)[0] > getimagesize($tempImage)[1]){
                    
                    //increment counter
                    $foundPosts++;
                    $counter++;
                    $tempShortDescription = shortenDescription($tempDescription);
                    $tempTitle = createTitle($tempShortDescription);
                    $tempDate = scrapeDate($nodes, $i);
                    $tempPoster = scrapePoster($nodes, $i);
                    $source = scrapeSource($nodes, $i);
                    $likes = scrapeLikes($nodes, $i);
                    $tempSaveImage = file_get_contents($tempImage);

                    //Store the image locally in case the URL should ever change
                    $filepath = "../assets/uploads/image";
                    $location = fopen($filepath . $foundPosts . ".jpg", "w");
                    fwrite($location, $tempSaveImage);
                    fclose($location);

                    //Add post details to database table.
                    $sqlSource = "INSERT INTO `Posts` (`image`, `description`, `short_description`, `postdate`, `postedby`, `likes`, `title`, `source`) VALUES ('$filepath" . "$foundPosts.jpg', '$tempDescription', '$tempShortDescription', '$tempDate', '$tempPoster', '$likes', '$tempTitle', '$source')";

                    if(mysqli_query($connection, $sqlSource)){

                        //echo "New record created successfully";
                    }else{

                        echo "Error creating post: " . $connection->error;

                        //Failed because of the description. Try posting without.
                        $sqlSource = "INSERT INTO `Posts` (`image`, `postdate`, `postedby`) VALUES ('$filepath" . "$i.jpg', '$tempDate', '$tempPoster')";
                    }
                }
                
                $i++;
            }
        }
        
        //Uncomment to see node structure of JSON.
        //var_dump($nodes);
    }

    function constructPostPages($connection, $totalPosts, $headerPath, $themeNo, $keywords){
            
        include("../operations/constructSite.php");
        
        global $postsArray;
        global $connection;
        
        $urlArray = array();
        
        $sql = "SELECT postid, post, image, description, short_description, postdate, likes, title, source FROM posts";
        $result = $connection->query($sql);
        
        //Collect all short descriptions.
        if ($result->num_rows > 0) {
        
            while($row = $result->fetch_assoc()) {

                //create a url from the post title and
                //push it to the array.
                $tempURL = createURL($row["title"], $row["postid"], $keywords);
                array_push($urlArray, $tempURL);
                
                $tempDate = $row["postdate"];
                $tempDate = date('M j, Y', $tempDate);
                
                //prepare the name of the new file to create, using the newly generated url.
                $filename = "../posts/" . $tempURL . ".php";
                $ourFileName = $filename;
                
                //Add the file path to the database.
                $tempSQL = "UPDATE posts SET post = '" . $ourFileName . "' WHERE postid = '" . $row["postid"] . "'";
                $connection->query($tempSQL);
                
                //Create the file.
                $ourFileHandle = fopen($ourFileName, 'w') or die("cannot open this file");
                $written = "";
                
                $likes = $row["likes"];
                
                $tempTagsBox = createTagsHTML($row["description"]);


                switch($themeNo){

                    //Construct the HTML based on the theme selected.
                        
                    case 1:

                            break;
                    case 2:                

                            break;
                    case 3:

                        $written = '
                        
                        <html>
                            <head>
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <link href="https://fonts.googleapis.com/css?family=Raleway:300" rel="stylesheet">
                                <link rel="stylesheet" type="text/css" href="../assets/css/stylesheet3.css">

                                <title>' . $row["title"] . '</title>
                            </head>
                            <body>
                                <div class="postPageContainer">
                                    <div class="col-3 sideColumn">
                                        <div class="buttonsContainer">
                                            <div class="info">
                                                <div class="infoProfile">
                                                    <p3>Posted:</p3>
                                                </div>
                                                <div class="infoAdditional"><p>' . $tempDate . '</p></div>
                                            </div>
                                            <div class="info">
                                                <div class="infoProfile">
                                                    <p3>Posted:</p3>
                                                </div>
                                                <div class="infoAdditional"><p>' . $tempDate . '</p></div>
                                            </div>
                                            <div class="info">
                                                <div class="infoProfile">
                                                    <p3>Likes: </p3>
                                                </div>
                                                <div class="infoAdditional"><p>' . $likes . '</p></div>
                                            </div>
                                            <div class="info">
                                                <div class="infoProfile">
                                                    <p3>Source:</p3>
                                                </div>
                                                <div class="infoAdditional"><p><a href="' . $row["source"] . '">Instagram.com</a></p></div>
                                            </div> 
                                        </div>
                                        <div class="tagsTitle">
                                        <postText>Tags</postText></div>
                                        <div class="tagsContainer">' . $tempTagsBox . '</div>
                                    </div>
                                    
                                    <div class="col-6 middleContainer">

                                        <div class="titleBox">  <postText>' . $row["title"] . '</postText>
                                        </div>
                                        
                                        <div class="featuredIMGBox">
                                            <div class="featuredIMG" style="background-image: URL(' . $row["image"] . '); background-repeat: no-repeat">
                                            </div>
                                        </div>
                                        
                                        <div class="lowerBox"></div>
                                    </div>
                                    
                                    <div class="col-3 sideColumn2">

                                        <div class="descripTitle">
                                            <postText>Description</postText>
                                        </div>
                                        
                                        <div class="descriptionContainer">
                                                <p>' . $row["description"] . '</p>
                                            </div>
                                    </div>
                                </div>
                            </body>
                        </html>';
                        break;
                    }

                    fwrite($ourFileHandle,$written);

                    fclose($ourFileHandle);
                    }
                }
        
        //Construct the index page. Pass the array of post url's in for linking.
        constructIndex($totalPosts, "", $themeNo, $urlArray);
    }

    function testOutput($total, $words, $output){
        
        
        if($total > 0){
            
            //echo "\n Total keywords: $total \n";
            
            for($i = 0; $i < count($output); $i++){

                //echo "Array $i $output[$i]";
            }
        }else{
            
            //echo "No keywords were entered. Imploding now";
        }
    }

    function findTotalKeywords($keywords){
        
        //Search for how many instances of # are used.
        return substr_count($keywords, "#");
    }

    function createTagsHTML($description){
        
        global $keywordsArray;
        $tagsArray = array();
        $tagsHTML = '';
        
        //parse hashtags out of the string
        $tempTagsArray = extractHashtags($description); 
        $tagsArray = array();
        
        for($i = 0; $i < sizeof($tempTagsArray[0]); $i++){  
            
            if(strlen($tempTagsArray[0][$i]) < 16){
                
                echo "The string being added is: " . $tempTagsArray[0][$i];
                array_push($tagsArray, $tempTagsArray[0][$i]);
            }
        }
        
        //Keep only 15 tags in the array.
        $tagsArray = array_slice($tagsArray, 0, 15);
        
        if(sizeof($tagsArray) < 1){
            
            for($i = 0; $i < sizeof($keywordsArray); $i++){
                
                array_push($tagsArray, $keywordsArray);
            }
        }
        
        //$tagsArray = array_slice($tagsArray, 0, 20);
        
        //Make sure the description isn't empty.
        if(strlen($description) < 1){
            
            return '<div class="tagBubble">
                        <tag>#Cars</tag>
                    </div>';
        }else{
            
            //for($i = 0; $i < sizeof($tagsArray[0]); $i++){
            for($i = 0; $i < sizeof($tagsArray); $i++){
                
                /*$tagsHTML = $tagsHTML . '<div class="tagBubble">
                                            <tag>' . $tagsArray[0][$i] . '</tag>
                                        </div>';*/
                 $tagsHTML = $tagsHTML . '<div class="tagBubble">
                                            <tag>' . $tagsArray[$i] . '</tag>
                                        </div>';
            }
            return $tagsHTML;
        }
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

    