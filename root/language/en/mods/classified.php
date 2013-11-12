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

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	// General
	'CL_CLASSIFIEDS'		          => 'Classifieds',
	'CL_RULES'							      => 'Classifieds rules',   
	'CL_AD_TITLE'							    => 'Ad title',
	'CL_AD_DESCRIPTION'				    => 'Description',
	'CL_AD_DATE'							    => 'Date',
	'CL_AD_LAST_EDIT'					    => '<i>Last edited on %1$s by %2$s</i>',
	'CL_LIST_AD'    				      => '1 ad',
	'CL_LIST_ADS'    			        => '%s ads',
	'CL_LIST_COMMENT'    	        => '1 comment',
	'CL_LIST_COMMENTS'    	      => '%s comments',
  'CL_NOT_ENOUGH_POSTS_TO_CREATE' => 'You have not enough posts to create a new ad',
	'CL_NOT_ENOUGH_POSTS_TO_VIEW' => 'You have not enough posts to view ads',
	'CL_EDIT_AD'						      => 'Edit this ad',
	'CL_REPORT_AD'					      => 'Report this ad',
	'CL_DELETE_AD'					      => 'Delete this ad',
	'CL_REPORTED_AD'				      => 'This advertisement has been reported',
	'CL_REPORTER'					        => 'Reported by',
	'CL_CLOSE_REPORT'			        => 'Close this report',
	'CL_WATCH_CAT'					      => 'Subscribe category',
	'CL_UNWATCH_CAT'				      => 'Unsubscribe category',
	'CL_CLASSIFIEDS_RSS'		      => 'RSS %s',
	'CL_FOR_CAT'					        => 'for category ',
	'CL_AD_REPORTED'				      => 'Reported ads',
	'CL_ADS'								      => 'ads',
	'CL_ADVERTISEMENTS'					  => 'Advertisements',
	'CL_AD_INFO'						      => 'Advertisement info',
	'CL_SELLER_INFO'				      => 'Advertiser info',
	'CL_RANDOM_AD'					      => 'Random ads',
	'CL_HOT_AD'						        => 'Hot ads',
	'CL_ADVERTISERS_ADS'		      => '\'s other ads',
	'CL_SELLER'						        => 'Seller',
	'CL_DOES_NOT_EXIST'		        => 'Ad does not exist',
	'CL_EMPTY_CAT'				        => 'This category is empty',
	'CL_AD_INFO'					        => 'Info',
	'CL_VIEW_ALL'				   	      => 'View all',
	'CL_ACTIVE_NEXT'			   	    => 'Active next',
	'CL_HAS_EXPIRED'              => 'This Ad has expired',
	'CL_PHONE_GUEST'				      => 'login to see the phone number',
	'CL_LOCATION_GUEST'		        => 'login to see the user\'s location',
	'CL_BUY_VIA_PAYPAL'		        => 'Buy via PayPal',
	'CL_IN_CAT'				            => 'in',
	'CL_FOR_ALL_ACTIVE_ADS'       => 'for all active ads',
	'CL_RSS'				              => 'RSS',
	'CL_LINK_TO_RULES'	          => '<a href="%1$s" %2$s>Read %3$s here</a>',
	'CL_CLASSIFIED_MANAGE_AD'     => 'Manage advertisement',
	'CL_CLASSIFIED_VIEW_AD'       => 'View advertisement',
	'CL_CLASSIFIED_RULES'         => 'View trading rules',
	'CL_NO_RULES'	                => 'No rules',
	'CL_VIEW_USERS_ADS'           => 'View user\'s ads',
	'CL_ADMIN_USERS_ADS'	   	    => 'Administrate user\'s ads',
	'CL_CHANGE_AD_STATUS'         => 'Change status',
	'CL_MAKE_ACTIVE_AD'           => 'Make Ad Active',
	'CL_MAKE_SOLD_AD'             => 'Make Ad Sold',
	'CL_MAKE_CLOSED_AD'           => 'Close this Ad',
	'CL_ACTIVE_AD'			          => 'active',
	'CL_SOLD_AD'			            => 'sold',
	'CL_CLOSED_AD'			          => 'closed',
	'CL_EXPIRE_AD'			          => 'expired',
	'CL_CLASSIFIEDS_ADS'		      => 'Classifieds',
	'CL_SEARCH_BY_PREFIX'         => '%s ads',    
	'CL_SEARCH_IN_PREFIXES'       => 'Search by prefix', 
  'CL_SEARCH_IN_LOCATIONS'      => 'Search by location',    
  'CL_CHOOSE_LOCATION'          => 'choose location',
  'CL_CHOOSE_PREFIX'            => 'choose prefix',
  'CL_SCHOOSE_CATEGORY'	        => 'choose category',
	'CL_SEARCH_RESULTS'           => 'Search results',
	'CL_SEARCH_ONLY_ACTIVE'       => 'search only active',
	'CL_FOR'                      => 'for',
	'CL_ORDER_TIME'               => 'order by time',
	'CL_ORDER_PRICE'              => 'order by price',
	'CL_ORDER_VIEWS'              => 'order by views',
	'CL_ORDER_DESCENDING'         => 'descending',
	'CL_ORDER_ASCENDING'          => 'ascending',
  	
	// Posting ad
	'CL_AD_TITLE'                 => 'Advertisement title',
	'CL_SHORT_DESC'               => 'Short description',
	'CL_SHORT_DESC_EXPLAIN'       => '(max 128 characters)',
	'CL_EMAIL_COMMENTS'           => 'Notify me by e-mail on new comments',
  'CL_CAT_NAME'                 => 'Category',
  'CL_DAYS_ACTIVE'              => 'Active days',
  'CL_DAYS_ACTIVE_EXPLAIN'      => 'Please set the number of days while this ad will be active. The Ad will expire after these days. Choose a number from the range of <b>%1$s</b> to <b>%2$s</b>.',
  'CL_PHONE'						        => 'Phone number',
	'CL_PRICE'						        => 'Price',
	'CL_PAYPAL_EMAIL'		          => 'Paypal e-mail',
	'CL_PAYPAL_EXPLAIN'	          => 'Enter your paypal e-mail if you want to do the transaction via paypal',
  'CL_CURRENCY'				          => 'Currency',
  'CL_AD_PREFIX'						    => 'Prefix',
	'CL_AD_LOCATION'						  => 'Your location',
  'CL_OBLIGATION_FIELDS'        => 'mandatory fields',
	'CL_ADDED_PERMISSIONS'        => 'You have successfully added classifieds permission options to your database.',
	'CL_DELETE_CONFIRM'			      => 'Are you sure you want to delete this ad?',
	'CL_EDIT_CONFIRM'				      => 'Are you sure you want to edit this ad?',
	'CL_DELETE_COMMENT_CONFIRM'   => 'Are you sure you want to delete this comment?',
	'CL_EXTEND_AD_CONFIRM'		    => 'Are you sure you want to extend this Ad?',
	'CL_MOVE_CLASSIFIED'			    => 'Change the category of ad',
	'CL_REPORT_CLASSIFIED'		    => 'Report this classified',
	'CL_REPORT_TEXT'					    => 'Reason',
	'CL_REPORT_TEXT_EXPLAIN'	    => 'Please, write why do you report this ad.',
	'CL_REPORT_CONFIRM'			      => 'Are you sure you want to report this ad?',
	'CL_CLOSE_REPORT_CONFIRM'     => 'Are you sure you want to close the report of this ad?',
	'CL_JUST_REPORTED'            => 'This advertisement has been reported yet.',
	'CL_NOT_REPORTED'             => 'This advertisement is not reported.',
	'CL_CANT_SOLVE_REPORTED'      => 'You are not allowed to list reported ads.',
	'CL_NO_AD_SELECTED'           => 'No ad selected',
	'CL_NOT_FILLED_LOCATION'      => 'You have to fill in your Location field in profile to create new ad',
	'CL_NO_REPORT_TEXT'           => 'Please, fill the reason field',
	'CL_CONTINUE'                 => 'Continue',
	'CL_EXTEND_CLASSIFIED'        => 'Extend this classified',
	'CL_MUST_AGREE'               => 'You have to agree to the terms',
	'CL_AGREE_WITH_RULES_LINK'    => 'I agree to these <a href="%s" target="_blank">terms</a>',
	'CL_AGREE_WITH_RULES'         => 'I agree to these terms',
	'CL_BY_AGREEMENT'             => 'by agreement',
	'CL_AD_PRICE_TEXT'            => 'Price by agreement',
	'CL_TOO_LONG'                 => 'Message is too long',
	
	// Left bar
  'CL_USER_CONTROLS'		        => 'User controls',
  'CL_CATEGORIES'			          => 'Categories',
	
	'CL_VIEW_ACTIVE'			        => 'View active ads',
	'CL_VIEW_REPORTED'            => 'View reported ads',
	                              
	'CL_MY_ADS'                   => 'My ads',
	'CL_VIEW_OWN_MY'              => 'My ',
	'CL_ACTIVE_ADS'               => 'Active ads',
	'CL_SOLD_ADS'				          => 'Sold ads',
	'CL_CLOSED_ADS'               => 'Closed ads',
	'CL_EXPIRED_ADS'              => 'Expired ads',

	// Ad status
	'CL_STATUS'                   => 'Status',
	'CL_NOT_SOLD'		              => 'Not sold',
	'CL_ACTIVE'				            => 'Active',
  'CL_SOLD'				              => 'Sold',
	'CL_CLOSED'                   => 'Closed',
	'CL_EXPIRED'			            => 'Expired',
	'CL_SINGLE_STATUS'            => 'Status',
	'CL_SINGLE_SOLD'			        => 'sold',
	'CL_SINGLE_CLOSED'            => 'closed',
	'CL_SINGLE_EXPIRED'           => 'expired',
	'CL_SINGLE_ACTIVE'            => 'active',
	
	// Buttons
	'CL_NEW_AD'                   => 'New ad',
	'CL_EXTEND'                   => 'Extend',
	'CL_BOOKMARK_AND_SHARE'       => 'Bookmark and Share',
	'CL_BUTTON_PAYPAL'            => 'en_GB',
  'CL_BUTTON_PAYPAL_ALT'        => 'PayPal - The safer, easier way to pay online.',
	
	// Errors
	'CL_NO_TITLE'                 => 'Please enter a title',
	'CL_NO_DESCRIPTION'           => 'Please enter a description',
	'CL_NO_SHORT_DESC'            => 'Please enter a short description',
	'CL_NO_PRICE'                 => 'Please enter a price',
	'CL_NO_PRICE_NUMERIC'         => 'Price has to be a number',
	'CL_AD_EXPIRED'               => 'Sorry this ad has expired, if you are the owner of this ad please contact the Board Administrator for more information.',
	'CL_CHOOSE_CATEGORY'	        => 'Please choose a category',
	'CL_BAD_MAIL'				          => 'Please enter a valid email',
	'CL_SET_NUM_DAYS'		          => 'Please enter a number of days while the ad will be active',
	'CL_SET_CORRECT_DAYS'         => 'Please enter a number of days within the allowed range',
	'CL_NO_PHONE'				          => 'Please enter a phone number',
	'CL_NO_PREFIX'                => 'Please choose an ad prefix',
	'CL_NO_LOCATION'              => 'Please choose your location',
	'CL_TOO_MANY_IMAGES'          => 'You can\'t upload more images for this Ad',
	
	// Image upload languages
	'CL_AD_UPLOAD_PHOTO'          => 'UPLOAD PHOTO',
	'CL_AD_IMAGES'                => 'Images',
	'CL_IMAGE_UPLOAD'             => 'Image uploader',
	'CL_SUPPORTED'                => 'Supported file types: gif, jpg, png',
	'CL_THUMB'                    => 'Thumbnail',
	'CL_THUMB_EXPLAIN'            => 'Add an URL of thumbnail or click on "<b>T</b>" after uploading an image',

  'CL_CLASSIFIEDS_COPYRIGHT'    => 'Powered by <a href="http://phpbb3hacks.com">Classifieds MOD</a> © 2010-2012 <a href="http://phpbb3hacks.com/memberlist.php?mode=viewprofile&u=2">Kamahl</a>',
));
?>