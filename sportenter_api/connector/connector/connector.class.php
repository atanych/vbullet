<?
error_reporting(E_ALL & ~E_NOTICE);
  
define('THIS_SCRIPT', 'index');

/* User changable options */
// change this to your forum path.
$forumroot = '/srv/sites/forum.sportenter.co.il/www';
/* end of User changable options */

define('CWD', $forumroot);
define('DIR', $forumroot);
define('TIMENOW', time());
define('IPADDRESS', $_SERVER['REMOTE_ADDR']);

require_once(CWD . '/includes/class_core.php');

class vBulletinHook { function fetch_hook() { return false; } }


/**
* API for vBulletin
*
* Attempts to open up certain functions/features to use in other applications
*
* @author     Nick Le Mouton <noodles@planetslackers.com>
* @copyright  2006
* @license    http://www.php.net/license/3_0.txt  PHP License 3.0
* @version    Release: 0.02
* @link       http://www.vbulletin.org/forum/showthread.php?t=114338
* @since      Class available since Release 0.01
*/
/*
  As this class opens everything up to you, it can be very dangerous if used incorrectly.
  At this point the class/functions does very little error checking/input processing.
  Be sure to process your own input variables, do error checking externally and don't
  trust any input from forms/client.
  
  USE THIS SCRIPT AT YOUR OWN RISK! IF IT SCREWS UP YOUR FORUM IT IS NOT MY FAULT
  
  TODO:
  Error checking function(s)
  Forum listings
  Topic/Post listings
*/
class vbConnector extends vB_Registry
{
  function vbConnector()
	{
		$this->vB_Registry();
		$this->fetch_config();

		//define('DIR', $this->config['Misc']['forumpath']);

		switch (strtolower($this->config['Database']['dbtype']))
		{
			case 'mysql':
			case '':
			{
				$db =& new vB_Database($this);
				break;
			}
			case 'mysqli':
			{
				$db =& new vB_Database_MySQLi($this);
				break;
			}
		}

		include(CWD . '/includes/functions.php');

		// make database connection
		
		$db->connect(
			$this->config['Database']['dbname'],
			$this->config['MasterServer']['servername'],
			$this->config['MasterServer']['port'],
			$this->config['MasterServer']['username'],
			$this->config['MasterServer']['password'],
			$this->config['MasterServer']['usepconnect'],
			$this->config['SlaveServer']['servername'],
			$this->config['SlaveServer']['port'],
			$this->config['SlaveServer']['username'],
			$this->config['SlaveServer']['password'],
			$this->config['SlaveServer']['usepconnect'],
			$this->config['Mysqli']['ini_file'],
			$this->config['Mysqli']['charset']
		);
		
		$this->db =& $db;
		$datastore_class = (!empty($this->config['Datastore']['class'])) ? $this->config['Datastore']['class'] : 'vB_Datastore';

		if ($datastore_class != 'vB_Datastore')
		{
			require_once(CWD . '/includes/class_datastore.php');
		}
		$this->datastore =& new $datastore_class($this, $db);
		$this->datastore->fetch($specialtemplates);
    /**
    * If shutdown functions are allowed, register exec_shut_down to be run on exit.
    * Disable shutdown function for IIS CGI with Gzip enabled since it just doesn't work, sometimes, unless we kill the content-length header
    * Also disable for PHP4 do to the echo() timeout issue
    */
		
    define('SAPI_NAME', php_sapi_name());
    if (!defined('NOSHUTDOWNFUNC'))
    {
      if (PHP_VERSION < '5' OR ((SAPI_NAME == 'cgi' OR SAPI_NAME == 'cgi-fcgi') AND $this->options['gzipoutput'] AND strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false))
      {
        define('NOSHUTDOWNFUNC', true);
      }
      else
      {
        vB_Shutdown::add('exec_shut_down');
      }
    }

    // fetch url of referring page after we have access to vboptions['forumhome']
    $this->url = $this->input->fetch_url();
    define('REFERRER_PASSTHRU', $this->url);

		$this->input =& new vB_Input_Cleaner($this);
		$this->input->clean_array_gpc('c', array(
  		'vbulletin_collapse'            => TYPE_STR,
  		COOKIE_PREFIX . 'referrerid'    => TYPE_UINT,
  		COOKIE_PREFIX . 'userid'                => TYPE_UINT,
  		COOKIE_PREFIX . 'password'              => TYPE_STR,
  		COOKIE_PREFIX . 'lastvisit'     => TYPE_UINT,
  		COOKIE_PREFIX . 'lastactivity'  => TYPE_UINT,
  		COOKIE_PREFIX . 'threadedmode'  => TYPE_STR,
  		COOKIE_PREFIX . 'sessionhash'   => TYPE_STR,
  		COOKIE_PREFIX . 'styleid'               => TYPE_UINT,
  		COOKIE_PREFIX . 'languageid'    => TYPE_UINT,
		));
		
		$this->input->clean_array_gpc('r', array(
  		's'          => TYPE_STR,
  		'styleid'    => TYPE_INT,
  		'langid'     => TYPE_INT,
		));


		$this->pluginlist = '';


		global $vbulletin;

		$vbulletin=$this;

		$sessionhash = (!empty($this->GPC['s']) ? $this->GPC['s'] : $this->GPC[COOKIE_PREFIX . 'sessionhash']);

		// build the session and setup the environment
		$this->session =& new vB_Session($this, $sessionhash, $this->GPC[COOKIE_PREFIX . 'userid'], $this->GPC[COOKIE_PREFIX . 'password'], 0, 0);


		// Hide sessionid in url if we are a search engine or if we have a cookie
		$this->session->set_session_visibility($show['search_engine'] OR $this->superglobal_size['_COOKIE'] > 0);
		$this->userinfo =& $this->session->fetch_userinfo();
		$this->session->do_lastvisit_update($this->GPC[COOKIE_PREFIX . 'lastvisit'], $this->GPC[COOKIE_PREFIX . 'lastactivity']);

		$permissions = cache_permissions($this->userinfo, true);
		$this->userinfo['permissions'] =& $permissions;
	}
	
