<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Holds the logic for all output related things
 */
class JEMOutput {

	/**
	 * Writes footer.
	 */
	static function footer()
	{
		$app 		= JFactory::getApplication();
		$settings 	= JemHelper::globalattribs();

		if ($settings->get('show_poweredby','1')==1) {
			if ($app->input->getInt('print')) {
				return;
			} else {
				echo 'Powered by <a href="http://www.joomlaeventmanager.net" target="_blank">JEM</a>';
			}
		}
	}

	/**
	 * Writes Event submission button
	 *
	 * @param int $dellink Access of user
	 * @param array $params needed params
	 **/
	static function submitbutton($dellink, $params)
	{
		if ($dellink)
		{
			$settings 	= JemHelper::globalattribs();
			$settings2	= JemHelper::config();

			$uri = JFactory::getURI();
			$app = JFactory::getApplication();

			# check for print-screen
			if ($app->input->getInt('print')) {
				return;
			}

			# check for icon-setting
			if ($settings->get('global_show_icons',1)) {
				$text = JHtml::_('image', 'com_jem/submitevent.png', JText::_('COM_JEM_DELIVER_NEW_EVENT'), NULL, true);
			} else {
				$text = JText::_('COM_JEM_DELIVER_NEW_EVENT');
			}

			JHtml::_('bootstrap.tooltip');

			$url = 'index.php?option=com_jem&task=editevent.add&return='.base64_encode(urlencode($uri)).'&a_id=0';
			$desc = JText::_('COM_JEM_SUBMIT_EVENT_DESC');
			$title = JText::_('COM_JEM_DELIVER_NEW_EVENT');

			$tip = JHtml::tooltipText($title, $desc, 0);

			$attribs = array();
			$attribs['title']   = $tip;
			$attribs['class'] = 'hasTooltip';
			$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

			return $output;
		}
	}

	/**
	 * Writes addvenuebutton
	 *
	 * @param int $addvenuelink Access of user
	 * @param array $params needed params
	 * @param $settings, retrieved from settings-table
	 *
	 * Active in views:
	 * venue, venues
	 **/
	static function addvenuebutton($addvenuelink, $params, $settings2)
	{
		if ($addvenuelink) {
			$app 		= JFactory::getApplication();
			$settings 	= JemHelper::globalattribs();

			# check for print
			if ($app->input->getInt('print')) {
				return;
			}

			$uri 		= JFactory::getURI();
			JHtml::_('bootstrap.tooltip');

			# check for icons
			if ($settings->get('global_show_icons',1)) {
				$text = JHtml::_('image', 'com_jem/addvenue.png', JText::_('COM_JEM_DELIVER_NEW_VENUE'), NULL, true);
			} else {
				$text = JText::_('COM_JEM_DELIVER_NEW_VENUE');
			}

			$url = 'index.php?option=com_jem&task=editvenue.add&return='.base64_encode(urlencode($uri)).'&a_id=0';
			$title = JText::_('COM_JEM_DELIVER_NEW_VENUE');
			$desc = JText::_('COM_JEM_DELIVER_NEW_VENUE_DESC');

			$tip = JHtml::tooltipText($title, $desc, 0);

			$attribs = array();
			$attribs['title']   = $tip;
			$attribs['class'] = 'hasTooltip';
			$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

			return $output;
		}
	}

	/**
	 * Writes Archivebutton
	 *
	 * @param array $params needed params
	 * @param string $task The current task (optional)
	 * @param int $id id of category/event/venue if useful (optional)
	 *
	 * Views:
	 * Categories, Categoriesdetailed, Category, Eventslist, Search, Venue, Venues
	 */
	static function archivebutton($params, $task = NULL, $id = NULL)
	{
		$settings	= JemHelper::globalattribs();
		$settings2	= JemHelper::config();
		$app		= JFactory::getApplication();

		if ($params->get('global_show_archive_icon',1)) {

			# check if we're in a print-screen
			if ($app->input->getInt('print')) {
				return;
			}

			# check for a view
			$view = $app->input->getWord('view');
			if (empty($view)) {
				return; // there must be a view - just to be sure...
			}

			if ($task == 'archive') {
				if ($settings->get('global_show_icons',1)) {
					$text = JHtml::_('image', 'com_jem/el.png', JText::_('COM_JEM_SHOW_EVENTS'), NULL, true);
				} else {
					$text = JText::_('COM_JEM_SHOW_EVENTS');
				}
				$desc = JText::_('COM_JEM_SHOW_EVENTS_DESC');
				$title = JText::_('COM_JEM_SHOW_EVENTS');

				if ($id) {
					$url = 'index.php?option=com_jem&view='.$view.'&id='.$id;
				} else {
					$url = 'index.php?option=com_jem&view='.$view;
				}
			} else {
				# here we're not in the archive-task

				if ($settings->get('global_show_icons',1)) {
					$text = JHtml::_('image', 'com_jem/archive_front.png', JText::_('COM_JEM_SHOW_ARCHIVE'), NULL, true);
				} else {
					$text = JText::_('COM_JEM_SHOW_ARCHIVE');
				}

				$desc = JText::_('COM_JEM_SHOW_ARCHIVE_DESC');
				$title = JText::_('COM_JEM_SHOW_ARCHIVE');

				if ($id) {
					$url = 'index.php?option=com_jem&view='.$view.'&id='.$id.'&task=archive';
				} else {
					$url = 'index.php?option=com_jem&view='.$view.'&task=archive';
				}
			}

			# output
			JHtml::_('bootstrap.tooltip');

			$tip = JHtml::tooltipText($title, $desc, 0);

			$attribs = array();
			$attribs['title']   = $tip;
			$attribs['class'] = 'hasTooltip';
			$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

			return $output;
		}
	}

