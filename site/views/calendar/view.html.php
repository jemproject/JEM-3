<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Calendar-View
 */
class JemViewCalendar extends JViewLegacy
{
	
	protected $state = null;
	
	/**
	 * Calendar-View
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document 	= JFactory::getDocument();
		$menu 		= $app->getMenu();
		$menuitem	= $menu->getActive();
		$jemsettings = JemHelper::config();
		$vsettings	= JemHelper::viewSettings('vcalendar');
		$state 			= $this->get('State');
		$params 		= $state->params;
		
		// Load css
		JemHelper::loadCss('jem');
		JemHelper::loadCss('calendar');
		JemHelper::loadCustomCss();
		JemHelper::loadCustomTag();

		$evlinkcolor = $params->get('eventlinkcolor');
		$evbackgroundcolor = $params->get('eventbackgroundcolor');
		$currentdaycolor = $params->get('currentdaycolor');
		$eventandmorecolor = $params->get('eventandmorecolor');

		$style = '
		div#jem div[id^=\'catz\'] a {color:' . $evlinkcolor . ' !important;}
		div#jem div[id^=\'catz\'] {background-color:'.$evbackgroundcolor .';}
		div#jem .eventcontent {background-color:'.$evbackgroundcolor .'; !important}
		div#jem .eventandmore {background-color:'.$eventandmorecolor .' !important;}
		div#jem .today .daynum {background-color:'.$currentdaycolor.' !important;}';

		$document->addStyleDeclaration($style);

		// add javascript (using full path - see issue #590)
		JHtml::_('script', 'media/com_jem/js/calendar.js');

		$year 	= $app->input->request->getInt('yearID', strftime("%Y"));
		$month 	= $app->input->request->getInt('monthID', strftime("%m"));

		//get data from model and set the month
		$model = $this->getModel();
		$model->setDate(mktime(0, 0, 1, $month, 1, $year));

		$rows			= $this->get('Items');

		//Set Page title
		$pagetitle   = $params->def('page_title', $menuitem->title);
		$params->def('page_heading', $pagetitle);
		$pageclass_sfx = $params->get('pageclass_sfx');

		// Add site name to title if param is set
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$pagetitle = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $pagetitle);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$pagetitle = JText::sprintf('JPAGETITLE', $pagetitle, $app->getCfg('sitename'));
		}

		$document->setTitle($pagetitle);
		$document->setMetaData('title', $pagetitle);

		//init calendar
		$cal = new JEMCalendar($year, $month, 0);
		$cal->enableMonthNav('index.php?view=calendar');
		$cal->setFirstWeekDay($params->get('firstweekday', 1));
		$cal->enableDayLinks(false);
		//$cal->enableDatePicker();

		$this->rows			= $rows;
		$this->params		= $params;
		$this->jemsettings	= $jemsettings;
		$this->vsettings	= $vsettings;
		$this->cal			= $cal;
		$this->pageclass_sfx = htmlspecialchars($pageclass_sfx);

		parent::display($tpl);
	}
}
