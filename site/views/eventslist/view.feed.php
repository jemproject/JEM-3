<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Eventslist-Feed
 */
class JemViewEventslist extends JViewLegacy
{
	/**
	 * Creates the Event Feed
	 */
	function display($cachable = false, $urlparams = false)
	{
		$app 	= JFactory::getApplication();
		$jinput = $app->input;
		$doc 	= JFactory::getDocument();

		// Get some data from the model
		$jinput->set('limit', $app->getCfg('feed_limit'));
		$rows = $this->get('Items');

		foreach ($rows as $row) {
			// strip html from feed item title
			$title = $this->escape($row->title);
			$title = html_entity_decode($title);

			// categories (object of stdclass to array), when there is something to show
			if (!empty($row->categories)) {
				$category = array();
				foreach ($row->categories AS $category2) {
					$category[] = $category2->catname;
				}

				// ading the , to the list when there are multiple category's
				$category = $this->escape(implode(', ', $category));
				$category = html_entity_decode($category);
			} else {
				$category = '';
			}

			//Format date and time
			$displaydate = JemOutput::formatLongDateTime($row->dates, $row->times,$row->enddates, $row->endtimes);

			// url link to event
			$link = JRoute::_(JemHelperRoute::getEventRoute($row->slug));

			// Venue
			$venue = "";

			if ($row->venue && $row->city) {
				$venue .= $row->venue.' / '.$row->city;
			}

			if ($row->venue && !($row->city)) {
				$venue .= $row->venue;
			}

			if (!$row->venue && $row->city) {
				$venue .= $row->city;
			}

			if (!$row->venue && !$row->city) {
				$venue .= "";
			}

			// feed item description text
			$description  = JText::_('COM_JEM_TITLE').': '.$title.'<br />';
			if ($venue) {
				$description .= JText::_('COM_JEM_VENUE').': '.$venue.'<BR />';
			}
			$description .= JText::_('COM_JEM_CATEGORY').': '.$category.'<br />';
			$description .= JText::_('COM_JEM_DATE').': '.$displaydate.'<br />';
			$description .= JText::_('COM_JEM_DESCRIPTION').': '.$row->introtext.$row->fulltext;

			// date
			# if we want to show the created time we can uncheck this line
			/* $created = ($row->created ? date('r', strtotime($row->created)) : ''); */

			// load individual item creator class
			$item = new JFeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->description 	= $description;
			$item->category 	= $category;

			// loads item info into rss array
			$doc->addItem($item);
		}

		# do we want an image on top of the feed?
		/*
		$image = new JFeedImage();
		$image->url		= JHtml::_('image', 'com_jem/feed.png', null, null, true, true);
		$image->link	= JUri::base();
		$image->title	= 'Home';
		$image->height	= '120';
		$image->width	= '120';

		# assign image to the document
		$this->document->image = $image;
		*/

	}
}
?>