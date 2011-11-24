<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
<?php 
    $user_name = $_GET{"user"};
    $list_name = $_GET{"list"};
    $f_name = $user_name . "." . $list_name;
    
?>

<h1>The Next Edge : TrustNet</h1>
<h2><?php echo "$user_name : $list_name"; ?></h2>


<?php
    if (!(file_exists($f_name))) {
        $com = "python2.4 trustlist.py --seed $user_name --list $list_name > $f_name";
        echo "<div>$com</div>";
        shell_exec($com);
        echo "<p>File didn't exist ... creating</p>";
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
        
    }

?>

</body>

</html>




