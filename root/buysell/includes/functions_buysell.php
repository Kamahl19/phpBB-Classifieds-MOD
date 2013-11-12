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

$template->assign_vars(array(
  'T_CLASSIFIEDS_PATH'       => append_sid("{$phpbb_root_path}".CL_DIRECTORY."/"),
));      

function resize_img($origFile, $extension, $newFile, $max_width, $max_height)
{
  $imgsize = getimagesize($origFile);
  $orig_width = $imgsize[0];
  $orig_height = $imgsize[1];  
  
  if ($orig_width <= $max_width && $orig_height <= $max_height)
  {
    $max_width = $orig_width;
    $max_height = $orig_height;
  }
  else
  {
    $orig_ratio = $orig_width / $orig_height;
    
    if ($max_width / $max_height > $orig_ratio)
    {
       $max_width = floor($max_height * $orig_ratio);
    }
    else
    {
       $max_height = floor($max_width / $orig_ratio);
    }
  }

  switch(strtolower($extension))
  {
    case 'jpg':
    case 'jpeg':
      $image = imagecreatefromjpeg($origFile);
      break;
      
    case 'png':
      $image = imagecreatefrompng($origFile);
      break;
      
    case 'gif':
      $image = imagecreatefromgif($origFile);
      break;
  }
  
  $picture = imagecreatetruecolor($max_width, $max_height);
  imagealphablending($picture, false);
  imagesavealpha($picture, true);
  imagecopyresampled($picture, $image, 0, 0, 0, 0, $max_width, $max_height, $orig_width, $orig_height);
  
  switch(strtolower($extension))
  {
    case 'jpg':
    case 'jpeg':
      imagejpeg($picture, $newFile);
      break;
      
    case 'png':
      imagepng($picture, $newFile);
      break;
      
    case 'gif':
      imagegif($picture, $newFile);
      break;
  }
    
  imagedestroy($picture);
  imagedestroy($image);  
}

function add_http($url = '')
{
  if ($url != '' && !preg_match("~^(?:f|ht)tps?://~i", $url))
  {
    $url = "http://" . $url;
  }
  
  return $url;
}

function build_categories()
{
	global $db, $phpbb_root_path, $phpEx, $config, $template;
	
  $sql_ary =  array(
    'SELECT'      => 'c.id, c.name, c.left_id, c.parent, c.parent_id, COUNT(a.ad_id) as num_ads',
    'FROM'        => array(
      CLASSIFIEDS_CATEGORY_TABLE      => 'c',
    ),
    'LEFT_JOIN'     => array(
      array(
        'FROM'  => array(CLASSIFIEDS_TABLE => 'a'),
        'ON'    => 'a.cat_id = c.id AND a.ad_status = ' . ACTIVE . ' AND a.ad_expire > ' . time(),
      )
    ),
    'GROUP_BY'      => 'c.id, c.name, c.left_id, c.parent, c.parent_id',
    'ORDER_BY'      => 'c.left_id ASC'
  );
   
  $sql = $db->sql_build_query('SELECT', $sql_ary);
  $result = $db->sql_query($sql);

	$category = '';
	
	while ($row = $db->sql_fetchrow($result)) 
	{ 
		$category_link = append_sid($phpbb_root_path . CL_DIRECTORY . '/index.' . $phpEx, 'mode=cat&amp;id=' . intval($row['id']));

		if ($row['parent'])
		{
		  if ($row['left_id'] != '1')
			{
        $category .= '<br />';
			}
			$category .= '<li class="header"><strong><a href="javascript:void(0)">'. $row['name'] .'</a></strong></li>';
		}
		else
		{
		  if ($row['parent_id'] == '0' && $row['left_id'] != '1')
			{
        $category .= '<br />';
			}
			$category .= '<li class="header">» <a href="'. $category_link .'">'. $row['name'] .'</a> (' . $row['num_ads'] . ')</li>';
		}   
		
		$template->assign_block_vars('cat',array(
			'NAME'		 =>	$row['name'],
			'ID'		   =>	$row['id'],
			'PARENT'	 => $row['parent'],
		));
	}
	return $category;
}