	function doLogin($username, $password, $cookiesend=0)
	{
		global $vbulletin;

		if(empty($username) || empty($password))
		{
			return false;
		}

		require_once(CWD . '/includes/functions_login.php');


		if (!verify_authentication($username, $password, '', '', $cookiesend, true))
		{
			// check password
			return false;
		}

		// create new session
		process_new_login('', '', '');

		$permissions = cache_permissions($this->userinfo, true);
		$this->userinfo['permissions'] =& $permissions;


		$result['password']=$this->userinfo['password'];
		$result['userid']=$this->userinfo['userid'];

		return $result;
	}
	
	function doLogout(){
	  global $vbulletin;
	  
	  require_once(CWD . '/includes/functions_login.php');
	  
	  // ignore logouthash at the moment, write function later to grab logout hash to pass to this function via GET
	  // logouthash possibly to stop cross site hack
	  // for some reason we can't access userid to check to see if user is logged in
	  // logging out twice doesn't seem to cause any problems though
	  
	  process_logout();
	}
	
	/*
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
	 
	*/
	
	function addUser($userArray){
	  global $vbulletin;
	  
	  if(empty($userArray['username']) Or empty($userArray['password']) Or empty($userArray['email'])){
	    // Cannot continue without username, password or email
	    return false;
	  }
	  
	  // init user datamanager class
    $userdata =& datamanager_init('User', $vbulletin, ERRTYPE_ARRAY);
    
    $userdata->set('username', $userArray['username']);
    
    $userdata->set('password', ($userArray['password_md5'] ? $userArray['password_md5'] : $userArray['password']));
    
    $userdata->set('email', $userArray['email']);
    
    if (!empty($userArray['referrername']))
  	{
  		$userdata->set('referrerid', $userArray['referrername']);
  	}
  	
  	// Set specified options
  	if (!empty($userArray['options']))
  	{
  		foreach ($userArray['options'] AS $optionname => $onoff)
  		{
  			$userdata->set_bitfield('options', $optionname, $onoff);
  		}
  	}
  	
  	// assign user to usergroup 3 if email needs verification
  	if ($vbulletin->options['verifyemail'] Or (isset($userArray['forceVerifyEmail']) And $userArray['forceVerifyEmail']))
  	{
  		$newusergroupid = 3;
  	}
  	else if ($vbulletin->options['moderatenewmembers'])
  	{
  		$newusergroupid = 4;
  	}
  	else
  	{
  		$newusergroupid = 2;
  	}
  	// set usergroupid
  	$userdata->set('usergroupid', $newusergroupid);
  	
  	if(!empty($userArray['usergroup'])){
  	  $userdata->set('usergroupid', $userArray['usergroup']);
  	}
  	
  	// set languageid
  	if(!empty($userArray['languageid'])){
	   $userdata->set('languageid', $vbulletin->userinfo['languageid']);
  	}
  	
  	// set profile fields
  	if(!empty($userArray['userfield'])){
	    $customfields = $userdata->set_userfields($userArray['userfield'], true, 'register');
  	}
  	
  	// set birthday
  	if(!empty($userArray['showbirthday'])){
  	  $userdata->set('showbirthday', $userArray['showbirthday']);
  	}
  	$userdata->set('birthday', array(
  		'day'   => $userArray['day'],
  		'month' => $userArray['month'],
  		'year'  => $userArray['year']
  	));
  	
  	// set time options
  	if(!empty($userArray['dst'])){
	    $userdata->set_dst($userArray['dst']);
  	}
  	if(!empty($userArray['timezoneoffset'])){
	    $userdata->set('timezoneoffset', $userArray['timezoneoffset']);
  	}
    
    // register IP address
	  $userdata->set('ipaddress', IPADDRESS);
	  
	  $userdata->pre_save();
	  
	  // check for errors
  	if (!empty($userdata->errors))
  	{
  	  return $userdata->errors; 
  	} else {
  	  // save the data
  		$vbulletin->userinfo['userid']
  			= $userid
  			= $userdata->save();
  			
  	  if($userid){
  	    // assume save succeeded
  	    return true;
  	  }
  	}
	}
	
