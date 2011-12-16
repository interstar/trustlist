<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> 
    <link rel="stylesheet" type="text/css" href="css//blueprint/screen.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>

<body>


<?php 

    include( 'application.php' );

    $username = $_GET{"user"};
    $listname = $_GET{"list"};
    $recalc = $_GET{"recalc"};   
    
    
    if (isset($username) and isset($listname)){

        $TN = new TrustNet($username, $listname);
        $results = True;

    }

?>

<!-- Main container -->

<div id="main" class="span-22 prepend-3">

    <img src="images/connections.gif">

    <h1 style="display:inline">TrustNet*</h1>
    <h2>

    <?php 
    
    /* If there are results, descrive in subtitle */
    
    if ($results == True){
        $subtitle = "Mapping <a href='http://wiki.thenextedge.org/doku.php?id=twitter_trustlists'> $TN->listname </a> from user <a href=\"http://www.twitter.com/webisteme\"> $TN->username </a>"; 
    } else {
        $subtitle = "A tool for mapping trust on Twitter.";
    }
    
    echo $subtitle;
    
    ?>

    </h2>
    
    
    <!--    LEFT SIDE -->
          
    <div id="left" class="span-10">  


        <div id="searchForm">
        <h3>Search</h3>
        <p class="success">Enter a Twitter username and list to start crawling from.</p>
        <br>
        <form method="GET" action="index.php">
            <h3 style="color:grey">User Name <br><input type="text" name="user"/ placeholder="@username"> <br/>
            <h3 style="color:grey">List Name <br><input type="text" name="list" placeholder="e.g. tne-github"/> </p>
            <input type="submit" value="Crawl"/>
        </form>
        </div>
    
    </div>

    
    <!-- RIGHT SIDE -->
    
    <div id="right" class="span-10 prepend-2 last">

    <?php
    
    if ($results == True){
               
        if ($TN->checkExists() == False) {
        
                    $TN->buildNet();
                    
                    echo "<p class=\"message\">File didn't exist ... Creating Trust Network</p>"; 
                    echo "<meta http-equiv=\"refresh\" content=\"25; url=?user=$TN->username&list=$TN->listname\"/>"; #autorefresh in 25 seconds
                    echo "<div><a href='?user=$TN->username&list=$TN->listname'>Reload</a></div>";
        
                } elseif ($recalc=="1") {
                
                    $TN->buildNet();
                
                    echo "<p class=\"message\">Recreating Trust Network</p>";
                    echo "<meta http-equiv=\"refresh\" content=\"25; url=?user=$TN->username&list=$TN->listname\"/>"; #autorefresh in 25 seconds
                    echo "<div><a href='?user=$TN->username&list=$TN->listname'>Reload</a></div>";
                      
                } else {
        
                    $TN->displayNet();
        
                }
                
            } else {


    ?>    
    
    <h3>What is this?</h3>
    
    <p>TrustNet crawls Twitter lists with a given name, and maps trust relationships within the resulting network.</p>
    
    <br>
    
    <h3>To star your own TrustNet</h3>
    
    <ul>
        
        <li> Create a Twitter list with a given name, e.g. "couchbuddies"</li>
        <li> Get some other people who are in your list to create their own list, with the same name.</li>
        <li> Put your username and the name of the list in the search box to the left</li>
    
    </ul>
    
    <br>
    
    <p>For example trustlists, check the <a href="http://wiki.thenextedge.org/doku.php?id=twitter_trustlists">wiki.</a></p>
    
    
    <?php    
    
    }
    
    ?>
    
    <?php
    
        if ($results == True){

            $TN->displayCloud();
        
        }
    
    ?>
    

    </div>
    
    <hr class="space">

    <div id="footer" class="span-10">
    
        <p style="display:inline">*alpha software
    
        <a href="http://wiki.thenextedge.org/doku.php?id=twitter_trustlists">Wiki</a> | 
        <a href="http://www.github.com/webisteme/trustlist">GitHub</a>
    
        </p>
    
    </div>

</div>


</body>

</html>