	/**
	 * Creates the edit button
	 *
	 * @param int $Itemid
	 * @param int $id
	 * @param array $params
	 * @param int $allowedtoedit
	 * @param string $view
	 *
	 * Views:
	 * Event, Venue
	 */
	static function editbutton($item, $params, $attribs, $allowedtoedit, $view)
	{
		if ($allowedtoedit) {
			$app = JFactory::getApplication();

			# check for print
			if ($app->input->getInt('print')) {
				return;
			}

			# Ignore if the state is negative.
			if ($item->published < 0) {
				return;
			}

			// Initialise variables.
			$user	= JFactory::getUser();
			$app = JFactory::getApplication();
			$userId	= $user->get('id');
			$uri	= JFactory::getURI();

			$settings = JemHelper::globalattribs();
			$jemsettings		= JemHelper::config();
			JHtml::_('bootstrap.tooltip');

			switch ($view)
			{
				case 'editevent':
					if (property_exists($item, 'checked_out') && property_exists($item, 'checked_out_time') && $item->checked_out > 0 && $item->checked_out != $userId) {
						$checkoutUser = JFactory::getUser($item->checked_out);
						$button = JHtml::_('image', 'system/checked_out.png', NULL, NULL, true);
						$date = JHtml::_('date', $item->checked_out_time);
						$tooltip = JText::sprintf('COM_JEM_GLOBAL_CHECKED_OUT_BY', $checkoutUser->name).' <br /> '.$date;
						return '<span class="hasTooltip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
					}

					$text = JHtml::_('image', 'com_jem/calendar_edit.png', JText::_('COM_JEM_EDIT_EVENT'), NULL, true);
					$id = $item->did;
					$desc = JText::_('COM_JEM_EDIT_EVENT_DESC');
					$title = JText::_('COM_JEM_EDIT_EVENT');
					$url = 'index.php?option=com_jem&task=editevent.edit&a_id='.$id.'&return='.base64_encode(urlencode($uri));
					break;

				case 'editvenue':
					if (property_exists($item, 'vChecked_out') && property_exists($item, 'vChecked_out_time') && $item->vChecked_out > 0 && $item->vChecked_out != $userId) {
						$checkoutUser = JFactory::getUser($item->vChecked_out);
						$button = JHtml::_('image', 'system/checked_out.png', NULL, NULL, true);
						$date = JHtml::_('date', $item->vChecked_out_time);
						$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_JEM_GLOBAL_CHECKED_OUT_BY', $checkoutUser->name).' <br /> '.$date;
						return '<span class="hasTooltip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
					}

					$text = JHtml::_('image', 'com_jem/calendar_edit.png', JText::_('COM_JEM_EDIT_VENUE'), NULL, true);
					$id = $item->locid;
					$desc = JText::_('COM_JEM_EDIT_VENUE_DESC');
					$title = JText::_('COM_JEM_EDIT_VENUE');
					$url = 'index.php?option=com_jem&task=editvenue.edit&a_id='.$id.'&return='.base64_encode(urlencode($uri));
					break;

				case 'venue':
					if (property_exists($item, 'vChecked_out') && property_exists($item, 'vChecked_out_time') && $item->vChecked_out > 0 && $item->vChecked_out != $userId) {
						$checkoutUser = JFactory::getUser($item->vChecked_out);
						$button = JHtml::_('image', 'system/checked_out.png', NULL, NULL, true);
						$date = JHtml::_('date', $item->vChecked_out_time);
						$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_JEM_GLOBAL_CHECKED_OUT_BY', $checkoutUser->name).' <br /> '.$date;
						return '<span class="hasTooltip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
					}

					$text = JHtml::_('image', 'com_jem/calendar_edit.png', JText::_('COM_JEM_EDIT_VENUE'), NULL, true);
					$id = $item->id;
					$desc = JText::_('COM_JEM_EDIT_VENUE_DESC');
					$title = JText::_('COM_JEM_EDIT_VENUE');
					$url = 'index.php?option=com_jem&task=editvenue.edit&a_id='.$id.'&return='.base64_encode(urlencode($uri));
					break;

				case 'eventslist':
					# check if we're allowed to edit

					$maintainer = JemUser::ismaintainer('edit',$item->id);
					$genaccess  = JemUser::editaccess($jemsettings->eventowner, $item->created_by, $jemsettings->eventeditrec, $jemsettings->eventedit);

					if ($maintainer || $genaccess || $user->authorise('core.edit','com_jem')) {
						# @todo finetune attribs/params
					if (property_exists($item, 'Checked_out') && property_exists($item, 'Checked_out_time') && $item->Checked_out > 0 && $item->Checked_out != $userId) {
						$checkoutUser = JFactory::getUser($item->Checked_out);
						$button = JHtml::_('image', 'system/checked_out.png', NULL, NULL, true);
						$date = JHtml::_('date', $item->Checked_out_time);
						$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_JEM_GLOBAL_CHECKED_OUT_BY', $checkoutUser->name).' <br /> '.$date;
						return '<span class="hasTooltip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
					}

					$text = JHtml::_('image', 'com_jem/calendar_edit.png', JText::_('COM_JEM_EDIT_EVENT'), NULL, true);
					$id = $item->id;
					$desc = JText::_('COM_JEM_EDIT_EVENT_DESC');
					$title = JText::_('COM_JEM_EDIT_EVENT');
					$url = 'index.php?option=com_jem&task=editevent.edit&a_id='.$id.'&return='.base64_encode(urlencode($uri));
					break;
					} else {
						return;
					}
			}

			if (!$url) {
				return; // we need at least url to generate useful output
			}

			$tip = JHtml::tooltipText($title, $desc, 0);

			$attribs = array();
			$attribs['title']   = $tip;
			$attribs['class'] = 'hasTooltip';
			$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

			return $output;
		}
	}

