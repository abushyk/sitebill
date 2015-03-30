<?php
error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors','On');

session_start();
require_once("../../../inc/db.inc.php");
if(!defined('SITE_ENCODING')){
	define('SITE_ENCODING', 'windows-1251');
}
header('Content-Type: text/html; charset='.SITE_ENCODING);
$settings=parse_ini_file('../../../settings.ini.php',true);
if(isset($settings['Settings']['estate_folder'])AND($settings['Settings']['estate_folder']!='')){
$folder='/'.$settings['Settings']['estate_folder'];
}else{
$folder='';
}
$sitebill_document_root = $_SERVER['DOCUMENT_ROOT'].$folder;

define('SITEBILL_DOCUMENT_ROOT', $sitebill_document_root);
define('SITEBILL_MAIN_URL', $folder);
define('DB_PREFIX', $__db_prefix);

ini_set("include_path", $include_path );
require_once(SITEBILL_DOCUMENT_ROOT.'/third/smarty/Smarty.class.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/system/init.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/db/MySQL.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/sitebill.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/system/uploadify/uploadify.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/admin/object_manager.php');
require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/system/multilanguage/multilanguage.class.php');

$smarty = new Smarty;
Sitebill::setLangSession();
Multilanguage::start('backend',$_SESSION['_lang']);

require_once(SITEBILL_DOCUMENT_ROOT.'/apps/config/admin/admin.php');
$config_admin = new config_admin();
echo $config_admin->ajax();
?>