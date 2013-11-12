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
include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
include($phpbb_root_path . CL_DIRECTORY . '/includes/functions_buysell.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/classified');

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

$now = time();

$start          = request_var('start', 0);
$cat            = request_var('id', 0);
$mode           = request_var('mode', '');  
$subscribe_cat  = request_var('subscribe', '');
$user_search    = request_var('user', 0);         
  
$sql_ary =  array(
	'SELECT'	=> 'a.ad_id, a.ad_title, a.ad_poster_id, a.ad_price, a.ad_price_text, a.ad_date, a.ad_status, a.paypal_currency, a.short_desc, a.reported, a.cat_id, a.ad_views, a.ad_expire, a.thumb, u.username, u.user_colour, p.prefix_short, p.prefix_color, c.name, c.parent, c.parent_id',
	'FROM'		=> array(
		CLASSIFIEDS_TABLE	=> 'a',
	),
	'LEFT_JOIN'	=> array(
 	  array(
			'FROM'	=> array(USERS_TABLE => 'u'),
			'ON'	=> 'u.user_id = a.ad_poster_id',
    ),
	  array(
			'FROM'	=> array(CLASSIFIEDS_PREFIXES_TABLE => 'p'),
			'ON'	=> 'a.ad_prefix_id = p.prefix_id',
	  ),
	  array(
			'FROM'	=> array(CLASSIFIEDS_CATEGORY_TABLE => 'c'),
			'ON'	=> 'c.id = a.cat_id',
		)
	),          
	'ORDER_BY'	=> 'a.ad_date DESC'
);    