function total_ads_per_category($id)
{
	global $db, $user;

	$sql = 'SELECT COUNT(ad_id) as total_advertisements
						FROM ' . CLASSIFIEDS_TABLE . '
						  WHERE cat_id = ' . $id . '
						    AND ad_status = ' . ACTIVE . '
						    AND ad_expire > ' . time();
	$result = $db->sql_query($sql);
	$total_advertisements = $db->sql_fetchfield('total_advertisements');
	$db->sql_freeresult($result);
	
	return $total_advertisements;
}

function user_total_ads($user_id, $style)
{
	global $db, $user, $config, $phpEx, $phpbb_root_path, $auth, $template;
	
	$sql = 'SELECT COUNT(ad_id) AS number_sold 
			FROM ' . CLASSIFIEDS_TABLE . ' 
			WHERE ad_status = ' . SOLD . ' 
				AND ad_poster_id = ' . $user_id;
	$result = $db->sql_query($sql);
	$total_sold = $db->sql_fetchfield('number_sold');
	$db->sql_freeresult($result);
	
	$sql = 'SELECT COUNT(ad_id) AS number_active 
			FROM ' . CLASSIFIEDS_TABLE . ' 
			WHERE ad_status = ' . ACTIVE . ' 
				AND ad_expire > ' . time() . ' 
				AND ad_poster_id = ' . $user_id;
	$result = $db->sql_query($sql);
	$total_active = $db->sql_fetchfield('number_active');
	$db->sql_freeresult($result);

	$sql = 'SELECT COUNT(ad_id) AS number_closed 
			FROM ' . CLASSIFIEDS_TABLE . ' 
			WHERE ad_status = ' . CLOSED . ' 
				AND ad_poster_id = ' . $user_id;
	$result = $db->sql_query($sql);
	$total_closed = $db->sql_fetchfield('number_closed');
	$db->sql_freeresult($result);
	
	$sql = 'SELECT COUNT(ad_id) AS number_expired 
			FROM ' . CLASSIFIEDS_TABLE . ' 
			WHERE ad_expire < ' . time() . '
        AND ad_status <> ' . SOLD . '  
        AND ad_status <> ' . CLOSED . '  
        AND ad_poster_id = ' . $user_id;
	$result = $db->sql_query($sql);
	$total_expired = $db->sql_fetchfield('number_expired');
	$db->sql_freeresult($result);
	      
	if ($style == 'left_bar')
	{
	  $own_active_ads = append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'mode=view_own_active');
    $own_expired_ads = append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'mode=view_own_expired');
    $own_sold_ads = append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'mode=view_own_sold');
    $own_closed_ads = append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'mode=view_own_closed');
    
    $list = '<ul>';
  	$list .= '<li class="header">» <a href="'.$own_active_ads.'">' . $user->lang['CL_ACTIVE_ADS'] . '</a> (' . $total_active . ')</li><li class="header">» <a href="'.$own_expired_ads.'">' . $user->lang['CL_EXPIRED_ADS'] . '</a> (' . $total_expired . ')</li><li class="header">» <a href="'.$own_sold_ads.'">' . $user->lang['CL_SOLD_ADS'] . '</a> (' . $total_sold . ')</li><li class="header">» <a href="'.$own_closed_ads.'">' . $user->lang['CL_CLOSED_ADS'] . '</a> (' . $total_closed . ')</li>';
  	$list .= '</ul>';    
    
    $template->assign_vars(array(
    	'USER_AD_STATS'    => $list,
    ));   
	}
	elseif ($style == 'single_ad')
	{
	  $ads_link = append_sid("{$phpbb_root_path}".CL_DIRECTORY."/index.$phpEx", 'mode=viewuser&amp;user='.$user_id);
	  $administrate_ads = ($auth->acl_get('a_')) ? ' &bull; [<a href="'.$phpbb_root_path.'adm/index.php?sid='.$user->session_id.'&amp;i=classifieds&amp;mode=manage&amp;status=viewuser&amp;u='.$user_id.'">' . $user->lang['CL_ADMIN_USERS_ADS'] . '</a>]' : '';
		$line = '<strong>' . $total_active . '</strong> ' . $user->lang['CL_ACTIVE_AD'] . ', <strong>' . $total_expired . '</strong> ' . $user->lang['CL_EXPIRE_AD'] . ', <strong>' . $total_sold . '</strong> ' . $user->lang['CL_SOLD_AD'] . ', <strong>' . $total_closed . '</strong> ' . $user->lang['CL_CLOSED_AD'] . ' [<a href="' . $ads_link . '">' . $user->lang['CL_VIEW_USERS_ADS'] . '</a>]'.$administrate_ads;
	  
	  $template->assign_vars(array(
    	'USER_ADVERTISEMENTS'			=> $line,
    )); 
  }
}

