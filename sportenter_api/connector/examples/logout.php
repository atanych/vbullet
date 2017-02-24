<?
  /*
    This is an example of how to log a user out
    
    The function returns doesn't return anything
  */

  session_start();
  
  include '../connector/connector.class.php';
  
  $connector = new vbConnector();
  
  $connector->doLogout();
?>