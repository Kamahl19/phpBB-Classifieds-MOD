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
class acp_classifieds_info
{
  function module()
  {
    return array(
      'filename'    => 'acp_classifieds',
      'title'        => 'ACP_CLASSIFIEDS',
      'version'    => '1.2.1',
      'modes'        => array(
      	'index'        => array('title' => 'ACP_CLASSIFIEDS_INDEX_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),
      	'cats'        => array('title' => 'ACP_CLASSIFIEDS_CATS_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),
      	'manage'        => array('title' => 'ACP_CLASSIFIEDS_MANAGE_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),
      	'purge'        => array('title' => 'ACP_CLASSIFIEDS_PURGE_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),
      	'blocks'        => array('title' => 'ACP_CLASSIFIEDS_BLOCKS_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),
      	'rules'        => array('title' => 'ACP_CLASSIFIEDS_RULES_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),
      	'prefixes'        => array('title' => 'ACP_CLASSIFIEDS_PREFIXES_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),
      	'locations'        => array('title' => 'ACP_CLASSIFIEDS_LOCATIONS_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),
      	'currency'        => array('title' => 'ACP_CLASSIFIEDS_CURRENCY_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),
      ),
    );
  }

  function install()
  {
  }

  function uninstall()
  {
  }
}
?>