function get_ad_parent($cat_id)
{
	global $db, $user;

	$sql = 'SELECT name
						FROM ' . CLASSIFIEDS_CATEGORY_TABLE . '
              WHERE id = ' . $cat_id;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	return $row['name'];
}

function get_ad_category($id)
{
	global $db, $user;

	$sql = 'SELECT name, parent, parent_id
						FROM ' . CLASSIFIEDS_CATEGORY_TABLE . '
						  WHERE id = ' . $id;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
  if ($row['parent'] == '0' && $row['parent_id'] != '0')
	{
		$sql2 = 'SELECT name
							FROM ' . CLASSIFIEDS_CATEGORY_TABLE . '
                WHERE id = ' . $row['parent_id'];
		$result2 = $db->sql_query($sql2);
		$row2 = $db->sql_fetchrow($result2);
		$db->sql_freeresult($result2);

		return $row2['name'] . ' » ' . $row['name'];
	}

	return $row['name'];
}

function get_category_parent($id)
{
	global $db, $user;

	$sql = 'SELECT name
						FROM ' . CLASSIFIEDS_CATEGORY_TABLE . '
						  WHERE id = ' . $id;
	$result = $db->sql_query($sql);
	$category_parent_name = $db->sql_fetchfield('name');
	$db->sql_freeresult($result);
	
	return '['.$category_parent_name.']';
}

function move_category_by($category_row, $action = 'move_up')
{
	global $db;

	/**
	* Fetch all the siblings between the module's current spot
	* and where we want to move it to. If there are less than $steps
	* siblings between the current spot and the target then the
	* module will move as far as possible
	*/
	$sql = 'SELECT *
						FROM ' . CLASSIFIEDS_CATEGORY_TABLE . '
						WHERE ' . (($action == 'move_up') ? "right_id < {$category_row['right_id']} ORDER BY right_id DESC" : "left_id > {$category_row['left_id']} ORDER BY left_id ASC");
	$result = $db->sql_query_limit($sql, 1);

	$target = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$target = $row;
	}
	$db->sql_freeresult($result);

	if (!sizeof($target))
	{
		return false;
	}

	/**
	* $left_id and $right_id define the scope of the nodes that are affected by the move.
	* $diff_up and $diff_down are the values to substract or add to each node's left_id
	* and right_id in order to move them up or down.
	* $move_up_left and $move_up_right define the scope of the nodes that are moving
	* up. Other nodes in the scope of ($left_id, $right_id) are considered to move down.
	*/
	if ($action == 'move_up')
	{
		$left_id = $target['left_id'];
		$right_id = $category_row['right_id'];

		$diff_up = $category_row['left_id'] - $target['left_id'];
		$diff_down = $category_row['right_id'] + 1 - $category_row['left_id'];

		$move_up_left = $category_row['left_id'];
		$move_up_right = $category_row['right_id'];
	}
	else
	{
		$left_id = $category_row['left_id'];
		$right_id = $target['right_id'];

		$diff_up = $category_row['right_id'] + 1 - $category_row['left_id'];
		$diff_down = $target['right_id'] - $category_row['right_id'];

		$move_up_left = $category_row['right_id'] + 1;
		$move_up_right = $target['right_id'];
	}

	// Now do the dirty job
	$sql = 'UPDATE ' . CLASSIFIEDS_CATEGORY_TABLE . "
						SET left_id = left_id + CASE
							WHEN left_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
							ELSE {$diff_down}
						END,
						right_id = right_id + CASE
							WHEN right_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
							ELSE {$diff_down}
						END
						WHERE
							left_id BETWEEN {$left_id} AND {$right_id}
							AND right_id BETWEEN {$left_id} AND {$right_id}";
	$db->sql_query($sql);

	return $target['name'];
}	

