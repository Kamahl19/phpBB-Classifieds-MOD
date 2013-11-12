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
if (!defined('IN_PHPBB'))
{
	exit;
}
class acp_classifieds
{
  var $u_action;
  var $new_config;

	function main($id, $mode)
  {
    global $db, $user, $auth, $template, $cache;
    global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

    include($phpbb_root_path . CL_DIRECTORY . '/includes/functions_buysell.' . $phpEx);
    include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
	  include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

    switch($mode)
    {
      case 'index':
        $this->page_title = 'ACP_CLASSIFIEDS';
        $this->tpl_name = 'acp_classifieds';
        
        $submit	= (isset($_POST['submit'])) ? true : false;

        if ($submit)
				{
					set_config('enable_classifieds', request_var('enable_classifieds', 0));
					set_config('disable_message', utf8_normalize_nfc(request_var('disable_message', '', true)));
					set_config('number_expire', request_var('number_expire', 0));
					set_config('allow_users_set_expiration', request_var('allow_users_set_expiration', 0));
					set_config('min_expiration_by_user', request_var('min_expiration_by_user', 0));
					set_config('max_expiration_by_user', request_var('max_expiration_by_user', 0));
					set_config('allow_comments', request_var('allow_comments', 0));
					set_config('enable_watch_cat', request_var('enable_watch_cat', 0));
					set_config('allow_classifieds_feeds', request_var('allow_classifieds_feeds', 0));
					set_config('number_ad_feeds', request_var('number_ad_feeds', 0));
					set_config('allow_upload', request_var('allow_upload', 0));
					set_config('max_img_size', request_var('max_img_size', 0) * 1024);
					set_config('required_posts_to_create', request_var('required_posts_to_create', 0));
					set_config('required_posts_to_view', request_var('required_posts_to_view', 0));
	        set_config('mandatory_phone', request_var('mandatory_phone', 0));
					set_config('sold_color', request_var('sold_color', ''));
					set_config('closed_color', request_var('closed_color', ''));
					set_config('number_ads', request_var('number_ads', 0));
					set_config('allow_addthis_button', request_var('allow_addthis_button', ''));
					set_config('email_ad', request_var('email_ad', 0));
					set_config('email_expire', request_var('email_expire', 0));
					set_config('max_images_per_ad', request_var('max_images_per_ad', 0));

     			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}

				$template->assign_vars(array(
					'U_ACTION'                     => $this->u_action,
					'ENABLE_CLASSIFIEDS'           => $config['enable_classifieds'],
					'DISABLE_MESSAGE'              => $config['disable_message'],
					'ALLOW_ADDTHIS_BUTTON'         => $config['allow_addthis_button'],
					'MANDATORY_PHONE'              => $config['mandatory_phone'],
					'NUMBER_ADS'                   => $config['number_ads'],
					'NUMBER_EXPIRE'                => $config['number_expire'],
					'ALLOW_USERS_SET_EXPIRATION'   => $config['allow_users_set_expiration'],
					'MIN_EXPIRATION_BY_USER'       => $config['min_expiration_by_user'],
					'MAX_EXPIRATION_BY_USER'       => $config['max_expiration_by_user'],
					'EMAIL_AD'                     => $config['email_ad'],
					'EMAIL_EXPIRE'                 => $config['email_expire'],
					'CLOSED_COLOR'                 => $config['closed_color'],
					'SOLD_COLOR'                   => $config['sold_color'],
					'ALLOW_COMMENTS'               => $config['allow_comments'],
					'ENABLE_WATCH_CAT'             => $config['enable_watch_cat'],
					'ALLOW_CLASSIFIEDS_FEEDS'      => $config['allow_classifieds_feeds'],
					'NUMBER_AD_FEEDS'              => $config['number_ad_feeds'],
					'ALLOW_UPLOAD'                 => $config['allow_upload'],
					'MAX_IMG_SIZE'                 => $config['max_img_size'] / 1024,
					'REQUIRED_POSTS_TO_CREATE'     => $config['required_posts_to_create'],
					'REQUIRED_POSTS_TO_VIEW'       => $config['required_posts_to_view'],
					'MAX_IMAGES_PER_AD'       		 => $config['max_images_per_ad'],
				));

      break;

      case 'blocks':
        $this->page_title = 'ACP_CLASSIFIEDS_BLOCKS_TITLE';
        $this->tpl_name = 'acp_classifieds_blocks';
             
        $submit	= (isset($_POST['submit'])) ? true : false;
        
      	if ($submit)
				{
					set_config('display_ads_on_index', request_var('display_ads_on_index', 0));
					set_config('recent_ads_place', request_var('recent_ads_place', 0));
					set_config('ad_num_display_on_index', request_var('ad_num_display_on_index', 0));
					set_config('display_rand_ads_on_index', request_var('display_rand_ads_on_index', 0));
					set_config('rand_ads_place', request_var('rand_ads_place', 0));
					set_config('rand_ad_num_display_on_index', request_var('rand_ad_num_display_on_index', 0));
          set_config('display_rand_miniblock', request_var('display_rand_miniblock', 0));
					set_config('rand_miniblock_place', request_var('rand_miniblock_place', 0));
					set_config('rand_miniblock_num_ads', request_var('rand_miniblock_num_ads', 0));
					set_config('display_advertisers_ads', request_var('display_advertisers_ads', 0));
					set_config('advertisers_block_place', request_var('advertisers_block_place', 0));
					set_config('advertisers_ads_num', request_var('advertisers_ads_num', 0));
					set_config('display_hot_ads', request_var('display_hot_ads', 0));
					set_config('hot_block_place', request_var('hot_block_place', 0));
					set_config('hot_ads_num', request_var('hot_ads_num', 0));
					set_config('display_profile_last_ads', request_var('display_profile_last_ads', 0));
					set_config('profile_num_last_ads', request_var('profile_num_last_ads', 0));

					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}

				$template->assign_vars(array(
					'U_ACTION'							       => $this->u_action,
					'DISPLAY_ADS_ON_INDEX'	       => $config['display_ads_on_index'],
					'RECENT_ADS_PLACE'			       => $config['recent_ads_place'],
					'AD_NUM_DISPLAY_ON_INDEX'			 => $config['ad_num_display_on_index'],
					'DISPLAY_RAND_ADS_ON_INDEX'		 => $config['display_rand_ads_on_index'],
					'RAND_ADS_PLACE'				       => $config['rand_ads_place'],
					'RAND_AD_NUM_DISPLAY_ON_INDEX' => $config['rand_ad_num_display_on_index'],
					'DISPLAY_RAND_MINIBLOCK'			 => $config['display_rand_miniblock'],
					'RAND_MINIBLOCK_PLACE'				 => $config['rand_miniblock_place'],
					'RAND_MINIBLOCK_NUM_ADS'			 => $config['rand_miniblock_num_ads'],
					'DISPLAY_ADVERTISERS_ADS'			 => $config['display_advertisers_ads'],
					'ADVERTISERS_BLOCK_PLACE'			 => $config['advertisers_block_place'],
					'ADVERTISERS_ADS_NUM'          => $config['advertisers_ads_num'],
					'DISPLAY_HOT_ADS'			         => $config['display_hot_ads'],
					'HOT_BLOCK_PLACE'              => $config['hot_block_place'],
					'HOT_ADS_NUM'                  => $config['hot_ads_num'],
					'DISPLAY_PROFILE_LAST_ADS'     => $config['display_profile_last_ads'],
					'PROFILE_LAST_ADS_NUM'         => $config['profile_num_last_ads'],
				));
      break;


      case 'manage':
        $this->page_title = 'ACP_CLASSIFIEDS_MANAGE_TITLE';
        $this->tpl_name = 'acp_classifieds_manage';

        $action	= request_var('action', '');
        $status	= request_var('status', '');
        $id	= request_var('id', 0);
        $ad_id = request_var('ad_id', 0);
        $profile_user = request_var('u', 0);
        $add_days = request_var('add_days', '');
        
        $limit =	20;
				$start = request_var('start', 0);

				switch ($action)
				{
					case "delete":
						if (confirm_box(true))
						{
       				$sql = 'DELETE FROM ' . CLASSIFIEDS_TABLE . '
							 				  WHERE ad_id = ' . $ad_id;
							$db->sql_query($sql);

							// select images from deleted ad
							$sql =  'SELECT *
												FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
												  WHERE ad_id = ' . $ad_id;
				   		$result = $db->sql_query($sql);

							while($row = $db->sql_fetchrow( $result ))
							{
								// remove images from FTP and DB
								@unlink("{$phpbb_root_path}".CL_DIRECTORY."/images/".$row['image_name']);

								$sql =  'DELETE FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
													WHERE ad_id = ' . $ad_id . '
														AND image_name = "' . $row['image_name'] . '"';
								$result2 = $db->sql_query($sql);
							}

							redirect(append_sid("{$this->u_action}", "status=active"));
						}
						else
						{
							confirm_box(false, $user->lang['DELETE_CONFIRM']);

							redirect(append_sid("{$this->u_action}", "status=active"));
						}
					break;

					case "add_days":
					  if (confirm_box(true))
						{
							$days = '+'.$add_days.'days';
							$now = time();
							$expire = strtotime($days, $now);
							
							$sql_ary = (array(
								'ad_expire'			=> $expire,
								'ad_date'				=>	$now,
								'expire_email'	=> 0,
							));

							$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . '
												SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
												  WHERE ad_id = ' . $ad_id;
							$db->sql_query($sql);

       				redirect(append_sid("{$this->u_action}", "status=expired"));
            }
						else {
       				confirm_box(false, $user->lang['EXTEND_CONFIRM'], build_hidden_fields(array(
        				'add_days'			=> $add_days,
							)));

       				redirect(append_sid("{$this->u_action}", "status=expired"));
						}
					break;
				}

				if ($status != '')
				{
          $pagination_url = ($status == 'viewuser') ? $this->u_action."&amp;status={$status}&amp;u=$profile_user" : $this->u_action."&amp;status={$status}";
				}
				else
				{
          $pagination_url = $this->u_action;
				}

				$template->assign_vars(array(
					'STATUS'        => $status,
					'VIEW_ALL_ACTIVE'        => append_sid("{$this->u_action}", "status=active"),
					'VIEW_ALL_CLOSED'        => append_sid("{$this->u_action}", "status=closed"),
					'VIEW_ALL_SOLD'        => append_sid("{$this->u_action}", "status=sold"),
					'VIEW_ALL_EXPIRED'        => append_sid("{$this->u_action}", "status=expired"),
				));

				if (!empty($status))
				{
				  $ad_ary = array(
					  'SELECT'    => 'a.* , u.user_id, u.username, u.user_colour',
					  'FROM'      => array(
					    CLASSIFIEDS_TABLE   => 'a',
					  ),
					  'LEFT_JOIN'	=> array(
        			array(
        				'FROM'	=> array(USERS_TABLE => 'u'),
        				'ON'	=> 'u.user_id = a.ad_poster_id',
        			)
        		),
					  'WHERE'     => 'a.ad_status = ' . ACTIVE . ' AND a.ad_expire > '. time(),
					  'ORDER_BY'	=> 'a.ad_date DESC'
					);

				  switch ($status)
					{
						case "active":
							$ad_ary['WHERE'] = 'a.ad_status = ' . ACTIVE . ' AND a.ad_expire > '. time();
						break;

						case "closed":
						  $ad_ary['WHERE'] = 'a.ad_status = ' . CLOSED . ' AND a.ad_expire > '. time();
						break;

						case "sold":
						  $ad_ary['WHERE'] = 'a.ad_status = ' . SOLD . ' AND a.ad_expire > '. time();
						break;

						case "expired":
						  $ad_ary['WHERE'] = 'a.ad_expire <'.time();
						break;

						case "viewuser":
						  $ad_ary['WHERE'] = 'a.ad_poster_id = "' . $profile_user . '"';
						break;
					}

					$sql = $db->sql_build_query('SELECT', $ad_ary);
					$result	 = $db->sql_query_limit($sql, $limit, $start);
					
					$username = '';
					
					while($row = $db->sql_fetchrow( $result ))
					{
						$template->assign_block_vars('ad',array(
							'AD_ID'				    => $row['ad_id'],
							'AD_TITLE'			  => $row['ad_title'],
							'AD_LINK'			    => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/single_ad.$phpEx", "ad_id={$row['ad_id']}"),
							'AD_DATE'			    => $user->format_date($row['ad_date']),
	      			'AD_CATEGORY'		  => get_ad_category($row['cat_id']),
							'AD_POSTER'			  => $row['username'],
							'AD_POSTER_COLOR'	=> $row['user_colour'],
							'AD_STATUS'			  => $row['ad_status'],
							'DELETE_LINK'		  => $this->u_action . '&amp;action=delete&amp;ad_id= ' . $row['ad_id'],
							'AD_EXPIRE'			  => $user->format_date($row['ad_expire']),
							'EXPIRE'			    => $row['ad_expire'],
							'EDIT_EXPIRE'		  => $this->u_action . '&amp;action=add_days&amp;ad_id=' . $row['ad_id'],
						));
						
						$username = $row['username'];
					}

					$ad_ary['SELECT'] = 'COUNT(a.ad_id) as total_ads';
					$sql = $db->sql_build_query('SELECT', $ad_ary);
					$result = $db->sql_query($sql);
					$total_ads = $db->sql_fetchfield('total_ads');

					$db->sql_freeresult($result);

					$template->assign_vars(array(
					  'USERS_ADS'        => $username . '\'s ' . $user->lang['ADS'],
					  'PAGINATION'       => generate_pagination($pagination_url, $total_ads, $limit, $start),
				    'PAGE_NUMBER'      => on_page($total_ads, $limit, $start),
				    'TOTAL_ADS'        => sprintf($user->lang['TOTAL_ADS'], $total_ads),
					));
        }

      break;

      case 'purge':
        $this->page_title = 'ACP_CLASSIFIEDS_PURGE_TITLE';
        $this->tpl_name = 'acp_classifieds_purge';

        if ((int) $user->data['user_type'] !== USER_FOUNDER)
				{
					trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

        $action	= request_var('action', '');

		    switch ($action)
				{
				  case 'purge_expired_sold_closed':

				  	if (!confirm_box(true) )
						{
							$confirm = true;
							$confirm_lang = 'PURGE_EXPIRED_SOLD_CLOSED_ADS_CONFIRM';

							if ($confirm)
							{
								confirm_box(false, $user->lang[$confirm_lang], build_hidden_fields(array(
									'i'			=> $id,
									'mode'		=> $mode,
									'action'	=> $action,
								)));
							}
						}
						else
						{
						  // select ad_id of all ads, which will be purged
						  $sql =  "SELECT ad_id
												FROM " . CLASSIFIEDS_TABLE . "
												WHERE ad_expire < " . time() . "
													AND ad_status != 0";
							$result = $db->sql_query($sql);

							while($row = $db->sql_fetchrow( $result ))
							{
							  // select images from deleted ad
								$sql =  'SELECT ad_id, image_name
													FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
													WHERE ad_id = ' . $row['ad_id'];
					   		$result2 = $db->sql_query($sql);

								while($row2 = $db->sql_fetchrow( $result2 ))
								{
										// remove images from FTP and DB
										@unlink("{$phpbb_root_path}".CL_DIRECTORY."/images/".$row2['image_name']);

										$sql =  'DELETE FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
															WHERE ad_id = ' . $row2['ad_id'] . '
																AND image_name = "' . $row2['image_name'] . '"';
										$result3 = $db->sql_query($sql);
								}
              }

							$sql =  "DELETE FROM " . CLASSIFIEDS_TABLE . "
												WHERE ad_expire < " . time() . "
													AND ad_status != 0";
							$result = $db->sql_query($sql);

							trigger_error($user->lang['PURGED_SUCCESFULLY'] . adm_back_link($this->u_action));
						}
					break;

     			case 'purge_closed':

     				if (!confirm_box(true) )
						{
							$confirm = true;
							$confirm_lang = 'PURGE_CLOSED_ADS_CONFIRM';

							if ($confirm)
							{
								confirm_box(false, $user->lang[$confirm_lang], build_hidden_fields(array(
									'i'			=> $id,
									'mode'		=> $mode,
									'action'	=> $action,
								)));
							}
						}
						else
						{
						  // select ad_id of all ads, which will be purged
						  $sql =  "SELECT ad_id
												FROM " . CLASSIFIEDS_TABLE . "
            						WHERE ad_status = 2";
							$result = $db->sql_query($sql);

							while($row = $db->sql_fetchrow( $result ))
							{
							  // select images from deleted ad
								$sql =  'SELECT ad_id, image_name
													FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
													WHERE ad_id = ' . $row['ad_id'];
					   		$result2 = $db->sql_query($sql);

								while($row2 = $db->sql_fetchrow( $result2 ))
								{
										// remove images from FTP and DB
										@unlink("{$phpbb_root_path}".CL_DIRECTORY."/images/".$row2['image_name']);

										$sql =  'DELETE FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
															WHERE ad_id = ' . $row2['ad_id'] . '
																AND image_name = "' . $row2['image_name'] . '"';
										$result3 = $db->sql_query($sql);
								}
              }

						  $sql =  "DELETE FROM " . CLASSIFIEDS_TABLE . "
												WHERE ad_status = 2";
							$result = $db->sql_query($sql);

							trigger_error($user->lang['PURGED_SUCCESFULLY'] . adm_back_link($this->u_action));
						}
					break;

					case 'purge_sold':

						if (!confirm_box(true) )
						{
							$confirm = true;
							$confirm_lang = 'PURGE_SOLD_ADS_CONFIRM';

							if ($confirm)
							{
								confirm_box(false, $user->lang[$confirm_lang], build_hidden_fields(array(
									'i'			=> $id,
									'mode'		=> $mode,
									'action'	=> $action,
								)));
							}
						}
						else
						{
						  // select ad_id of all ads, which will be purged
						  $sql =  "SELECT ad_id
												FROM " . CLASSIFIEDS_TABLE . "
            						WHERE ad_status = 1";
							$result = $db->sql_query($sql);

							while($row = $db->sql_fetchrow( $result ))
							{
							  // select images from deleted ad
								$sql =  'SELECT ad_id, image_name
													FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
													WHERE ad_id = ' . $row['ad_id'];
					   		$result2 = $db->sql_query($sql);

								while($row2 = $db->sql_fetchrow( $result2 ))
								{
										// remove images from FTP and DB
										@unlink("{$phpbb_root_path}".CL_DIRECTORY."/images/".$row2['image_name']);

										$sql =  'DELETE FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
															WHERE ad_id = ' . $row2['ad_id'] . '
																AND image_name = "' . $row2['image_name'] . '"';
										$result3 = $db->sql_query($sql);
								}
              }

						  $sql =  "DELETE FROM " . CLASSIFIEDS_TABLE . "
												WHERE ad_status = 1";
							$result = $db->sql_query($sql);

							trigger_error($user->lang['PURGED_SUCCESFULLY'] . adm_back_link($this->u_action));
						}
					break;

					case 'purge_expired':

					  if (!confirm_box(true) )
						{
							$confirm = true;
							$confirm_lang = 'PURGE_EXPIRED_ADS_CONFIRM';

							if ($confirm)
							{
								confirm_box(false, $user->lang[$confirm_lang], build_hidden_fields(array(
									'i'			=> $id,
									'mode'		=> $mode,
									'action'	=> $action,
								)));
							}
						}
						else
						{
						  // select ad_id of all ads, which will be purged
						  $sql =  "SELECT ad_id
												FROM " . CLASSIFIEDS_TABLE . "
												  WHERE ad_expire < " . time();
							$result = $db->sql_query($sql);

							while($row = $db->sql_fetchrow( $result ))
							{
							  // select images from deleted ad
								$sql =  'SELECT ad_id, image_name
													FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
													WHERE ad_id = ' . $row['ad_id'];
					   		$result2 = $db->sql_query($sql);

								while($row2 = $db->sql_fetchrow( $result2 ))
								{
										// remove images from FTP and DB
										@unlink("{$phpbb_root_path}".CL_DIRECTORY."/images/".$row2['image_name']);

										$sql =  'DELETE FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
															WHERE ad_id = ' . $row2['ad_id'] . '
																AND image_name = "' . $row2['image_name'] . '"';
										$result3 = $db->sql_query($sql);
								}
              }

						  $sql =  "DELETE FROM " . CLASSIFIEDS_TABLE . "
													ad_expire < ".time();
							$result = $db->sql_query($sql);

							trigger_error($user->lang['PURGED_SUCCESFULLY'] . adm_back_link($this->u_action));
						}
					break;

          case 'purge_active':

					  if (!confirm_box(true) )
						{
							$confirm = true;
							$confirm_lang = 'PURGE_ACTIVE_ADS_CONFIRM';

							if ($confirm)
							{
								confirm_box(false, $user->lang[$confirm_lang], build_hidden_fields(array(
									'i'			=> $id,
									'mode'		=> $mode,
									'action'	=> $action,
								)));
							}
						}
						else
						{
						  // select ad_id of all ads, which will be purged
						  $sql =  "SELECT ad_id
												FROM " . CLASSIFIEDS_TABLE . "
            						WHERE ad_status = 0
													AND ad_expire > " . time();
							$result = $db->sql_query($sql);

							while($row = $db->sql_fetchrow( $result ))
							{
							  // select images from deleted ad
								$sql =  'SELECT ad_id, image_name
													FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
													WHERE ad_id = ' . $row['ad_id'];
					   		$result2 = $db->sql_query($sql);

								while($row2 = $db->sql_fetchrow( $result2 ))
								{
										// remove images from FTP and DB
										@unlink("{$phpbb_root_path}".CL_DIRECTORY."/images/".$row2['image_name']);

										$sql =  'DELETE FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
															WHERE ad_id = ' . $row2['ad_id'] . '
																AND image_name = "' . $row2['image_name'] . '"';
										$result3 = $db->sql_query($sql);
								}
              }

							$sql =  "DELETE FROM " . CLASSIFIEDS_TABLE . "
												WHERE ad_status = 0
													AND ad_expire > " . time();
							$result = $db->sql_query($sql);

							trigger_error($user->lang['PURGED_SUCCESFULLY'] . adm_back_link($this->u_action));
						}
					break;

				}

				$template->assign_vars(array(
				  'U_PURGE_EXPIRED_SOLD_CLOSED'	=> $this->u_action . '&amp;action=purge_expired_sold_closed',
				  'U_PURGE_CLOSED'							=> $this->u_action . '&amp;action=purge_closed',
					'U_PURGE_SOLD'								=> $this->u_action . '&amp;action=purge_sold',
					'U_PURGE_EXPIRED'							=> $this->u_action . '&amp;action=purge_expired',
					'U_PURGE_ACTIVE'							=> $this->u_action . '&amp;action=purge_active',
					'S_FOUNDER'			=> ($user->data['user_type'] == USER_FOUNDER) ? true : false,
				));

      break;

      case 'cats':
        $this->page_title = 'ACP_CLASSIFIEDS_CATS_TITLE';
        $this->tpl_name = 'acp_classifieds_cats';

        $action	= request_var('action', '');
        $id	= request_var('id', 0);
				$name = request_var('name', '', true);
    		$delete_cat	= request_var('delete_cat', '');
    		$delete_parent	= request_var('delete_parent', '');

				$sql_ary = (array(
					'name'       		=> $name,
					'parent'			=> request_var('parent', 0),
					'parent_id'			=> request_var('parent_id', 0)
				));

		    switch ($action)
				{
					case 'move_up':
					case 'move_down':
						if (!$id)
						{
							trigger_error($user->lang['NO_CATEGORY'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						$sql = 'SELECT *
										FROM ' . CLASSIFIEDS_CATEGORY_TABLE . "
										WHERE id = $id";
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						if (!$row)
						{
							trigger_error($user->lang['NO_CATEGORY'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						$move_category_name = move_category_by($row, $action);

						if ($move_category_name !== false)
						{
							$cache->destroy('sql', CLASSIFIEDS_CATEGORY_TABLE);
						}
					break;

					case "newcat":
						$sql = 'SELECT MAX(right_id) AS right_id
											FROM ' . CLASSIFIEDS_CATEGORY_TABLE;
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

      			$sql_ary['left_id'] = $row['right_id'] + 1;
						$sql_ary['right_id'] = $row['right_id'] + 2;

						$sql = 'INSERT INTO ' . CLASSIFIEDS_CATEGORY_TABLE . $db->sql_build_array('INSERT', $sql_ary);
						$db->sql_query($sql);
					break;

					case "editcat":
						$sql = 'UPDATE ' . CLASSIFIEDS_CATEGORY_TABLE . '
											SET ' . $db->sql_build_array('UPDATE', $sql_ary). '
											WHERE id = ' . $id;
						$db->sql_query($sql);
					break;

					case "purgecat":
         		if (confirm_box(true))
						{
						  // select ad_id of all ads, which will be purged
						  $sql =  "SELECT ad_id
												FROM " . CLASSIFIEDS_TABLE . "
													WHERE cat_id = " . $id;
							$result = $db->sql_query($sql);

							while($row = $db->sql_fetchrow( $result ))
							{
							  // select images from deleted ad
								$sql =  'SELECT ad_id, image_name
													FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
													WHERE ad_id = ' . $row['ad_id'];
					   		$result2 = $db->sql_query($sql);

								while($row2 = $db->sql_fetchrow( $result2 ))
								{
										// remove images from FTP and DB
										@unlink("{$phpbb_root_path}".CL_DIRECTORY."/images/".$row2['image_name']);

										$sql =  'DELETE FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
															WHERE ad_id = ' . $row2['ad_id'] . '
																AND image_name = "' . $row2['image_name'] . '"';
										$result3 = $db->sql_query($sql);
								}
              }

						  $sql =  "DELETE FROM " . CLASSIFIEDS_TABLE . "
												WHERE cat_id = " . $id;
							$result = $db->sql_query($sql);

							trigger_error($user->lang['PURGED_SUCCESFULLY'] . adm_back_link($this->u_action));
						}
						else
						{
              confirm_box(false, $user->lang['PURGE_CAT_CONFIRM']);
						}
					break;

					case "deletecat":
						if (confirm_box(true) )
						{
						  if ($delete_parent == '')
						  {
							  // Now if there are ads under the deleted category they need to be moved to another category or deleted
								if (empty($delete_cat) || $delete_cat == '0')
								{
								  // select ad_id of all ads, which will be purged
								  $sql =  "SELECT ad_id
														FROM " . CLASSIFIEDS_TABLE . "
															WHERE cat_id = " . $id;
									$result = $db->sql_query($sql);

									while($row = $db->sql_fetchrow( $result ))
									{
									  // select images from deleted ad
										$sql =  'SELECT ad_id, image_name
															FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
															WHERE ad_id = ' . $row['ad_id'];
							   		$result2 = $db->sql_query($sql);

										while($row2 = $db->sql_fetchrow( $result2 ))
										{
												// remove images from FTP and DB
												@unlink("{$phpbb_root_path}".CL_DIRECTORY."/images/".$row2['image_name']);

												$sql =  'DELETE FROM  ' . CLASSIFIEDS_IMAGES_TABLE . '
																	WHERE ad_id = ' . $row2['ad_id'] . '
																		AND image_name = "' . $row2['image_name'] . '"';
												$result3 = $db->sql_query($sql);
										}
		              }

	                $sql = 'DELETE FROM ' . CLASSIFIEDS_TABLE . '
														WHERE cat_id = ' . $id;
									$db->sql_query($sql);
								}
								else
								{
	                $sql = 'UPDATE ' . CLASSIFIEDS_TABLE . '
														SET cat_id = ' . $delete_cat . '
														WHERE cat_id= ' . $id;
									$db->sql_query($sql);
								}
							}
							else
							{
                $sql = 'UPDATE ' . CLASSIFIEDS_CATEGORY_TABLE . '
													SET parent_id = 0
													WHERE parent_id = ' . $id;
								$db->sql_query($sql);
							}

							$sql = 'DELETE FROM ' . CLASSIFIEDS_CATEGORY_TABLE . '
												WHERE id = ' . $id;
							$db->sql_query($sql);
						}
						else
						{
       				confirm_box(false, $user->lang['DELETE_CAT_CONFIRM'], build_hidden_fields(array(
								'delete_cat'			=> $delete_cat,
							)));
						}
					break;
				}

				$sql = 'SELECT *
								FROM ' . CLASSIFIEDS_CATEGORY_TABLE . '
									ORDER BY left_id ASC';
				$result	 = $db->sql_query($sql);

				while($row = $db->sql_fetchrow( $result ))
				{
					$url = $this->u_action . "&amp;id={$row['id']}";

					$template->assign_block_vars('cat',array(
						'NAME'			=> $row['name'],
						'ID'			=> $row['id'],
						'EDIT_CAT'  	=> $this->u_action . '&amp;action=editcat&amp;id=' . $row['id'],
						'PURGE_CAT'  	=> $this->u_action . '&amp;action=purgecat&amp;id=' . $row['id'],
						'DELETE_PARENT_CAT' 	=> $this->u_action . '&amp;action=deletecat&amp;id=' . $row['id'] . '&amp;delete_parent=1',
						'DELETE_CAT' 	=> $this->u_action . '&amp;action=deletecat&amp;id=' . $row['id'] . '&amp;delete_cat='.$delete_cat,
						'U_MOVE_UP'		=> $url . '&amp;action=move_up',
						'U_MOVE_DOWN'	=> $url . '&amp;action=move_down',
						'PARENT'		=> $row['parent'],
						'PARENT_ID'		=> $row['parent_id'],
						'PARENT_CAT'	=> get_category_parent($row['parent_id'])
					));
				}

				$template->assign_vars(array(
					'U_NEW_CAT'		=> $this->u_action . '&amp;action=newcat',
				));

      break;

      case 'rules':

     		$this->page_title = 'ACP_CLASSIFIEDS_RULES_TITLE';
        $this->tpl_name = 'acp_classifieds_rules';

        $user->add_lang('posting');

				include ($phpbb_root_path . 'includes/functions_display.' . $phpEx);
    		include ($phpbb_root_path . 'includes/message_parser.' . $phpEx);

    		$rules	= request_var('rules', '');

				$template->assign_vars(array(
					'RULES'						=> $rules,
					'GENERAL_RULES'		=> append_sid($this->u_action, "rules=general"),
					'BUYER_RULES'			=> append_sid($this->u_action, "rules=buyer"),
					'SELLER_RULES'		=> append_sid($this->u_action, "rules=seller"),
				));

				if (!empty($rules))
				{
				  if ($rules == 'general') { $id = 1; }
				    elseif ($rules == 'buyer') { $id = 2; }
						elseif ($rules == 'seller') { $id = 3; };

	      	// select rules data
					$sql = 'SELECT *
                    FROM ' . CLASSIFIEDS_RULES_TABLE  . '
                      WHERE rules_id = ' . $id . '
										    ORDER BY rules_id ASC';
					$result	 = $db->sql_query($sql);
					$row = $db->sql_fetchrow( $result );

      		$template->assign_vars(array(
						'RULES_ID'		=>	$row['rules_id'],
						'RULES_TITLE'	=>	$row['rules_title'],
						'RULES_TEXT'	=>	$row['rules_text'],
						'DISPLAY_RULES'	=>	$row['display_rules'],
						'MUST_AGREE'		=>	$row['must_agree'],
						'DISPLAY_AS_LINK'	=>	$row['display_as_link'],

						'L_ACP_CLASSIFIEDS_RULES'	=> $user->lang('ACP_CLASSIFIEDS_RULES'.$id),
						'L_RULES_TITLE'	=> $user->lang('RULES_TITLE'.$id),
						'L_RULES_DISPLAY'	=> $user->lang('RULES_DISPLAY'.$id),
						'L_MUST_AGREE'	=> $user->lang('MUST_AGREE'.$id),
						'L_MUST_AGREE_EXPLAIN'	=> $user->lang('MUST_AGREE_EXPLAIN'.$id),
						'L_DISPLAY_AS_LINK'	=> $user->lang('DISPLAY_AS_LINK'.$id),
						'L_DISPLAY_AS_LINK_EXPLAIN'	=> $user->lang('DISPLAY_AS_LINK_EXPLAIN'.$id),
					));
					$db->sql_freeresult($result);

					display_custom_bbcodes();

					$submit	= (isset($_POST['submit'])) ? true : false;
					$preview	= (isset($_POST['preview'])) ? true : false;

	        if ($submit)
					{
					  $rules_id = request_var('rules_id', 0);
						$rules_title = utf8_normalize_nfc(request_var('rules_title', '', true));
						$display_rules = request_var('display_rules', 0);
	          $must_agree = request_var('must_agree', 0);
	          $rules_text = utf8_normalize_nfc(request_var('rules_text', '', true));
	          $display_as_link = request_var('display_as_link', 0);

	          $sql_ary = (array(
							'rules_id'		=> $rules_id,
							'rules_title'		=> $rules_title,
							'display_rules'		=> $display_rules,
							'must_agree'		=> $must_agree,
							'rules_text'		=> $rules_text,
							'display_as_link'		=> $display_as_link,
						));

		      	$sql = 'UPDATE ' . CLASSIFIEDS_RULES_TABLE . '
											SET ' . $db->sql_build_array('UPDATE', $sql_ary). '
											WHERE rules_id = ' . $rules_id;
						$db->sql_query($sql);

          	trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action . '&amp;rules=' . $rules));
					}

					if ($preview == true)
					{
      			$rules_text = utf8_normalize_nfc(request_var('rules_text', '', true));
            $rules_text2 = $rules_text;

						$uid = $bitfield = $options = '';
						$allow_bbcode	= $allow_smilies	= $allow_urls	= true;

						generate_text_for_storage($rules_text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

						$preview_text	= generate_text_for_display($rules_text, $uid, $bitfield, $options);

            $template->assign_vars(array(
							'RULES_TEXT'	=>	$rules_text2,
						));
					}

					$template->assign_vars(array(
					  'U_ACTION'			=> $this->u_action . '&amp;rules=' . $rules,

					  'S_BBCODE_ALLOWED'	=> true,
						'S_BBCODE_QUOTE'	=> true,
						'S_BBCODE_IMG'		=> true,
						'S_LINKS_ALLOWED'	=> true,
						'S_BBCODE_FLASH'	=> false,

						'PREVIEW_TEXT'		=> ($preview) ? $preview_text  : '',
			      'S_PREVIEW'			=> $preview,
					));

        }

      break;         
      
      case 'prefixes':
        $this->page_title = 'ACP_CLASSIFIEDS_PREFIXES_TITLE';
        $this->tpl_name = 'acp_classifieds_prefixes';

        $action	= request_var('action', '');
        $prefix_id	= request_var('prefix_id', '');
        $prefix_short	= request_var('prefix_short', '');
        $prefix_name	= request_var('prefix_name', '', true);
        $prefix_color	= request_var('prefix_color', '');
        
				switch ($action)
				{
					case "delete_prefix":
						if (confirm_box(true))
						{
       				$sql = 'DELETE FROM ' . CLASSIFIEDS_PREFIXES_TABLE . '
							 				WHERE prefix_id = ' . $prefix_id;
							$db->sql_query($sql);

							redirect(append_sid("{$this->u_action}"));
						}
						else
						{
							confirm_box(false, $user->lang['DELETE_PREFIX_CONFIRM']);

							redirect(append_sid("{$this->u_action}"));
						}
					break;

					case "add_prefix":
						$sql = 'INSERT INTO ' . CLASSIFIEDS_PREFIXES_TABLE . ' (prefix_short, prefix_name, prefix_color)
                      VALUES ("' . $prefix_short . '", "' . $prefix_name . '", "' . $prefix_color . '")';
						$db->sql_query($sql);

     				redirect(append_sid("{$this->u_action}"));
					break;
					
					case "edit_prefix":
					  if (confirm_box(true))
						{
						  $sql_ary = (array(
								'prefix_name'			  => $prefix_name,
        				'prefix_short'			=> $prefix_short,
        				'prefix_color'			=> $prefix_color,
							));
						  
      				$sql = 'UPDATE ' . CLASSIFIEDS_PREFIXES_TABLE . '
  											SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
                          WHERE prefix_id = ' . $prefix_id;
  						$db->sql_query($sql);

							redirect(append_sid("{$this->u_action}"));
						}
						else
						{
							confirm_box(false, $user->lang['EDIT_PREFIX_CONFIRM'], build_hidden_fields(array(
        				'prefix_name'			  => $prefix_name,
        				'prefix_short'			=> $prefix_short,
        				'prefix_color'			=> $prefix_color,
							)));

							redirect(append_sid("{$this->u_action}"));
						}
					break;
				}
				
				$submit	= (isset($_POST['submit'])) ? true : false;
				
				if($submit)
				{
				  set_config('mandatory_ad_prefix', request_var('mandatory_ad_prefix', 0));

     			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
        }

				$sql = 'SELECT *
                  FROM ' . CLASSIFIEDS_PREFIXES_TABLE;
				$result	 = $db->sql_query($sql);

				while($row = $db->sql_fetchrow( $result ))
				{
					$template->assign_block_vars('prefixes',array(
						'ID'              => $row['prefix_id'],
						'NAME'            => $row['prefix_name'],
						'SHORT'           => $row['prefix_short'],
						'COLOR'           => $row['prefix_color'],
            'L_SEARCH_PREFIX' => sprintf($user->lang['SEARCH_PREFIX'], $row['prefix_short']), 
						'U_SEARCH'			  => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", "mode=search&amp;sprefix={$row['prefix_id']}"),
						'U_DELETE'		    => $this->u_action . '&amp;action=delete_prefix&amp;prefix_id= ' . $row['prefix_id'],
						'U_EDIT'  	      => $this->u_action . '&amp;action=edit_prefix&amp;prefix_id=' . $row['prefix_id'],
					));
				}
				
				$template->assign_vars(array(
          'U_ADD_PREFIX'   => $this->u_action . '&amp;action=add_prefix',
          'U_ACTION'            => $this->u_action,
          'MANDATORY_AD_PREFIX'			     => $config['mandatory_ad_prefix'],
				));

      break;
      
      case 'locations':
        $this->page_title = 'ACP_CLASSIFIEDS_LOCATIONS_TITLE';
        $this->tpl_name = 'acp_classifieds_locations';

        $action	= request_var('action', '');
        $location_id	= request_var('location_id', '');
        $location_name	= utf8_normalize_nfc(request_var('location_name', '', true));
        
				switch ($action)
				{
					case "delete_location":
						if (confirm_box(true))
						{
       				$sql = 'DELETE FROM ' . CLASSIFIEDS_LOCATIONS_TABLE . '
							 				WHERE location_id = ' . $location_id;
							$db->sql_query($sql);

							redirect(append_sid("{$this->u_action}"));
						}
						else
						{
							confirm_box(false, $user->lang['DELETE_LOCATION_CONFIRM']);

							redirect(append_sid("{$this->u_action}"));
						}
					break;

					case "add_location":
					  $location_list = explode("\n", $location_name);
					  
					  if (empty($location_name))
      			{
      			  trigger_error($user->lang['NO_LOCATION'] . adm_back_link($this->u_action), E_USER_WARNING);
      			}
      			else
      			{
              $sql_ary = array();

          		foreach ($location_list as $location_entry)
          		{
          			$sql_ary[] = array(
          				'location_name'	=> (string) $location_entry,
          			);
          		}
          
          		$db->sql_multi_insert(CLASSIFIEDS_LOCATIONS_TABLE, $sql_ary);
            }
					  
     				redirect(append_sid("{$this->u_action}"));
					break;          
					
					case "edit_location":
					  if (confirm_box(true))
						{
						  $sql_ary = (array(
								'location_name'			  => $location_name,
							));
						  
      				$sql = 'UPDATE ' . CLASSIFIEDS_LOCATIONS_TABLE . '
  											SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
                          WHERE location_id = ' . $location_id;
  						$db->sql_query($sql);

							redirect(append_sid("{$this->u_action}"));
						}
						else
						{
							confirm_box(false, $user->lang['EDIT_LOCATION_CONFIRM'], build_hidden_fields(array(
        				'location_name'			  => $location_name,
							)));

							redirect(append_sid("{$this->u_action}"));
						}
					break;
				}
				
				$submit	= (isset($_POST['submit'])) ? true : false;
				
				if($submit)
				{
				  set_config('mandatory_ad_location', request_var('mandatory_ad_location', 0));
				  set_config('fill_location_to_trade', request_var('fill_location_to_trade', 0));

     			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
        }

				$sql = 'SELECT *
                  FROM ' . CLASSIFIEDS_LOCATIONS_TABLE . '
                    ORDER BY location_name ASC';
				$result	 = $db->sql_query($sql);

				while($row = $db->sql_fetchrow( $result ))
				{
					$template->assign_block_vars('locations',array(
						'ID'              => $row['location_id'],
						'NAME'            => $row['location_name'],
            'L_SEARCH_LOCATION' => sprintf($user->lang['SEARCH_LOCATION'], $row['location_name']), 
						'U_SEARCH'			  => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", "mode=search&amp;slocation={$row['location_id']}"),
						'U_DELETE'		    => $this->u_action . '&amp;action=delete_location&amp;location_id= ' . $row['location_id'],
						'U_EDIT'  	      => $this->u_action . '&amp;action=edit_location&amp;location_id=' . $row['location_id'],
					));
				}
				
				$template->assign_vars(array(
          'U_ADD_LOCATION'            => $this->u_action . '&amp;action=add_location',
          'U_ACTION'            => $this->u_action,
          'MANDATORY_AD_LOCATION'			=> $config['mandatory_ad_location'],
          'FILL_LOCATION_TO_TRADE'       => $config['fill_location_to_trade'],
				));

      break;
      
      case 'currency':
        $this->page_title = 'ACP_CLASSIFIEDS_CURRENCY_TITLE';
        $this->tpl_name = 'acp_classifieds_currency';

        $action	= request_var('action', '');
        $currency_id	= request_var('currency_id', '');
        $currency_short	= request_var('short', '', true);
        $currency_name	= request_var('name', '', true);
        
				switch ($action)
				{
					case "delete_currency":
						if (confirm_box(true))
						{
       				$sql = 'DELETE FROM ' . CLASSIFIEDS_CURRENCY_TABLE . '
							 				WHERE id = ' . $currency_id;
							$db->sql_query($sql);

							redirect(append_sid("{$this->u_action}"));
						}
						else
						{
							confirm_box(false, $user->lang['DELETE_CURRENCY_CONFIRM']);

							redirect(append_sid("{$this->u_action}"));
						}
					break;

					case "add_currency":
						$sql = 'INSERT INTO ' . CLASSIFIEDS_CURRENCY_TABLE . ' (short, name)
                      VALUES ("' . $currency_short . '", "' . $currency_name . '")';
						$db->sql_query($sql);

     				redirect(append_sid("{$this->u_action}"));
					break;
					
					case "edit_currency":
					  if (confirm_box(true))
						{
						  $sql_ary = (array(
								'name'			=> $currency_name,
        				'short'			=> $currency_short
							));
						  
      				$sql = 'UPDATE ' . CLASSIFIEDS_CURRENCY_TABLE . '
  											SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
                          WHERE id = ' . $currency_id;
  						$db->sql_query($sql);

							redirect(append_sid("{$this->u_action}"));
						}
						else
						{
							confirm_box(false, $user->lang['EDIT_CURRENCY_CONFIRM'], build_hidden_fields(array(
        				'name'			=> $currency_name,
        				'short'			=> $currency_short
							)));

							redirect(append_sid("{$this->u_action}"));
						}
					break;
				}
				
				$submit	= (isset($_POST['submit'])) ? true : false;
				
				if($submit)
				{
				  set_config('default_currency', request_var('default_currency', ''));

     			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
        }

				$sql = 'SELECT *
                  FROM ' . CLASSIFIEDS_CURRENCY_TABLE;
				$result	 = $db->sql_query($sql);

				while($row = $db->sql_fetchrow( $result ))
				{
					$template->assign_block_vars('currency',array(
						'ID'              => $row['id'],
						'NAME'            => $row['name'],
						'SHORT'           => $row['short'],
						'U_DELETE'		    => $this->u_action . '&amp;action=delete_currency&amp;currency_id= ' . $row['id'],
						'U_EDIT'  	      => $this->u_action . '&amp;action=edit_currency&amp;currency_id=' . $row['id'],
					));
				}
				
				$template->assign_vars(array(
          'U_ADD_CURRENCY'        => $this->u_action . '&amp;action=add_currency',
          'U_ACTION'              => $this->u_action,
          'DEFAULT_CURRENCY'      => $config['default_currency'],
				));

      break;
      
    }

	}
}
?>