switch ($mode)
{
	case "cat":
		$sql_ary['WHERE']	  = 'a.cat_id= ' . $cat . ' AND a.ad_status = ' . ACTIVE . ' AND a.ad_expire > ' . $now;
		$pagination_url     = append_sid($phpbb_root_path . CL_DIRECTORY . '/index.' . $phpEx . '?mode=cat&amp;id='.$cat);
		$classifieds_title  = get_ad_category($cat);
	break;
	
	case "view_own_active":
		$sql_ary['WHERE']	  = 'a.ad_poster_id = '. $db->sql_escape($user->data['user_id']) .' AND a.ad_status = ' . ACTIVE . ' AND a.ad_expire > ' . $now;
		$pagination_url     = append_sid($phpbb_root_path . CL_DIRECTORY . '/index.' . $phpEx . '?mode=view_own_active');
		$classifieds_title  = $user->lang['CL_VIEW_OWN_MY'].$user->lang['CL_ACTIVE_ADS'];
	break;
	
	case "view_own_expired":
		$sql_ary['WHERE']	  = 'a.ad_poster_id = '. $db->sql_escape($user->data['user_id']) .' AND ad_status <> ' . SOLD . ' AND ad_status <> ' . CLOSED . ' AND a.ad_expire < ' . $now;
	  $pagination_url     = append_sid($phpbb_root_path . CL_DIRECTORY . '/index.' . $phpEx . '?mode=view_own_expired');
		$classifieds_title  = $user->lang['CL_VIEW_OWN_MY'].$user->lang['CL_EXPIRED_ADS'];
  break;
	
	case "view_own_sold":
		$sql_ary['WHERE']	  = 'a.ad_poster_id = '. $db->sql_escape($user->data['user_id']) .' AND a.ad_status = ' . SOLD;
		$pagination_url     = append_sid($phpbb_root_path . CL_DIRECTORY . '/index.' . $phpEx . '?mode=view_own_sold');
		$classifieds_title  = $user->lang['CL_VIEW_OWN_MY'].$user->lang['CL_SOLD_ADS'];
	break;
	
	case "view_own_closed":
		$sql_ary['WHERE']	  = 'a.ad_poster_id = '. $db->sql_escape($user->data['user_id']) .' AND a.ad_status = ' . CLOSED;
		$pagination_url     = append_sid($phpbb_root_path . CL_DIRECTORY . '/index.' . $phpEx . '?mode=view_own_closed');
		$classifieds_title  = $user->lang['CL_VIEW_OWN_MY'].$user->lang['CL_CLOSED_ADS'];
	break;
	
	case "reported":
   	($auth->acl_get('a_') || $auth->acl_get('m_report_classifieds')) ? $sql_ary['WHERE']	= 'a.reported = "1"' : trigger_error('CL_CANT_SOLVE_REPORTED');
   	$pagination_url     = append_sid($phpbb_root_path . CL_DIRECTORY . '/index.' . $phpEx . '?mode=reported');
		$classifieds_title  = $user->lang['CL_AD_REPORTED'];
	break;
	
	case "viewuser":
		$sql_ary['WHERE']	  = 'a.ad_poster_id = '. $db->sql_escape($user_search);     
    $pagination_url     = append_sid($phpbb_root_path . CL_DIRECTORY . '/index.' . $phpEx . '?mode=viewuser&amp;user='. $user_search);
		$classifieds_title  = get_username($user_search) . '\'s ' . $user->lang['CL_ADS'];  
	break;   
	
	case "search":
	  $term           = utf8_normalize_nfc(request_var('stext', '', true)); 
    $prefix_id      = request_var('sprefix', 0);
    $category_id    = request_var('scategory', 0);
    $location_id    = request_var('slocation', 0);
    $only_active    = request_var('sactive', 0);
    $order          = request_var('sorder', '');
    $order_type     = request_var('sordertype', '');

		$searchterm = '*' . strtolower($term) . '*';
		if ($searchterm != '**')
		{
			$searchterm = str_replace('*', $db->any_char , $searchterm);
			$searchterm = str_replace('?', $db->one_char , $searchterm);
		}
		
		$sql_where  = 'a.ad_expire > ' . $now . '';
		$sql_where .= ($term         != '')  ? ' AND ( LOWER(a.ad_description) ' . $db->sql_like_expression($searchterm) . ' OR LOWER(a.ad_title) ' . $db->sql_like_expression($searchterm) . ' OR LOWER(a.short_desc) ' . $db->sql_like_expression($searchterm) . ')' : '';
		$sql_where .= ($prefix_id    != 0)   ? ' AND a.ad_prefix_id = ' . $prefix_id . '' : '';
		$sql_where .= ($location_id  != 0)   ? ' AND a.ad_location_id = ' . $location_id . '' : '';
		$sql_where .= ($category_id  != 0)   ? ' AND a.cat_id = ' . $category_id . '' : '';
		$sql_where .= ($only_active  != 0)   ? ' AND a.ad_status = ' . ACTIVE . '' : '';
		
    if ($order == 'time' && $order_type != '')
    {
      $sql_order = ($order_type == 'desc') ? 'a.ad_date DESC' : 'a.ad_date ASC';  
    }
    elseif ($order == 'views' && $order_type != '')
    {
      $sql_order = ($order_type == 'desc') ? 'a.ad_views DESC' : 'a.ad_views ASC';  
    }
    elseif ($order == 'price' && $order_type != '')
    {
      $sql_order = ($order_type == 'desc') ? 'a.ad_price DESC' : 'a.ad_price ASC';  
    }
    elseif ($order == '' || $order_type == '')
    {
      $sql_order = 'a.ad_date DESC';    
    }
		
		$sql_ary['WHERE']	    = $sql_where;
		$sql_ary['ORDER_BY']	= $sql_order;
		
 		$template->assign_vars(array(
    	'STEXT'            => $term,
    	'SPREFIX'          => $prefix_id,
    	'SCATEGORY'        => $category_id,
    	'SLOCATION'        => $location_id,
    	'SACTIVE'          => $only_active,
    	'SORDER'           => $order,
    	'SORDERTYPE'       => $order_type,
    ));
    
    $pagination_url     = append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.{$phpEx}", 'mode=search&amp;stext='.$term.'&amp;sprefix='.$prefix_id.'&amp;slocation='.$location_id.'&amp;sactive='.$only_active.'&amp;scategory='.$category_id.'&amp;sorder='.$order.'&amp;sordertype='.$order_type);
 		$classifieds_title  = ($term != '') ? $user->lang['CL_SEARCH_RESULTS'].' '.$user->lang['CL_FOR'].' "'.$term.'"' : $user->lang['CL_SEARCH_RESULTS'];
	break;
	
	default:
	  $sql_ary['WHERE']	= 'a.ad_status = ' . ACTIVE . ' AND a.ad_expire > ' . $now;   
	  $pagination_url     = append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx");
    $classifieds_title  = $user->lang['CL_ACTIVE_ADS'];
}

$sql = $db->sql_build_query('SELECT', $sql_ary);
$result = $db->sql_query_limit($sql, $config['number_ads'], $start);

