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
 
    $white = "/\A([a-zA-Z0-9-])*\z/";

    if (!(preg_match($white,$user_name,$matches))) {
        exit("<p>Invalid user name</p>");
    }
    
    if (!(preg_match($white,$list_name,$matches))) {
        exit("<p>Invalid list name</p>");   
    }
?>

<h1>The Next Edge : TrustNet</h1>
<h2>Who does <?php echo "$user_name trust in the context of <a href='http://wiki.thenextedge.org/doku.php?id=twitter_trustlists'>$list_name</a>?"; ?></h2>


<?php
    $com = "python2.4 trustlist.py --seed $user_name --list $list_name -w --dot $f_name.dot > $f_name";        

    if (!(file_exists($f_name))) {
        echo "<p>File didn't exist ... Creating Trust Network</p>";
        //echo "<div>$com</div>";
        shell_exec($com);
        echo "<div><a href='?user=$user_name&list=$list_name'>Reload</a></div>";
    } elseif ($recalc=="1") {
        echo "<p>Recreating Trust Network</p>";
        shell_exec($com);
        echo "<div><a href='?user=$user_name&list=$list_name'>Reload</a></div>";
    } else {
        echo "<div id='cloud'>";
        $lines = file($f_name);
        $depth = "-1";
        foreach ($lines as $line) {
            if ($line[0] == ":") {
                $depth = $depth+1;
            } else {
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

<h2>Visualise</h2>
<?php
    $lines=file($f_name.".dot");
    $dot = implode("",$lines);
    echo "<div><img src='http://chart.googleapis.com/chart?cht=gv&chl=$dot&chs=400x300'/></div>";
?>


<h2>Search Again</h2>
<div>
<form method="GET" action="/">
    <p>User Name : <input type="text" name="user"/></p>
    <p>List Name : <input type="text" name="list"/></p>
    <input type="submit"/>
</form>
</div>
</body>

</html>




