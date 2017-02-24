<?
  /*
    This is an example of how to delete a user
    Beware, once you delete a user they cannot be undeleted.
    
    Pass the following to the delUser function either of the following:
    Username (String)
    UserID (int)
    
    The function returns a boolean
  */
  
  include '../connector/connector.class.php';
  
  $connector = new vbConnector();
  
  $result = $connector->delUser("testuser");
  if($result){
    echo "success";
  } else {
    echo "failed";
  }
?>