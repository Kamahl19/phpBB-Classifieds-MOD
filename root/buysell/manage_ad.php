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
include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
include_once($phpbb_root_path . CL_DIRECTORY . '/includes/functions_buysell.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/classified');
$user->add_lang('posting');

// if classifieds mod is disabled
if (!$config['enable_classifieds'])
{
  trigger_error($config['disable_message']);
}   
// if is bot
if ($user->data['is_bot'])
{
	redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
}
// if is not logged in
if ($user->data['user_id'] == ANONYMOUS)
{
	login_box();
}
   
$mode = request_var('mode', '');

// check the mode
if (!$mode || ($mode != 'create_ad' && $mode != 'edit_ad' && $mode != 'move_ad' && $mode != 'delete_ad' && $mode != 'report_ad' && $mode != 'close_report' && $mode != 'extend_ad'))
{
  trigger_error('NO_MODE');
}          

// load rules
$rules_must_agree = load_rules(3);

/* show custom bbcode, smilies, categories, prefixes, locations, curency
*  request and generate temporary advertisement id for images
*  set some template variables
*/
if($mode == 'create_ad' || $mode == 'edit_ad')
{
  if (!function_exists('display_custom_bbcodes'))
	{
		include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
	}
	display_custom_bbcodes();
	
	generate_smilies('inline', 0);	
	
	list_categories();
	
  load_currency(); 
  
  $prefix_exist = load_prefixes();
  
  $location_exist = load_locations();
  
	$template->assign_vars(array(
		'S_BBCODE_ALLOWED'          => true,
		'S_BBCODE_IMG'              => true,
		'S_LINKS_ALLOWED'           => true,
		'S_SMILIES_ALLOWED'         => true,
		'S_BBCODE_QUOTE'            => true,
		'S_SHOW_SMILEY_LINK' 	      => true,
		'S_MAX_IMG_SIZE'            => $config['max_img_size'] / 1024,
		'S_COMMENTS_ENABLED'        => $config['allow_comments'],
		'S_MANDATORY_PHONE'         => $config['mandatory_phone'],
		'S_MANDATORY_AD_PREFIX'		  => $config['mandatory_ad_prefix'],
		'S_MANDATORY_AD_LOCATION'		=> $config['mandatory_ad_location'],
		'S_DEFAULT_CURRENCY'        => $config['default_currency'],
		'S_ALLOW_UPLOAD'            => $config['allow_upload'],
		'U_MORE_SMILIES' 		        => append_sid("{$phpbb_root_path}posting.$phpEx", 'mode=smilies'),
		'T_CL_IMAGES_PATH'          => generate_board_url() . '/' . CL_DIRECTORY . '/images/',
	  'T_THUMB_PATH'              => generate_board_url() . '/' . CL_DIRECTORY . '/images/thumb/',
	));
}

