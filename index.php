<html>
<head>
    <link rel="stylesheet" type="text/css" href="css//blueprint/screen.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>

<body>
<?php 
    $user_name = $_GET{"user"};
    $list_name = $_GET{"list"};
    $recalc = $_GET{"recalc"};
    
    $f_name = $user_name . "." . $list_name;
 
    $userwhite = "/\A([a-zA-Z0-9_]){1,15}\z/";

    #This is actually not complete, apparently almost all characters are allowed.
    #We can use this more restrictive set though.
    $listwhite = "/\A([a-zA-Z0-9-_]){1,25}\z/";
    
    if (isset($user_name) and isset($list_name)){

        if (!(preg_match($userwhite,$user_name,$matches))) {
            exit("<p class=\"error\">Invalid user name</p>");
        }
    
        if (!(preg_match($listwhite,$list_name,$matches))) {
            exit("<p class=\"error\">Invalid list name</p>");   
        }
        
        $results = True;
    
    }
?>



<div id="main" class="span-22 prepend-3">

    <img src="images/connections.gif">

    <h1 style="display:inline">TrustNet*</h1>
    <h2>

    <?php 
    
    /* if results */
    
    if ($results == True){
    
        echo "Mapping <a href='http://wiki.thenextedge.org/doku.php?id=twitter_trustlists'>$list_name</a> from user <a href=\"http://www.twitter.com/webisteme\">$user_name</a>"; 
    
    } else {
    
    /* welcome */
    
        echo "A tool for mapping trust on Twitter.";
    
    }
    
    ?>

    </h2>
    
    
 <!--    Left side -->
          
    <div id="left" class="span-10">  


        <div id="searchForm">
        <h3>Search</h3>
        <p class="success">Enter a Twitter username and list to start crawling from.</p>
        <br>
        <form method="GET" action="/">
            <h3 style="color:grey">User Name <br><input type="text" name="user"/ placeholder="@username"> <br/>
            <h3 style="color:grey">List Name <br><input type="text" name="list" placeholder="e.g. tne-github"/> </p>
            <input type="submit" value="Crawl"/>
        </form>
        </div>
    
    </div>

    
    <!-- Right side -->
    
    <div id="right" class="span-10 prepend-2 last">

    <?php
    
    
        
        
    if ($results == True){
        
        $com = "python trustlist.py --seed $user_name --list $list_name -w --dot $f_name.dot --net $f_name.net > $f_name";
        echo "<!-- $com -->";
        
        if (!(file_exists($f_name))) {
            echo "<p class=\"message\">File didn't exist ... Creating Trust Network</p>"; 
            shell_exec($com);
            echo "<meta http-equiv=\"refresh\" content=\"5\"/>"; #autorefresh in 5 seconds
            echo "<div><a href='?user=$user_name&list=$list_name'>Reload</a></div>";
        } elseif ($recalc=="1") {
            echo "<p class=\"message\">Recreating Trust Network</p>";
            shell_exec($com);
            echo "<meta http-equiv=\"refresh\" content=\"5\"/>"; #autorefresh in 5 seconds
            echo "<div><a href='?user=$user_name&list=$list_name'>Reload</a></div>";
        } else {
            echo "<div id='cloud'>";
            $lines = file($f_name);
            $depth = "-1";
            foreach ($lines as $line) {
                if ($line[0] == ":") {
                    $depth = $depth+1;
                } else {
                    if ($depth < 0) { continue; }
                    $names = split(" ",$line);
                    foreach ($names as $name) {
                        echo "<span class='d$depth'><a href='http://twitter.com/#!/$name'>$name</a> </span>";
                    }
                }
            }
            echo "</div>";
            
        }
        
    } else {
    
    /* show this on welcome */

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
        $dName = $f_name . ".dot";
        if (file_exists($dName)) {
            echo "<h3>TrustNet for list <b>$list_name</b></h3>";
            echo "<br />";
            $lines=file($dName);
            $dot = implode("",$lines);
            $high = intval($depth)*200;
            echo "<div><img width=\"100%\" src='http://chart.googleapis.com/chart?cht=gv&chl=$dot'/></div>";
            
            echo "<br><br>";
            
            echo "<p style=\"display:inline\">See <a href='http://chart.googleapis.com/chart?cht=gv&chl=$dot'>larger</a> | </p>";
            echo "<a href='?user=$user_name&list=$list_name&recalc=1'>Rebuild</a> | ";       
            echo "<a href='index.php'>Clear</a>";
        }
    
        /*
$nName = $f_name . ".dot";
        if (file_exists($nName)) {
            echo "<h2>Historical Graph</h2>";
            $lines=file($nName);
            $dot = implode("",$lines);
            $high = intval($depth)*200;
            echo "<div><img src='http://chart.googleapis.com/chart?cht=gv&chl=$dot'/></div>";        
        }
*/
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