function watching_cat($user_id, $cat_id)
{
	global $db;
	
	$watching_cat = false;

	$sql = 'SELECT cat_id
            FROM ' . CLASSIFIEDS_CAT_WATCH_TABLE . '
						  WHERE user_id = ' . $user_id . '
						    AND cat_id = ' . $cat_id;
	$db->sql_query($sql);

  if ($db->sql_affectedrows())
  {
    $watching_cat = true;
	}

	return $watching_cat;
}

function subscribe_cat($user_id, $cat_id)
{
	global $db;

	$sql = 'INSERT INTO ' . CLASSIFIEDS_CAT_WATCH_TABLE . '
						VALUES (' . $cat_id . ', ' . $user_id . ')';
	$db->sql_query($sql);
}

function unsubscribe_cat($user_id, $cat_id)
{
	global $db;

	$sql = 'DELETE FROM ' . CLASSIFIEDS_CAT_WATCH_TABLE . '
						WHERE cat_id = "' . $cat_id . '"
							AND user_id = "' . $user_id . '"';
	$db->sql_query($sql);
}

function update_views($id)
{
  global $db;
  
  $sql = 'UPDATE ' . CLASSIFIEDS_TABLE . '
						SET ad_views = ad_views +1
						  WHERE ad_id = ' . $id;
	$db->sql_query($sql);
}

function load_currency()
{
  global $db, $template;
  
  $sql =  'SELECT id, short, name
						FROM  ' . CLASSIFIEDS_CURRENCY_TABLE;
	$result = $db->sql_query($sql);

	while($row = $db->sql_fetchrow($result))
	{
	  $template->assign_block_vars('currency',array(
			'ID'         =>  $row['id'],
			'SHORT'      =>  $row['short'],
			'NAME'       =>  $row['name'],
		));
	}
	$db->sql_freeresult($result);
}

function list_categories()
{
  global $db, $template;
  
  $sql = 'SELECT id, name, parent
            FROM ' . CLASSIFIEDS_CATEGORY_TABLE . '
              ORDER BY left_id ASC';
	$result	 = $db->sql_query($sql);

	while($row = $db->sql_fetchrow( $result ))
	{
		$template->assign_block_vars('cat',array(
			'NAME'		 =>	$row['name'],
			'ID'		   =>	$row['id'],
			'PARENT'	 => $row['parent'],
		));
	}
	$db->sql_freeresult($result); 
}

function load_prefixes()
{
  global $db, $template, $user, $phpEx, $phpbb_root_path;
  
  $sql =  'SELECT prefix_id, prefix_name, prefix_short
						FROM  ' . CLASSIFIEDS_PREFIXES_TABLE;
	$result = $db->sql_query($sql);
  $prefix_exist = false;

	while($row = $db->sql_fetchrow($result))
	{
	  $short_name = '['.$row['prefix_short'].'] '.$row['prefix_name'];
	  
	  $template->assign_block_vars('prefixes',array(
			'ID'                 =>  $row['prefix_id'],
			'SHORT'              =>  $row['prefix_short'],
			'NAME'               =>  $row['prefix_name'],
		  'L_CL_SEARCH_BY_PREFIX' => sprintf($user->lang['CL_SEARCH_BY_PREFIX'], $short_name ), 
		));
		 $prefix_exist = true;
	}
  $db->sql_freeresult($result);         
  
	return $prefix_exist; 
}

