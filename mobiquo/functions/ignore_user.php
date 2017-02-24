<?php
defined('IN_MOBIQUO') or exit;

require_once('./global.php');

function ignore_user_func($xmlrpc_params)
{
	global $vbulletin, $db;
	
	if ($_POST['do'] == 'doaddlist')
	{
		$vbulletin->input->clean_array_gpc('p', array(
			'userid'   => TYPE_UINT,
			'userlist' => TYPE_NOHTML,
			'friend'   => TYPE_BOOL,
			'deny'     => TYPE_NOHTML,
		));
	
		$userinfo = verify_id('user', $vbulletin->GPC['userid'], true, true);
		cache_permissions($userinfo);
	
		($hook = vBulletinHook::fetch_hook('profile_doaddlist_start')) ? eval($hook) : false;
	
		// no referring URL, send them back to the profile page
		if ($vbulletin->url == fetch_seo_url('forumhome|nosession', array()))
		{
			$vbulletin->url = fetch_seo_url('member', $userinfo);
		}
	
		// No was clicked
		if ($vbulletin->GPC['deny'])
		{
			print_standard_redirect('action_cancelled');  
		}
	
		if ($vbulletin->GPC['userlist'] != 'ignore')
		{
			$vbulletin->GPC['userlist'] = $vbulletin->GPC['friend'] ? 'friend' : 'buddy';
		}
	
		if ($vbulletin->GPC['userlist'] == 'friend' AND (!($vbulletin->options['socnet'] & $vbulletin->bf_misc_socnet['enable_friends']) OR !($userinfo['permissions']['genericpermissions2'] & $vbulletin->bf_ugp_genericpermissions2['canusefriends']) OR !($vbulletin->userinfo['permissions']['genericpermissions2'] & $vbulletin->bf_ugp_genericpermissions2['canusefriends'])))
		{
			$vbulletin->GPC['userlist'] = 'buddy';
		}
	
		$users = array();
		switch ($vbulletin->GPC['userlist'])
		{
			case 'friend':
			case 'buddy':
	
				// No slave here
				$ouruser = $db->query_first("
					SELECT friend
					FROM " . TABLE_PREFIX . "userlist
					WHERE relationid = $userinfo[userid]
						AND userid = " . $vbulletin->userinfo['userid'] . "
						AND type = 'buddy'
				");
			break;
			case 'ignore':
				$uglist = $userinfo['usergroupid'] . (trim($userinfo['membergroupids']) ? ",$userinfo[membergroupids]" : '');
				if (!$vbulletin->options['ignoremods'] AND can_moderate(0, '', $userinfo['userid'], $uglist) AND !($permissions['adminpermissions'] & $vbulletin->bf_ugp_adminpermissions['cancontrolpanel']))
				{
					standard_error(fetch_error('listignoreuser', $userinfo['username']));
				}
				else if ($vbulletin->userinfo['userid'] == $userinfo['userid'])
				{
					standard_error(fetch_error('cantlistself_ignore'));
				}
	
				$db->query_write("
					INSERT IGNORE INTO " . TABLE_PREFIX . "userlist
						(userid, relationid, type, friend)
					VALUES
						(" . $vbulletin->userinfo['userid'] . ", " . intval($userinfo['userid']) . ", 'ignore', 'no')
				");
				$users[] = $vbulletin->userinfo['userid'];
				$redirect_phrase = array('redirect_addlist_ignore',$userinfo['username']);
			break;
			default:
				standard_error(fetch_error('invalidid', 'list', $vbulletin->options['contactuslink']));
		}
	
		if ($vbulletin->GPC['userlist'] == 'buddy')
		{ // if an entry exists already then we're fine
			if (empty($ouruser))
			{
				$db->query_write("
					INSERT IGNORE INTO " . TABLE_PREFIX . "userlist
						(userid, relationid, type, friend)
					VALUES
						(" . $vbulletin->userinfo['userid'] . ", " . intval($userinfo['userid']) . ", 'buddy', 'no')
				");
				$users[] = $vbulletin->userinfo['userid'];
			}
			$redirect_phrase = array('redirect_addlist_contact',$userinfo['username']);
		}

	
		require_once(DIR . '/includes/functions_databuild.php');
		foreach($users AS $userid)
		{
			build_userlist($userid);
		}
	
		($hook = vBulletinHook::fetch_hook('profile_doaddlist_complete')) ? eval($hook) : false;
	
//		print_standard_redirect($redirect_phrase, true, true);  
	}

	if ($_POST['do'] == 'doremovelist')
	{
		$vbulletin->input->clean_array_gpc('p', array(
			'userid'   => TYPE_UINT,
			'userlist' => TYPE_NOHTML,
			'friend'   => TYPE_BOOL,
			'deny'     => TYPE_NOHTML,
		));
	
		$userinfo = verify_id('user', $vbulletin->GPC['userid'], true, true);
		cache_permissions($userinfo);
	
		($hook = vBulletinHook::fetch_hook('profile_doremovelist_start')) ? eval($hook) : false;
	
		// no referring URL, send them back to the profile page
		if ($vbulletin->url == fetch_seo_url('forumhome|nosession', array()))
		{
			$vbulletin->url = fetch_seo_url('member', $userinfo);
		}
	
		// No was clicked
		if ($vbulletin->GPC['deny'])
		{
			print_standard_redirect('action_cancelled');  
		}
	
		$users = array();
		switch ($vbulletin->GPC['userlist'])
		{
			case 'friend':
				$db->query_write("
					UPDATE " . TABLE_PREFIX . "userlist
					SET friend = 'no'
					WHERE userid = " . $vbulletin->userinfo['userid'] . "
						AND relationid = $userinfo[userid]
						AND type = 'buddy'
						AND friend = 'yes'
				");
				if ($db->affected_rows())
				{
					$users[] = $vbulletin->userinfo['userid'];
					$db->query_write("
						UPDATE " . TABLE_PREFIX . "userlist
						SET friend = 'no'
						WHERE relationid = " . $vbulletin->userinfo['userid'] . "
							AND userid = $userinfo[userid]
							AND type = 'buddy'
							AND friend = 'yes'
					");
					if ($db->affected_rows())
					{
						$users[] = $userinfo['userid'];
					}
					$db->query_write("
						UPDATE " . TABLE_PREFIX . "user
						SET friendcount = IF(friendcount >= 1, friendcount - 1, 0)
						WHERE userid IN(" . implode(", ", $users) . ")
							AND friendcount <> 0
					");
				}
				// this option actually means remove buddy in this case, do don't break so we fall through.
				if (!$vbulletin->GPC['friend'])
				{
					break;
				}
			case 'buddy':
				$db->query_write("
					DELETE FROM " . TABLE_PREFIX . "userlist
					WHERE userid = " . $vbulletin->userinfo['userid'] . "
						AND relationid = $userinfo[userid]
						AND type = 'buddy'
				");
				if ($db->affected_rows())
				{
					$users[] = $vbulletin->userinfo['userid'];
	
					// The user could have been a friend too
					list($pendingcount) = $db->query_first("
						SELECT COUNT(*)
						FROM " . TABLE_PREFIX . "userlist AS userlist
						LEFT JOIN " . TABLE_PREFIX . "userlist AS userlist_ignore ON(userlist_ignore.userid = " . $userinfo['userid'] . " AND userlist_ignore.relationid = userlist.userid AND userlist_ignore.type = 'ignore')
						WHERE userlist.relationid = " . $userinfo['userid'] . "
							AND userlist.type = 'buddy'
							AND userlist.friend = 'pending'
							AND userlist_ignore.type IS NULL", DBARRAY_NUM
					);
	
					$db->query_write("
						UPDATE " . TABLE_PREFIX . "user
						SET friendreqcount = $pendingcount
						WHERE userid = " . $userinfo['userid']
					);
				}
			break;
			case 'ignore':
				$db->query_write("
					DELETE FROM " . TABLE_PREFIX . "userlist
					WHERE userid = " . $vbulletin->userinfo['userid'] . "
						AND relationid = $userinfo[userid]
						AND type = 'ignore'
				");
				if ($db->affected_rows())
				{
					$users[] = $vbulletin->userinfo['userid'];
				}
			break;
			default:
				standard_error(fetch_error('invalidid', 'list', $vbulletin->options['contactuslink']));
		}
	
		require_once(DIR . '/includes/functions_databuild.php');
		foreach($users AS $userid)
		{
			build_userlist($userid);
		}
	
		($hook = vBulletinHook::fetch_hook('profile_doremovelist_complete')) ? eval($hook) : false;
	
//		print_standard_redirect(array('redirect_removelist_' . $vbulletin->GPC['userlist'],$userinfo['username']), true, true);  
	}
	
	return new xmlrpcresp(new xmlrpcval(array(
        'result' => new xmlrpcval(true, 'boolean'),
  ), 'struct'));
}