while($row = $db->sql_fetchrow($result)) 
{ 
  if ($row['ad_status'] == 0)
  {
    $ad_status = ($row['ad_expire'] > $now ) ? false : '<b>[' . $user->lang['CL_EXPIRED'] . ']</b>';
  }
  elseif ($row['ad_status'] == 1)
  {
    $ad_status = '<b>[' . $user->lang['CL_SOLD'] . ']</b>';
  }
  elseif ($row['ad_status'] == 2)
  {
    $ad_status = '<b>[' . $user->lang['CL_CLOSED'] . ']</b>';
  }
  else
  {
    $ad_status = false;  
  }

	$template->assign_block_vars('ad',array(
		'AD_VIEWS'          => $row['ad_views'],
		'AD_TITLE'          => $row['ad_title'],
		'AD_PRICE'		      => ($row['ad_price_text'] == 0) ? str_replace('.00', '', $row['ad_price']) . ' ' . $row['paypal_currency'] : $user->lang['CL_BY_AGREEMENT'],
		'AD_DATE'			      => $user->format_date($row['ad_date']),
		'AD_POSTER'		      => get_username_string('full', $row['ad_poster_id'], $row['username'], $row['user_colour']),
		'AD_SHORT_DESC'	    => $row['short_desc'],
		'AD_STATUS'		      => $row['ad_status'],
		'AD_STATUS_TEXT'    => $ad_status,
		'AD_PREFIX'         => $row['prefix_short'],        
		'AD_PREFIX_COLOR'   => $row['prefix_color'],
		'THUMB'				      => $row['thumb'],
		'S_EXPIRED'         => ( $row['ad_expire'] < $now ) ? true : false,
		'S_REPORTED'	      => ( $row['reported'] && ( $auth->acl_get('a_') || $auth->acl_get('m_report_classifieds') ) ) ? true : false,
		'CATEGORY'		      => ($row['parent'] == '0' && $row['parent_id'] != '0') ? get_ad_parent($row['parent_id']) . ' » ' . $row['name'] : $row['name'],
		'U_AD_LINK' 		    => append_sid($phpbb_root_path . CL_DIRECTORY . '/single_ad.' . $phpEx ,'ad_id=' . $row['ad_id']),
		'U_EXTEND_LINK'		  => ($auth->acl_get('m_extend_classifieds') || $auth->acl_get('u_can_extend_classifieds') || $auth->acl_get('a_')) ? append_sid($phpbb_root_path . CL_DIRECTORY . '/manage_ad.' . $phpEx ,'mode=extend_ad&amp;ad_id=' . $row['ad_id']) : '',
		'U_DELETE_LINK'		  => (($auth->acl_get('u_can_delete_classifieds') && $user->data['user_id'] == $row['ad_poster_id'] ) || $auth->acl_get('a_') || $auth->acl_get('m_delete_classifieds')) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx", 'mode=delete_ad&amp;ad_id=' . $row['ad_id']) : '',
		'U_EDIT_LINK'		    => (($auth->acl_get('u_edit_own_classifieds') && $user->data['user_id'] == $row['ad_poster_id']) || $auth->acl_get('a_') || $auth->acl_get('m_edit_classifieds') ) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx", 'mode=edit_ad&amp;ad_id=' . $row['ad_id']) : '',
	));    
}        
$db->sql_freeresult($result); 

// pagination                  
$sql_ary['SELECT'] = 'COUNT(ad_id) as all_ads';
$sql = $db->sql_build_query('SELECT', $sql_ary);

$result = $db->sql_query($sql);
$total_ads = $db->sql_fetchfield('all_ads');
$db->sql_freeresult($result);

$template->assign_vars(array(
	'PAGINATION'     => generate_pagination($pagination_url, $total_ads, $config['number_ads'], $start),
	'PAGE_NUMBER'    => on_page($total_ads, $config['number_ads'], $start),
	'TOTAL_ADS'      => ($total_ads == 1) ? $user->lang['CL_LIST_AD'] : sprintf($user->lang['CL_LIST_ADS'], $total_ads),
));

// Subscribing
if (!empty($cat) && $config['enable_watch_cat'])
{
  $can_watch_cat = true;
  $watching_cat = watching_cat($user->data['user_id'], $cat);
  
  if ($subscribe_cat == "1")
  {
  	if (!$watching_cat)
  	{
  		subscribe_cat($user->data['user_id'], $cat);
  
  		redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'id='.$cat) );
  	}
  	elseif ($watching_cat)
  	{
      unsubscribe_cat($user->data['user_id'], $cat);
  
  		redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'id='.$cat) );
  	}
  }
}
else
{
  $can_watch_cat = false;
	$watching_cat = false;
}

if ($config['allow_classifieds_feeds'])
{
	if ( !empty($cat) )
	{
		$feeds_mode = $cat;
	}
	else
	{
    $feeds_mode = 'active';
	}
}

// Random ads block
if ($config['display_rand_miniblock'] && $config['rand_miniblock_num_ads'] != 0)
{
	display_random_ads($config['rand_miniblock_num_ads']);
}

// Hot ads block
if ($config['display_hot_ads'] && $config['hot_ads_num'] != 0)
{
	display_hot_ads($config['hot_ads_num']);
}

load_rules(1);

load_prefixes();

load_locations();

