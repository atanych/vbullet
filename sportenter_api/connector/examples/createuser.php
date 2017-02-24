<?
  /*
    This is an example of how to create a basic user
    
    Pass the following to the addUser function:
    userArray (Array)
	 
    required:
    username (String)
    password (String)
    email (String)
    
    optional:
    password_md5 - MD5 hash of password (String)
    options (Array)
      coppauser - bit
      showreputation - bit
      adminemail - bit
      showemail - bit
      invisible - bit
      showvcard - bit
      receivepm - bit
      emailonpm - bit
      pmpopup - bit
      showsignatures - bit
      showavatars - bit
      showimages - bit
      dstauto - bit
      dstonoff - bit
    referrername - name of referral user (String)
    day - birthday day (String)
    month - birthday month (String)
    year - birthday year (String)
    timezoneoffset (String)
    dst (String)
    userfield (String)
    showbirthday (String)
    forceVerifyEmail - Override vBulletin's internal settings (boolean)
    languageid (String)
    usergroup - Override new group setting (May cause problems with verify email) (String)
    
    The function returns a boolean true if successful or an error array if unsuccessful.
  */
  
  include '../connector/connector.class.php';

  $connector = new vbConnector();
  
  $user = array('username' => 'testuser', 'password' => 'test', 'email' => 'test@test.com');
  
  $result = $connector->addUser($user);
  
  if($result === true){
    echo "success";
  } else {
    echo "failed";
    print_r ($result);
  }
?>