function load_locations()
{
  global $db, $template, $user, $phpEx, $phpbb_root_path;
  
  $sql =  'SELECT location_id, location_name
						FROM  ' . CLASSIFIEDS_LOCATIONS_TABLE . '
              ORDER BY location_name ASC';
	$result = $db->sql_query($sql);
  $location_exist = false;

	while($row = $db->sql_fetchrow($result))
	{
	  $template->assign_block_vars('locations',array(
			'ID'         =>  $row['location_id'],
			'NAME'       =>  $row['location_name'],
		));
		
		$location_exist = true;
	} 
  $db->sql_freeresult($result);           
	
	return $location_exist; 
}

function get_username($user_id)
{
	global $db;

	$sql = 'SELECT username
            FROM ' . USERS_TABLE . '
						  WHERE user_id = ' . $user_id;
	$result = $db->sql_query($sql);
  $username = $db->sql_fetchfield('username');
	$db->sql_freeresult($result);

	return $username;
}

function load_rules($rules_id)
{
  global $db, $template, $phpEx, $user, $phpbb_root_path;
  
  $sql =  'SELECT rules_title, rules_text, display_rules, must_agree, display_as_link
  					FROM  ' . CLASSIFIEDS_RULES_TABLE . '
              WHERE rules_id = ' . $rules_id;
  $result = $db->sql_query($sql);
  $row = $db->sql_fetchrow( $result );
  
  $uid = $bitfield = $options = '';
  $allow_bbcode	= $allow_smilies	= $allow_urls	= true;
  
  generate_text_for_storage($row['rules_text'], $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
  $rules_text	= generate_text_for_display($row['rules_text'], $uid, $bitfield, $options);
  
  $template->assign_vars(array(
  	'S_RULES_DISPLAY'            =>  ($row['display_rules'] && !empty($row['rules_text'])) ? true : false,
  	'S_RULES_TITLE'              =>  $row['rules_title'],
  	'S_RULES_TEXT'               =>  $rules_text,
  	'S_RULES_AS_LINK'            =>  $row['display_as_link'],
  	'S_MUST_AGREE'               =>  $row['must_agree'],
	  'L_CL_AGREE_WITH_RULES_LINK'    =>  $user->lang('CL_AGREE_WITH_RULES_LINK', append_sid("{$phpbb_root_path}".CL_DIRECTORY."/rules.$phpEx", "rules_id=$rules_id")),
  	'L_CL_LINK_TO_RULES'            =>  $user->lang('CL_LINK_TO_RULES', append_sid("{$phpbb_root_path}".CL_DIRECTORY."/rules.$phpEx", "rules_id=$rules_id"), ($rules_id == '3') ? 'target="_blank"' : '',$row['rules_title']),
  ));  
  
  $db->sql_freeresult($result); 
  
  return $row['must_agree']; 
}

function format_date($timestamp)
{
	return date('D, d M Y H:i:s O', $timestamp);
}

function display_recent_ads($limit)
{
	global $config, $db, $template, $phpbb_root_path, $phpEx, $user;
	
	$sql_ary =  array(
		'SELECT'	=> 'a.ad_id, a.ad_title, a.ad_poster_id, a.ad_price, a.ad_price_text, a.thumb, a.paypal_currency, u.username, u.user_colour, c.name',
		'FROM'		=> array(
			CLASSIFIEDS_TABLE				=> 'a',
		),
		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(USERS_TABLE => 'u'),
				'ON'	=> 'u.user_id = a.ad_poster_id',
			),
			array(
				'FROM'	=> array(CLASSIFIEDS_CATEGORY_TABLE => 'c'),
				'ON'	=> 'a.cat_id = c.id',
			)
		),
		'WHERE'		=> 'a.ad_status = ' . ACTIVE . ' AND a.ad_expire > '.time(),
		'ORDER_BY'	=> 'a.ad_date DESC'
	);

	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result	= $db->sql_query_limit($sql, $limit);

	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('ad' ,array(
   		'AD_TITLE'		=>	truncate_string($row['ad_title'], 50),
			'AD_PRICE'		=>	($row['ad_price_text'] == 0) ? str_replace('.00', '', $row['ad_price']) . ' ' . $row['paypal_currency'] : $user->lang['CL_BY_AGREEMENT'],
			'AD_POSTER'		=>	get_username_string('full', $row['ad_poster_id'], $row['username'], $row['user_colour']),
			'THUMB'				=>  $row['thumb'],
			'AD_LINK' 		=>	append_sid($phpbb_root_path . CL_DIRECTORY . '/single_ad.' . $phpEx ,'ad_id='.$row['ad_id']),
			'THUMB'				=>  $row['thumb'],
			'CATEGORY'		=>  $row['name'],
		));
	}
	$db->sql_freeresult($result);
}

