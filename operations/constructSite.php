<?php

    function constructIndex($totalPosts, $headerPath, $themeNo, $urlArray){
        
        $filename = "../operations/constructIndex.php";
        $ourFileName = $filename;
        
        $ourFileHandle = fopen($ourFileName, 'w');
        $generatedVarCode = generateVarsCode($totalPosts, $themeNo, $urlArray);
        $generatedTiles = createTilesHTML($totalPosts, $themeNo);
        $profilePic = "../assets/uploads/avatar.jpg";
        $written = "";

        switch($themeNo){

            case 1:

                //The code for the index page of the website
                $written = '<?php

                include("../posts/testDescription.php");' . $generatedVarCode . '

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

                include "../posts/testDescription.php";' . $generatedVarCode . '

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

                include "./posts/testDescription.php";
                    initBuild();' . $generatedVarCode . '

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

        fwrite($ourFileHandle, $written);
        fclose($ourFileHandle);
    }

    function generateVarsCode($totalPosts, $themeNo, $urlArray){

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

                        $tempVars = $tempVars . '$tile' . $i . ' = "<div class=\'imgholder\'>
                            <img class=\'aspectIMG\' src=\'$imageArray[' . $i . ']\' width=\'100%\' height=\'100%\' />
                            <div class=\'textBox\'>
                            <br>
                            <p1>$descriptionArray[' . $i . ']</p1>
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
                        <div class=\'tileCover\' onclick=\'window.location=\"posts/' . $urlArray[$i] . '.php\"\'>
                        </div>";';
                    }else{

                        $tempVars = $tempVars . '$tile'.$i.' = "<div class=\'imgholder\'>
                        <img class=\'aspectIMG\' src=\'$imageArray['.$i.']\' width=\'100%\' height=\'100%\' />
                        <div class=\'textBox\'>
                        <br>
                        <p1>$descriptionArray['.$i.']</p1>
                        </div>
                    </div>
                    <div class=\'tileCover\' onclick=\'window.location=\"posts/' . $urlArray[$i] . '.php\"\'>
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
?>