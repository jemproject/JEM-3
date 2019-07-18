<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

// include files
require_once JPATH_COMPONENT_SITE . '/helpers/countries.php';
require_once JPATH_COMPONENT_SITE . '/helpers/helper.php';
require_once JPATH_COMPONENT_SITE . '/helpers/route.php';
require_once JPATH_COMPONENT_SITE . '/classes/activecalendarweek.php';
require_once JPATH_COMPONENT_SITE . '/classes/attachment.class.php';
require_once JPATH_COMPONENT_SITE . '/classes/calendar.class.php';
require_once JPATH_COMPONENT_SITE . '/classes/image.class.php';
require_once JPATH_COMPONENT_SITE . '/classes/output.class.php';
require_once JPATH_COMPONENT_SITE . '/classes/user.class.php';
require_once JPATH_COMPONENT_SITE . '/classes/Zebra_Image.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php';

# include Recurr files
JLoader::registerNamespace('Recurr', JPATH_COMPONENT_SITE . '/classes');

# Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');

# perform cleanup if it wasn't done today (archive, delete)
JEMHelper::cleanup();

# Get an instance of the controller
$controller = JControllerLegacy::getInstance('Jem');

# Perform task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

# Redirect if set by the controller
$controller->redirect();
