<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Editvenue-View
 */
class JemViewEditvenue extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $return_page;
	protected $state;

	/**
	 * Editvenue-View
	 */
	public function display($tpl=null)
	{
		// Initialise variables.
		$jemsettings = JemHelper::config();
		$app         = JFactory::getApplication();
		$user        = JFactory::getUser();
		$document    = JFactory::getDocument();
		$model       = $this->getModel();
		$menu        = $app->getMenu();
		$menuitem    = $menu->getActive();
		$pathway     = $app->getPathway();
		$url         = JUri::root();

		$language 	= JFactory::getLanguage();
		$language 	= $language->getTag();
		$language 	= substr($language, 0,2);

		// Get model data.
		$this->state 		= $this->get('State');
		$this->item 		= $this->get('Item');
		$this->params 		= $this->state->get('params');
		$this->settings2	= JemHelper::globalattribs();

		// Create a shortcut for $item and params.
		$item = $this->item;
		$params = $this->params;

		$this->form = $this->get('Form');
		$this->return_page = $this->get('ReturnPage');

		// check for guest
		if (!$user || $user->id == 0) {
			$app->enqueueMessage(JText::_('COM_JEM_EDITVENUE_NOAUTH'), 'warning');
			return false;
		}

		if (empty($this->item->id)) {
			// we're submitting a new venue
			if (JEMUser::addVenue($this->settings2)) {
				$authorised = true;
			} else {
				$authorised = false;
			}
		} else {
			// Check if user can edit
			if (JEMUser::editVenue($this->settings2,false,$this->item->id,false,false,$this->item->created_by)) {
				$editVenue = true;
			} else {
				$editVenue = false;
			}
			
			$authorised = $this->item->params->get('access-edit') || $editVenue;
		}

		if ($authorised !== true) {
			$app->enqueueMessage(JText::_('COM_JEM_EDITVENUE_NOAUTH'), 'warning');
			return false;
		}

		// Decide which parameters should take priority
		$useMenuItemParams = ($menuitem && $menuitem->query['option'] == 'com_jem'
		                                && $menuitem->query['view']   == 'editvenue'
		                                && 0 == $item->id); // menu item is always for new venues

		$title = ($item->id == 0) ? JText::_('COM_JEM_EDITVENUE_VENUE_ADD')
		                          : JText::sprintf('COM_JEM_EDITVENUE_VENUE_EDIT', $item->venue);

		if ($useMenuItemParams) {
			$pagetitle = $menuitem->title ? $menuitem->title : $title;
			$params->def('page_title', $pagetitle);
			$params->def('page_heading', $pagetitle);
			$pathway->setItemName(1, $pagetitle);

			// Load layout from menu item if one is set else from venue if there is one set
			if (isset($menuitem->query['layout'])) {
				$this->setLayout($menuitem->query['layout']);
			} elseif ($layout = $item->params->get('venue_layout')) {
				$this->setLayout($layout);
			}

			$item->params->merge($params);
		} else {
			$pagetitle = $title;
			$params->set('page_title', $pagetitle);
			$params->set('page_heading', $pagetitle);
			$params->set('show_page_heading', 1); // ensure page heading is shown
			$params->set('introtext', ''); // there is no introtext in that case
			$params->set('showintrotext', 0);
			$pathway->addItem($pagetitle, ''); // link not required here so '' is ok

			// Check for alternative layouts (since we are not in an edit-venue menu item)
			// Load layout from venue if one is set
			if ($layout = $item->params->get('venue_layout')) {
				$this->setLayout($layout);
			}

			$temp = clone($params);
			$temp->merge($item->params);
			$item->params = $temp;
		}

		if (!empty($this->item) && isset($this->item->id)) {
			// $this->item->images = json_decode($this->item->images);
			// $this->item->urls = json_decode($this->item->urls);

			$tmp = new stdClass();
			// $tmp->images = $this->item->images;
			// $tmp->urls = $this->item->urls;
			$this->form->bind($tmp);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$access2 		= JemHelper::getAccesslevelOptions();
		$this->access	= $access2;

		// Load css
		JemHelper::loadCss('geostyle');
		JemHelper::loadCss('jem');
		JemHelper::loadCustomCss();
		JemHelper::loadCustomTag();

		// Load script
		JHtml::_('bootstrap.framework');
		JHtml::_('script', 'com_jem/attachments.js', false, true);

		# retrieve mapType setting
		$settings 		= JemHelper::globalattribs();
		$mapType		= $settings->get('mapType','0');

		switch($mapType) {
			case '0':
				$type = '"roadmap"';
				break;
			case '1':
				$type = '"satellite"';
				break;
			case '2':
				$type = '"hybrid"';
				break;
			case '3':
				$type = '"terrain"';
				break;
		}

		$this->mapType = $type;

		$this->pageclass_sfx	= htmlspecialchars($item->params->get('pageclass_sfx'));
		$this->jemsettings		= $jemsettings;
		$this->limage 			= JemImage::flyercreator($this->item->locimage, 'venue');
		$this->infoimage		= JHtml::_('image', 'com_jem/icon-16-hint.png', JText::_('COM_JEM_NOTES'), NULL, true);

		$this->user = $user;

		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();

		$title = $this->params->get('page_title');
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		// TODO: Is it useful to have meta data in an edit view?
		//       Also shouldn't be "robots" set to "noindex, nofollow"?
		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
