<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="../assets/css/admin.css">
        <title>InstaBuilder - Admin</title>
    </head>
    <body>
        
        <div class="col-12 adminBlue">
            <h2>ADMIN PANEL</h2>
        </div>
        
        <div class="col-12"> 
            <div class="container">
                <div onclick="" class="userDiv adminBlue"><h2>USER</h2></div>
                <div onclick="" class="postsDiv adminBlue"><h2>POSTS</h2></div>
                <div onclick="" class="settingsDiv adminBlue"><h2>SETTINGS</h2></div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="userPanel adminBlue">
                <div class="contentRow">
                    <img src="../assets/uploads/avatar.jpg" class="avatarPrev" />
                </div>
                <div class="uploadRow">
                    <input type="button" class="uploadBTN" value="Upload" /> 
                </div>
                <br />
                <div class="titleRow">
                    <p>About Section:</p>
                </div>
                <div class="contentRow">
                    <textarea rows="15" cols="50" class="bioBox">
                        <?php 
                            include "../operations/collectFromDB.php";
                        
                            //echo getBio();
                        ?>
                    </textarea>
                    <input type="submit" class="submitBTN" value="Save All"/> 
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="postsPanel adminBlue">
                <div class="titleRow">
                    <div class="imageCol"><p>Image URL</p></div>
                    <div class="descripCol"><p>Description</p></div>
                </div>
                
                <?php 
                   // include "../operations/collectFromDB.php";
                
                    echo getTable();
                ?>
                
                <div class="titleRow">
                    <p>Add New Posts</p>
                </div>
                <div class="titleRow">
                    <div class="keywordCol"><p>Keyword</p></div>
                    <div class="numCol"><p>Amount</p></div>
                </div>
                <div class="tableRow">
                    <input type="text" class="keywordsBox" />
                    <input type="number" class="numBox" />
                    <input type="submit" class="findBTN" value="Add Posts"/> 
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="postsPanel adminBlue">
                <div class="titleRow">
                    <div class="imageCol"><p>Site Title</p></div>
                </div>
                
                <div class="tableRow">
                    <input type="text" class="imageBox" />
                </div>
                <div class="titleRow">
                    <div class="imageCol"><p>Theme</p></div>
                </div>
                
                <div class="tableRow">
                    <select>
                        <option value="Dark">Dark</option>
                        <option value="Light">Light</option>
                        <option value="Pink">Pink</option>
                        <option value="Blue">Blue</option>
                    </select>
                </div>
            </div>
        </div>
    </body>
</html>