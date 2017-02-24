<?
  /*
    This is an example of how to grab user info
    
    Pass the following to the getUser function:
    userid (int)
        
    The function returns an array if successful, or a boolean false if not.
  */
  
  include '../connector/connector.class.php';

  $connector = new vbConnector();
  
  $result = $connector->getUser(1);
  
  if($result !== false){
    echo "success<br>\n";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
  } else {
    echo "failed";
  }
?>