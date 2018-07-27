<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="assets/css/stylesheet1.css">
        <title>InstaBuilder</title>
    </head>
<body>

    <div class="header">
        <div class="row">
            <div class="col-2"><h1>Header</h1></div>
            <div class="col-5"></div>
            <div class="col-5"><p>NavBar</p></div>
        </div>
    </div>
    
    <div class="container">    
        <div class="row">
            <div class="col-4 tile">
                <p>IMG</p>
                <div class="textBox">
                    <?php include 'posts/testDescription.php';
                        echo "<h3>$title1</h3><br><p>$description1</p>";
                    
                    ?>
                </div>
            </div>
            <div class="col-4 tile">
                <p>IMG</p>
            </div>
            <div class="col-4 tile">
                <p>IMG</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-4 tile">
                <p>IMG</p>
            </div>
            <div class="col-4 tile">
                <p>IMG</p>
            </div>
            <div class="col-4 tile">
                <p>IMG</p>
            </div>
        </div>

        <div class="row">
            <div class="col-4 tile">
                <p>IMG</p>
            </div>
            <div class="col-4 tile">
                <p>IMG</p>
            </div>
            <div class="col-4 tile">
                <p>IMG</p>
            </div>
        </div>
            
        <div class="header">
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4"><p>NavBar</p></div>
                <div class="col-4"></div>
            </div>
        </div>
    </div>
            
</body>
</html>