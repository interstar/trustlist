<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
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

    if (!(preg_match($userwhite,$user_name,$matches))) {
        exit("<p class=\"error\">Invalid user name</p>");
    }
    
    if (!(preg_match($listwhite,$list_name,$matches))) {
        exit("<p class=\"error\">Invalid list name</p>");   
    }
?>

<h1>The Next Edge : TrustNet</h1>
<h2>Who does <?php echo "$user_name trust in the context of <a href='http://wiki.thenextedge.org/doku.php?id=twitter_trustlists'>$list_name</a>?"; ?></h2>


<?php
    $com = "python2.4 trustlist.py --seed $user_name --list $list_name -w --dot $f_name.dot --net $f_name.net> $f_name";

    if (!(file_exists($f_name))) {
        echo "<p class=\"message\">File didn't exist ... Creating Trust Network</p>";
        //echo "<div>$com</div>";
        shell_exec($com);
        echo "<meta http-equiv=\"refresh\" content=\"5\"/>" #autorefresh in 5 seconds
        echo "<div><a href='?user=$user_name&list=$list_name'>Reload</a></div>";
    } elseif ($recalc=="1") {
        echo "<p class=\"message\">Recreating Trust Network</p>";
        shell_exec($com);
        echo "<meta http-equiv=\"refresh\" content=\"5\"/>" #autorefresh in 5 seconds
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
        echo "<div><a href='?user=$user_name&list=$list_name&recalc=1'>Reconstruct Network</a></div>";
        
    }

?>

<?php
    $dName = $f_name . ".dot";
    if (file_exists($dName)) {
        echo "<h2>Current Graph</h2>";
        $lines=file($dName);
        $dot = implode("",$lines);
        $high = intval($depth)*200;
        echo "<div><img src='http://chart.googleapis.com/chart?cht=gv&chl=$dot'/></div>";        
    }

    $nName = $f_name . ".net";
    if (file_exists($nName)) {
        echo "<h2>Historical Graph</h2>";
        $lines=file($nName);
        $dot = implode("",$lines);
        $high = intval($depth)*200;
        echo "<div><img src='http://chart.googleapis.com/chart?cht=gv&chl=$dot'/></div>";        
    }
?>

<div id="searchForm">
    <h2>Search Again</h2>
    <form method="GET" action="/">
        <p>User Name : <input type="text" name="user"/></p>
        <p>List Name : <input type="text" name="list"/></p>
        <input type="submit"/>
    </form>
</div>
</body>

</html>




