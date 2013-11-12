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

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// The name of the mod to be displayed during installation.
$mod_name = 'Classifieds MOD';

/*
* The name of the config variable which will hold the currently installed version
* You do not need to set this yourself, UMIL will handle setting and updating the version itself.
*/
$version_config_name = 'classifieds_version';

/*
* The language file which will be included when installing
* Language entries that should exist in the language file for UMIL (replace $mod_name with the mod's name you set to $mod_name above)
* $mod_name
* 'INSTALL_' . $mod_name
* 'INSTALL_' . $mod_name . '_CONFIRM'
* 'UPDATE_' . $mod_name
* 'UPDATE_' . $mod_name . '_CONFIRM'
* 'UNINSTALL_' . $mod_name
* 'UNINSTALL_' . $mod_name . '_CONFIRM'
*/
$language_file = 'mods/classified';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
$versions = array(
	'0.9.0' => array(
		'table_add' => array(
			array(CLASSIFIEDS_TABLE, array(
				'COLUMNS'			=> array(
					'ad_id'				=> array('UINT', NULL, 'auto_increment'),
					'ad_title'			=> array('VCHAR', ''),
					'ad_description'	=> array('TEXT', ''),
					'ad_poster_id'		=> array('UINT', 0),
					'ad_price'			=> array('VCHAR', ''),
					'ad_date'			=> array('VCHAR', ''),
					'bbcode_uid'		=> array('STEXT_UNI', ''),
					'bbcode_bitfield'	=> array('VCHAR', '0'),
					'bbcode_options'	=> array('VCHAR', '0'),
					'enable_bbcode'		=> array('USINT', 1),
					'enable_magic_url'	=> array('USINT', 1),
					'enable_smilies'	=> array('USINT', 1),
					'ad_status'			=> array('BOOL', 1),
					'cat_id'			=> array('UINT',0),
					'ad_views'			=> array('VCHAR', '0'),
					'ad_expire'			=> array('VCHAR:255', ''),
					'expire_email'			=> array('VCHAR:255', ''),
					'allow_comments'			=> array('USINT', 0),
					'notify_comments'			=> array('USINT', 0),
					'thumb'			=> array('VCHAR', ''),
					'phone'			=> array('VCHAR', ''),
     			'last_edit_by'			=> array('VCHAR', '0'),
					'edit_time'			=> array('VCHAR', '0'),
					'paypal'			=> array('VCHAR', ''),
					'paypal_currency'			=> array('VCHAR', ''),
				),
				'PRIMARY_KEY' => array('ad_id'),
			)),

			array(CLASSIFIEDS_CATEGORY_TABLE, array(
				'COLUMNS'	=> array(
					'id'		=> array('UINT', NULL, 'auto_increment'),
					'name'		=> array('VCHAR', ''),
					'left_id'		=> array('UINT', 0),
					'right_id'		=> array('UINT', 0),
					'parent'		=> array('VCHAR', ''),
					'parent_id'		=> array('VCHAR', 0),
				),
				'PRIMARY_KEY' => array('id'),
			)),

			array(CLASSIFIEDS_COMMENTS_TABLE, array(
				'COLUMNS'	=> array(
					'comment_id'			=> array('UINT', NULL, 'auto_increment'),
					'comment_date'			=> array('VCHAR', ''),
					'comment_poster_id'		=> array('UINT', '0'),
					'comment_text'			=> array('TEXT', ''),
					'ad_id'					=> array('VCHAR', ''),
					'bbcode_bitfield'		=> array('VCHAR', ''),
					'bbcode_uid'			=> array('VCHAR', ''),
					'bbcode_options'		=> array('UINT', '0'),
					'enable_smilies'		=> array('TINT:', 1),
					'enable_bbcode'			=> array('TINT:', 1),
					'enable_magic_url'		=> array('TINT:', 1),
				),
				'PRIMARY_KEY' => array('comment_id'),
			)),
		),
		
		'table_row_insert' => array(
			array(CLASSIFIEDS_CATEGORY_TABLE, array(
        'id'  => '1',
        'name'  => 'Example',
        'left_id'  => '1',
        'right_id'  => '2',
        'parent'  => '0',
        'parent_id'  => '0',
      )),
    ),

		'module_add' => array(
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_CLASSIFIEDS'),
			array('acp', 'ACP_CLASSIFIEDS', array(
				'module_basename'  => 'classifieds',
        'modes'   => array('index'),
      ), ),
      array('acp', 'ACP_CLASSIFIEDS', array(
      	'module_basename'	=> 'classifieds',
        'modes'	=> array('manage'),
      ), ),
		),

		'config_add' => array(
			array('enable_classifieds', '1', '0'),
			array('disable_message', 'classifieds disabled', '0'),
			array('number_ads', '10', '0'),
			array('number_expire', '7', '0'),
			array('email_ad', '0' , '0'),
			array('email_expire', '0', '0'),
			array('pm_ad', '0' , '0'),
			array('pm_expire', '0' , '0'),
			array('show_rules', '1', '0'),
			array('rules_message', 'Please go to your ACP - .MODs and rename the "Example" category. Then you can add more categories. This is just an example of trading rules. You can edit it in ACP - .MODs', '0'),
			array('pm_id', '2', '0'),
			array('sold_color', '#ECD5D8' , ''),
			array('closed_color', '#FFE4B5', ''),
			array('allow_tinypic', '1' , '0'),
			array('allow_comments', '1', '0'),
			array('allow_upload', '1' , ''),
			array('upload_size', '400', ''),
			array('sort_active_first', '0', '0'),
			array('display_ads_on_index', '1', '0'),
			array('ad_num_display_on_index', '6', '0'),
			array('show_full', '0', '0'),
		),

		'permission_add' => array(
			array('u_view_classifieds', 1),
			array('u_post_classifieds', 1),
			array('u_edit_own_classifieds', 1),
			array('u_can_delete_classifieds', 1),
		),

		'table_column_add' => array(
			array(USERS_TABLE, 'classified_email', array('USINT', 0)),
			array(USERS_TABLE, 'classified_display_recent_ads', array('USINT', '1')),
			array(USERS_TABLE, 'last_classifieds_visit', array('VCHAR', '0')),
		),
	),
	
	'0.9.1' => array(
    'config_remove' => 'rules_message',
    
    'config_add' => array(
			array('recent_ads_place', '1', '0'),
		),
	),
	
	'0.9.2' => array(
    'config_add' => array(
			array('allow_addthis_button', '1', '0'),
			array('required_posts_to_create', '0', '0'),
			array('required_posts_to_view', '0', '0'),
			array('allow_ad_prefix', '1', '0'),
			array('ad_prefix_color', '#FF0000', '0'),
		),
		
		'permission_add' => array(
			array('m_edit_classifieds', 1),
			array('m_move_classifieds', 1),
			array('m_delete_classifieds', 1),
		),
		
		'module_add' => array(
      array('acp', 'ACP_CLASSIFIEDS', array(
      	'module_basename'	=> 'classifieds',
        'modes'	=> array('purge'),
      ), ),
		),
		
		'table_column_add' => array(
			array(CLASSIFIEDS_TABLE, 'ad_prefix', array('VCHAR', '')),
		),
	),
	
	'0.9.3' => array(
	
	  'config_add' => array(
			array('mandatory_ad_prefix', '0', '0'),
		),
	),
	
	'0.9.4' => array(

   'table_column_add' => array(
			array(CLASSIFIEDS_TABLE, 'reported', array('TINT:1', '0')),
			array(CLASSIFIEDS_TABLE, 'report_text', array('VCHAR:1024', '')),
			array(CLASSIFIEDS_TABLE, 'reported_by', array('UINT', 0)),
		),
		
		'config_add' => array(
			array('allow_ad_report', '1', '0'),
		),
		
		'permission_add' => array(
			array('m_report_classifieds', 1),
		),
	),
	
	'0.9.5' => array(

		'config_update' => array(
			array('upload_size', '600',),
		),
		
		'config_add' => array(
			array('enable_watch_cat', '1', '0'),
			array('rand_ad_num_display_on_index', '4', '0'),
			array('display_rand_ads_on_index', '1', '0'),
			array('rand_ads_place', '1', '0'),
			array('rand_miniblock_place', '0', '0'),
			array('display_rand_miniblock', '1', '0'),
			array('rand_miniblock_num_ads', '4', '0'),
			array('advertisers_block_place', '0', '0'),
			array('display_advertisers_ads', '1', '0'),
			array('advertisers_ads_num', '4', '0'),
		),
		
		'config_remove' => 'show_full',
		'config_remove' => 'sort_active_first',
		
		'table_add' => array(
			array(CLASSIFIEDS_CAT_WATCH_TABLE, array(
				'COLUMNS'	=> array(
					'cat_id'		=> array('UINT', 0),
					'user_id'		=> array('UINT', 0),
				),
			)),
		),
		
		'table_column_add' => array(
			array(USERS_TABLE, 'classified_watch', array('USINT', 0)),
			array(PROFILE_FIELDS_TABLE, 'field_show_on_cl', array('TINT:1', '0')),
			array(CLASSIFIEDS_TABLE, 'short_desc', array('VCHAR:128', '')),
		),
		
		'table_column_remove' => array(
			array(USERS_TABLE, 'classified_email'),
		),
		
		'permission_add' => array(
			array('u_can_report_classifieds', 1),
			array('m_extend_classifieds', 1),
		),
		
		'module_add' => array(
      array('acp', 'ACP_CLASSIFIEDS', array(
      	'module_basename'	=> 'classifieds',
        'modes'	=> array('cats'),
      ), ),
      array('acp', 'ACP_CLASSIFIEDS', array(
      	'module_basename'	=> 'classifieds',
        'modes'	=> array('blocks'),
      ), ),
		),
	),
	
	'0.9.6' => array(

	  'config_add' => array(
			array('user_can_disable_comments', '1', '0'),
			array('hot_block_place', '1', '0'),
			array('display_hot_ads', '1', '0'),
			array('hot_ads_num', '3', '0'),
		),

		'table_column_update' => array(
   		array(CLASSIFIEDS_TABLE, 'ad_views', array('UINT', '0')),
		),
	),
	
	'0.9.7' => array(

		'table_column_update' => array(
   		array(CLASSIFIEDS_TABLE, 'ad_status', array('TINT:1', '0')),
		),
		
		'config_add' => array(
			array('allow_users_set_expiration', '0', '0'),
			array('min_expiration_by_user', '0', '0'),
			array('max_expiration_by_user', '0', '0'),
		),
		
		'table_add' => array(
			array(CLASSIFIEDS_IMAGES_TABLE, array(
				'COLUMNS'	=> array(
					'ad_id'		=> array('UINT', 0),
					'image_name'		=> array('VCHAR', ''),
				),
			)),
		),
		
		'table_column_add' => array(
			array(CLASSIFIEDS_TABLE, 'invisible', array('TINT:1', '0')),
			array(CLASSIFIEDS_TABLE, 'secret_key', array('CHAR:32', '0')),
			array(CLASSIFIEDS_TABLE, 'days_active', array('VCHAR', '')),
		),

		'permission_add' => array(
			array('u_can_extend_classifieds', 1),
		),
	),
	
	'0.9.8' => array(
	
	  'config_add' => array(
			array('fill_location_to_trade', '0', '0'),
			array('mandatory_phone', '0', '0'),
			array('mandatory_thumb', '0', '0'),
			array('allow_classifieds_feeds', '1', '0'),
			array('number_ad_feeds', '10', '0'),
			array('default_currency', '1', '0'),
		),
		
		'config_remove' => 'allow_ad_report',
		'config_remove' => 'show_rules',
		
		'module_add' => array(
      array('acp', 'ACP_CLASSIFIEDS', array(
      	'module_basename'	=> 'classifieds',
        'modes'	=> array('rules'),
      ), ),
		),
		
		'table_add' => array(
			array(CLASSIFIEDS_RULES_TABLE, array(
				'COLUMNS'	=> array(
				  'rules_id'		=> array('UINT', 0),
				  'rules_title'			=> array('VCHAR', ''),
					'rules_text'			=> array('TEXT', ''),
					'display_rules'		=> array('TINT:1', 0),
					'must_agree'		=> array('TINT:1', 0),
					'display_as_link'		=> array('TINT:1', 0),
				),
			)),
		),

		'table_row_insert' => array(
			array(CLASSIFIEDS_RULES_TABLE, array(
			  'rules_id'  => '1',
        'rules_title'  => 'General trading rules',
        'rules_text'  => 'This is just an example of General trading rules. You can rewrite it via ACP - .MODs - Tradign rules management.',
        'display_rules'  => 0,
        'must_agree'  => 0,
        'display_as_link'  => 0,
      )),
      array(CLASSIFIEDS_RULES_TABLE, array(
        'rules_id'  => '2',
        'rules_title'  => 'Rules for buyer',
        'rules_text'  => 'This is just an example of rules for buyer. You can rewrite it via ACP - .MODs - Tradign rules management.',
        'display_rules'  => 0,
        'must_agree'  => 0,
        'display_as_link'  => 0,
      )),
      array(CLASSIFIEDS_RULES_TABLE, array(
      	'rules_id'  => '3',
        'rules_title'  => 'Rules for seller',
        'rules_text'  => 'This is just an example of rules for seller. You can rewrite it via ACP - .MODs - Tradign rules management.',
        'display_rules'  => 0,
        'must_agree'  => 0,
        'display_as_link'  => 0,
      )),
    ),
	),
	
	'0.9.8.5' => array(
	),
	
	'0.9.9' => array(
	),
	
	'1.0.0' => array(
	   
	  'custom' => array( 'check_price' ),
	                                            
	  'table_column_remove' => array(
			array(USERS_TABLE, 'classified_display_recent_ads'),
			array(USERS_TABLE, 'last_classifieds_visit'),
			array(USERS_TABLE, 'classified_watch'),
			array(CLASSIFIEDS_TABLE, 'allow_comments'),
			array(CLASSIFIEDS_TABLE, 'ad_prefix'),
			array(CLASSIFIEDS_TABLE, 'invisible'),
			array(CLASSIFIEDS_TABLE, 'secret_key'),
			array(CLASSIFIEDS_TABLE, 'enable_bbcode'),
			array(CLASSIFIEDS_TABLE, 'enable_magic_url'),
			array(CLASSIFIEDS_TABLE, 'enable_smilies'),
			array(CLASSIFIEDS_COMMENTS_TABLE, 'enable_bbcode'),
			array(CLASSIFIEDS_COMMENTS_TABLE, 'enable_magic_url'),
			array(CLASSIFIEDS_COMMENTS_TABLE, 'enable_smilies'),
		),
		
		'config_remove' => 'allow_ad_prefix',
		'config_remove' => 'ad_prefix_color',       
		'config_remove' => 'pm_ad',
		'config_remove' => 'pm_expire',
		'config_remove' => 'pm_id',
		'config_remove' => 'user_can_disable_comments',  
		'config_remove' => 'upload_size',   
		
		'config_add' => array(
			array('upload_max_width', '500', '0'),
			array('upload_max_height', '500', '0'),
			array('mandatory_ad_location', '0', '0'),
			array('last_check_expired', time(), '0'),
		),
		       
		'table_add' => array(
			array(CLASSIFIEDS_CURRENCY_TABLE, array(
				'COLUMNS'	=> array(
				  'id'      => array('UINT', NULL, 'auto_increment'),
					'short'		=> array('VCHAR', ''),
					'name'		=> array('VCHAR', ''),
				),
				'PRIMARY_KEY' => array('id'),
			)),   
			array(CLASSIFIEDS_PREFIXES_TABLE, array(
				'COLUMNS'	=> array(
				  'prefix_id'      => array('UINT', NULL, 'auto_increment'),
					'prefix_short'		=> array('VCHAR', ''),
					'prefix_name'		=> array('VCHAR', ''),
					'prefix_color'			=> array('VCHAR', ''),
				),
				'PRIMARY_KEY' => array('prefix_id'),
			)),
			array(CLASSIFIEDS_LOCATIONS_TABLE, array(
				'COLUMNS'	=> array(
				  'location_id'      => array('UINT', NULL, 'auto_increment'),
					'location_name'		=> array('VCHAR', ''),
				),
				'PRIMARY_KEY' => array('location_id'),
			)),
		),       

		'table_row_insert' => array(
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '1', 'short'  => 'EUR', 'name'  => 'Euro')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '2', 'short'  => 'GBP', 'name'  => 'British pound')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '3', 'short'  => 'USD', 'name'  => 'American dollar')),  
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '4', 'short'  => 'CAD', 'name'  => 'Canadian dollar')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '5', 'short'  => 'AUD', 'name'  => 'Australian dollar')),  
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '6', 'short'  => 'NZD', 'name'  => 'New Zealand dollar')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '7', 'short'  => 'SGD', 'name'  => 'Singapore dollar')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '8', 'short'  => 'HKD', 'name'  => 'Hong Kong dollar')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '9', 'short'  => 'CZK', 'name'  => 'Czech crown')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '10', 'short'  => 'DKK', 'name'  => 'Danish krone')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '11', 'short'  => 'NOK', 'name'  => 'Norwegian krone')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '12', 'short'  => 'SEK', 'name'  => 'Swedish krona')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '13', 'short'  => 'CHF', 'name'  => 'Swiss franc')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '14', 'short'  => 'HUF', 'name'  => 'Hungarian forint')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '15', 'short'  => 'JPY', 'name'  => 'Japanese yen')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '16', 'short'  => 'PLN', 'name'  => 'Polish zloty')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '17', 'short'  => 'ARS', 'name'  => 'Argentine peso')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '18', 'short'  => 'BRL', 'name'  => 'Brazilian real')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '19', 'short'  => 'BYR', 'name'  => 'Belarusian ruble')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '20', 'short'  => 'AED', 'name'  => 'United Arab Emirates dirham')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '21', 'short'  => 'UAH', 'name'  => 'Ukrainian hryvnia')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '22', 'short'  => 'TRY', 'name'  => 'Turkish lira')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '23', 'short'  => 'TND', 'name'  => 'Tunisian dinar')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '24', 'short'  => 'THB', 'name'  => 'Thai baht')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '25', 'short'  => 'ZAR', 'name'  => 'South African rand')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '26', 'short'  => 'RSD', 'name'  => 'Serbian dinar')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '27', 'short'  => 'RUB', 'name'  => 'Russian ruble')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '28', 'short'  => 'RON', 'name'  => 'Romanian leu')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '29', 'short'  => 'PHP', 'name'  => 'Philippine peso')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '30', 'short'  => 'PKR', 'name'  => 'Pakistani Rupee')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '31', 'short'  => 'MXP', 'name'  => 'Mexican peso')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '32', 'short'  => 'INR', 'name'  => 'Indian rupee')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '33', 'short'  => 'ISK', 'name'  => 'Icelandic króna')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '34', 'short'  => 'IDR', 'name'  => 'Indonesian rupiah')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '35', 'short'  => 'EGP', 'name'  => 'Egyptian pound')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '36', 'short'  => 'HRK', 'name'  => 'Croatian kuna')),
      array(CLASSIFIEDS_CURRENCY_TABLE, array('id'  => '37', 'short'  => 'CNY', 'name'  => 'Renminbi')),                
      array(CLASSIFIEDS_PREFIXES_TABLE, array('prefix_id'  => '1', 'prefix_short'  => 'WTB', 'prefix_name'  => 'want to buy', 'prefix_color'  => '')),
      array(CLASSIFIEDS_PREFIXES_TABLE, array('prefix_id'  => '2', 'prefix_short'  => 'S', 'prefix_name'  => 'for sale', 'prefix_color'  => '')),
      array(CLASSIFIEDS_PREFIXES_TABLE, array('prefix_id'  => '3', 'prefix_short'  => 'T', 'prefix_name'  => 'for trade', 'prefix_color'  => '')),  
      array(CLASSIFIEDS_PREFIXES_TABLE, array('prefix_id'  => '4', 'prefix_short'  => 'S_T', 'prefix_name'  => 'for sale or trade', 'prefix_color'  => '')),
      array(CLASSIFIEDS_PREFIXES_TABLE, array('prefix_id'  => '5', 'prefix_short'  => 'FREE', 'prefix_name'  => 'for free', 'prefix_color'  => '')),  
    ),               
         
    'table_column_add' => array(
			array(CLASSIFIEDS_IMAGES_TABLE, 'temp_id', array('UINT', '0')),
			array(CLASSIFIEDS_TABLE, 'ad_prefix_id', array('UINT', '0')),
			array(CLASSIFIEDS_TABLE, 'ad_location_id', array('UINT', '0')),
			array(CLASSIFIEDS_TABLE, 'ad_price_text', array('UINT', '0')),
		),            
    
    'table_column_update' => array(
   		array(CLASSIFIEDS_TABLE, 'ad_price', array('DECIMAL:15:2', '0')),
		),      
		  
		'module_add' => array(
      array('acp', 'ACP_CLASSIFIEDS', array(
      	'module_basename'	=> 'classifieds',
        'modes'	=> array('prefixes'),
      ), ),
      array('acp', 'ACP_CLASSIFIEDS', array(
      	'module_basename'	=> 'classifieds',
        'modes'	=> array('locations'),
      ), ),
		),       
	),
	
	'1.1.0' => array(
	
	  'config_add' => array(
			array('display_profile_last_ads', '1', '0'),
			array('profile_num_last_ads', '5', '0'),
			array('max_img_size', '1048576', '0'),
		),   
		
		'config_remove' => 'allow_tinypic',
		'config_remove' => 'mandatory_thumb',
		'config_remove' => 'upload_max_width',
		'config_remove' => 'upload_max_height',
		
		'module_add' => array(
      array('acp', 'ACP_CLASSIFIEDS', array(
      	'module_basename'	=> 'classifieds',
        'modes'	=> array('currency'),
      ), ),
		),    
		
		'custom' => array( 'truncate_images' ),
		          
		'cache_purge' => array(
			'imageset',
			'template',
			'theme',
			'cache',
		),
	),
	
	'1.2.0' => array(
	),
	
	'1.2.1' => array(
		'config_add' => array(
			array('max_images_per_ad', '0', '0'),
		),
	),
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

function check_price($action, $version)
{
  global $db;
  
  if ($action == 'update')
  {
    $sql = 'SELECT ad_price, ad_id
              FROM ' . CLASSIFIEDS_TABLE;
    $result = $db->sql_query($sql);
    
    while($row = $db->sql_fetchrow( $result )) 
    {
      if ( !is_numeric($row['ad_price']) )
      {
        $sql2 = 'UPDATE ' . CLASSIFIEDS_TABLE . ' SET ad_price = 0 WHERE ad_id = '.$row['ad_id'];
        $db->sql_query($sql2);  
      }
    }
  }
}

function truncate_images($action, $version)
{
  global $db;
  
  if ($action == 'update')
  {
    $db->sql_query('DELETE FROM ' . CLASSIFIEDS_IMAGES_TABLE);
  }
}

?>