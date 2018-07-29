<?php 

    //26/07/2018
    //Joshua Styles
    //Handling database and registration details.
    //Confirms success to user.

    $totalPosts = 30;
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

    //Retrieve required SQL files and build tables.
    $sqlSource = file_get_contents('db/createUsersTable.sql');
    createTable($connection, $sqlSource);
    $sqlSource = file_get_contents('db/createPostsTable.sql');
    createTable($connection, $sqlSource);

    //Find the hashtags entered by the user and store them seperately in an array.
    $keywords = $_POST['siteKeywords'];

    //Count how many keywords were entered.
    $totalKeywords = findTotalKeywords($keywords);

    //Initialise array with the first hashtag.
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

    
    scrapeKeywords($totalPosts, $keywordsArray, $connection);


    //Check how many entries in the Database were made.
    $sql = "SELECT postid, image, description FROM posts";
    $result = $connection->query($sql);        
    $totalPosts = $result->num_rows;

    //Finally, generate HTML to build the desired site.
    constructSite($totalPosts);

    //Testing
    //testOutput($totalKeywords, $keywords, $keywordsArray);

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
                //$tempTitle = scrapeTitle($nodes, $i);
                $tempDescription = scrapeDescription($nodes, $i);
                $tempDate = scrapeDate($nodes, $i);
                $tempPoster = scrapePoster($nodes, $i);

                $tempSaveImage = file_get_contents($tempImage);

                //Store the image locally in case the URL should ever change
                $filepath = "../assets/uploads/image";
                $location = fopen($filepath . $i . ".jpg", "w");
                fwrite($location, $tempSaveImage);
                fclose($location);

                //Add post details to database table.
                $sqlSource = "INSERT INTO `Posts` (`image`, `description`, `postdate`, `postedby`) VALUES ('$filepath" . "$i.jpg', '$tempDescription', '$tempDate', '$tempPoster')";

                if(mysqli_query($connection, $sqlSource)){

                    echo "New record created successfully";
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

    function constructSite($totalPosts){
        
        
        
        $filename = "../operations/constructIndex.php";
        $ourFileName =$filename;
        $ourFileHandle = fopen($ourFileName, 'w');
        $generatedVarCode = generateVarsCode($totalPosts);
        $generatedTiles = createTilesHTML($totalPosts);


        //The code for the index page of the website
        $written = '<?php
                        
        include "./posts/testDescription.php";' . $generatedVarCode . '
        
        $index = \'<div class="header">
                    <div class="row">
                        <div class="col-4 banner"><h1>Header</h1></div>
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

            fwrite($ourFileHandle,$written);

            fclose($ourFileHandle);
    }

    function generateVarsCode($totalPosts){
        
        $tempVars = "";
        
        //Generate a new variable for each tile until the total requested has been matched.
        for($i = 0; $i < $totalPosts; $i++){
            
            if($i == 0){
                
                $tempVars = '$tile0 = "<div class=\'imgholder\'><img class=\'aspectIMG\' src=\'$imageArray[0]\' width=\'100%\' height=\'100%\' /><div class=\'textBox\'><br><p>$descriptionArray[0]</p></div></div>";';
            }else{
                
                $tempVars = $tempVars . '$tile'.$i.' = "<div class=\'imgholder\'><img class=\'aspectIMG\' src=\'$imageArray['.$i.']\' width=\'100%\' height=\'100%\' /><div class=\'textBox\'><br><p>$descriptionArray['.$i.']</p></div></div>";';
            }
        }
        
        return $tempVars;
    }

    function createTilesHTML($totalPosts){
        
        $tempHTML = "";
        
        for($i = 0; $i < $totalPosts; $i++){
            
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
        }
        
        return $tempHTML;
        
    }

    function findKeyword($string, $from, $to){
        
        //Locate a substring between desired search terms.
        $sub = substr($string, strpos($string,$from)+strlen($from),strlen($string));
        return substr($sub,0,strpos($sub,$to));
    }

    function extractKeyword($str, $removeStr){
        
        //Remove everything except the desired string.
        return str_replace("str", "", "#" . $removeStr . " ");
    }

    function removeString ($string, $search){
        
        //Remove nothing except the desired string.
        return str_replace($search, "", $string);
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

    