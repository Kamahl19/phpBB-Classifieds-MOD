<?php
/**
*
* @package phpBB Classifieds MOD
* @author Kamahl kamahl19@gmail.com
* @version 1.2.0
* @copyright (c) 2012 http://phpbb3hacks.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include( $phpbb_root_path . 'includes/functions_messenger.' .$phpEx );
include($phpbb_root_path . CL_DIRECTORY . '/includes/functions_buysell.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/classified');

$id = request_var('ad_id', 0);
$mode = request_var('mode', '');
$poster = request_var('p', 0);
$comment = request_var('comment', 0);

switch($mode)
{
	case "new_comment":
	
		$comment_text = request_var('comment_text','', true);
		$ad_id = request_var('ad_id', 0);
		
		if (!empty($comment_text))
  	{
  		$uid = $bitfield = $options = '';
  		$allow_bbcode = $allow_urls = $allow_smilies = true;
  
  		generate_text_for_storage($comment_text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
  
  		$sql_ary = (array(
      	'comment_date'      => time(),
  			'comment_poster_id' => $user->data['user_id'],
  			'comment_text'      => $comment_text,
      	'bbcode_uid'        => $uid,
      	'bbcode_bitfield'   => $bitfield,
     		'bbcode_options'    => $options,
     		'ad_id'						  => $ad_id,
  		));
  
  		$sql = 'INSERT INTO ' . CLASSIFIEDS_COMMENTS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
  		$db->sql_query($sql);
  	
  		$sql_ary =  array(
  			'SELECT'	=> ' a.*, u.user_id, u.username, u.user_colour, u.user_from, u.user_colour, u.user_lang, u.user_email, u.user_jabber, u.user_notify_type',
  			'FROM'		=> array(
  				CLASSIFIEDS_TABLE				=> 'a',
  			),
  			'LEFT_JOIN'	=> array(
  				array(
  					'FROM'	=> array(USERS_TABLE => 'u'),
  					'ON'	=> 'u.user_id = a.ad_poster_id',		
  				)
  			),
  			'WHERE'		=> 'a.ad_id ='.$ad_id.' AND u.user_id = a.ad_poster_id',
  		);
  
  		$sql = $db->sql_build_query('SELECT', $sql_ary);
  		
  		$result	 = $db->sql_query($sql);
  
  		$row = $db->sql_fetchrow($result);
  	
  		// Send a email to the ad poster if he wants to be notified
    	if($row['notify_comments'] && $row['user_id'] != $user->data['user_id'])
  		{
  			send_mail_notify_comment($row['user_lang'], $row['user_email'], $row['username'], $row['user_jabber'], $ad_id, $row['user_notify_type']);
  		}
		}
    redirect(append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx","ad_id=" . $id));

	break;

	case "delete":
	
		if ($user->data['is_registered'] && ($auth->acl_getf_global('m_') || $auth->acl_get('a_') || $user->data['user_id'] == $poster))
		{
			if (confirm_box(true))
			{
	    	$sql = 'DELETE
						      FROM ' . CLASSIFIEDS_COMMENTS_TABLE . '
                  WHERE comment_id = ' . $comment;
				$db->sql_query($sql);
				$id = request_var('ad_id', 0);

				redirect(append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", "ad_id=" . $id));
			}
			else
			{
				confirm_box(false, $user->lang['CL_DELETE_COMMENT_CONFIRM']);
				redirect(append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", "ad_id=" . $id));
			}
		}
		else
		{
	    trigger_error('NOT_AUTHORISED');
		}
	
	break;	
}

?>