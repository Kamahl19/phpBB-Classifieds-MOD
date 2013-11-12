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
* DO NOT CHANGE
*/

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}
// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
  'ACP_CLASSIFIEDS'                    => 'Classifieds MOD',

  'ACP_CLASSIFIEDS_INDEX_TITLE'        => 'Classifieds control',
    
  'GENERAL_SETTINGS'				        	 => 'General settings',
	'ENABLE_CLASSIFIEDS'			        	 =>	'Enable classifieds',
	'ENABLE_CLASSIFIEDS_EXPLAIN'	    	 =>	'Turn classifieds on or off.',
	'DISABLE_MESSAGE'				        	   =>	'Disabled message',
	'DISABLE_MESSAGE_EXPLAIN'			       =>	'Set the message to display when the classifieds are set to off.',
	'ALLOW_COMMENTS'			        		   =>	'Allow commenting',
	'ALLOW_COMMENTS_EXPLAIN'		       	 =>	'Allow users to comment on Advertisements.',
	'ENABLE_WATCH_CAT'			      		   =>	'Allow subscribing categories',
	'ENABLE_WATCH_CAT_EXPLAIN'	      	 =>	'Allow users to subscribe categories to receive PM / E-mail about new Ads.',
	'ALLOW_CLASSIFIEDS_FEEDS'				     =>	'Allow RSS feeds from classifieds',
	'ALLOW_CLASSIFIEDS_FEEDS_EXPLAIN'		 =>	'Allow users to subscribe to RSS channels from classifieds.',
	'NUMBER_AD_FEEDS'				          	 =>	'Number of ads in RSS',
	'NUMBER_AD_FEEDS_EXPLAIN'	         	 =>	'Set how much ads will show in RSS channel.',
	'ALLOW_ADDTHIS_BUTTON'				       => 'Display the sharing button',
	'ALLOW_ADDTHIS_BUTTON_EXPLAIN'			 => 'Display the "Addthis" button by which users can share the ads on their social networks.',
	'NUMBER_ADS'						             =>	'Number of ads to display',
 	'NUMBER_ADS_EXPLAIN'				         =>	'Number of ad\'s to display on each page.',
	'CLOSED_COLOR'						           =>	'Closed color',
	'CLOSED_COLOR_EXPLAIN'			      	 =>	'When a ad is marked closed it will show up in this color<br /> Example : <font color="#FFE4B5">#FFE4B5</font> or a color name like <font color="red">red</font>.',
	'SOLD_COLOR'						             =>	'Sold color',
	'SOLD_COLOR_EXPLAIN'			           =>	'When a ad is marked sold it will show up in this color <br /> Example : <font color="#ECD5D8">#ECD5D8</font> or a color name like <font color="red">red</font>.',

  'EXPIRATION_SETTINGS'					       => 'Expiration settings',
  'NUMBER_EXPIRE'					             => 'Number of days the ad should show',
 	'NUMBER_EXPIRE_EXPLAIN'				       =>	'Set the number of days ads should show up for. When they will expire they will not be deleted but rather removed from active ads and sorted into the expired ads.',
  'ALLOW_USERS_SET_EXPIRATION'				 =>	'Allow users to set expiration',
  'ALLOW_USERS_SET_EXPIRATION_EXPLAIN' =>	'Allow users to set the date when their ad will expire and wont be active, instead of the default date set above.',
  'MIN_EXPIRATION_BY_USER'				     =>	'Minimum number of active days',
  'MIN_EXPIRATION_BY_USER_EXPLAIN'		 =>	'If you allowed users to set their own date for expiration, you have to set also the minimum number of days when their ad will be active. Set to 0 for unlimited days.',
  'MAX_EXPIRATION_BY_USER'				     =>	'Maximum number of active days',
  'MAX_EXPIRATION_BY_USER_EXPLAIN'		 =>	'If you allowed users to set their own date for expiration, you have to set also the maximum number of days when their ad will be active. Set to 0 for unlimited days.',

  'REQUIRE_SETTINGS'					         => 'Requirements settings',
  'REQUIRED_POSTS_TO_CREATE'					 =>	'Required posts to create ad',
	'REQUIRED_POSTS_TO_CREATE_EXPLAIN'	 => 'Set the required posts which new users need to create new ads. If set 0, users do not need any posts to create an ad.',
  'REQUIRED_POSTS_TO_VIEW'						 =>	'Required posts to view ads',
	'REQUIRED_POSTS_TO_VIEW_EXPLAIN'		 => 'Set the required posts which users need to view ads. If set 0, users do not need any posts to view ads.',


  'FIELDS_SETTINGS'				             => 'Creating ad settings',
	'MANDATORY_PHONE'					           => 'Make an Phone field mandatory',
	'MANDATORY_PHONE_EXPLAIN'			       => 'Set if the Phone field is mandatory or not.',
	'ALLOW_UPLOAD'					             =>	'Allow Image Uploading',
	'ALLOW_UPLOAD_EXPLAIN'				       =>	'If enabled users will be able to upload an image to your server.',
	'MAX_IMG_SIZE'					             =>	'Max image filesize',
	'MAX_IMG_SIZE_EXPLAIN'				       =>	'Max filesize for uploaded images in kilobytes.',
	'DEFAULT_CURRENCY'						       =>	'Default currency',
	'DEFAULT_CURRENCY_EXPLAIN'					 =>	'Set default currency',
	'MAX_IMAGES_PER_AD'					         =>	'Max images per Ad',
	'MAX_IMAGES_PER_AD_EXPLAIN'				   =>	'Max number of images uploaded in one Ad. Set 0 for unlimited uploading.',

	'EMAIL_SETTINGS'					           => 'Email settings',
	'EMAIL_NEW_AD'						           =>	'Email on new Ad',
	'EMAIL_NEW_AD_EXPLAIN'				       =>	'Send an email to the ad poster upon new ad, will tell them when there ad will expire and explain how to use it more. ',
	'EMAIL_EXPIRED'						           => 'Email on expired ad',
	'EMAIL_EXPIRED_EXPLAIN'				       =>	'Send an email to the ad poster when there ad has expired, will explain how to get the ad revived if needed and will only send if the ad is marked Active.',


  'ACP_CLASSIFIEDS_MANAGE_TITLE'       => 'Classifieds management',

	'ACTIVE_ADS'			                   => 'Active ads',
	'CLOSED_ADS'			                   => 'Closed ads',
	'SOLD_ADS'			                     => 'Sold ads',
	'EXPIRED_ADS'			                   => 'Expired ads',
	'EXPIRED_ADS_EXPLAIN'	               => 'Below is a list of expired classifieds ads. <b>If the ad is highlighted in red, it is either Closed or Sold and should be deleted.</b>',
  'TOTAL_ADS'						             	 =>	'%s ads',
  'NO_ADS'							               =>	'No ads',
	'TITLE'							               	 =>	'Title & Category',
 	'DATE'								               =>	'Date',
 	'POST_DATE'								           =>	'Post date',
	'EXPIRE_DATE'			                   => 'Expire date',
	'SET_DAYS'				                   => 'Set the number of days to extend the ad for.',
	'VIEW_ALL'								           =>	'View all',
	'RED_LINE_DESC'                      => '<span style="color:#C95A5A;">*</span> If the expired ad is highlighted in <span style="color:#C95A5A;">red</span>, it is either Closed or Sold and should be deleted.',
  'DELETE_CONFIRM'	                   =>	'Are you sure you want to delete this ad?',
  'EXTEND_CONFIRM'	                   =>	'Are you sure you want to extend this ad?',
  'ADS'				                         =>	'ads',


	'ACP_CLASSIFIEDS_PURGE_TITLE'       => 'Purge ads',

	'PURGE_EXPIRED_SOLD_CLOSED_ADS'			=> 'Purge sold & closed ads, which have expired',
  'PURGE_EXPIRED_SOLD_CLOSED_ADS_EXPLAIN' => 'This is the <b>most recommended</b> option of purging ads. This will delete all closed and sold ads, which have expired.',
  'PURGE_EXPIRED_SOLD_CLOSED_ADS_CONFIRM' => 'Are you sure you wish to purge all closed and sold ads, which have expired?',
	'PURGE_CLOSED_ADS'			            => 'Purge closed ads',
	'PURGE_CLOSED_ADS_EXPLAIN'		      => 'This will delete all closed ads (expired and unexpired).',
	'PURGE_CLOSED_ADS_CONFIRM'			    => 'Are you sure you wish to purge all closed ads?',
	'PURGE_SOLD_ADS'			              => 'Purge sold ads',
	'PURGE_SOLD_ADS_EXPLAIN'		        => 'This will delete all sold ads (expired and unexpired).',
	'PURGE_SOLD_ADS_CONFIRM'			      => 'Are you sure you wish to purge all sold ads?',
	'PURGE_EXPIRED_ADS'			            => 'Purge expired ads',
	'PURGE_EXPIRED_ADS_EXPLAIN'		      => 'This will delete all expired ads (active, closed and sold).',
	'PURGE_EXPIRED_ADS_CONFIRM'			    => 'Are you sure you wish to purge all expired ads?',
	'PURGE_ACTIVE_ADS'			            => 'Purge active ads',
	'PURGE_ACTIVE_ADS_EXPLAIN'		      => 'This is <b>not recommended</b> option of purging ads. This will delete all active ads.',
	'PURGE_ACTIVE_ADS_CONFIRM'			    => 'Are you sure you wish to purge all active ads?',
	'PURGED_SUCCESFULLY'                => 'Ads were purged successfully.',


	'ACP_CLASSIFIEDS_CATS_TITLE'        => 'Categories management',
	
 	'CATEGORIES'						            => 'Categories',
	'CATEGORIES_EXPLAIN'				        => 'Enter a category title.',
  'PARENT'							              => 'Parent :',
  'PARENTS_EXPLAIN'		                => 'After setting an ad to a parent you must then sort it into the correct parent in the list below using the up and down arrows.',
	'NO_CATEGORY'						            => 'No category under the selected id!',
	'PARENT_ID'							            => 'Parent ID :',
	'PARENT_EXPLAIN'					          => '(1 = yes or 0 = no)',
	'DELETE_CAT_CONFIRM'	              => 'Are you sure you want to delete this category?',
  'DELETE_CAT_EXPLAIN'			          => 'Choose the category to move the ads to. If you want to delete also all ads in this category, leave it blank or write 0.',
	'DELETE_CAT_EXPLAIN2'			          => 'Move ads to category (id).',
	'ID'			                          => 'id',
	'PURGE_CAT'			                    => 'Purge cat',
	'PURGE_CAT_CONFIRM'	                => 'Are you sure you wish to purge this category (delete all ads)?',
	
	
  'ACP_CLASSIFIEDS_BLOCKS_TITLE'      => 'Ads blocks management',
  
	'RECENT_ADS_BLOCK'					        => 'Recent ads block on index',
	'DISPLAY_ADS_ON_INDEX'						  => 'Display Recent ads block on index',
	'DISPLAY_ADS_ON_INDEX_EXPLAIN'			=> 'Show newest advertisements on the index of board.',
	'RECENT_ADS_PLACE'						      => 'Display Recent ads block on top or bottom',
	'RECENT_ADS_PLACE_EXPLAIN'				  => 'Set where Recent ads block should be shown.',
	'TOP'						                    => 'Top',
	'BOTTOM'					  	              => 'Bottom',
	'NUMBER_ADS_ON_INDEX'						    => 'Number of Recent ads displayed on index',
	'NUMBER_ADS_ON_INDEX_EXPLAIN'				=> 'Set number of Recent ads displayed on index.',

	'RANDOM_ADS_BLOCK'					        => 'Random ads block on index',
	'DISPLAY_RAND_ADS_ON_INDEX'					=> 'Display Random ads block on index',
	'DISPLAY_RAND_ADS_ON_INDEX_EXPLAIN'	=> 'Show random advertisements on the index of board.',
	'RAND_ADS_PLACE'						        => 'Display Random ads block on top or bottom',
	'RAND_ADS_PLACE_EXPLAIN'				    => 'Set where Random ads block should be shown.',
	'NUMBER_RAND_ADS_ON_INDEX'					=> 'Number of Random ads displayed on index',
	'NUMBER_RAND_ADS_ON_INDEX_EXPLAIN'  => 'Set number of Random ads displayed on index.',

	'RANDOM_ADS_CLASSIFIEDS_SETTINGS'		=> 'Random ads block on Classifieds index',
	'DISPLAY_RAND_ADS_ON_MOD_INDEX'     => 'Display Random ads block on Classifieds index',
	'DISPLAY_RAND_ADS_ON_MOD_INDEX_EXPLAIN'  => 'Show random advertisements on the Classifieds index.',
	'RAND_ADS_LEFT'						          => 'Display Random ads block on left or bottom',
	'RAND_ADS_LEFT_EXPLAIN'				      => 'Set where Random ads block should be shown.',
	'LEFT'                              => 'Left',
	'NUMBER_RAND_ADS_ON_MOD_INDEX'      => 'Number of Random ads displayed on Classifieds index',
	'NUMBER_RAND_ADS_ON_MOD_INDEX_EXPLAIN' => 'Set number of Random ads displayed on Classifieds index.',
	
	'ADVERTISERS_ADS_CLASSIFIEDS_SETTINGS' => 'Advertiser\'s other ads block on single ad page',
	'DISPLAY_ADVERTISERS_ADS'						=> 'Display Advertiser\'s ads block',
	'DISPLAY_ADVERTISERS_ADS_EXPLAIN'		=> 'Show Advertiser\'s ads block on single ad page.',
	'ADVERTISERS_BLOCK_PLACE'						=> 'Display Advertiser\'s ads on left or bottom',
	'ADVERTISERS_BLOCK_PLACE_EXPLAIN'		=> 'Set where Advertiser\'s ads should be shown.',
	'ADVERTISERS_ADS_NUM'						    => 'Number of Advertiser\'s ads displayed on single ad page',
	'ADVERTISERS_ADS_NUM_EXPLAIN'				=> 'Set number of Advertiser\'s ads displayed on single ad page.',
	
	'HOT_ADS_CLASSIFIEDS_SETTINGS'			=> 'Hot ads (most viewed ads) block on Classifieds index',
	'DISPLAY_HOT_ADS'						        => 'Display Hot ads block',
	'DISPLAY_HOT_ADS_EXPLAIN'				    => 'Show Hot ads block on Classifieds index.',
	'HOT_BLOCK_PLACE'						        => 'Display Hot ads on left or bottom',
	'HOT_BLOCK_PLACE_EXPLAIN'				    => 'Set where Hot ads should be shown.',
	'HOT_ADS_NUM'						            => 'Number of Hot ads displayed on Classifieds index',
	'HOT_ADS_NUM_EXPLAIN'				        => 'Set number of Hot ads displayed on Classifieds index.',
	
	'PROFILE_LAST_ADS_SETTINGS' =>  'User\'s recent ads block on his profile',
	'DISPLAY_PROFILE_LAST_ADS' =>  'Display User\'s recent ads block',
	'DISPLAY_PROFILE_LAST_ADS_EXPLAIN' =>  'Show User\'s recent ads block on his profile page.',
	'PROFILE_LAST_ADS_NUM' =>  'Number of User\'s recent ads displayed on his profile page',
	'PROFILE_LAST_ADS_NUM_EXPLAIN' =>  'Set number of User\'s recent ads displayed on his profile page.',
	
	
  'ACP_CLASSIFIEDS_RULES_TITLE'       => 'Trading rules managment',
  
  'ACP_CLASSIFIEDS_RULES1'            => 'General trading rules',
  'RULES_TITLE1'                      => 'Title of General trading rules',
  'RULES_DISPLAY1'                    => 'Display General trading rules on classified index page',
  'MUST_AGREE1'                       => '-',
  'MUST_AGREE_EXPLAIN1'               => '-',
  'DISPLAY_AS_LINK1'                  => 'Display General trading rules only as link',
  'DISPLAY_AS_LINK_EXPLAIN1'          => 'If set No, full text of rules will be displayed. If set Yes, display only link to text of rules.',
  
  'ACP_CLASSIFIEDS_RULES2'            => 'Rules for buyer',
  'RULES_TITLE2'                      => 'Title of Rules for buyer',
  'RULES_DISPLAY2'                    => 'Display Rules for buyer while viewing an ad',
  'MUST_AGREE2'                       => '-',
  'MUST_AGREE_EXPLAIN2'               => '-',
  'DISPLAY_AS_LINK2'                  => 'Display Rules for buyer only as link',
  'DISPLAY_AS_LINK_EXPLAIN2'          => 'If set No, full text of rules will be displayed. If set Yes, display only link to text of rules.',
  
  'ACP_CLASSIFIEDS_RULES3'            => 'Rules for seller',
  'RULES_TITLE3'                      => 'Title of Rules for seller',
  'RULES_DISPLAY3'                    => 'Display Rules for seller while creating and editing ad',
  'MUST_AGREE3'                       => 'Seller have to agree with rules',
  'MUST_AGREE_EXPLAIN3'               => 'If set Yes, seller have to fill the checkbox that he agrees with the rules to create a new ad.',
  'DISPLAY_AS_LINK3'                  => 'Display Rules for seller only as link',
  'DISPLAY_AS_LINK_EXPLAIN3'          => 'If set No, full text of rules will be displayed. If set Yes, display only link to text of rules.',
  
	'DISPLAY_ON_CL'                     => 'Display on classified ad',
	'DISPLAY_ON_CL_EXPLAIN'             => 'If this option is enabled, the field will be displayed in the classified advertisement in the Classifieds MOD.',
	
	
	'ACP_CLASSIFIEDS_PREFIXES_TITLE'    => 'Prefixes management',
	
	'PREFIX_NAME'                       => 'Prefix name',
	'PREFIX_SHORT'                      => 'Prefix short',
	'PREFIX_SEARCH'                     => 'Search ads',
	'PREFIX_EDIT'                       => 'Edit prefix',
	'PREFIX_COLOR'                      => 'Prefix color',
	'SEARCH_PREFIX'                     => 'Search %s ads',
	'NO_PREFIXES'                       => 'You have not created any prefix yet',
	'DELETE_PREFIX_CONFIRM'             => 'Are you sure you want to delete this prefix?',
	'EDIT_PREFIX_CONFIRM'               => 'Are you sure you want to edit this prefix?',
	'ADD_PREFIX'                        => 'Add new prefix',
	'PREFIX_SETTINGS'                   => 'Prefix settings',
	'MANDATORY_AD_PREFIX'					      => 'Make an Ad prefix mandatory',
	'MANDATORY_AD_PREFIX_EXPLAIN'			  => 'Set if the user have to choose one of Ad prefixes below while creating a new Ad.',
	
	'ACP_CLASSIFIEDS_LOCATIONS_TITLE'   => 'Locations management',
	
	'LOCATION_NAME'                     => 'Location name',
	'LOCATION_SEARCH'                   => 'Search locations',
	'LOCATION_EDIT'                     => 'Edit location',
	'SEARCH_LOCATION'                   => 'Search %s locations',
	'NO_LOCATION'                       => 'You have not created any location yet',
	'DELETE_LOCATION_CONFIRM'           => 'Are you sure you want to delete this location?',
	'EDIT_LOCATION_CONFIRM'             => 'Are you sure you want to edit this location?',
	'ADD_LOCATION'                      => 'Add new location',
	'LOCATION_SETTINGS'                 => 'Location settings',
	'MANDATORY_AD_LOCATION'             => 'Make Ad location mandatory',
	'MANDATORY_AD_LOCATION_EXPLAIN'     => 'Set if the user have to choose his location from locations below while creating a new Ad.',
	'FILL_LOCATION_TO_TRADE'						=> 'Required Location field filled to create ad',
	'FILL_LOCATION_TO_TRADE_EXPLAIN'		=> 'Set if users have to fill their Location field in profile to create new ads.',
	'NO_LOCATIONS'                      => 'You have not added any location yet',
	'LOC_ON_NEW_LINE'                   => 'You can add multiple locations in one go by entering each name on a new line. ',
	                        
	                        
	'ACP_CLASSIFIEDS_CURRENCY_TITLE'    => 'Currency management',
	
	'CURRENCY_NAME'                     => 'Currency name',
	'CURRENCY_SHORT'                    => 'Currency short',
	'CURRENCY_EDIT'                     => 'Edit currency',
	'NO_CURRENCY'                       => 'You have not created any currency yet',
	'DELETE_CURRENCY_CONFIRM'           => 'Are you sure you want to delete this currency?',
	'EDIT_CURRENCY_CONFIRM'             => 'Are you sure you want to edit this currency?',
	'ADD_CURRENCY'                      => 'Add new currency',
	'CURRENCY_SETTINGS'                 => 'Currency settings',
	                    
                                      
  'IMG_BUTTON_POST_AD'                => 'New Ad',   
  'IMG_BUTTON_EXTEND_AD'              => 'Extended Ad',
));
?>