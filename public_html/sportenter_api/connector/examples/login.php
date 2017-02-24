<?
  /*
    This is an example of how to log a user in
    
    Pass the following to the doLogin function:
    Username (string)
    Password (string)
    Cookiesend (bit) - Used to "remember" user using cookies
    
    The function returns an array if successful, or a boolean false if not.
  */
  
  session_start();
  
  include '../connector/connector.class.php';

  $connector = new vbConnector();


  $result = $connector->doLogin("username", "password", 1);
  if($result !== false){
    echo "success";
  } else {
    echo "failed";
  }

?>
