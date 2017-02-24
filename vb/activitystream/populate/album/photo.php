<?php

/* ======================================================================*\
  || #################################################################### ||
  || # vBulletin 4.2.0 Patch Level 2 - Licence Number VBF0DF625C
  || # ---------------------------------------------------------------- # ||
  || # Copyright �2000-2012 vBulletin Solutions Inc. All Rights Reserved. ||
  || # This file may not be redistributed in whole or significant part. # ||
  || # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
  || # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
  || #################################################################### ||
  \*====================================================================== */

/**
 * Class to populate the activity stream from existing content
 *
 * @package	vBulletin
 * @version	$Revision: 57655 $
 * @date		$Date: 2012-01-09 12:08:39 -0800 (Mon, 09 Jan 2012) $
 */
class vB_ActivityStream_Populate_Album_Photo extends vB_ActivityStream_Populate_Base
{
	/**
	 * Constructor - set Options
	 *
	 */
	public function __construct()
	{
		return parent::__construct();
	}

	/*
	 * Don't get: Deleted threads, redirect threads, CMS comment threads
	 *
	 */
	public function populate()
	{
		$typeid = vB::$vbulletin->activitystream['album_photo']['typeid'];
		$this->delete($typeid);

		if (!vB::$vbulletin->activitystream['album_photo']['enabled'])
		{
			return;
		}

		$contenttypeid = vB_Types::instance()->getContentTypeID('vBForum_Album');
		$timespan = TIMENOW - vB::$vbulletin->options['as_expire'] * 60 * 60 * 24;
		vB::$db->query_write("
			INSERT INTO " . TABLE_PREFIX . "activitystream
				(userid, dateline, contentid, typeid, action)
				(SELECT
					userid, dateline, attachmentid, '{$typeid}', 'create'
				FROM " . TABLE_PREFIX . "attachment
				WHERE
					dateline >= {$timespan}
						AND
					contenttypeid = {$contenttypeid}
				)
		");
	}
}

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 06:01, Fri Aug 3rd 2012
|| # CVS: $RCSfile$ - $Revision: 57655 $
|| ####################################################################
\*======================================================================*/