	/**
	 * Creates the print button
	 *
	 * @param string $print_link
	 * @param array $params
	 */
	static function printbutton($print_link, &$params,$view=false)
	{
		$app 		= JFactory::getApplication();
		$settings	= JemHelper::globalattribs();

		if ($settings->get('global_show_print_icon',0)) {
			JHtml::_('bootstrap.tooltip');

			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=800,height=480,directories=no,location=no';

			# check for icon setting
			if ($settings->get('global_show_icons',1)) {
				$text = JHtml::_('image','system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
			} else {
				$text = JText::_('JGLOBAL_PRINT');
			}

			# check if we're in a print-screen
			if ($app->input->getInt('print')) {
				//button in popup
				$overlib = JText::_('COM_JEM_PRINT_DESC');
				$text = JText::_('COM_JEM_PRINT');
				$title = 'title='.JText::_('JGLOBAL_PRINT').' class="icon-print"';
				$pimage = JHtml::_('image','system/printButton.png', JText::_('JGLOBAL_PRINT'), $title, true);
				$output = '<a href="#" onclick="window.print();return false;">'.$pimage.'</a>';
			} else {
				$overlib = JText::_('COM_JEM_PRINT_DESC');
				$title = JHtml::tooltipText(JText::_('JGLOBAL_PRINT'), $overlib, 0);

				$attribs = array();
				$attribs['title']   = $title;
				if ($view == 'btn') {
					$attribs['class'] = 'btn btn-small hasTooltip';
				} else {
					$attribs['class'] = 'hasTooltip';
				}
				$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
				$attribs['rel']     = 'nofollow';
				return JHtml::_('link', $print_link, $text, $attribs);

			}

			return $output;
		}

		return;
	}

	/**
	 * Creates the email button
	 *
	 * @param object $slug
	 * @param $view
	 * @param array $params
	 *
	 * Views:
	 * Category, Event, Venue
	 */
	static function mailbutton($slug, $view, $params)
	{
		$app 		= JFactory::getApplication();
		$settings	= JemHelper::globalattribs();

		if ($settings->get('global_show_email_icon')) {

			# check for print-screen
			if ($app->input->getInt('print')) {
				return;
			}

			JHtml::_('bootstrap.tooltip');
			require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';

			$uri = JUri::getInstance();
			$base = $uri->toString(array('scheme', 'host', 'port'));
			$template = JFactory::getApplication()->getTemplate();
			$link = $base.JRoute::_('index.php?option=com_jem&view='.$view.'&id='.$slug, false);

			$url = 'index.php?option=com_mailto&tmpl=component&template='.$template.'&link='.MailToHelper::addLink($link);
			$status = 'width=400,height=350,menubar=yes,resizable=yes';

			# check for icon-setting
			if ($settings->get('global_show_icons')) {
				$text = JHtml::_('image','system/emailButton.png', JText::_('JGLOBAL_EMAIL'), NULL, true);
			} else {
				$text = JText::_('JGLOBAL_EMAIL');
			}

			$desc = JText::_('COM_JEM_EMAIL_DESC');
			$title = JText::_('JGLOBAL_EMAIL');

			$tip = JHtml::tooltipText($title, $desc, 0);

			$attribs = array();
			$attribs['title']   = $tip;
			$attribs['class'] = 'hasTooltip';
			$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";

			$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

			return $output;
		}
	}

	/**
	 * Creates the ical button
	 *
	 * @param object $slug
	 * @param array $params
	 */
	static function icalbutton($slug, $view)
	{
		$app = JFactory::getApplication();
		$settings = JemHelper::globalattribs();

		if ($settings->get('global_show_ical_icon','0')==1) {

			# check for print-screen
			if ($app->input->getInt('print')) {
				return;
			}

			JHtml::_('bootstrap.tooltip');

			$text = JHtml::_('image', 'com_jem/iCal2.0.png', JText::_('COM_JEM_EXPORT_ICS'), NULL, true);

			$desc = JText::_('COM_JEM_ICAL_DESC');
			$title = JText::_('COM_JEM_ICAL');

			$tip = JHtml::tooltipText($title, $desc, 0);
			$url = 'index.php?option=com_jem&view='.$view.'&id='.$slug.'&format=raw&layout=ics';

			$attribs = array();
			$attribs['title']   = $tip;
			$attribs['class'] = 'hasTooltip';

			$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

			return $output;
		}
	}

	/**
	 * Creates the publish button
	 *
	 * View:
	 * Myevents
	 */
	static function publishbutton()
	{
		$app = JFactory::getApplication();

		//$image = JHtml::_('image', 'com_jem/publish.png', JText::_('COM_JEM_PUBLISH'), NULL, true).'&#160;'.JText::_('COM_JEM_PUBLISH');
		$image = '<span class="icon-publish"></span>&#160;'.JText::_('COM_JEM_PUBLISH');

		if ($app->input->getInt('print')) {
			//button in popup
			return;
		}

		JHtml::_('bootstrap.tooltip');

		# button in view
		$desc = JText::_('COM_JEM_PUBLISH_DESC');
		$title = JText::_('COM_JEM_PUBLISH');
		$tip = JHtml::tooltipText($title, $desc, 0);

		$onclick = 'Joomla.submitbutton(\'myevents.publish\')';

		$print_link = "javascript:void(0);return true";
		$output	= '<button onclick="'.$onclick.'" class="btn btn-small hasTooltip" title="'.$tip.'">'.$image.'</button>';

		return $output;
	}

	/**
	 * Creates the trash button
	 *
	 * View:
	 * Myevents
	 */
	static function trashbutton()
	{
		$app = JFactory::getApplication();

		//$image = JHtml::_('image', 'com_jem/trash.png', JText::_('COM_JEM_TRASH'), NULL, true).'&#160;'.JText::_('COM_JEM_TRASH');
		$image = '<span class="icon-trash"></span>&#160;'.JText::_('COM_JEM_TRASH');

		if ($app->input->getInt('print')) {
			//button in popup
			return;
		}

		JHtml::_('bootstrap.tooltip');

		//button in view
		$desc = JText::_('COM_JEM_TRASH_DESC');
		$title = JText::_('COM_JEM_TRASH');

		$tip = JHtml::tooltipText($title, $desc, 0);

		$onclick = 'Joomla.submitbutton(\'myevents.trash\')';
		$output	= '<button onclick="'.$onclick.'" class="btn btn-small hasTooltip" title="'.$tip.'">'.$image.'</button>';

		return $output;
	}

	/**
	 * Creates the unpublish button
	 *
	 * View:
	 * Myevents
	 */
	static function unpublishbutton()
	{
		$app = JFactory::getApplication();

		//$image = JHtml::_('image', 'com_jem/unpublish.png', JText::_('COM_JEM_UNPUBLISH'), NULL, true).'&#160;'.JText::_('COM_JEM_UNPUBLISH');
		$image = '<span class="icon-unpublish"></span>&#160;'.JText::_('COM_JEM_UNPUBLISH');

		if ($app->input->getInt('print')) {
			return;
		}

		JHtml::_('bootstrap.tooltip');

		# button in view
		$desc = JText::_('COM_JEM_UNPUBLISH_DESC');
		$title = JText::_('COM_JEM_UNPUBLISH');

		$tip = JHtml::tooltipText($title, $desc, 0);

		$onclick = 'Joomla.submitbutton(\'myevents.unpublish\')';
		$output	= '<button onclick="'.$onclick.'" class="btn btn-small hasTooltip" title="'.$tip.'">'.$image.'</button>';

		return $output;
	}

	/**
	 * Creates the export button
	 *
	 * view:
	 * attendees
	 */
	static function exportbutton($eventid)
	{
		$app = JFactory::getApplication();

		JHtml::_('bootstrap.tooltip');

		$text = JHtml::_('image', 'com_jem/export_excel.png', JText::_('COM_JEM_EXPORT'), NULL, true).' '.JText::_('COM_JEM_EXPORT');

		if ($app->input->getInt('print')) {
			return;
		}
			# button in view
			$desc = JText::_('COM_JEM_EXPORT_DESC');
			$title = JText::_('COM_JEM_EXPORT');

			$tip = JHtml::tooltipText($title, $desc, 0);

			$url = 'index.php?option=com_jem&view=attendees&task=attendees.export&tmpl=raw&id='.$eventid;

			$attribs = array();
			$attribs['title']   = $tip;
			$attribs['class'] = 'btn btn-small hasTooltip';

			$output = JHtml::_('link', JRoute::_($url), $text, $attribs);


		return $output;
	}

	/**
	 * Creates the back button
	 *
	 * view:
	 * attendees
	 */
	static function backbutton($backlink, $view)
	{
		$app 	= JFactory::getApplication();
		$jinput = $app->input;

		$id 	= $jinput->getInt('id');
		$fid 	= $jinput->getInt('Itemid');

		JHtml::_('bootstrap.tooltip');

		$text = JHtml::_('image', 'com_jem/icon-16-back.png', JText::_('COM_JEM_BACK'), NULL, true).' '.JText::_('COM_JEM_BACK');

		if ($jinput->getInt('print')) {
			return;
		}

		//button in view
		$desc = JText::_('COM_JEM_BACK');
		$title = JText::_('COM_JEM_BACK');
		$tip = JHtml::tooltipText($title, $desc, 0);

		$url = 'index.php?option=com_jem&view='.$view.'&id='.$id.'&Itemid='.$fid.'&task=attendees.back';

		$attribs = array();
		$attribs['title']   = $tip;
		$attribs['class'] = 'btn btn-small hasTooltip';

		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

		return $output;
	}

	/**
	 * Creates the map button
	 *
	 * @param obj $data
	 */
	static function mapicon($data,$view=false,$params)
	{
		$global = JemHelper::globalattribs();

		//stop if disabled
		if (!$data->map) {
			return;
		}

		if ($view == 'event') {
			$mapserv	= $params->get('event_show_mapserv');
		} elseif ($view == 'venues') {
			// @todo check
			$mapserv	= $params->get('show_mapserv');
		} else {
			$mapserv	= $params->get('show_mapserv');
		}

		//Link to map
		$mapimage = JHtml::_('image', 'com_jem/map_icon.png', JText::_('COM_JEM_MAP'), NULL, true);

		//set var
		$output = null;
		$attributes = null;

		$data->country = JString::strtoupper($data->country);

		if ($data->latitude == 0.000000) {
			$data->latitude = null;
		}
		if ($data->longitude == 0.000000) {
			$data->longitude = null;
		}

		// google map link or include
		switch ($mapserv)
		{
			case 1:
				$url = 'http://maps.google.com/maps?&q='.urlencode($data->street.', '.$data->postalCode.' '.$data->city.', '.$data->country.'+ ('.$data->venue.')').'&ie=UTF8&z=15&output=embed" ';

				// link
				if($data->latitude && $data->longitude) {
					$url = 'http://maps.google.com/maps?q='.$data->latitude.',+'.$data->longitude.'&ie=UTF8&z=15&output=embed';
				}

				$message = JText::_('COM_JEM_MAP').':';
				$attributes = ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}" latitude="" longitude=""';
				$output = '<dt class="venue_mapicon">'.$message.'</dt><dd class="venue_mapicon"><a class="flyermodal mapicon" title="'.JText::_('COM_JEM_MAP').'" target="_blank" href="'.$url.'"'.$attributes.'>'.$mapimage.'</a></dd>';
				break;

			case 2:
				$api		= trim($global->get('global_googleapi'));

				# iframe
				if($data->latitude && $data->longitude) {
					$output = '<iframe width="500" height="250" frameborder="0" style="border: 1px solid #000" src="https://www.google.com/maps/embed/v1/search?key='.$api.'&q='.urlencode($data->latitude.',+'.$data->longitude).'"></iframe>';
				} else {
					$output = '<iframe width="500" height="250" frameborder="0" style="border: 1px solid #000" src="https://www.google.com/maps/embed/v1/search?key='.$api.'&q='.urlencode($data->street.', '.$data->postalCode.' '.$data->city).'"></iframe>';
				}

				break;

			case 3:
				// include - Google API3
				# https://developers.google.com/maps/documentation/javascript/tutorial
				$api		= trim($params->get('global_googleapi'));
				$clientid	= trim($params->get('global_googleclientid'));

				$document 	= JFactory::getDocument();

				# do we have a client-ID?
				if ($clientid) {
					$document->addScript('http://maps.googleapis.com/maps/api/js?client='.$clientid.'&sensor=false&v=3.15');
				} else {
					# do we have an api-key?
					if ($api) {
						$document->addScript('https://maps.googleapis.com/maps/api/js?key='.$api.'&sensor=false');
					} else {
						$document->addScript('https://maps.googleapis.com/maps/api/js?sensor=false');
					}
				}

				JemHelper::loadCss('googlemap');
				JHtml::_('script', 'com_jem/infobox.js', false, true);
				JHtml::_('script', 'com_jem/googlemap.js', false, true);

				$output = '<div id="map-canvas" class="map_canvas"/></div>';
				break;

		}

		return $output;
	}

