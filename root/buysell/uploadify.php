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

include_once($phpbb_root_path . 'common.' . $phpEx);  
include_once($phpbb_root_path . CL_DIRECTORY . '/includes/functions_buysell.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$mode = request_var('mode', '');
$filename = request_var('filename', '');

$targetFolder = $phpbb_root_path . CL_DIRECTORY . '/images/';

switch($mode)
{
  case 'delete':
  
    $sql = 'DELETE FROM ' . CLASSIFIEDS_IMAGES_TABLE . '
              WHERE image_name = "' . $filename . '"';
    $db->sql_query($sql);
    
    @unlink($targetFolder . $filename);
    @unlink($targetFolder . 'thumb/' . $filename);
  
    break;
    
  default:

		if (!empty($_FILES))
    {
      $FiledataName = $_FILES['Filedata']['name'];
      $FiledataTmp_name = $_FILES['Filedata']['tmp_name'];
    
      $fileParts           = pathinfo($FiledataName);
    	$targetPath          = $targetFolder;
      $typesArray          = explode('| ', str_replace(';', '|', str_replace('*.', '', request_var('fileExts', ''))));
      $temp_id             = request_var('tempId', 0);
      $ad_id               = request_var('adId', 0);
      $time                = request_var('time', 0);

    	$targetFile          = str_replace('//', '/', $targetPath) . $fileParts['filename'] . '_d'  . $time . '.' . $fileParts['extension'];
    	$targetThumbFile     = str_replace('//', '/', $targetPath) . '/thumb/' . $fileParts['filename'] . '_'  . $time . '.' . $fileParts['extension'];
    	$targetResizedFile   = str_replace('//', '/', $targetPath) . $fileParts['filename'] . '_'  . $time . '.' . $fileParts['extension'];
    	$filename_to_db      = $fileParts['filename'] . '_'  . $time . '.' . $fileParts['extension'];

      if (in_array(strtolower($fileParts['extension']), $typesArray))
      {
        $total_images = 0;
				if ($config['max_images_per_ad'] > 0)
				{
				  if ($ad_id != 0)
	        {
						$sql = 'SELECT COUNT(ad_id) as total_images
											FROM ' . CLASSIFIEDS_IMAGES_TABLE . '
	                  		WHERE ad_id = ' . $ad_id;
	        }
	        else
	        {
						$sql = 'SELECT COUNT(temp_id) as total_images
											FROM ' . CLASSIFIEDS_IMAGES_TABLE . '
												WHERE temp_id = ' . $temp_id;
	        }
	        $db->sql_query($sql);
	        $total_images = $db->sql_fetchfield('total_images');
        }

        if ($config['max_images_per_ad'] == 0 || $total_images < $config['max_images_per_ad'])
        {
	        move_uploaded_file($FiledataTmp_name, $targetFile);

	        if ($ad_id != 0)
	        {
	          $sql = 'INSERT INTO ' . CLASSIFIEDS_IMAGES_TABLE . ' (image_name, ad_id)
	                    VALUES ("' . $filename_to_db . '", ' . $ad_id . ')';
	        }
	        else
	        {
	          $sql = 'INSERT INTO ' . CLASSIFIEDS_IMAGES_TABLE . ' (image_name, temp_id)
	                    VALUES ("' . $filename_to_db . '", ' . $temp_id . ')';
	        }
	        $db->sql_query($sql);

	        echo $filename_to_db;

	        resize_img($targetFile, $fileParts['extension'], $targetThumbFile, 100, 100);
	        resize_img($targetFile, $fileParts['extension'], $targetResizedFile, 800, 600);

	        @unlink($targetFile);
        }
        else
				{
					echo "10"; // Too many files
				}
      }
      else
      {
    	 	echo "20"; // Invalid extension
      }
    }
}

?>