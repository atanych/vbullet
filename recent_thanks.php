<?php  
/*======================================================================*\
|| #################################################################### ||
|| # Recent Thanks v2.6 for [Ajax] Post Thank you Hack                # ||
|| # by Scandal for vBulletin 4.x                                     # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2012 Scandal @ vBulletin.org                          # ||
|| #                                                                  # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
// ####################### SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);

// #################### DEFINE IMPORTANT CONSTANTS #######################
define('THIS_SCRIPT', 'recent_thanks');  

// #################### PRE-CACHE TEMPLATES AND DATA ######################
// get special phrase groups
$phrasegroups = array();

// get special data templates from the datastore
$specialtemplates = array(); 

// pre-cache templates used by all actions
$globaltemplates = array(
    'recent_thanks', 
    'recent_thank_bit'
);

// pre-cache templates used by specific actions
$actiontemplates = array();

// ######################### REQUIRE BACK-END ############################
require_once('./global.php');

// #################### DEFINE IMPORTANT VARIABLES #######################
$uid = $vbulletin->userinfo['userid'];
$num_of_thanks = $vbulletin->options['recent_thanks_number'];

// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################

// generate navbar
	$navbits = construct_navbits(array('' => 'Recent Thanks'));
	$navbar = render_navbar_template($navbits);
  $pagetitle = 'Recent Thanks';
  
// Check ON/OFF field
if (($vbulletin->options['recent_thanks_on_off']))
{
  // User ID validation and queries
  if ((is_numeric($uid) == true) && ($uid > 0))
  {
    $db->query_write("
      UPDATE " . TABLE_PREFIX . "user
      SET recent_thankcnt = 0
      WHERE userid = ". $vbulletin->userinfo['userid'] ."
      ");
                 
    $result_thank_query = $db->query_read("
      SELECT post_thanks.postid, post_thanks.date, post_thanks.username, post_thanks.userid, thread.title 
      FROM " . TABLE_PREFIX . "post_thanks AS post_thanks
      LEFT JOIN " . TABLE_PREFIX . "post AS post
      ON post_thanks.postid = post.postid
      LEFT JOIN " . TABLE_PREFIX . "thread AS thread
      ON thread.threadid = post.threadid      
      WHERE post.userid = '$uid'
      ORDER BY post_thanks.id DESC
      LIMIT $num_of_thanks 
      ");
  }
  else
  {
    print_no_permission(); 
  }
}
else
{
	print_no_permission();
}

// Set the results' bits template
  while ($recent_thank_content_fetcharray = $db->fetch_array($result_thank_query))
  {
      $rt_postid = $recent_thank_content_fetcharray['postid'];
      $rt_username = $recent_thank_content_fetcharray['username'];
      $rt_userid = $recent_thank_content_fetcharray['userid'];
        if (empty($recent_thank_content_fetcharray['title']))
          {
          $rt_title = "(PostID: $rt_postid)";
          }
        else
          {
          $rt_title = $recent_thank_content_fetcharray['title'];
          }
      // format for the date/time from a timestamp
      $date_timestamp = $recent_thank_content_fetcharray['date'];
      $rt_date = date("d-m-Y - H:i", $date_timestamp);
 
      /* render template and register variables for vB4 */
      $templater = vB_Template::create('recent_thank_bit');
          $templater->register('rt_postid', $rt_postid);
          $templater->register('rt_username', $rt_username);
          $templater->register('rt_userid', $rt_userid);
          $templater->register('rt_title', $rt_title);
          $templater->register('rt_date', $rt_date);
      $recent_thank_bits .= $templater->render();  
                 
                   
  }
    $db->free_result($result_thank_query);  


// Output the results' main template
 $templater = vB_Template::create('recent_thanks');
 $templater->register_page_templates();
 $templater->register('navbar', $navbar);
 $templater->register('usercss', $usercss);
 $templater->register('num_of_thanks', $num_of_thanks);
 $templater->register('recent_thank_bits', $recent_thank_bits);  
  print_output($templater->render());
  
?>