function display_random_ads($limit)
{
	global $config, $db, $template, $phpbb_root_path, $phpEx, $user;

	$sql_ary =  array(
		'SELECT'	=> 'a.ad_id, a.ad_title, a.ad_poster_id, a.ad_price, a.ad_price_text, a.thumb, a.paypal_currency, u.username, u.user_colour, c.name',
		'FROM'		=> array(
			CLASSIFIEDS_TABLE				=> 'a',
		),
		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(USERS_TABLE => 'u'),
				'ON'	=> 'u.user_id = a.ad_poster_id',
			),
			array(
				'FROM'	=> array(CLASSIFIEDS_CATEGORY_TABLE => 'c'),
				'ON'	=> 'a.cat_id = c.id',
			)
		),
		'WHERE'		=> 'a.ad_status = ' . ACTIVE . ' AND a.ad_expire > '.time(),
		'ORDER_BY'	=> 'RAND()'
	);

	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result	= $db->sql_query_limit($sql, $limit);

	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('rand_ad' ,array(
   		'AD_TITLE'		=>	truncate_string($row['ad_title'], 50),
			'AD_PRICE'		=>	($row['ad_price_text'] == 0) ? str_replace('.00', '', $row['ad_price']) . ' ' . $row['paypal_currency'] : $user->lang['CL_BY_AGREEMENT'],
			'AD_POSTER'		=>	get_username_string('full', $row['ad_poster_id'], $row['username'], $row['user_colour']),
			'THUMB'				=>  $row['thumb'],
			'AD_LINK' 		=>	append_sid($phpbb_root_path . CL_DIRECTORY . '/single_ad.' . $phpEx ,'ad_id='.$row['ad_id']),
			'THUMB'				=>  $row['thumb'],
			'CATEGORY'		=>  $row['name'],
		));
	}
	$db->sql_freeresult($result);
}

function display_advertisers_ads($limit, $advertiser_id, $ad_id)
{
	global $config, $db, $template, $phpbb_root_path, $phpEx, $user;

	$sql_ary =  array(
		'SELECT'	=> 'a.ad_id, a.ad_title, a.ad_poster_id, a.cat_id, a.ad_price, a.ad_price_text, a.thumb, a.paypal_currency, u.username, u.user_colour, c.name',
		'FROM'		=> array(
			CLASSIFIEDS_TABLE				=> 'a',
		),
		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(USERS_TABLE => 'u'),
				'ON'	=> 'u.user_id = ' . $advertiser_id,
			),
			array(
				'FROM'	=> array(CLASSIFIEDS_CATEGORY_TABLE => 'c'),
				'ON'	=> 'a.cat_id = c.id',
			)                                           
		),
		'WHERE'		=> 'a.ad_poster_id = ' . $advertiser_id . ' AND a.ad_id != ' . $ad_id . ' AND a.ad_status = ' . ACTIVE . ' AND a.ad_expire > '.time(),
		'ORDER_BY'	=> 'RAND()'
	);

	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result	= $db->sql_query_limit($sql, $limit);

	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('advertisers_ad' ,array(
   		'AD_TITLE'		=>	truncate_string($row['ad_title'], 50),
			'AD_PRICE'		=>	($row['ad_price_text'] == 0) ? str_replace('.00', '', $row['ad_price']) . ' ' . $row['paypal_currency'] : $user->lang['CL_BY_AGREEMENT'],
			'AD_POSTER'		=>	get_username_string('full', $row['ad_poster_id'], $row['username'], $row['user_colour']),
			'THUMB'				=>  $row['thumb'],
			'AD_LINK' 		=>	append_sid($phpbb_root_path . CL_DIRECTORY . '/single_ad.' . $phpEx ,'ad_id='.$row['ad_id']),
			'THUMB'				=>  $row['thumb'],
			'CATEGORY'		=>  $row['name'],
		));
	}
	$db->sql_freeresult($result);
}

