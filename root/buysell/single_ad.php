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
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . CL_DIRECTORY . '/includes/functions_buysell.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/classified');
$user->add_lang('posting');

$ad_id = request_var('ad_id', 0);
$start = request_var('start', 0);     
$action = request_var('action', '');

if (!$config['enable_classifieds'])
{
  trigger_error($config['disable_message']);
}   

if (!$auth->acl_get('u_view_classifieds') && !$user->data['is_bot'])
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

if ( $user->data['user_posts'] < $config['required_posts_to_view'] && !$user->data['is_bot'] )
{
	trigger_error('CL_NOT_ENOUGH_POSTS_TO_VIEW');
}

// Select all data about an Ad and user
$sql_ary =  array(
	'SELECT'	=> 'c.name, a.ad_title, a.cat_id, a.ad_description, a.ad_poster_id, a.ad_price, a.ad_price_text, a.ad_date, a.bbcode_uid, a.bbcode_bitfield, a.bbcode_options, a.ad_status, a.ad_expire, a.phone, a.last_edit_by, a.edit_time, a.paypal, a.paypal_currency, a.reported, p.prefix_short, p.prefix_color, l.location_name, u.user_id, u.username, u.user_colour, u.user_from, u.user_regdate, u.user_posts, u.user_aim, u.user_msnm, u.user_yim, u.user_jabber, u.user_email, u.user_allow_viewemail, u.user_allow_pm',
	'FROM'		=> array(
		CLASSIFIEDS_TABLE	=> 'a',
	),
	'LEFT_JOIN'	=> array(
 	array(
			'FROM'	=> array(USERS_TABLE => 'u'),
			'ON'	=> 'u.user_id = a.ad_poster_id',
		),
	array(
			'FROM'	=> array(CLASSIFIEDS_CATEGORY_TABLE => 'c'),
			'ON'	=> 'a.cat_id = c.id',
		),
	array(
			'FROM'	=> array(CLASSIFIEDS_PREFIXES_TABLE => 'p'),
			'ON'	=> 'a.ad_prefix_id = p.prefix_id',
		),
	array(
			'FROM'	=> array(CLASSIFIEDS_LOCATIONS_TABLE => 'l'),
			'ON'	=> 'a.ad_location_id = l.location_id',
		)
	),
	'WHERE'		=> 'a.ad_id = ' . $ad_id,
);
$sql = $db->sql_build_query('SELECT', $sql_ary);
$result	 = $db->sql_query($sql);

if (!$db->sql_affectedrows())
{
  trigger_error('CL_DOES_NOT_EXIST');
}

$row = $db->sql_fetchrow( $result );
$db->sql_freeresult($result);  

// Quick edit - make Ad sold, closed or active
if ($action == 'active_ad' || $action == 'sold_ad' || $action == 'close_ad')
{
  if ($user->data['user_id'] != $row['ad_poster_id'] && !$auth->acl_get('a_'))
  {
    trigger_error('NOT_AUTHORISED');
  }
  else
  {
    switch($action)
    {
      case "active_ad":
        $new_status = 0;
        break;
        
      case "sold_ad":
        $new_status = 1;
        break;
        
      case "close_ad":
        $new_status = 2;
        break;
    }
    
  	$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . '
              SET ad_status = ' . $new_status . '
    						WHERE ad_id = ' . $ad_id;
    $db->sql_query($sql);
    
    redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", 'ad_id='.$ad_id) );
  }
}    

// Update the views for the ad.
update_views($ad_id);          

// Is the ad reported?
if ($row['reported'])
{
	$sql = 'SELECT c.reported_by, c.report_text, u.username, u.user_colour, u.user_id
		FROM ' . CLASSIFIEDS_TABLE . ' c, ' . USERS_TABLE . ' u
		WHERE c.reported_by = u.user_id
			AND c.ad_id = ' . $ad_id;
	$result = $db->sql_query_limit($sql, 1);
	$report = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$template->assign_vars(array(
		'REPORTER'				=> get_username_string('full', $report['user_id'], $report['username'], $report['user_colour']),
		'REPORT_TEXT'  => $report['report_text'],
  ));
}

$allow_bbcode = $allow_urls = $allow_smilies = true;

$row['bbcode_options'] = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) +
    (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) +
    (($allow_urls) ? OPTION_FLAG_LINKS : 0);

