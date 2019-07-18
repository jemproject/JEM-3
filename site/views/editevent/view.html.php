<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Editevent-View
 */
class JemViewEditevent extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $return_page;
	protected $state;

	public function display($tpl = null)
	{

		if ($this->getLayout() == 'choosevenue') {
			$this->_displaychoosevenue($tpl);
			return;
		}

		if ($this->getLayout() == 'choosecontact') {
			$this->_displaychoosecontact($tpl);
			return;
		}
		
		// Initialise variables.
		$jemsettings = JEMHelper::config();
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$valguest	= JEMUser::validate_guest();

		$document	= JFactory::getDocument();
		$model		= $this->getModel();
		$menu		= $app->getMenu();
		$menuitem	= $menu->getActive();
		$pathway	= $app->getPathway();
		$url		= JUri::root();
		$template	= $app->getTemplate();

		$settings 	= JemHelper::globalattribs();
		$vsettings	= JemHelper::viewSettings('veditevent');

		$this->vsettings = $vsettings;
		$this->settings = $settings;
		$this->valguest	= $valguest;

		// Get model data.
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->params = $this->state->get('params');
		
		// Create a shortcut for $item and params.
		$item = $this->item;
		$params = $this->params;

		$this->form = $this->get('Form');
		$this->return_page = $this->get('ReturnPage');

		if (empty($this->item->id)) {
			// we're submitting a new event
			if (JEMUser::addEvent($settings)) {
				$authorised = true;
			} else {
				$authorised = false;
			}
		} else {
			// Check if user can edit
			if (JEMUser::editEvent($settings,false,$this->item->id,$this->item->categories,false,$this->item->created_by)) {
				$editEvent = true;
			} else {
				$editEvent = false;
			}

			$authorised = $this->item->params->get('access-edit') || $editEvent ;
		}
		
		if ($authorised !== true) {
			
			
			$app->enqueueMessage(JText::_('COM_JEM_EDITEVENT_NOAUTH'), 'warning');
			
			
			
			return false;
		}

		// Decide which parameters should take priority
		$useMenuItemParams = ($menuitem && $menuitem->query['option'] == 'com_jem'
				&& $menuitem->query['view']   == 'editevent'
				&& 0 == $item->id); // menu item is always for new event

		$title = ($item->id == 0) ? JText::_('COM_JEM_EDITEVENT_ADD_EVENT')
		                          : JText::sprintf('COM_JEM_EDITEVENT_EDIT_EVENT', $item->title);

		if ($useMenuItemParams) {
			$pagetitle = $menuitem->title ? $menuitem->title : $title;
			$params->def('page_title', $pagetitle);
			$params->def('page_heading', $pagetitle);
			$pathway->setItemName(1, $pagetitle);

			// Load layout from menu item if one is set else from event if there is one set
			if (isset($menuitem->query['layout'])) {
				$this->setLayout($menuitem->query['layout']);
			} elseif ($layout = $item->params->get('event_layout')) {
				$this->setLayout($layout);
			}

			$item->params->merge($params);
		} else {
			$pagetitle = $title;
			$params->set('page_title', $pagetitle);
			$params->set('page_heading', $pagetitle);
			$params->set('show_page_heading', 1); // ensure page heading is shown
			$params->set('introtext', ''); // there is definitely no introtext.
			$params->set('show_introtext', 0);
			$pathway->addItem($pagetitle, ''); // link not required here so '' is ok

			// Check for alternative layouts (since we are not in a edit-event menu item)
			// Load layout from event if one is set
			if ($layout = $item->params->get('event_layout')) {
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

			// check for recurrence
			if (($this->item->recurrence_type != 0) || ($this->item->recurrence_first_id != 0)) {
				$tmp->recurrence_type = 0;
				$tmp->recurrence_first_id = 0;
			}

			// $tmp->images = $this->item->images;
			// $tmp->urls = $this->item->urls;
			$this->form->bind($tmp);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$this->access = JEMHelper::getAccesslevelOptions();

		// add css file
		JemHelper::loadCss('jem');
		JemHelper::loadCustomCss();

		# Load scripts
		JHtml::_('bootstrap.framework');

		if ($vsettings->get('editevent_show_attachmentstab',1)) {
			JHtml::_('script', 'com_jem/attachments.js', false, true);
		}

		if ($vsettings->get('editevent_show_othertab',1)) {
			JHtml::_('script', 'com_jem/other.js', false, true);
			JHtml::_('script', 'com_jem/recurrence.js', false, true);
		}

		JHtml::_('script', 'com_jem/seo.js', false, true);
		if (JEMUser::validate_guest()) {
			JHtml::_('script', 'com_jem/antispam.js', false, true);
		}
		JHtml::_('behavior.tabstate');


		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($item->params->get('pageclass_sfx'));
		$this->dimage = JemImage::flyercreator($this->item->datimage, 'event');
		$this->jemsettings = $jemsettings;
		$this->infoimage = JHtml::_('image', 'com_jem/icon-16-hint.png', JText::_('COM_JEM_NOTES'), NULL, true);

		$this->user = $user;

		if ($params->get('enable_category') == 1) {
			$this->form->setFieldAttribute('catid', 'default', $params->get('catid', 1));
			$this->form->setFieldAttribute('catid', 'readonly', 'true');
		}

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

	/**
	 * Creates the output for the venue select listing
	 */
	protected function _displaychoosevenue($tpl)
	{
		$app         = JFactory::getApplication();
		$jinput      = JFactory::getApplication()->input;
		$jemsettings = JemHelper::config();
		$db          = JFactory::getDBO();
		$document    = JFactory::getDocument();
		$itemid 	 = $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$filter_order     = $app->getUserStateFromRequest('com_jem.selectvenue.'.$itemid.'.filter_order', 'filter_order', 'l.venue', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('com_jem.selectvenue.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
		$filter_type      = $app->getUserStateFromRequest('com_jem.selectvenue.'.$itemid.'.filter_type', 'filter_type', '', 'int');
		$filter_state     = $app->getUserStateFromRequest('com_jem.selectvenue.'.$itemid.'.filter_state', 'filter_state', '*', 'word');
		$search           = $app->getUserStateFromRequest('com_jem.selectvenue.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$search           = $db->escape(trim(JString::strtolower($search)));

		// Get/Create the model
		$rows  		= $this->get('Venues');
		$pagination = $this->get('VenuesPagination');

		JHtml::_('behavior.modal', 'a.flyermodal');

		// filter state
		$lists['state'] = JHtml::_('grid.state', $filter_state);

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']     = $filter_order;

		$document->setTitle(JText::_('COM_JEM_SELECT_VENUE'));
		JemHelper::loadCss('jem');

		$filters = array();
		$filters[] = JHtml::_('select.option', '1', JText::_('COM_JEM_VENUE'));
		$filters[] = JHtml::_('select.option', '2', JText::_('COM_JEM_CITY'));
		$filters[] = JHtml::_('select.option', '3', JText::_('COM_JEM_STATE'));
		$searchfilter = JHtml::_('select.genericlist', $filters, 'filter_type', array('size'=>'1','class'=>'inputbox'), 'value', 'text', $filter_type);

		$this->rows         = $rows;
		$this->searchfilter = $searchfilter;
		$this->pagination   = $pagination;
		$this->lists        = $lists;
		$this->filter       = $search;

		parent::display($tpl);
	}

	/**
	 * Creates the output for the contact select listing
	 */
	protected function _displaychoosecontact($tpl)
	{
		$app         = JFactory::getApplication();
		$jinput      = JFactory::getApplication()->input;
		$jemsettings = JemHelper::config();
		$db          = JFactory::getDBO();
		$document    = JFactory::getDocument();
		$itemid 	 = $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		$filter_order     = $app->getUserStateFromRequest('com_jem.selectcontact.'.$itemid.'.filter_order', 'filter_order', 'con.name', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('com_jem.selectcontact.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_type      = $app->getUserStateFromRequest('com_jem.selectcontact.'.$itemid.'.filter_type', 'filter_type', '', 'int');
		$search           = $app->getUserStateFromRequest('com_jem.selectcontact.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$search           = $db->escape(trim(JString::strtolower($search)));

		// Load css
		JemHelper::loadCss('jem');

		$document->setTitle(JText::_('COM_JEM_SELECT_CONTACT'));

		// Get/Create the model
		$rows  		= $this->get('Contacts');
		$pagination = $this->get('ContactsPagination');

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']     = $filter_order;

		//Build search filter
		$filters = array();
		$filters[] = JHtml::_('select.option', '1', JText::_('COM_JEM_NAME'));
	/*	$filters[] = JHtml::_('select.option', '2', JText::_('COM_JEM_ADDRESS')); */ // data security
		$filters[] = JHtml::_('select.option', '3', JText::_('COM_JEM_CITY'));
		$filters[] = JHtml::_('select.option', '4', JText::_('COM_JEM_STATE'));
		$searchfilter = JHtml::_('select.genericlist', $filters, 'filter_type', array('size'=>'1','class'=>'inputbox'), 'value', 'text', $filter_type);

		// search filter
		$lists['search']= $search;

		//assign data to template
		$this->searchfilter = $searchfilter;
		$this->lists        = $lists;
		$this->rows         = $rows;
		$this->pagination   = $pagination;

		parent::display($tpl);
	}
}
