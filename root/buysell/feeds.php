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

if (!$auth->acl_get('u_view_classifieds') || !$config['enable_classifieds'])
{
	trigger_error('NOT_AUTHORISED');
}

$mode = request_var('mode', '');
$chars = 128;
$now = time();
$feed_link = generate_board_url() . "/" . CL_DIRECTORY . "/feeds.$phpEx"."?mode=".$mode;

// Common feed header part
$rdf = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss version=\"2.0\"
xmlns:atom=\"http://www.w3.org/2005/Atom\"
xmlns:dc=\"http://purl.org/dc/elements/1.1/\">
	<channel>
		<atom:link href=\"$feed_link\" rel=\"self\" type=\"application/rss+xml\" />
		<title>".strip_tags($config['sitename'])."</title>
		<description>".strip_tags($config['site_desc'])."</description>
		<link>" . generate_board_url() . "</link>
		<lastBuildDate>" . format_date($now) . "</lastBuildDate>";

$sql_ary =  array(
	'SELECT'	=> 'a.ad_id, a.ad_date, a.ad_title, a.short_desc, a.cat_id, u.username, p.prefix_short',
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
	),
	'WHERE'		=> 'a.ad_status = ' . ACTIVE . ' AND a.ad_expire > ' . $now,
	'ORDER_BY'	=> 'a.ad_date DESC'
);

if (is_numeric($mode))
{
	$sql_ary['FROM']	= array(
			CLASSIFIEDS_TABLE				=> 'a',
			CLASSIFIEDS_CATEGORY_TABLE		=> 'c',);
	$sql_ary['WHERE']	= 'a.ad_poster_id = u.user_id AND c.id = a.cat_id and c.id = '. $mode .' AND a.ad_status = ' . ACTIVE . ' AND a.ad_expire > ' . $now;
}

$sql = $db->sql_build_query('SELECT', $sql_ary);
$result	 = $db->sql_query_limit($sql, $config['number_ad_feeds']);

while( $row = $db->sql_fetchrow($result) )
{
	$author = $row['username'];
	$time = format_date($row['ad_date']);
	$link = generate_board_url()."/".CL_DIRECTORY."/single_ad.$phpEx" ."?". 'ad_id=' . $row['ad_id'];
	$title = ($row['prefix_short']) ? '['.$row['prefix_short'].'] '.censor_text($row['ad_title']) : censor_text($row['ad_title']);
	$category = $user->lang['CL_IN_CAT'].' '.get_ad_category($row['cat_id']);
	$text = censor_text($row['short_desc']);
	
	$rdf .= "
		<item>
			<dc:creator>$author</dc:creator>
			<pubDate>$time</pubDate>
			<guid>$link</guid>
			<link>$link</link>
			<title>$title $category</title>
			<description>$text</description>
		</item>";
}

// Gzip compression
if ($config['gzip_compress'])
{
	if (@extension_loaded('zlib') && !headers_sent())
	{
		ob_start('ob_gzhandler');
	}
}

// RSS feed footer
$rdf .= "
	</channel>
</rss>";

header ('Content-Type: application/rss+xml; charset=UTF-8');

// Output the feed
echo($rdf);
?>