$description = generate_text_for_display($row['ad_description'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']);

// Load custom profile fields
if (!class_exists('custom_profile'))
{
	include($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
}
$cp = new custom_profile();

// Grab all profile fields from users in id cache for later use
$profile_fields_tmp = $cp->generate_profile_fields_template('grab', $row['ad_poster_id']);

// filter out fields not to be displayed on classifieds
$profile_fields_cache = array();
foreach ($profile_fields_tmp as $profile_user_id => $profile_fields)
{
	$profile_fields_cache[$profile_user_id] = array();
	foreach ($profile_fields as $used_ident => $profile_field)
	{
		if ($profile_field['data']['field_show_on_cl'])
		{
			$profile_fields_cache[$profile_user_id][$used_ident] = $profile_field;
		}
	}
}
unset($profile_fields_tmp);

$cp_row = array();
$cp_row = (isset($profile_fields_cache[$row['ad_poster_id']])) ? $cp->generate_profile_fields_template('show', false, $profile_fields_cache[$row['ad_poster_id']]) : array();

if ($row['ad_status'] == 0)
{
  $ad_status = ($row['ad_expire'] > time()) ? false : $user->lang['CL_EXPIRED'];
} 
else if ($row['ad_status'] == 1)
{
  $ad_status = $user->lang['CL_SOLD'];  
}
else if ($row['ad_status'] == 2)
{
  $ad_status = $user->lang['CL_CLOSED'];
}

// Display Advertiser's ads block
if ($config['display_advertisers_ads'] && $config['advertisers_ads_num'] != 0 && $row['ad_poster_id'] != '')
{
	display_advertisers_ads($config['advertisers_ads_num'], $row['user_id'], $ad_id);
	
	$template->assign_vars(array(
    'AD_POSTER_USERNAME'        =>  $row['username'],
    'ADVERTISERS_BLOCK_WIDTH'   =>  ($config['advertisers_ads_num'] != 0) ? floor(100 / $config['advertisers_ads_num']) : '',
  ));
}

// User can edit or only move an ad
if ( ($auth->acl_get('u_edit_own_classifieds') && $user->data['user_id'] == $row['ad_poster_id']) || $auth->acl_get('a_') || $auth->acl_get('m_edit_classifieds') )
{
  $edit_link = "{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx?mode=edit_ad&amp;ad_id=".$ad_id;
}
elseif ( $auth->acl_get('m_move_classifieds') )
{
  $edit_link = "{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx?mode=move_ad&amp;ad_id=".$ad_id;
}
else
{
  $edit_link = false;
}

load_rules(2);  

load_prefixes();

load_locations();

user_total_ads($row['user_id'], 'single_ad');
user_total_ads($user->data['user_id'], 'left_bar');

$template->assign_block_vars('navlinks', array(
  'FORUM_NAME'    => $user->lang['CL_CLASSIFIEDS'],
  'U_VIEW_FORUM'  => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx"),
));

$template->assign_block_vars('navlinks', array(
  'FORUM_NAME'    => censor_text($row['name']),
  'U_VIEW_FORUM'  => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", "mode=cat&id=".$row['cat_id']),
));

$template->assign_block_vars('navlinks', array(
  'FORUM_NAME'    => censor_text($row['ad_title']),
  'U_VIEW_FORUM'  => append_sid($phpbb_root_path . CL_DIRECTORY . '/single_ad.' . $phpEx ,'ad_id=' . $ad_id),
));

$template->assign_vars(array(
  'S_IN_CLASSIFIEDS_SINGLE_AD'      => true,
  'S_BBCODE_ALLOWED' 	              => true,
	'S_ALLOWED_ADDTHIS_BUTTON' 	      => $config['allow_addthis_button'],
	'S_COMMENTS_ENABLED'	            => $config['allow_comments'],
	'S_CUSTOM_FIELDS'	                => (isset($cp_row['row']) && sizeof($cp_row['row'])) ? true : false,
	'S_ADVERTISERS_LEFT'				      => $config['advertisers_block_place'],
	'S_DISPLAY_ADVERTISERS_BLOCK'	    => $config['display_advertisers_ads'],
	'S_CAN_QUICK_AD'                  => ($user->data['user_id'] == $row['ad_poster_id'] || $auth->acl_get('a_')) ? true : false,
	
	'CATEGORIES'			                => build_categories(),

	'EXPIRATION_DATE'	                => $user->format_date($row['ad_expire']),
	'AD_DESCRIPTION'                  => censor_text($description),
	'LOCATION'                        => ($row['location_name']) ? $row['location_name'] : $row['user_from'],
	'EXPIRE_DATE'                     => $row['ad_expire'],
	'NUM_POSTS'                       => $row['user_posts'],
	'JOINED'                          => $user->format_date($row['user_regdate']),
	'AD_TITLE'                        => censor_text($row['ad_title']),
	'AD_PRICE'                        => ($row['ad_price_text'] == 0) ? str_replace('.00', '', $row['ad_price']) : $user->lang['CL_BY_AGREEMENT'],
	'PAYPAL_PRICE'                    => ($row['ad_price_text'] == 0) ? str_replace('.00', '', $row['ad_price']) : '',
	'CAT_NAME'                        => $row['name'],
	'AD_DATE'                         => $user->format_date($row['ad_date']),
	'AD_POSTER'                       => get_username_string('full', $row['user_id'],$row['username'], $row['user_colour']),
	'AD_STATUS'                       => $row['ad_status'],
	'AD_STATUS_TEXT'	                => $ad_status,
	'USER_AIM'                        => $row['user_aim'],
	'USER_MSN'                        => $row['user_msnm'],
	'USER_YIM'                        => $row['user_yim'],
	'USER_JABBER'                     => $row['user_jabber'],
	'PHONE'                           => $row['phone'],
	'PAYPAL'                          => $row['paypal'],
	'AD_CURRENCY'                     => $row['paypal_currency'],
	'AD_PREFIX'                       => $row['prefix_short'],        
	'AD_PREFIX_COLOR'                 => $row['prefix_color'],
	'LAST_EDIT'		                    => ($row['edit_time']) ? sprintf($user->lang['CL_AD_LAST_EDIT'], $user->format_date($row['edit_time']), $row['last_edit_by']) : '',

  'S_SHOW_SMILEY_LINK' 	            => true,
	'U_MORE_SMILIES' 		              => append_sid("{$phpbb_root_path}posting.$phpEx", 'mode=smilies'),
  'S_SMILIES_ALLOWED'               => true,
  'S_BBCODE_ALLOWED'                => true,
	'S_BBCODE_IMG'                    => true,
	'S_LINKS_ALLOWED'                 => true,
	'S_BBCODE_QUOTE'                  => true,

	'U_VIEW_REPORTED_LINK'            => ($auth->acl_get('a_') || $auth->acl_get('m_report_classifieds')) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'mode=reported') : '',
	'U_SEARCH_ADS'                    => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'mode=search'),
	'U_AD_ACTION' 	                  => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", "ad_id=$ad_id"),
	'U_COMMENT'                       => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/includes/class_comment.$phpEx","mode=new_comment&amp;ad_id=".$ad_id),
	'U_CLOSE_REPORT'                  => (($row['reported']) && ($auth->acl_get('a_') || $auth->acl_get('m_report_classifieds'))) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx", 'mode=close_report&amp;ad_id='.$ad_id) : '',
	'U_DELETE_LINK'                   => (($auth->acl_get('u_can_delete_classifieds') && $user->data['user_id'] == $row['ad_poster_id'] ) || $auth->acl_get('a_') || $auth->acl_get('m_delete_classifieds')) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx", 'mode=delete_ad&amp;ad_id='.$ad_id) : '',
 	'U_EXTEND_LINK'                   => (($auth->acl_get('m_extend_classifieds') || ($auth->acl_get('u_can_extend_classifieds') && $user->data['user_id'] == $row['ad_poster_id']) || $auth->acl_get('a_')) && ($row['ad_expire'] < time()) ) ? append_sid($phpbb_root_path . CL_DIRECTORY . '/manage_ad.' . $phpEx ,'mode=extend_ad&amp;ad_id='.intval($ad_id)) : '',
 	'U_EDIT_LINK'                     => ($edit_link) ? append_sid($edit_link) : '',
	'U_REPORT_LINK'                   => ($auth->acl_get('u_can_report_classifieds')) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx", 'mode=report_ad&amp;ad_id='.$ad_id) : '',
	'U_PM'                            => ($config['allow_privmsg'] && $auth->acl_get('u_sendpm') && ($row['user_allow_pm'] || $auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_'))) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&amp;mode=compose&amp;u='.$row['user_id']) : '',
	'U_AIM'                           => ($row['user_aim'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=aim&amp;u='.$row['user_id']) : '',
	'U_EMAIL'                         => ((!empty($row['user_allow_viewemail']) && $auth->acl_get('u_sendemail')) || $auth->acl_get('a_user')) ? ($config['board_email_form'] && $config['email_enable']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=email&amp;u='.$row['user_id']) : (($config['board_hide_emails'] && !$auth->acl_get('a_user')) ? '' : 'mailto:' . $row['user_email']) : '',
	'U_MSN'                           => ($row['user_msnm'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=msnm&amp;u='.$row['user_id']) : '',
	'U_YIM'                           => ($row['user_yim']) ? 'http://edit.yahoo.com/config/send_webmesg?.target=' . urlencode($row['user_yim']) . '&amp;.src=pg' : '',
	'U_JABBER'                        => ($row['user_jabber'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=contact&amp;action=jabber&amp;u='.$row['user_id']) : '',
));

// Select images
$sql =  'SELECT image_name
					FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
					WHERE ad_id = ' . $ad_id;
$result = $db->sql_query($sql);

while($row = $db->sql_fetchrow( $result ))
{
  $template->assign_block_vars('images', array(
		'SOURCE'            => generate_board_url() . '/' . CL_DIRECTORY . '/images/' .$row['image_name'],
		'THUMB_SOURCE'      => generate_board_url() . '/' . CL_DIRECTORY . '/images/thumb/' . $row['image_name'],
	));
}
$db->sql_freeresult($result);

if (!empty($cp_row['blockrow']))
{
	foreach ($cp_row['blockrow'] as $field_data)
	{
		$template->assign_block_vars('custom_fields', $field_data);
	}
}

if ($config['allow_comments'])
{
	$sql_ary = array(
    'SELECT'    => 'c.comment_id, c.comment_date, c.comment_poster_id, c.comment_text, c.bbcode_bitfield, c.bbcode_uid, c.bbcode_options, u.username, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour, u.user_from, u.user_regdate, u.user_allow_pm, u.user_website',
	  'FROM'      => array(
	    CLASSIFIEDS_COMMENTS_TABLE 	=> 'c',
	  ),
	  'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(USERS_TABLE => 'u'),
				'ON'	=> 'u.user_id = c.comment_poster_id',
			)
		),
	  'WHERE'     => 'c.ad_id = ' . $ad_id,
    'ORDER_BY'  => 'c.comment_id ASC',
	);
	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result = $db->sql_query_limit($sql, 10, $start);
	
	while($row = $db->sql_fetchrow( $result ))
	{
		$allow_bbcode = $allow_urls = $allow_smilies = true;

    $row['bbcode_options'] = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) +
      (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) +
      (($allow_urls) ? OPTION_FLAG_LINKS : 0);
      
		$comment_text_format = generate_text_for_display($row['comment_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']);

		$template->assign_block_vars('comment',array(
			'COMMENT_TEXT'       => censor_text($comment_text_format),
			'COMMENT_AVATAR'     => get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']),
			'USERNAME'           => get_username_string('full', $row['comment_poster_id'], $row['username'], $row['user_colour']),
			'COMMENT_DATE'       => $user->format_date($row['comment_date']),
			'FROM'               => $row['user_from'],
			'JOINED'             => $user->format_date($row['user_regdate']),

			'U_DELETE_COMMENT'   => ($user->data['is_registered'] && ($auth->acl_getf_global('m_') || $auth->acl_get('a_') || $user->data['user_id'] == $row['comment_poster_id'])) ? append_sid($phpbb_root_path . CL_DIRECTORY . "/includes/class_comment.$phpEx","mode=delete&amp;comment=" . $row['comment_id'] . "&amp;p=" . $row['comment_poster_id'] . "&amp;ad_id=" . $ad_id) : '',
			'U_PM'               => ($row['comment_poster_id'] != ANONYMOUS && $config['allow_privmsg'] && $auth->acl_get('u_sendpm') && ($row['user_allow_pm'] || $auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_'))) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&amp;mode=compose&amp;u=' . $row['comment_poster_id']) : '',
			'U_WWW'              => $row['user_website'],
		));
	}
	
	display_custom_bbcodes();
	
	include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
	
	generate_smilies('inline', 0);	
	
	// pagination
	$pagination_url = append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", "ad_id=$ad_id");
	
  $sql_ary = array(
	'SELECT'    => 'COUNT(c.comment_id) as total_comments',
	  'FROM'      => array(
	    CLASSIFIEDS_COMMENTS_TABLE 	=> 'c',
	  ),
	  'WHERE'     => 'c.ad_id = ' . $ad_id,
	);
	$sql = $db->sql_build_query('SELECT', $sql_ary);
  $result = $db->sql_query($sql);
	$total_comments = $db->sql_fetchfield('total_comments');
	$db->sql_freeresult($result);
  
  $template->assign_vars(array(
  	'PAGINATION'        => generate_pagination($pagination_url, $total_comments, $config['posts_per_page'], $start),
  	'PAGE_NUMBER'       => on_page($total_comments, $config['posts_per_page'], $start),
  	'TOTAL_COMMENTS'    => ($total_comments == 1) ? $user->lang['CL_LIST_COMMENT'] : sprintf($user->lang['CL_LIST_COMMENTS'], $total_comments),
  ));
}

// Output page
page_header($user->lang('CL_CLASSIFIED_VIEW_AD'));

$template->set_filenames(array(
	'body' => 'classifieds_single_body.html'
));

page_footer();

?>