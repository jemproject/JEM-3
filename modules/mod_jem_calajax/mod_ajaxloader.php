<?php
/**
 * @package JEM
 * @subpackage JEM - Module-Calendar(AJAX)
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Bart Eversdijk. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * To use the Ajax-handler from a call with in a module use  (do NOT use JRoute::_() )  
 * $url = JURI::base().'modules/mod_'.$module->name.'/mod_ajaxloader.php?modid='.$module->id.'&Itemid='.$Itemid.'&<your-own-request-parameters>';
 *
 * To determine if your module was handled trough a AJAX request one can use 
 *
 * if (defined('_IN_AJAXCALL')) {}
 *
 */

 // Direct access is allowed for AJAX call handling -- Set up a Joomla! mockup environment first
 // TO DO: make sure this call was send by module it self (integrety check)
 define( '_JEXEC', 1 );
 define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../..' ));
 define( 'DS', DIRECTORY_SEPARATOR );
 define( '_IN_AJAXCALL', 1 );


// Make sure the BASE-path starts at the base of Joomla!
// Define this variable, because normal variable are cleared by the framework
define ('MODNAME', substr(dirname(__FILE__), strrpos(dirname(__FILE__), DS) + 1));
if (strpos(php_sapi_name(), 'cgi') !== false && !empty($_SERVER['REQUEST_URI']) &&
				    (!ini_get('cgi.fix_pathinfo') || version_compare(PHP_VERSION, '5.2.4', '<'))) {
    $_SERVER['PHP_SELF'] =  str_replace ('/modules/'.MODNAME, '', $_SERVER['PHP_SELF']);
} else {
    $_SERVER['SCRIPT_NAME'] =  str_replace ('/modules/'.MODNAME, '',$_SERVER['SCRIPT_NAME']);
}
require_once ( JPATH_BASE.'/includes/defines.php' );
require_once ( JPATH_BASE.'/includes/framework.php' );

jimport('joomla.language.language');

// $lng = JComponentHelper::getParams('com_languages')->get('site');

$mainframe 	= JFactory::getApplication('site');
$mainframe->initialise();

// Get the module properties and render the module in a minimal HTML page
$document	= JFactory::getDocument();
$renderer	= $document->loadRenderer('module');

$app = JFactory::getApplication();
$input = $app->input;
$Itemid = $input->get('Itemid','0');

$modid		= JRequest::getInt('modid', 0);
$wheremenu 	= isset( $Itemid ) ? ' AND ( mm.menuid = '. (int) $Itemid .' OR mm.menuid = 0 )' : '';

$user		= JFactory::getUser();
$db		= JFactory::getDBO();
// $aid		= $user->get('aid', 0);
$groups = implode(',', $user->getAuthorisedViewLevels());

$query = 'SELECT id, title, module, position, content, showtitle, params'
		. ' FROM #__modules AS m'
		. ' LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id'
		. ' WHERE m.published = 1'
		. ' AND m.id = '.(int)$modid
		. ' AND m.access IN( '.$groups.')'
		. ' AND m.client_id = '. (int)$mainframe->getClientId()
		. $wheremenu
		. ' ORDER BY position, ordering';
$db->setQuery( $query );
if (null === ($modules = $db->loadObjectList())) {
    $mod = JModuleHelper::getModule(str_replace('mod_', '', MODNAME));
} else {
    $mod = $modules[0];
}

// Fill in missing fields
if (!isset($mod->name))  { $mod->name  = str_replace('mod_', '', MODNAME); }
if (!isset($mod->user))  { $mod->user  = 0; }
if (!isset($mod->style)) { $mod->style = ''; }


$module = JModuleHelper::getModule('mod_jem_calajax');
$params = new JRegistry($module->params);

print $renderer->render($mod,$params);
