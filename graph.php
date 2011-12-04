<?php
  $user_name = $_GET{"user"};
  $list_name = $_GET{"list"};

  $f_name = $user_name . "." . $list_name;
  $dName = $f_name . ".net";

  $userwhite = "/\A([a-zA-Z0-9_]){1,15}\z/";
  $listwhite = "/\A([a-zA-Z0-9-_]){1,25}\z/";

  if (!(preg_match($userwhite,$user_name,$matches))) {
        exit("<p class=\"error\">Invalid user name</p>");
  }
    
  if (!(preg_match($listwhite,$list_name,$matches))) {
        exit("<p class=\"error\">Invalid list name</p>");   
  }


  if (file_exists($dName)) {
    $lines=file($dName);
    $dot = implode("",$lines);
  
    header('content-type: image/png');
    $url = 'https://chart.googleapis.com/chart?chid='. md5(uniqid(rand(), true));

    // Add data, chart type, chart size, and scale to params.
    $chart = array(
      'cht' => 'gv',
      'chl' => $dot);

    // Send the request, and print out the returned bytes.
    $context = stream_context_create(
      array('http' => array(
        'method' => 'POST',
        'content' => http_build_query($chart)
      ))
    );
    
    fpassthru(fopen($url, 'r', false, $context));
  } else {
    exit("Image not found");
  }
  
?>
