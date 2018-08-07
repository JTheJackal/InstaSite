<?php

                include "./posts/testDescription.php";$tile0 = "<div class='imgholder'>
                            <img class='aspectIMG' src='$imageArray[0]' width='100%' height='100%' />
                            <div class='textBox'>
                            <br>
                            <p1>$descriptionArray[0]</p1>
                            </div>
                        </div>
                        <div class='tileCover' onclick='window.location=\"https://www.google.com\"'>
                        </div>";$tile1 = "<div class='imgholder'>
                        <img class='aspectIMG' src='$imageArray[1]' width='100%' height='100%' />
                        <div class='textBox'>
                        <br>
                        <p1>$descriptionArray[1]</p1>
                        </div>
                    </div>
                    <div class='tileCover' onclick='window.location=\"https://www.google.com\"'>
                    </div>";$tile2 = "<div class='imgholder'>
                        <img class='aspectIMG' src='$imageArray[2]' width='100%' height='100%' />
                        <div class='textBox'>
                        <br>
                        <p1>$descriptionArray[2]</p1>
                        </div>
                    </div>
                    <div class='tileCover' onclick='window.location=\"https://www.google.com\"'>
                    </div>";

                $index = '<div class="col-12 pageContainer">
                            <div class="col-8 featuredContainer">
                            
                                <div class="container"><div class="row">
                                <div class="col-4 tile">'
                                . $tile0 .
                                '</div><div class="col-4 tile">'
                                . $tile1 .
                                '</div><div class="col-4 tile">'
                                . $tile2 .
                                '</div>
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
                      </div>'
                    ?>