	/*
	  Delete user
	  Use either userID or username
	*/
	
	function delUser($username="", $userid=0){
	  global $vbulletin;
	  
	  if(!empty($username)){
	    $userid = getUserID($username);
	  }
	  
	  if($userid == 0){
	    return false;
	  }
	  
	  $nodelete = explode(',', $vbulletin->config['SpecialUsers']['undeletableusers']);
	  if (in_array($userid, $nodelete)){
	    return false;
	  }
	  
	  $info = fetch_userinfo($userid);
		if ($info['userid'] == $userid)
		{
			$userdm =& datamanager_init('User', $vbulletin, ERRTYPE_CP);
			$userdm->set_existing($info);
			$userdm->delete();
			unset($userdm);
		}
		return true;
	}
	
	/*
	  Get User ID
	  Returns a user ID for a username
	*/
	
	function getUserID($username){
	  
	  $user = $this->db->query_first("
		SELECT user.userid
		FROM " . TABLE_PREFIX . "user AS user
		WHERE username = '".mysql_escape_string($username)."'
		LIMIT 0,1");
	  
	  if(!empty($user['userid'])){
	    return $user['userid'];
	  } else {
	    //username not found
	    return false;
	  }
	}
	
	/*
	  Get User
	  Returns an array of user information from a user ID
	*/
	
	function getUser($userid){
	  global $vbulletin;
	  
	  $result = $this->db->query_first("SELECT
		user.userid, reputation, username, usergroupid, birthday_search, email,
		parentemail,(options & " . $vbulletin->bf_misc_useroptions['coppauser'] . ") AS coppauser, homepage, icq, aim, yahoo, msn, skype, signature,
		usertitle, joindate, lastpost, posts, ipaddress, lastactivity, userfield.*
		FROM " . TABLE_PREFIX . "user AS user
		LEFT JOIN " . TABLE_PREFIX . "userfield AS userfield ON(userfield.userid = user.userid)
		LEFT JOIN " . TABLE_PREFIX . "usertextfield AS usertextfield ON(usertextfield.userid = user.userid)
		WHERE user.userid = $userid
		LIMIT 0,1");
	  
	  if(!empty($result['userid'])){
	    return $result;
	  } else {
	    //user not found
	    return false;
	  }
	}
	
	/*
	  Edit User
	  Takes array of values/options etc and passes them to vbulletin to update.
	
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
	*/
	
	function editUser($userArray){
	  global $vbulletin;

	  $noalter = explode(',', $vbulletin->config['SpecialUsers']['undeletableusers']);
	  if (!empty($noalter[0]) AND in_array($userArray['userid'], $noalter))
  	{
  	  //user_is_protected_from_alteration_by_undeletableusers_var
  		return "user is protected";
  	}
  	
  	// init data manager
  	$userdata =& datamanager_init('User', $vbulletin, ERRTYPE_CP);
  	$userdata->adminoverride = true;
  	
  	
  	
  	// set existing info if this is an update
  	if ($userArray['userid'])
  	{
  		$userinfo = fetch_userinfo($userArray['userid']);
  		if(!empty($userArray['user']['posts'])){
  		  $userinfo['posts'] = intval($userArray['user']['posts']);
  		}
  		$userdata->set_existing($userinfo);
  		if ($userdata->existing['userid'] != $userArray['userid'])
  		{
  		  //invalid_user_specified
  			return "invalid user specified";
  		}
  	}

  	// password
  	if (!empty($userArray['password']))
  	{
  		$userdata->set('password', $userArray['password']);
  	}
  	else if (!$userArray['userid'])
  	{
  		//invalid_password_specified
  		return "invalid password specified";
  	}

  	// user options
  	if(isset($userArray['options'])){
    	foreach ($userArray['options'] AS $key => $val)
    	{
    		$userdata->set_bitfield('options', $key, $val);
    	}
  	}

  	// user fields
  	if(isset($userArray['user'])){
    	foreach ($userArray['user'] AS $key => $val)
    	{
    		$userdata->set($key, $val);
    	}
  	}
    
  	/*
  	Not implemented yet
  	if (empty($vbulletin->GPC['user']['membergroupids']))
  	{
  		$userdata->set('membergroupids', '');
  	}*/

	  // custom profile fields
	  if(isset($userArray['userfield'])){
	    $userdata->set_userfields($userArray['userfield'], false, 'admin');
	  }

	  // save data
	  $userid = $userdata->save();
  	if ($userArray['userid'])
  	{
  		$userid = $userArray['userid'];
  	}
  	
  	return true;
	}
}

?>