switch($mode)
{
	case 'create_ad':

		// if dont have permissions to create ad
	  if (!($auth->acl_get('u_post_classifieds')))
	  {
      trigger_error('NOT_AUTHORISED');
		}
    // if location necessary
		if ($config['fill_location_to_trade'] && !$user->data['user_from'])
		{
      trigger_error('CL_NOT_FILLED_LOCATION');
		}
		// if not enough posts
		if ($user->data['user_posts'] < $config['required_posts_to_create'])
		{
      trigger_error('CL_NOT_ENOUGH_POSTS_TO_CREATE');  
    }
    
    $temp_ad_id = request_var('temp_ad_id', '');
    
    if ( !empty($temp_ad_id) )
    {
      // Select images
      $sql =  'SELECT image_name
      					FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
      					WHERE temp_id = ' . $temp_ad_id;
      $result = $db->sql_query($sql);
      
      while($row = $db->sql_fetchrow($result))
      {
        $template->assign_block_vars('images', array(
      		'SOURCE'        => generate_board_url() . '/' . CL_DIRECTORY . '/images/' . $row['image_name'],
      		'THUMB_SOURCE'  => generate_board_url() . '/' . CL_DIRECTORY . '/images/thumb/' . $row['image_name'],
      		'FILENAME'      => $row['image_name'],
      		'FILE'          => str_replace(' ', '_', substr($row['image_name'], 0, strrpos($row['image_name'], '.'))),
      	));
      }
      $db->sql_freeresult($result);
    }
    
	  (empty($temp_ad_id)) ? $temp_ad_id = mt_rand(1, 8300000) : $temp_ad_id;

	  $template->assign_vars(array(
			'U_ACTION'                       =>	append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx", 'mode=create_ad'), 
			'S_ALLOW_USERS_SET_EXPIRATION'   => $config['allow_users_set_expiration'],
			'L_CL_DAYS_ACTIVE_EXPLAIN'       => $user->lang('CL_DAYS_ACTIVE_EXPLAIN', ($config['min_expiration_by_user'] != '0') ? $config['min_expiration_by_user'] : '1', ($config['max_expiration_by_user'] != '0') ? $config['max_expiration_by_user'] : 'unlimited'),
		  'TEMP_AD_ID'                     => $temp_ad_id,
    ));
    
		$submit	= (isset($_POST['submit'])) ? true : false;     

		if ($submit)
		{
		  // request variables
			$ad_title        = utf8_normalize_nfc(request_var('ad_title', '', true));
			$short_desc      = utf8_normalize_nfc(request_var('short_desc', '', true));
			$price           = str_replace(',', '.', request_var('ad_price', ''));
			$price_text      = request_var('ad_price_text', 0);
			$cat             = request_var('cat', '', true);
			$prefix_id       = request_var('ad_prefix_id', 0);
			$location_id     = request_var('ad_location_id', 0);
			$notify_comments = request_var('notify_comments', 0);
			$phone           = request_var('phone', '');
			$days_active     = request_var('days_active', '');
			$paypal          = request_var('paypal', '');
			$ad_currency     = request_var('ad_currency', '', true);
			$agree_rules     = request_var('agree_rules', 0);
			$now             = time();
			$ad_description  = utf8_normalize_nfc(request_var('message', '', true));
			$uid = $bitfield = $options = '';
			$allow_bbcode    = $allow_urls = $allow_smilies = true;
			$thumb           = add_http(request_var('thumb', ''));
			if ($config['allow_users_set_expiration'])
			{
        $days = '+' . $days_active . 'days';
			}
			else
			{
        $days = '+' . $config['number_expire'] . 'days';
			}
			$expire = strtotime($days, $now);
			
			// check for errors
			$error = array();
                   
			if (!$ad_title)
			{
	   	 	$error[] = $user->lang['CL_NO_TITLE'];
			}
			if (!$short_desc)
			{
				$error[] = $user->lang['CL_NO_SHORT_DESC'];
			}
			if ($price_text == 0)
			{
			  if ($price == '')
  			{
  	    	$error[] = $user->lang['CL_NO_PRICE'];
  			}
  			if ($price && !is_numeric($price))
  			{
  	    	$error[] = $user->lang['CL_NO_PRICE_NUMERIC'];
  			}
      }
      else
      {
        $price = 0;  
      }
			if ($cat == '')
			{
	    	$error[] = $user->lang['CL_CHOOSE_CATEGORY'];
			}
			if ($config['mandatory_ad_prefix'] && $prefix_id == '' && $prefix_exist)
			{
        $error[] = $user->lang['CL_NO_PREFIX'];
			}
			if ($config['mandatory_ad_location'] && $location_id == '' && $location_exist)
			{
        $error[] = $user->lang['CL_NO_LOCATION'];
			}
			if ($paypal && !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $paypal))
			{
	    	$error[] = $user->lang['CL_BAD_MAIL'];
			}
			if (!$days_active && $config['allow_users_set_expiration'])
			{
	    	$error[] = $user->lang['CL_SET_NUM_DAYS'];
			}
			if ( ( !is_numeric($days_active) || ( $config['max_expiration_by_user'] != '0' && $days_active > $config['max_expiration_by_user'] ) || ( $config['min_expiration_by_user'] != '0' && $days_active < $config['min_expiration_by_user'] ) ) && $config['allow_users_set_expiration'] && $days_active)
			{
	    	$error[] = $user->lang['CL_SET_CORRECT_DAYS'];
			}
			if ($config['mandatory_phone'] && $phone == '')
			{
        $error[] = $user->lang['CL_NO_PHONE'];
			}
			if (!$agree_rules && $rules_must_agree)
			{
	   	 	$error[] = $user->lang['CL_MUST_AGREE'];
			}
			if (!$ad_description)
			{
				$error[] = $user->lang['CL_NO_DESCRIPTION'];
			}
			
			$template->assign_vars(array(
				'ERROR'					  => (sizeof($error)) ? implode('<br />', $error) : '',
				'AD_TITLE'				=> $ad_title,
				'SHORT_DESC'		  => $short_desc,
				'CAT_ID'				  => $cat,
				'AD_PRICE'				=> $price,
				'AD_PRICE_TEXT'   => $price_text,
				'PHONE'					  => $phone,
				'NOTIFY_COMMENTS' => $notify_comments,
				'PAYPAL'				  => $paypal,
				'DAYS_ACTIVE'			=> $days_active,
				'AD_CURRENCY'		  => $ad_currency,     
				'AD_DESCRIPTION'	=> $ad_description,
				'THUMB'					  => $thumb,
				'AD_PREFIX_ID'		=> $prefix_id,
				'AD_LOCATION_ID'	=> $location_id,
			));          
      
      // if no error
			if(!sizeof($error))
			{
			  generate_text_for_storage($ad_description, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			  
				$sql_ary = (array(
					'ad_title'        => $ad_title,
					'short_desc'      => $short_desc,
					'ad_price'        => $price,
					'ad_price_text'   => $price_text,
					'ad_poster_id'    => $user->data['user_id'],
	   			'cat_id'   			 	=> $cat,
	   			'notify_comments'	=> $notify_comments,
	   			'phone'						=> $phone,
	   			'days_active'			=> $days_active,
	   			'paypal'					=> $paypal,
	    		'paypal_currency'	=> $ad_currency,
	   			'edit_time'				=> '',
	   			'last_edit_by'		=> '',
	   			'ad_date'       	=> $now,
	    		'ad_expire'				=> $expire,
					'ad_description'  => $ad_description,
					'bbcode_uid'      => $uid,
	    		'bbcode_bitfield' => $bitfield,
	   			'bbcode_options'  => $options,
	   			'thumb'						=> $thumb,
	   			'ad_prefix_id'		=> $prefix_id,
	   			'ad_location_id'	=> $location_id,               
				));

        // insert ad to db
    		$sql = 'INSERT INTO ' . CLASSIFIEDS_TABLE . $db->sql_build_array('INSERT', $sql_ary);
				$db->sql_query($sql);

        // select ID of inserted ad
				$sql = $db->sql_build_query('SELECT', array(
					'SELECT'	=> ' a.ad_id',
					'FROM'		=> array(
						CLASSIFIEDS_TABLE				=> 'a',
					),
					'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array(USERS_TABLE => 'u'),
							'ON'	=> 'u.user_id = a.ad_poster_id',
						)
					),
					'WHERE'		=> 'u.user_id = ' . $user->data['user_id'],
					'ORDER_BY'	=> 'a.ad_id DESC'
				));
				$result = $db->sql_query_limit($sql, 1);
				$row = $db->sql_fetchrow($result);
				$advertisement_id = $row['ad_id'];

        // change temp ad_id of image to real ad_id
        $sql = 'UPDATE ' . CLASSIFIEDS_IMAGES_TABLE . '
									SET ad_id = ' . $advertisement_id . ', temp_id = 0
									WHERE temp_id = ' . $temp_ad_id;
				$db->sql_query($sql);

        // Send email to ad poster
				if($config['email_ad'])
				{
					$sql =  'SELECT user_id, username, user_lang, user_email, user_jabber, user_notify_type
										FROM  '. USERS_TABLE .'
										WHERE user_id = ' . $user->data['user_id'];
					$result	 = $db->sql_query($sql);
					$row = $db->sql_fetchrow( $result );
					$db->sql_freeresult($result);

					// Send e-mail to Ad poster
					if($config['email_ad'])
					{
		        send_mail_to_ad_poster($row['user_lang'], $row['user_email'], $row['username'], $row['user_jabber'], $row['user_notify_type'], $ad_title, $expire);
					}
				}

				// Select users who are watching ads from this category
				$sql =  'SELECT u.user_id, u.username, u.user_lang, u.user_email, u.user_jabber, u.user_notify_type, cw.cat_id
									FROM  '. USERS_TABLE . ' u
										INNER JOIN  '. CLASSIFIEDS_CAT_WATCH_TABLE . ' cw
										ON u.user_id = cw.user_id
										WHERE cw.cat_id = ' . $cat;
				$result	 = $db->sql_query($sql);
				
				// send email to subscribers
				$ad_link = generate_board_url() . "/" . CL_DIRECTORY . "/single_ad.$phpEx?ad_id=.$advertisement_id";
				
				if ($price_text == 1)
				{
          $price = $user->lang['CL_BY_AGREEMENT']; 
        }
        else
        {
          $price = str_replace('.00', '', $price);
        }

	    	while($row = $db->sql_fetchrow($result))
				{
	        send_mail_to_subscribers($row['user_lang'], $row['user_email'], $row['username'], $row['user_jabber'], $row['user_notify_type'], $ad_title, $price, $advertisement_id, $short_desc, $ad_link);
				}
				$db->sql_freeresult($result);

        redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", 'ad_id='.$advertisement_id) );
			}             
		}                  
	break;

	case 'edit_ad':

	  // Request edited ad id
	  $ad_id = request_var('ad_id', 0);

    // if wrong ad_id
	  if (!$ad_id)
	  {
      trigger_error('CL_NO_AD_SELECTED');
		}

	  // check if user is ad poster
	  $sql_ary = array(
			'SELECT'    => 'a.ad_poster_id',
    	'FROM'      => array(
       	CLASSIFIEDS_TABLE   		=> 'a',
    	),
   		'WHERE'     => 'a.ad_id = ' . $ad_id,
		);
		$sql = $db->sql_build_query('SELECT', $sql_ary);
		$result	 = $db->sql_query($sql);
		$row = $db->sql_fetchrow( $result );
		$db->sql_freeresult($result);

		// if not is ad poster and dont have permissions to edit ads
   	if ( !( $auth->acl_get('u_edit_own_classifieds') && $user->data['user_id'] == $row['ad_poster_id'] ) && !$auth->acl_get('m_edit_classifieds') && !$auth->acl_get('a_'))
	  {
      trigger_error('NOT_AUTHORISED');
		}
            
		// select ad data
	  $sql_ary = array(
			'SELECT'    => 'c.name, a.*',
    		'FROM'      => array(
       		CLASSIFIEDS_TABLE   		=> 'a',
        	CLASSIFIEDS_CATEGORY_TABLE 	=> 'c',
    		),
   			'WHERE'     => 'a.ad_id = ' . $ad_id . ' and a.cat_id = c.id',
		);
		$sql = $db->sql_build_query('SELECT', $sql_ary);
		$result	 = $db->sql_query($sql);
		$row = $db->sql_fetchrow( $result );
		$db->sql_freeresult($result);

  	$desc = decode_message($row['ad_description'], $row['bbcode_uid']);
  	
		$template->assign_vars(array(
			'AD_TITLE'                   =>	$row['ad_title'],
			'AD_PRICE'                   =>	$row['ad_price'],
			'AD_PRICE_TEXT'              => $row['ad_price_text'],
			'SHORT_DESC'                 => $row['short_desc'],
			'CAT_ID'                     => $row['cat_id'],
			'CATEGORY'                   =>	$row['name'],
			'NOTIFY_COMMENTS'            =>	$row['notify_comments'],
			'PHONE'                      => $row['phone'],
			'PAYPAL'                     => $row['paypal'],
			'AD_CURRENCY'                => $row['paypal_currency'],
   		'U_ACTION'                   =>	append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx", "mode=edit_ad&amp;ad_id=$ad_id"),
			'AD_DESCRIPTION'             => $row['ad_description'],
			'THUMB'                      => $row['thumb'],
			'AD_ID'                      =>	$ad_id,
			'AD_PREFIX_ID'			         => $row['ad_prefix_id'],
			'AD_LOCATION_ID'			       => $row['ad_location_id'],
		));
		
		// Select images
    $sql =  'SELECT image_name
    					FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
    					WHERE ad_id = ' . $ad_id;
    $result = $db->sql_query($sql);
    
    while($row = $db->sql_fetchrow($result))
    {
      $template->assign_block_vars('images', array(
    		'SOURCE'        => generate_board_url() . '/' . CL_DIRECTORY . '/images/' . $row['image_name'],
    		'THUMB_SOURCE'  => generate_board_url() . '/' . CL_DIRECTORY . '/images/thumb/' . $row['image_name'],
    		'FILENAME'      => $row['image_name'],
    		'FILE'          => str_replace(' ', '_', substr($row['image_name'], 0, strrpos($row['image_name'], '.'))),
    	));
    }
    $db->sql_freeresult($result);
		
		$submit	= (isset($_POST['submit'])) ? true : false;             

		if ($submit)
		{
		  // request variables
			$ad_title        = utf8_normalize_nfc(request_var('ad_title', '', true));
			$short_desc      = utf8_normalize_nfc(request_var('short_desc', '', true));
			$price           = str_replace(',', '.', request_var('ad_price', ''));
			$price_text      = request_var('ad_price_text', 0);
			$cat             = request_var('cat', '', true);
			$prefix_id       = request_var('ad_prefix_id', 0);
			$location_id     = request_var('ad_location_id', 0);
			$notify_comments = request_var('notify_comments', 0);
			$phone           = request_var('phone', '');
			$paypal          = request_var('paypal', '');
			$ad_currency     = request_var('ad_currency', '', true);
			$agree_rules     = request_var('agree_rules', 0);
			$now             = time();
			$ad_description  = utf8_normalize_nfc(request_var('message', '', true));
  		$uid = $bitfield = $options = '';
      $allow_bbcode    = $allow_urls = $allow_smilies = true;
      $thumb           = add_http(request_var('thumb', ''));
			
			// check for errors
			$error = array();

			if (!$ad_title)
			{
	   	 	$error[] = $user->lang['CL_NO_TITLE'];
			}
			if (!$short_desc)
			{
				$error[] = $user->lang['CL_NO_SHORT_DESC'];
			}
			if ($price_text == 0)
			{
			  if ($price == '')
  			{
  	    	$error[] = $user->lang['CL_NO_PRICE'];
  			}
  			if ($price && !is_numeric($price))
  			{
  	    	$error[] = $user->lang['CL_NO_PRICE_NUMERIC'];
  			}
      }
      else
      {
        $price = 0;  
      }
			if ($cat == '')
			{
	    	$error[] = $user->lang['CL_CHOOSE_CATEGORY'];
			}
			if ($config['mandatory_ad_prefix'] && $prefix_id == '' && $prefix_exist)
			{
        $error[] = $user->lang['CL_NO_PREFIX'];
			}
			if ($config['mandatory_ad_location'] && $location_id == '' && $location_exist)
			{
        $error[] = $user->lang['CL_NO_LOCATION'];
			}
			if ($paypal && !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $paypal))
			{
	    	$error[] = $user->lang['CL_BAD_MAIL'];
			}
			if ($config['mandatory_phone'] && $phone == '')
			{
        $error[] = $user->lang['CL_NO_PHONE'];
			}
			if (!$agree_rules && $rules_must_agree)
			{
	   	 	$error[] = $user->lang['CL_MUST_AGREE'];
			}
			if (!$ad_description)
			{
				$error[] = $user->lang['CL_NO_DESCRIPTION'];
			}

      // if no errors
			if(!sizeof($error))
			{
     		generate_text_for_storage($ad_description, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

				$sql_ary = (array(            
					'ad_title'        => $ad_title,
					'short_desc'      => $short_desc,
					'ad_price'        => $price,
					'ad_price_text'   => $price_text,
 					'cat_id'   			 	=> $cat,
 					'notify_comments'	=> $notify_comments,
 					'phone'						=> $phone,
 					'paypal'					=> $paypal,
 					'paypal_currency'	=> $ad_currency,
 					'edit_time'				=> $now,
 					'last_edit_by'		=> $user->data['username'],
					'ad_description'  => $ad_description,
					'bbcode_uid'      => $uid,
  				'bbcode_bitfield' => $bitfield,
 					'bbcode_options'  => $options,
 					'thumb'						=> $thumb,
 					'ad_prefix_id'    => $prefix_id,
 					'ad_location_id'  => $location_id,
				));

        // update ad in db
    		$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . '
									SET ' . $db->sql_build_array('UPDATE', $sql_ary). '
									WHERE ad_id = ' . $ad_id;
				$db->sql_query($sql);
				
				redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", 'ad_id='.$ad_id) );
			}
			
			$template->assign_vars(array(
				'ERROR'					  => (sizeof($error)) ? implode('<br />', $error) : '',
				'AD_TITLE'				=> $ad_title,
				'SHORT_DESC'		  => $short_desc,
				'CAT_ID'				  => $cat,
				'AD_PRICE'				=> $price,
				'AD_PRICE_TEXT'	  => $price_text,
				'PHONE'					  => $phone,
				'PAYPAL'				  => $paypal,
				'AD_CURRENCY'		  => $ad_currency,
				'NOTIFY_COMMENTS'	=> $notify_comments,
				'AD_DESCRIPTION'	=> $ad_description,
				'THUMB'					  => $thumb,
				'AD_PREFIX_ID'	  => $prefix_id,
				'AD_LOCATION_ID'	=> $location_id,
      ));
			
		}
	break;

	case 'move_ad':

	  // if dont have permissions to move ads
   	if ( !$auth->acl_get('a_') && !$auth->acl_get('m_move_classifieds') )
	  {
      trigger_error('NOT_AUTHORISED');
		}

	  // Request moved ad id
	  $ad_id = request_var('ad_id', 0);

	  if (!$ad_id)
	  {
      trigger_error('CL_NO_AD_SELECTED');
		}

		// list categories
	  list_categories();

		// select ad data
	  $sql_ary = array(
			'SELECT'    => 'c.id, c.name, a.cat_id',
    		'FROM'      => array(
       		CLASSIFIEDS_TABLE   		=> 'a',
        	CLASSIFIEDS_CATEGORY_TABLE 	=> 'c',
    		),
   			'WHERE'     => 'a.ad_id = "' . $ad_id . '" AND a.cat_id = c.id',
		);
		$sql = $db->sql_build_query('SELECT', $sql_ary);
		$result	 = $db->sql_query($sql);
		$row = $db->sql_fetchrow( $result );
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'U_ACTION'	=> append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx", "mode=move_ad&amp;ad_id=$ad_id"),
			'CAT_ID'		=> $row['cat_id'],
			'CATEGORY'	=> $row['name'],
		));

		$submit	= (isset($_POST['submit'])) ? true : false;

		if ($submit)
		{
		  // request variables
    	$cat = request_var('cat', '', true);

			$error = array();

			if ($cat == '')
			{
	    	$error[] = $user->lang['CL_CHOOSE_CATEGORY'];
			}

			if(!sizeof($error))
			{
			  if (confirm_box(true))
				{
					$sql_ary = (array(
   					'cat_id'	=> $cat,
					));

					$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . '
									SET ' . $db->sql_build_array('UPDATE', $sql_ary). '
									WHERE ad_id = ' . $ad_id;
					$db->sql_query($sql);
        }
				else
				{
					$s_hidden_fields = build_hidden_fields(array(
					  'submit'	=> true,
     				'cat'   	=> $cat,
					));

					confirm_box(false, $user->lang['CL_EDIT_CONFIRM'], $s_hidden_fields);
				}

				redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", 'ad_id='.$ad_id) );
			}

			$template->assign_vars(array(
				'ERROR'					=> (sizeof($error)) ? implode('<br />', $error) : '',
				'CAT_ID'				=> $cat,
			));
		}

	break;

	case 'extend_ad':

	  if(!($auth->acl_get('a_') || $auth->acl_get('m_extend_classifieds') || $auth->acl_get('u_can_extend_classifieds')))
	  {
      trigger_error('NOT_AUTHORISED');
		}

		// Request moved ad id
	  $ad_id = request_var('ad_id', 0);

	  if (!$ad_id)
	  {
      trigger_error('CL_NO_AD_SELECTED');
		}
		
		if ( $config['allow_users_set_expiration'] )
		{
	  	$template->assign_vars(array(
				'U_ACTION'	                    => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx", "mode=extend_ad&amp;ad_id=$ad_id"),
	   		'S_ALLOW_USERS_SET_EXPIRATION'  => $config['allow_users_set_expiration'],
	   		'L_CL_DAYS_ACTIVE_EXPLAIN'         => $user->lang('CL_DAYS_ACTIVE_EXPLAIN', $config['min_expiration_by_user'], $config['max_expiration_by_user']),
			));

			$submit	= (isset($_POST['submit'])) ? true : false;

			if ($submit)
			{
				$days_active = request_var('days_active', '');

				$error = array();

				if (!$days_active && $config['allow_users_set_expiration'])
				{
		    	$error[] = $user->lang['CL_SET_NUM_DAYS'];
				}
				if ( ( !is_numeric($days_active) || ( $config['max_expiration_by_user'] != '0' && $days_active > $config['max_expiration_by_user'] ) || ( $config['min_expiration_by_user'] != '0' && $days_active < $config['min_expiration_by_user'] ) ) && $config['allow_users_set_expiration'] && $days_active)
				{
		    	$error[] = $user->lang['CL_SET_CORRECT_DAYS'];
				}

				$template->assign_vars(array(
					'ERROR'          => (sizeof($error)) ? implode('<br />', $error) : '',
	    		'DAYS_ACTIVE'    => $days_active,
				));

				if(!sizeof($error))
				{
				  if (confirm_box(true))
					{
						$now = time();
		        $days = '+' . $days_active . 'days';
						$new_date = strtotime($days, $now);

						$sql_ary = (array(
							'ad_expire'			=>	$new_date,
							'ad_date'				=>	$now,
							'expire_email'	=>	'',
						));

			   		$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . '
											SET ' . $db->sql_build_array('UPDATE', $sql_ary). '
											WHERE ad_id = ' . $ad_id;
						$db->sql_query($sql);
					}
					else
					{
	      		$s_hidden_fields = build_hidden_fields(array(
						  'submit'	=> true,
	     				'days_active'   	=> $days_active,
						));

						confirm_box(false, $user->lang['CL_EDIT_CONFIRM'], $s_hidden_fields);
					}

	  			redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", 'ad_id='.$ad_id) );
				}
			}
  	}
		else
		{

		  if (confirm_box(true))
			{
			  $now = time();
			  $add_days = '+' . $config['number_expire'] . 'days';
				$new_date = strtotime($add_days, $now);

				$sql_ary = (array(
					'ad_expire'			=>	$new_date,
					'ad_date'				=>	$now,
					'expire_email'	=>	'',
				));

	   		$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . '
									SET ' . $db->sql_build_array('UPDATE', $sql_ary). '
									WHERE ad_id = ' . $ad_id;
				$db->sql_query($sql);
			}
			else
			{
				confirm_box(false, $user->lang['CL_EXTEND_AD_CONFIRM']);
			}

   		redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", 'ad_id='.$ad_id) );
    }
	break;

	case 'report_ad':

    if (!($auth->acl_get('u_can_report_classifieds')))
	  {
      trigger_error('NOT_AUTHORISED');
		}

		// Request moved ad id
	  $ad_id = request_var('ad_id', 0);

	  if (!$ad_id)
	  {
      trigger_error('CL_NO_AD_SELECTED');
		}

		$sql = 'SELECT ad_id, reported
							FROM  ' . CLASSIFIEDS_TABLE . '
							WHERE ad_id = ' . $ad_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow( $result );
		$db->sql_freeresult($result);

		if ( $row['reported'] == '1')
		{
      trigger_error('CL_JUST_REPORTED');
		}

		$template->assign_vars(array(
      'U_ACTION'    => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/manage_ad.$phpEx", "mode=report_ad&amp;ad_id=$ad_id"),
		));

		$submit	= (isset($_POST['submit'])) ? true : false;
		
		$error = array();

 		if ($submit)
		{
			$report_text = request_var('report_text', '', true);

			if (!$report_text)
			{
	    	$error[] = $user->lang['CL_NO_REPORT_TEXT'];
			}

			if (strlen($report_text) > 1024)
			{
	    	$error[] = $user->lang['CL_TOO_LONG'];
			}

			$template->assign_vars(array(
				'ERROR'				=> (sizeof($error)) ? implode('<br />', $error) : '',
				'REPORT_TEXT' => $report_text,
			));

			if (!sizeof($error))
			{
				if (confirm_box(true))
				{
					$sql_ary = (array(
					  'reported'   		=> '1',
   					'report_text'   => $report_text,
   					'reported_by'   => $user->data['user_id'],
					));

					$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . '
										SET ' . $db->sql_build_array('UPDATE', $sql_ary). '
										WHERE ad_id = ' . $ad_id;
					$db->sql_query($sql);
				}
				else
				{
					$s_hidden_fields = build_hidden_fields(array(
					  'submit'			=> true,
        		'report_text' => $report_text,
					));

					confirm_box(false, $user->lang['CL_REPORT_CONFIRM'], $s_hidden_fields);
				}

				redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", 'ad_id='.$ad_id) );
			}
		}

	break;

	case 'close_report':

    if (!$auth->acl_get('a_') && !$auth->acl_get('m_report_classifieds'))
	  {
      trigger_error('NOT_AUTHORISED');
		}

		// Request moved ad id
	  $ad_id = request_var('ad_id', 0);

	  if (!$ad_id)
	  {
      trigger_error('CL_NO_AD_SELECTED');
		}

		$sql = 'SELECT ad_id, reported
							FROM  ' . CLASSIFIEDS_TABLE . '
							WHERE ad_id = ' . $ad_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow( $result );
		$db->sql_freeresult($result);

		if ( $row['reported'] == '0')
		{
      trigger_error('CL_NOT_REPORTED');
		}

		if (confirm_box(true))
		{
   		$sql_ary = (array(
			  'reported'       => '0',
				'report_text'    => '',
				'reported_by'    => '0',
			));

			$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . '
								SET ' . $db->sql_build_array('UPDATE', $sql_ary). '
								WHERE ad_id = ' . $ad_id;
			$db->sql_query($sql);
		}
		else
		{
			confirm_box(false, $user->lang['CL_CLOSE_REPORT_CONFIRM']);
		}

	  redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", 'ad_id=' . $ad_id) );

	break;

	case 'delete_ad':

	  $ad_id = request_var('ad_id', 0);

	  if (!$ad_id)
	  {
      trigger_error('CL_NO_AD_SELECTED');
		}

		// check if user is ad poster
	  $sql_ary = array(
			'SELECT'    => 'a.ad_poster_id',
    		'FROM'      => array(
        	USERS_TABLE        			=> 'u',
       		CLASSIFIEDS_TABLE   		=> 'a',
    		),
   			'WHERE'     => 'u.user_id = a.ad_poster_id and ad_id = ' . $ad_id,
		);
		$sql = $db->sql_build_query('SELECT', $sql_ary);
		$result	 = $db->sql_query($sql);
		$row = $db->sql_fetchrow( $result );
		$db->sql_freeresult($result);

		if (!($user->data['user_id'] == $row['ad_poster_id'] && $auth->acl_get('u_can_delete_classifieds')) && !$auth->acl_get('a_') && !$auth->acl_get('m_delete_classifieds'))
	  {
      trigger_error('NOT_AUTHORISED');
		}

		if (confirm_box(true))
		{
			$sql = 'DELETE FROM ' . CLASSIFIEDS_TABLE . '
								WHERE ad_id= ' . $ad_id;
			$db->sql_query($sql);

			// select images from deleted ad
      $sql =  'SELECT *
								FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
								WHERE ad_id = ' . $ad_id;
   		$result = $db->sql_query($sql);
   		
			while($row = $db->sql_fetchrow( $result ))
			{
		    // remove images from FTP and DB
				@unlink('images/' . $row['image_name']);

				$sql =  'DELETE FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
									WHERE ad_id = ' . $ad_id . '
										AND image_name = "' . $row['image_name'] . '"';
				$result2 = $db->sql_query($sql);
			}
		}
		else
		{
			confirm_box(false, $user->lang['CL_DELETE_CONFIRM']);
		}

		redirect( append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx") );

	break;      
}

// display navigation links
$template->assign_block_vars('navlinks', array(
  'FORUM_NAME'    => $user->lang['CL_CLASSIFIEDS'],
  'U_VIEW_FORUM'  => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx"),
));

$template->assign_vars(array(
  'S_IN_CLASSIFIEDS_MANAGE_AD'  => true,
  'S_MODE'    => $mode,
));

page_header($user->lang('CL_CLASSIFIED_MANAGE_AD'));

$template->set_filenames(array(
  'body' => 'classifieds_manage_body.html',
));

page_footer();

?>