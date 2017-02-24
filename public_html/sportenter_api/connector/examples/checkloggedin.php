<?
  /*
    This is an example of how to check that a user is logged in
    It just uses some variables which are opened up from vbulletin
  */
  
  session_start();
  
  include '../connector/connector.class.php';

  $connector = new vbConnector();
  
  if($vbulletin->userinfo['userid'] != 0){
    echo "you are logged in as:<br>";
    echo $vbulletin->userinfo['username'];
  } else {
    echo "you are not logged in";
  }
?>