user_total_ads($user->data['user_id'], 'left_bar');

$template->assign_block_vars('navlinks', array(
  'FORUM_NAME'    => $user->lang['CL_CLASSIFIEDS'],
  'U_VIEW_FORUM'  => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx"),
));

$template->assign_block_vars('navlinks', array(
  'FORUM_NAME'    => $classifieds_title,
  'U_VIEW_FORUM'  => $pagination_url,
));

$template->assign_vars(array(
  'S_IN_CLASSIFIEDS_INDEX' => true,
  'S_WATCHING_CAT'			   =>	$watching_cat,
	'S_RANDOM_LEFT'				   => $config['rand_miniblock_place'],
	'S_DISPLAY_RANDOM_BLOCK' => $config['display_rand_miniblock'],
	'S_HOT_LEFT'				     => $config['hot_block_place'],
	'S_DISPLAY_HOT_BLOCK'	   => $config['display_hot_ads'],
	'S_CLOSED_COLOR'	       =>	$config['closed_color'],
	'S_SOLD_COLOR'	         =>	$config['sold_color'],

  'RANDOM_BLOCK_WIDTH'	   => ($config['rand_miniblock_num_ads'] != 0) ? floor(100 / $config['rand_miniblock_num_ads']) : '',
  'HOT_BLOCK_WIDTH'	       => ($config['hot_ads_num'] != 0) ? floor(100 / $config['hot_ads_num']) : '',
  'CATEGORIES'					   =>	build_categories(),
  'CLASSIFIEDS_TITLE'		   =>	$classifieds_title,

  'L_CL_CLASSIFIEDS_RSS'	 => $user->lang('CL_CLASSIFIEDS_RSS', (!empty($cat)) ? $user->lang['CL_FOR_CAT'].$classifieds_title : $user->lang['CL_FOR_ALL_ACTIVE_ADS']),

	'U_VIEW_REPORTED_LINK'	 =>	($auth->acl_get('a_') || $auth->acl_get('m_report_classifieds')) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.{$phpEx}", 'mode=reported') : '',
	'U_NEW_AD'						   =>	($auth->acl_get('u_post_classifieds')) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.{$phpEx}", 'mode=create_ad') : '',
	'U_SEARCH_ADS'				   => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.{$phpEx}", "mode=search"),
	'U_WATCH_CAT'					   => ($can_watch_cat) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.{$phpEx}", 'id='.$cat.'&amp;subscribe=1') : '',
	'U_CLASSIFIEDS_RSS'		   =>	($config['allow_classifieds_feeds']) ? append_sid("{$phpbb_root_path}".CL_DIRECTORY."/feeds.{$phpEx}", "mode={$feeds_mode}") : '',

	'REPORTED_IMG'				   => $user->img('icon_topic_reported', 'TOPIC_REPORTED'),
));      

if ($now > $config['last_check_expired'] + 3600)
{
  // Select all Active but Expired ads and sent emails to their owners
  $sql =  'SELECT a.ad_expire, a.ad_id, a.ad_title, a.ad_date, a.ad_price, a.ad_price_text, u.user_id, u.username, u.user_lang, u.user_email, u.user_jabber, u.user_notify_type
  					FROM  '. USERS_TABLE .' u, '. CLASSIFIEDS_TABLE .' a
  					WHERE u.user_id = a.ad_poster_id
  						AND ' . $now . ' > a.ad_expire
  						AND (a.expire_email = 0 OR a.expire_email = "")
  						AND a.ad_status = ' . ACTIVE;
  $result = $db->sql_query($sql);
    
  while($row = $db->sql_fetchrow( $result )) 
  {			
  	if($config['email_expire'])
  	{
  	  if ($row['ad_price_text'] == 1)
  		{
        $row['ad_price'] = $user->lang['CL_BY_AGREEMENT']; 
      }
      else
      {
        $row['ad_price'] = str_replace('.00', '', $row['ad_price']);
      }
        
      send_mail_after_expiration($row['user_lang'], $row['user_email'], $row['username'], $row['user_jabber'], $row['ad_id'], $row['ad_title'], $row['ad_date'], $row['ad_price'], $row['ad_expire'], $row['user_notify_type']);
  	}
  
  	$sql2 = 'UPDATE ' . CLASSIFIEDS_TABLE . ' SET expire_email = 1 WHERE ad_id = ' . $row['ad_id'];
  	$result2 = $db->sql_query($sql2);
  }
  
  $db->sql_freeresult($result);
  
  set_config('last_check_expired', $now);
}

page_header($user->lang('CL_CLASSIFIEDS'));

$template->set_filenames(array(
  'body' => 'classifieds_index_body.html',
));

page_footer();

?>