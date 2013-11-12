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

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
include($phpbb_root_path . CL_DIRECTORY . '/includes/functions_buysell.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/classified');

if (!$config['enable_classifieds'])
{
  trigger_error($config['disable_message']);
}   

if (!$auth->acl_get('u_view_classifieds'))
{
	if ($user->data['user_id'] == ANONYMOUS)
  {
    login_box();
  }
	else
	{
		trigger_error('NOT_AUTHORISED');
	}
}

$rules_id = request_var('rules_id', '');

$uid = $bitfield = $options = '';
$allow_bbcode	= $allow_smilies	= $allow_urls	= true;

$sql_ary =  array(
	'SELECT'	=> 'r.rules_id, r.rules_title, r.rules_text, r.display_rules, r.must_agree, r.display_as_link',
	'FROM'		=> array(
		CLASSIFIEDS_RULES_TABLE	=> 'r',
	),
	'WHERE'		=> 'r.rules_id = '.$rules_id,
	'ORDER_BY'	=> 'r.rules_id ASC'
);

if (!is_numeric($rules_id) || $rules_id > 3 || $rules_id < 1)
{
	$sql_ary['WHERE']	= '';
}

$sql = $db->sql_build_query('SELECT', $sql_ary);
$result = $db->sql_query($sql);

while($row = $db->sql_fetchrow( $result ))
{
	generate_text_for_storage($row['rules_text'], $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
	$row['rules_text']	= generate_text_for_display($row['rules_text'], $uid, $bitfield, $options);

	$template->assign_block_vars('rules',array(
		'S_RULES_DISPLAY'	=>	($row['display_rules'] && !empty($row['rules_text'])) ? true : false,
		'S_RULES_TITLE'	  =>	$row['rules_title'],
		'S_RULES_TEXT'	  =>	$row['rules_text'],
	));
}     
$db->sql_freeresult($result);

if ($config['allow_classifieds_feeds'])
{
  $feeds_mode = 'active';

	$template->assign_vars(array(
 		'L_CL_CLASSIFIEDS_RSS'	=> $user->lang('CL_CLASSIFIEDS_RSS', (!empty($cat)) ? $user->lang['CL_FOR_CAT'].get_ad_category($cat) : $user->lang['CL_FOR_ALL_ACTIVE_ADS']),
	));

	$allow_feeds = true;
}

load_prefixes();

load_locations();

user_total_ads($user->data['user_id'], 'left_bar');

$template->assign_block_vars('navlinks', array(
  'FORUM_NAME'    => $user->lang['CL_CLASSIFIEDS'],
  'U_VIEW_FORUM'  => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx"),
));

$template->assign_block_vars('navlinks', array(
  'FORUM_NAME'    => (empty($row['rules_title'])) ? $user->lang['CL_RULES'] : $row['rules_title'],
  'U_VIEW_FORUM'  => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/rules.$phpEx", "rules_id=$rules_id"),
));

$template->assign_vars(array(
  'S_IN_CLASSIFIEDS_RULES' => true,
  'CATEGORIES'					   => build_categories(),
	'U_VIEW_REPORTED_LINK'	 => ($auth->acl_get('a_') || $auth->acl_get('m_report_classifieds')) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'mode=reported') : '',
	'U_SEARCH_ADS'				   => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'mode=search'),
	'U_CLASSIFIEDS_RSS'		   => ($allow_feeds) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/feeds.$phpEx", "mode={$feeds_mode}") : '',
));

page_header($user->lang('CL_RULES'));

$template->set_filenames(array(
  'body' => 'classifieds_rules_body.html',
));

page_footer();

?>