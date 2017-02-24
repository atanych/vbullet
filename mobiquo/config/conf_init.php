<?php
/*======================================================================*\
 || #################################################################### ||
 || # Copyright &copy;2009 Quoord Systems Ltd. All Rights Reserved.    # ||
 || # This file may not be redistributed in whole or significant part. # ||
 || # This file is part of the Tapatalk package and should not be used # ||
 || # and distributed for any other purpose that is not approved by    # ||
 || # Quoord Systems Ltd.                                              # ||
 || # http://www.tapatalk.com | http://www.tapatalk.com/license.html   # ||
 || #################################################################### ||
 \*======================================================================*/

defined('IN_MOBIQUO') or exit;

class mobiquo_config
{
    function get_config()
    {
        global $vbulletin;
        $config = array();
        $config = $this->read_config_file();

        if($config['is_open'] ==1 && $vbulletin->options['bbactive']==1){
            $config['is_open'] = 1;
        } else {
            $config['is_open'] = 0;
        }
//        if($vbulletin->options['threadmarking'] == 0)
//            $config['can_unread'] = 0;
        if(isset($vbulletin->options['reg_url']) && !empty($vbulletin->options['reg_url']))
        {
            $config['reg_url'] = $vbulletin->options['reg_url'];
        }
        if(($vbulletin->usergroupcache['1']['forumpermissions'] & $vbulletin->bf_ugp_forumpermissions['canview'])){
            $config['guest_okay'] = 1;
        }else{
            $config['guest_okay'] = 0;
        }

        if(empty($vbulletin->options['allowregistration']))
        {
            $config['sign_in'] = 0;
            $config['inappreg'] = 0;
            
            $config['sso_signin'] = 0;
            $config['sso_register'] = 0;
            $config['native_register'] = 0;
        }
        if (!function_exists('curl_init') && !@ini_get('allow_url_fopen')) 
        {
            $config['sign_in'] = 0;
            $config['inappreg'] = 0;
            
            $config['sso_login'] = 0;
            $config['sso_signin'] = 0;
            $config['sso_register'] = 0;
        }
        if (isset($vbulletin->options['tapatalk_reg_type']))
        {
            if ($vbulletin->options['tapatalk_reg_type'] == 1)
            {
                $config['sign_in'] = 0;
                $config['inappreg'] = 0;
                
                $config['sso_signin'] = 0;
                $config['sso_register'] = 0;
                $config['native_register'] = 0;
            }
        }
        $config['min_search_length'] = $vbulletin->options['minsearchlength'];
        $config['charset'] = vB_Template_Runtime::fetchStyleVar('charset');
        if(isset($vbulletin->options['push_key']) && !empty($vbulletin->options['push_key']))
        {
            $config['api_key'] = md5($vbulletin->options['push_key']);
        }
        if (isset($vbulletin->options['tapatalk_hide_forum']))
        {
            $config['hide_forum_id'] = unserialize($vbulletin->options['tapatalk_hide_forum']);
        }
      
        return $config;
    }
    
    function read_config_file()
    {
        require_once CWD1. "/config/config.php";

        $hide_forum_key = array('hide_forum_id');

        foreach($hide_forum_key as $key)
        {
            $hide_forums = preg_split('/\s*,\s*/', $config[$key], -1, PREG_SPLIT_NO_EMPTY);
            count($hide_forums) and $config[$key] = $hide_forums;
        }   

        $mobiquo_config = $config;
        
        return $mobiquo_config;
    }   
}
