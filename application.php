<?php

/*
ini_set('display_errors',1); 
error_reporting(E_ALL);
*/


class TrustNet {


    function __construct($username, $listname){
        
        /* Check username and list name are valid */
        
        $userwhite = "/\A([a-zA-Z0-9_]){1,15}\z/";
        $listwhite = "/\A([a-zA-Z0-9-_]){1,25}\z/";
    
        if (!(preg_match($userwhite,$username,$matches))) {
            exit("<p class=\"error\">Invalid user name</p>");
        }
    
        if (!(preg_match($listwhite,$listname,$matches))) {
            exit("<p class=\"error\">Invalid list name</p>");   
        }
        
        /* save variables */
        
        $this->username = $username;
        $this->listname = $listname;
        $this->fname = $username . "." . $listname;
        $this->dName = $this->fname . ".dot";
        
    }



    // If file doesn't exist, create it and return False; 
    // otherwise return True.
    
    public function checkExists() {

        if (!(file_exists($this->fname))){
            return False;
        } else {
            return True;
        }
    }
    
    // Creates new net files if they don't already exist,
    // rebuilds them if they do (see trustlist.py)
    
    public function buildNet() {

        $com = "python trustlist.py --seed $this->username --list $this->listname -w ";
        shell_exec($com);
    
    }
    
    // Displays the trust net
    
    public function displayNet() {
    
        if (file_exists($this->dName)) {
        
            echo "<div id='cloud'>";
            echo "<h3>TrustNet for list <b>$this->listname</b></h3>";
            echo "<br />";
            echo "<div><img width=\"100%\" src='graph.php?user=$this->username&list=$this->listname'/></div>";
            
            echo "<br><br>";
            
            echo "<p style=\"display:inline\">See <a href='graph.php?user=$this->username&list=$this->listname'>larger</a> | </p>";
            echo "<a href='?user=$this->username&list=$this->listname&recalc=1'>Rebuild</a> | ";       
            echo "<a href='index.php'>Clear</a>";
            echo "</div>";
                
        } else {
            echo 'asdfasdf';
        }
    }
    
    
    public function displayCloud() {
    
        if (file_exists($this->dName)) {    
        
            $lines = file($this->fname);
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
            
        }
    }

}
            
        
    
    
    
    





/*
$TN = new TrustNet('webisteme', 'tne-github');


$TN->displayCloud();
*/





?>