function display_hot_ads($limit)
{
	global $config, $db, $template, $phpbb_root_path, $phpEx, $user;

	$sql_ary =  array(
		'SELECT'	=> 'a.ad_id, a.ad_title, a.ad_views, a.ad_poster_id, a.ad_price_text, a.cat_id, a.ad_price, a.thumb, a.paypal_currency, u.username, u.user_colour, c.name',
		'FROM'		=> array(
			CLASSIFIEDS_TABLE				=> 'a',
		),
		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(USERS_TABLE => 'u'),
				'ON'	=> 'u.user_id = a.ad_poster_id',
			),
			array(
				'FROM'	=> array(CLASSIFIEDS_CATEGORY_TABLE => 'c'),
				'ON'	=> 'a.cat_id = c.id',
			)
		),
		'WHERE'		=> 'a.ad_status = ' . ACTIVE . ' AND a.ad_expire > '.time(),
		'ORDER_BY'	=> 'a.ad_views DESC'
	);

	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result	= $db->sql_query_limit($sql, $limit);

	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('hot_ad' ,array(
   		'AD_TITLE'		=>	truncate_string($row['ad_title'], 50),
   		'AD_VIEWS'		=>	$row['ad_views'],
			'AD_PRICE'		=>	($row['ad_price_text'] == 0) ? str_replace('.00', '', $row['ad_price']) . ' ' . $row['paypal_currency'] : $user->lang['CL_BY_AGREEMENT'],
			'AD_POSTER'		=>	get_username_string('full', $row['ad_poster_id'], $row['username'], $row['user_colour']),
			'THUMB'				=>  $row['thumb'],
			'AD_LINK' 		=>	append_sid($phpbb_root_path . CL_DIRECTORY . '/single_ad.' . $phpEx ,'ad_id='.$row['ad_id']),
			'THUMB'				=>  $row['thumb'],
			'CATEGORY'		=>  $row['name'],
		));
	}
	$db->sql_freeresult($result);
}        

function display_profile_ads($limit, $advertiser_id)
{
	global $config, $db, $template, $phpbb_root_path, $phpEx, $user;
	
	$sql_ary =  array(
		'SELECT'	=> 'a.ad_id, a.ad_title, a.ad_price, a.ad_price_text, a.thumb, a.paypal_currency, c.name',
		'FROM'		=> array(
			CLASSIFIEDS_TABLE				=> 'a',
		),
		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(CLASSIFIEDS_CATEGORY_TABLE => 'c'),
				'ON'	=> 'a.cat_id = c.id',
			)
		),
		'WHERE'		=> 'a.ad_status = ' . ACTIVE . ' AND a.ad_expire > ' . time() . ' AND a.ad_poster_id = ' . $advertiser_id,
		'ORDER_BY'	=> 'a.ad_date DESC'
	);

	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result	= $db->sql_query_limit($sql, $limit);

	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('profile_ad' ,array(
   		'AD_TITLE'		=>	truncate_string($row['ad_title'], 50),
			'AD_PRICE'		=>	($row['ad_price_text'] == 0) ? str_replace('.00', '', $row['ad_price']) . ' ' . $row['paypal_currency'] : $user->lang['CL_BY_AGREEMENT'],
			'THUMB'				=>  $row['thumb'],
			'AD_LINK' 		=>	append_sid($phpbb_root_path . CL_DIRECTORY . '/single_ad.' . $phpEx ,'ad_id='.$row['ad_id']),
			'THUMB'				=>  $row['thumb'],
			'CATEGORY'		=>  $row['name'],
		));
	}
	$db->sql_freeresult($result);
	
	$template->assign_vars(array(
    'PROFILE_AD_BLOCK_WIDTH'	   => ($config['profile_num_last_ads'] != 0) ? floor(100 / $config['profile_num_last_ads']) : '',
  )); 
	
}