	/**
	 * Creates the recurrence icon
	 *
	 * @param obj  $event
	 * @param bool $showinline Add css class to scale icon to fit text height
	 * @param bool $showtitle  Add title (tooltip)
	 */
	static function recurrenceicon($event, $showinline = true, $showtitle = true)
	{
		$settings = JemHelper::globalattribs();

		if ($event->recurrence_group) {
			$image = 'com_jem/icon-32-recurrence.png';
			$attr_class = 'class="icon-recurrence"';
			$attr_title = 'title="' . JText::_('COM_JEM_RECURRING_EVENT').'"';
			$output = JHtml::_('image', $image, JText::_('COM_JEM_RECURRING_EVENT'), $attr_class . $attr_title, true);
			return $output;
		} else {
			return;
		}

	}

	/**
	 * Creates the flyer
	 *
	 * basename($imagefile) = filename
	 * dirname($imagefile) = dirname
	 *
	 * @param obj $data
	 * @param array $image
	 * @param string $type
	 */
	static function flyer($data, $image, $type,$id = null)
	{
		# determine the type and set variables
		switch($type) {
			case 'event':
				$folderx = 'events';
				$imagefile = $data->datimage;
				$info = $data->title;
				break;

			case 'category':
				$folderx = 'categories';
				$imagefile = $data->image;
				$info = $data->catname;
				break;

			case 'venue':
				$folderx = 'venues';
				$imagefile = $data->locimage;
				$info = $data->venue;
				break;
		}

		// Do we have an image?
		if (empty($imagefile)) {
			return;
		}

		$id_attr = $id ? 'id="'.$id.'"' : '';

		# load settings
		$settings = JemHelper::config();

		# import filesystem
		jimport('joomla.filesystem.file');

		# are we dealing with an image of previous JEM/EL versions
		if (strpos($imagefile,'images/') !== false) {

		} else {
			$imagefile = 'images/jem/'.$folderx.'/'.$imagefile;
		}

		$filename = basename($imagefile);
		$dirname = dirname($imagefile);


		# do we have a image from a thumb folder?
		if (strpos($dirname,'/small') !== false) {
			# yes the image selected is from an thumb directory
			$check = JFile::exists(JPATH_SITE.'/'.$dirname.'/'.$filename);
			$thumbx = $dirname.'/'.$filename;
		} else {
			# no, the image selected is not from an thumb directory
			# append 'small'
			$check = JFile::exists(JPATH_SITE.'/'.$dirname.'/small/'.$filename);
			$thumbx = $dirname.'/small/'.$filename;
		}

		if ($check) {
			# the thumb-file exists
			if ($settings->lightbox == 0) {
				# thumb in page
				$attributes = $id_attr.' class="flyerimage" onclick="window.open(\''.JUri::base().'/'.$image['original'].'\',\'Popup\',\'width='.$image['width'].',height='.$image['height'].',location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no\')"';
				$icon = '<img '.$attributes.' src="'.JUri::base().'/'.$image['thumb'].'" width="'.$image['thumbwidth'].'" height="'.$image['thumbheight'].'" alt="'.$info.'" title="'.JText::_('COM_JEM_CLICK_TO_ENLARGE').'" />';
				$output = '<div class="flyerimage">'.$icon.'</div>';

			} else {
				# thumb in popup
				JHtml::_('behavior.modal', 'a.flyermodal');
				$url = JUri::base().$image['original'];
				$attributes = $id_attr.' class="flyermodal flyerimage" title="'.$info.'"';
			}
			$icon = '<img src="'.JUri::base().$thumbx.'" alt="'.$info.'" title="'.JText::_('COM_JEM_CLICK_TO_ENLARGE').'" />';
			$output = '<div class="flyerimage"><a href="'.$url.'" '.$attributes.'>'.$icon.'</a></div>';
		} else {
			# we don't have a thumb so we're using the image sizes from the backend
			# @todo apply check for valid file
			$output = '<div class="flyerimage"><img '.$id_attr.' class="notmodal" src="'.JUri::base().$image['original'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.$info.'" /></div>';
		}

		return $output;
	}

	/**
	 * Formats date
	 *
	 * @param string $date
	 * @param string $format
	 * @return string $formatdate
	 */
	static function formatdate($date, $format = "")
	{
		$settings 	= JemHelper::config();
		$check 		= JemHelper::isValidDate($date);
		//$timezone	= JemHelper::getTimeZoneName();
		$timezone	= null;

		if ($check == true) {
			$jdate = new JDate($date,$timezone);
			if (!$format) {
				// If no format set, use long format as standard
				$format = $settings->formatdate;
			}

			return $jdate->format($format);
		} else {
			return false;
		}
	}

	/**
	 * Formats time
	 *
	 * @param string $time
	 * @return string $formattime
	 */
	static function formattime($time, $format = "", $addSuffix = true)
	{
		$settings	= JemHelper::config();
		$check 		= JemHelper::isValidTime($time);

		if (!$check)
		{
			return;
		}

		if(!$format) {
			// If no format set, use settings format as standard
			$format = $settings->formattime;
		}

		$formattedTime = strftime($format, strtotime($time));

		if($addSuffix) {
			$formattedTime .= ' '.$settings->timename;
		}

		return $formattedTime;
	}

	/**
	 * Formats the input dates and times to be used as a from-to string for
	 * events. Takes care of unset dates and or times.
	 *
	 * @param string $dateStart Start date of event
	 * @param string $timeStart Start time of event
	 * @param string $dateEnd End date of event
	 * @param string $timeEnd End time of event
	 * @param string $format Date Format
	 * @return string Formatted date and time string to print
	 */
	static function formatDateTime($dateStart, $timeStart, $dateEnd = "", $timeEnd = "", $format = "")
	{
		$settings = JemHelper::globalattribs();
		$output = "";

		if(JemHelper::isValidDate($dateStart)) {
			$output .= '<span class="date">'.self::formatdate($dateStart, $format).'</span>';

			if($settings->get('global_show_timedetails','1') && JemHelper::isValidTime($timeStart)) {
				$output .= ', <span class="time">'.self::formattime($timeStart).'</span>';
			}

			// Display end date only when it differs from start date
			$displayDateEnd = JemHelper::isValidDate($dateEnd) && $dateEnd != $dateStart;
			if($displayDateEnd) {
				$output .= ' - <span class="time">'.self::formatdate($dateEnd, $format).'</span>';
			}

			// Display end time only when both times are set
			if($settings->get('global_show_timedetails','1') && JemHelper::isValidTime($timeStart) && JemHelper::isValidTime($timeEnd))
			{
				$output .= $displayDateEnd ? ', ' : ' - ';
				$output .= '<span class="time">'.self::formattime($timeEnd).'</span>';
			}
		} else {
			$output .= '<span class="date">'.JText::_('COM_JEM_OPEN_DATE').'</span>';

			if($settings->get('global_show_timedetails','1')) {
				if(JemHelper::isValidTime($timeStart)) {
					$output .= ', <span class="date">'.self::formattime($timeStart).'</span>';
				}
				// Display end time only when both times are set
				if(JemHelper::isValidTime($timeStart) && JemHelper::isValidTime($timeEnd)) {
					$output .= ' - <span class="time">'.self::formattime($timeEnd).'<span>';
				}
			}
		}

		return $output;
	}

	/**
	 * Formats the input dates and times to be used as a long from-to string for
	 * events. Takes care of unset dates and or times.
	 *
	 * @param string $dateStart Start date of event
	 * @param string $timeStart Start time of event
	 * @param string $dateEnd End date of event
	 * @param string $timeEnd End time of event
	 * @return string Formatted date and time string to print
	 */
	static function formatLongDateTime($dateStart, $timeStart, $dateEnd = "", $timeEnd = "")
	{
		return self::formatDateTime($dateStart, $timeStart, $dateEnd, $timeEnd);
	}

	/**
	 * Formats the input dates and times to be used as a short from-to string for
	 * events. Takes care of unset dates and or times.
	 *
	 * @param string $dateStart Start date of event
	 * @param string $timeStart Start time of event
	 * @param string $dateEnd End date of event
	 * @param string $timeEnd End time of event
	 * @return string Formatted date and time string to print
	 */
	static function formatShortDateTime($dateStart, $timeStart, $dateEnd = "", $timeEnd = "")
	{
		$settings = JemHelper::config();

		// Use format saved in settings if specified or format in language file otherwise
		if(isset($settings->formatShortDate) && $settings->formatShortDate) {
			$format = $settings->formatShortDate;
		} else {
			$format = JText::_('COM_JEM_FORMAT_SHORT_DATE');
		}
		return self::formatDateTime($dateStart, $timeStart, $dateEnd, $timeEnd, $format);
	}

	static function formatSchemaOrgDateTime($dateStart, $timeStart, $dateEnd = "", $timeEnd = "") {
		$settings = JemHelper::globalattribs();
		$output = "";
		$formatD = "Y-m-d";
		$formatT = "%H:%M";

		if(JemHelper::isValidDate($dateStart)) {
			$content = self::formatdate($dateStart, $formatD);

			if($settings->get('global_show_timedetails','1') && $timeStart) {
				$content .= 'T'.self::formattime($timeStart, $formatT, false);
			}
			$output .= '<meta itemprop="startDate" content="'.$content.'" />';

			if(JemHelper::isValidDate($dateEnd)) {
				$content = self::formatdate($dateEnd, $formatD);

				if($settings->get('global_show_timedetails','1') && $timeEnd) {
					$content .= 'T'.self::formattime($timeEnd, $formatT, false);
				}
				$output .= '<meta itemprop="endDate" content="'.$content.'" />';
			}
		} else {
			// Open date

			if($settings->get('global_show_timedetails','1')) {
				if($timeStart) {
					$content = self::formattime($timeStart, $formatT, false);
					$output .= '<meta itemprop="startDate" content="'.$content.'" />';
				}
				// Display end time only when both times are set
				if($timeStart && $timeEnd) {
					$content .= self::formattime($timeEnd, $formatT, false);
					$output .= '<meta itemprop="endDate" content="'.$content.'" />';
				}
			}
		}
		return $output;
	}

	/**
	 * Returns an array for ical formatting
	 * @todo alter, where is this used for?
	 *
	 * @param string date
	 * @param string time
	 * @return array
	 */
	static function getIcalDateArray($date, $time = null)
	{
		if ($time) {
			$sec = strtotime($date. ' ' .$time);
		} else {
			$sec = strtotime($date);
		}
		if (!$sec) {
			return false;
		}

		//Format date
		$parsed = strftime('%Y-%m-%d %H:%M:%S', $sec);

		$date = array('year' => (int) substr($parsed, 0, 4),
				'month' => (int) substr($parsed, 5, 2),
				'day' => (int) substr($parsed, 8, 2));

		//Format time
		if (substr($parsed, 11, 8) != '00:00:00')
		{
			$date['hour'] = substr($parsed, 11, 2);
			$date['min'] = substr($parsed, 14, 2);
			$date['sec'] = substr($parsed, 17, 2);
		}
		return $date;
	}

	/**
	 * Get a category names list
	 * @param unknown $categories Category List
	 * @param boolean $doLink Link the categories to the respective Category View
	 * @return string|multitype:
	 */
	static function getCategoryList($categories, $doLink,$backend=false,$FixItemID=false) {
		$output = array_map(
			function ($category) use ($doLink,$backend,$FixItemID) {
				if ($doLink) {
					if ($backend) {
						$path = $category->path;
						$path = str_replace('/',' &#187; ',$path);

						$value = '<span class="hasTooltip" title="'.JHtml::tooltipText(JText::_('COM_JEM_EDIT_CATEGORY'), $path, 0).'">';
						$value .= '<a href="index.php?option=com_jem&amp;task=category.edit&amp;id='. $category->id.'">'.
								$category->catname.'</a>';
						$value .= '</span>';
					} else {
						if ($FixItemID) {
							$value = '<a href="'.JRoute::_('index.php?option=com_jem&view=category&id='.$category->catslug.'&Itemid='.$FixItemID).'">'.$category->catname.'</a>';
						} else {
							$value = '<a href="'.JRoute::_(JemHelperRoute::getCategoryRoute($category->catslug)).'">'.$category->catname.'</a>';
						}
					}
				} else {
					$value = $category->catname;
				}
				return $value;
			},
			$categories);

		return $output;
	}

	static function statuslabel($published = false) {

		# @todo check function
		$user	= JFactory::getUser();
		$app	= JFactory::getApplication();
		$userId	= $user->get('id');
		$admin	= JEMUser::superuser();
		$status = '';

		if ($published != 1 && $published != 2 && $admin) {
			# determine the type and set variables
			switch($published) {
				case '1':
					$status = 'JPUBLISHED';
					break;
				case '0':
					$status = 'JUNPUBLISHED';
					break;
				case '2':
					$status = 'JARCHIVED';
					break;
				case '-2':
					$status = 'JTRASHED';
					break;
			}
			return '<span class="label">'.JText::_($status).'</span>';
		}
	}

	static function eventDateTime($row, $dateStart = false, $timeStart = false, $dateEnd = false, $timeEnd = false) {
		# @todo add support for suffix time
		# @todo replace strftime by date

		if (!$row) {
			return false;
		}

		if (!JemHelper::isValidDate($row->dates)) {
			# for the date function we need at least a valid startdate
			return false;
		}

		$result 	= "";
		$settings 	= JemHelper::config();

		$timeFormat = $settings->formattime;
		$dateFormat = $settings->formatdate;


		if ($timeFormat) {
			# mapping of time values
			# incoming values are in strftime format
			# output is in date format
			$timeFormat = str_replace('%H','H',$timeFormat);
			$timeFormat = str_replace('%M','i',$timeFormat);
			$timeFormat = str_replace('%p','A',$timeFormat);
			$timeFormat = str_replace('%P','a',$timeFormat);
			$timeFormat = str_replace('%I','h',$timeFormat);
			$timeFormat = str_replace('%l','g',$timeFormat);
		} else {
			$timeFormat	= "H:i";
		}

		if (!($dateFormat)) {
			$dateFormat = "D, M. j, Y";
		}

		# we have a row so let's combine the date+time and retrieve the date parts

		########################
		## COMBINE DATE/TIMES ##
		########################

		# -- date/time Start --
		# at this point we have a startddate
		# but we've to see if we have a starttime

		$rowtime = $row->times;

		if ($row->times == null || $row->times == '00:00:00') {
			$rowtime	= null;
			$row->times = '00:00:00';
		}

		$datetimeStart	= new JDate($row->dates.' '.$row->times);
		$otimeStart		= $datetimeStart->format('H:i');
		$odateStart		= $datetimeStart->format($dateFormat);
		
		if ($otimeStart == '00.00' || $otimeStart == '00:00') {
			if ($rowtime == null) {
				$otimeStart = "";
			} else {
				$otimeStart = $datetimeStart->format($timeFormat);
			}
		} else {
			$otimeStart = $datetimeStart->format($timeFormat);
		}
		

		# -- date/time End --
		# - no enddate + no endtime = blank both variables
		# - no enddate + endtime = the time will be combined with the startdate
		# - enddate + endtime = the time + enddate will be filled
		# - enddate + no endtime = the time will be blanked, but the date will be outputted

		$rowendtime = $row->endtimes;

		if ($row->endtimes == null || $row->endtimes == '00:00:00') {
			$rowendtime	   = null;
			$row->endtimes = '00:00:00';
		}

		if (!($row->enddates == null || $row->enddates == '0000-00-00')) {
			# we have a enddate
			$datetimeEnd	= new JDate($row->enddates.' '.$row->endtimes);
			$odateEnd		= $datetimeEnd->format($dateFormat);
			$otimeEnd		= $datetimeEnd->format('H:i');

			if ($otimeEnd == '00.00' || $otimeEnd == '00:00') {
				if ($rowendtime == null) {
					$otimeEnd = "";
				} else {
					$otimeEnd = $datetimeEnd->format($timeFormat);
				}
			} else {
				$otimeEnd	= $datetimeEnd->format($timeFormat);
			}
		}

		if ($row->enddates == null || $row->enddates == '0000-00-00') {
			# we don't have a enddate, but do we have a endtime?

			$odateEnd = "";

			if ($rowendtime == null) {
				$otimeEnd = "";
			} else {
				# we do have a endtime but no enddate so let's combine it with the startddate
				$datetimeEnd	= new JDate($row->dates.' '.$row->endtimes);
				//$odateEnd		= $datetimeEnd->format($dateFormat);
				$otimeEnd		= $datetimeEnd->format($timeFormat);
			}

		}

		# outputting
		$result = array();

		if ($dateStart) {
			$result['dateStart']	= $odateStart;
		}
		if ($timeStart) {
			$lang = JFactory::getLanguage();
			if ($lang->getTag() == 'zh-TW') {
			}
			$result['timeStart']	= $otimeStart;
		}

		if ($dateStart && $timeStart) {
			# we like to output both values so let's combine the values

			if ($odateStart && $otimeStart) {
				$value = $odateStart.' '.$otimeStart;
			}
			if (!$odateStart && $otimeStart) {
				$value = $otimeStart;
			}
			if ($odateStart && !$otimeStart) {
				$value = $odateStart;
			}
			if (!$odateStart && !$otimeStart) {
				$value = "";
			}

			$result['startDateTime'] = $value;
		}
		if ($dateEnd) {
			$result['dateEnd']	= $odateEnd;
		}
		if ($timeEnd) {
			$lang = JFactory::getLanguage();
			if ($lang->getTag() == 'zh-TW') {
			}
			$result['timeEnd']	= $otimeEnd;
		}
		if ($dateEnd && $timeEnd) {
			# we like to output both values so let's combine the values

			if ($odateEnd && $otimeEnd) {
				$value = $odateEnd.' '.$otimeEnd;
			}
			if (!$odateEnd && $otimeEnd) {
				# let's check if we have a startdate
				if ($odateStart) {
					$value = $odateStart.' '.$otimeEnd;
				} else {
					$value = $otimeEnd;
				}
			}
			if ($odateEnd && !$otimeEnd) {
				$value = $odateEnd;
			}
			if (!$odateEnd && !$otimeEnd) {
				$value = "";
			}

			$result['endDateTime'] = $value;
		}

		if ($result['startDateTime'] && $result['endDateTime']) {
			# check if the dates are the same
			# if so we will define a new value called "combinedDateTime"

			$start_day	= $datetimeStart->format('m-d-Y');
			$end_day	= $datetimeEnd->format('m-d-Y');

			if ($start_day == $end_day) {

				$value = '';
				$value .= $odateStart.' ';

				if ($otimeStart & $otimeEnd) {
					$value .= $otimeStart. '-' .$otimeEnd;
				}
				if (!$otimeStart & $otimeEnd) {
					$value .= $otimeEnd;
				}
				if ($otimeStart & !$otimeEnd) {
					$value .= $otimeStart;
				}
				if (!$otimeStart & !$otimeEnd) {
					$value .= '';
				}

				$result['combinedDateTime'] = $value;
			}
		}

		return $result;
	}
}
