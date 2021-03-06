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
if (!defined('IN_PHPBB'))
{
    exit;
}

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
  'acl_u_view_classifieds'           => array('lang' => 'Can view Classifieds ads', 'cat'		=> 'classifieds'),
  'acl_u_post_classifieds'           => array('lang' => 'Can post new classifieds ads', 'cat'	=> 'classifieds'),
  'acl_u_edit_own_classifieds' 	     => array('lang' => 'Can edit own ads', 'cat'	=> 'classifieds'),
  'acl_u_can_delete_classifieds'	   => array('lang' => 'Can delete own ads', 'cat'	=> 'classifieds'),
  'acl_u_can_extend_classifieds'	   => array('lang' => 'Can extend his own expired ads', 'cat'	=> 'classifieds'),
  'acl_m_edit_classifieds'	         => array('lang' => 'Can edit ads', 'cat'	=> 'classifieds'),
  'acl_m_move_classifieds'	         => array('lang' => 'Can move ads', 'cat'	=> 'classifieds'),
  'acl_m_delete_classifieds'	       => array('lang' => 'Can delete ads', 'cat'	=> 'classifieds'),
  'acl_m_report_classifieds'	       => array('lang' => 'Can solve reported ads', 'cat'	=> 'classifieds'),
  'acl_u_can_report_classifieds'	   => array('lang' => 'Can report ads', 'cat'	=> 'classifieds'),
  'acl_m_extend_classifieds'	       => array('lang' => 'Can extend expired ads', 'cat'	=> 'classifieds'),
));

?>