function send_mail_to_ad_poster($user_lang, $user_email, $username, $user_jabber, $user_notify_type, $ad_title, $expire)
{
  global $messenger, $user, $config;
  
	$messenger = new messenger();
	$messenger->template('cl_new_ad', $user_lang);
	$messenger->to($user_email, $username);
	$messenger->im($user_jabber, $username);

	$messenger->assign_vars(array(
  	'USERNAME'    	 => $username,
  	'TITLE'			 => $ad_title,
  	'EXPIRE_DATE'    => $user->format_date($expire),
  	'SITE_NAME'		 => $config['sitename'],
	));

	$messenger->send($user_notify_type);
	$messenger->save_queue();
}

function send_mail_to_subscribers($user_lang, $user_email, $username, $user_jabber, $user_notify_type, $ad_title, $price, $advertisement_id, $short_desc, $ad_link)
{
  global $messenger, $user, $config, $phpEx;
  
  $messenger = new messenger();
	$messenger->template('cl_notify', $user_lang);
	$messenger->to($user_email, $username);
	$messenger->im($user_jabber, $username);

	$messenger->assign_vars(array(
 		'USERNAME'    	 => $username,
 		'TITLE'			 => $ad_title,
 		'POSTER'		 => $user->data['username'],
		'SITE_NAME'		 => $config['sitename'],
 		'PRICE'			 => $price,
 		'AD_LINK'		 => $ad_link,
 		'DESCRIPTION'	 => $short_desc,
	));

	$messenger->send($user_notify_type);
	$messenger->save_queue();
}        
                                                                                                                     
function send_mail_after_expiration($user_lang, $user_email, $username, $user_jabber, $ad_id, $ad_title, $ad_date, $ad_price, $ad_expire, $user_notify_type)
{
  global $config, $messenger, $template, $auth, $phpEx, $user;
  
	$messenger = new messenger();
	$template = ($auth->acl_get('u_can_extend_classifieds')) ? "cl_ad_expired_user_extend" : "cl_ad_expired";
	$messenger->template($template, $user_lang);
 	$messenger->to($user_email, $username);
  $messenger->im($user_jabber, $username);
  
  $ad_link = generate_board_url() . "/" . CL_DIRECTORY . "/single_ad.$phpEx?ad_id=$ad_id";
  $extend_ad = generate_board_url() . "/" . CL_DIRECTORY . "/manage_ad.$phpEx?mode=extend_ad&amp;ad_id=$ad_id";

	$messenger->assign_vars(array(
   	'USERNAME'   	=> $username,
    'TITLE'			=> $ad_title,
    'AD_DATE'		=> $user->format_date($ad_date),
		'PRICE'			=> $ad_price,
    'EXPIRE_DATE'   => $user->format_date($ad_expire),
    'SITE_NAME'		=> $config['sitename'],
    'AD_LINK'			=> $ad_link,
    'EXTEND_AD'  => $extend_ad,
	));
  	
  $messenger->send($user_notify_type);
	$messenger->save_queue();
}

function send_mail_notify_comment($user_lang, $user_email, $username, $user_jabber, $ad_id, $user_notify_type)
{
  global $messenger, $config, $phpEx;
  
  $messenger = new messenger();
  
  $messenger->template('cl_comment', $user_lang);
  $messenger->to($user_email, $username);
  $messenger->im($user_jabber, $username);
  
  $messenger->assign_vars(array(
  	'USERNAME'     => $username,
  	'SITE_NAME'    => $config['sitename'],
  	'AD_LINK'      => generate_board_url() . "/" . CL_DIRECTORY . "/single_ad.$phpEx" . "?ad_id=" . $ad_id,
  	'SIGNATURE'    => $config['board_email_sig'],	
  ));
  
  $messenger->send($user_notify_type);
  $messenger->save_queue();	
}

?>