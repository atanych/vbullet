<?
  /*
    This is an example of how to edit a basic user
    
    Pass the following to the editUser function:
    userArray (Array)
    	 
	  required:
	  userid (int)
	 
	  optional:
	  password (String)
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
   
    user (Array)
      username - string (100)
      email - string (100)
      languageid - int
      usertitle - string (250)
      customtitle (0 - No, 1 - Admin set, 2 - user set)
      homepage - string (100)
      birthday - Array
      	day - int
      	month - int
      	year - int
      showbirthday - smallint
      signature - mediumtext
      icq - string (20)
      aim - string (20)
      yahoo - string (32)
      msn - string (100)
      skype - string (32)
      parentemail - string (50)
      posts - int
      referrerid - int
      ipaddress - string (15)
      usergroupid - smallint
      reputation - int
      autosubscribe - int (-1 - Do not subscribe, 0 - subscribe w/ no notification, 1 - instant email, 2 - daily email, 3 - weekly email)
      threadedmode - int (0 - Linear - oldest to newest, 1 - threaded, 2 hybrid, 3 - linear newest to oldest)
      showvbcode - int (0 - do not show editor toolbar, 1 - show standard editor, 2 - show enhanced editor)
      styleid - int
      timezoneoffset - char(4)
      daysprune - smallint
      joindate - Array
      	day - int
      	month - int
      	year - int
      	hour - int
      	minute - int
      lastactivity - Array
      	day - int
      	month - int
      	year - int
      	hour - int
      	minute - int
      lastpost - Array
      	day - int
      	month - int
      	year - int
      	hour - int
      	minute - int
    userfield (Array)
      field1 - mediumtext
      field2 - mediumtext
      field3 - mediumtext
      field4 - mediumtext
    
    The function returns a boolean true if successful or an error string if unsuccessful.
  */
  
  include '../connector/connector.class.php';

  $connector = new vbConnector();
  
  
  $user = array("email" => "test@test2.com");
  $options = array("showsignature" => 0,
    "invisible" => 1);
  $userArray = array("userid" => 999999, 
    "password" => "testpassword",
    "user" => $user,
    "options" => $options);
    
  $result = $connector->editUser($userArray);
    
  if($result === true){
    echo "success";
  } else {
    echo "failed - " . $result;
  }
?>