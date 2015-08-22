<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Controller-Myevents
 */
class JEMControllerMyevents extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Logic to publish events
	 *
	 * @access public
	 * @return void
	 *
	 */
	public function publish()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$app = JFactory::getApplication();
		$input = $app->input;

		// Get items to publish.
		$cid = $jinput->get('cid',array(),'array');
		JArrayHelper::toInteger($cid);

		if (empty($cid)) {
			$this->setRedirect(JEMHelperRoute::getMyEventsRoute(),JText::_('COM_JEM_SELECT_ITEM_TO_PUBLISH'),'warning');
		} else {
			$model = $this->getModel('myevents');
			if(!$model->publish($cid, 1)) {
				$this->setRedirect(JEMHelperRoute::getMyEventsRoute(),$msg);
				return;
			} else {
				$total = count($cid);
				$msg 	= $total.' '.JText::_('COM_JEM_EVENT_PUBLISHED');
			}
		}
		$this->setRedirect(JEMHelperRoute::getMyEventsRoute(),$msg);
	}

	/**
	 * Unpublish
	 */
	function unpublish()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$app = JFactory::getApplication();
		$input = $app->input;

		$cid = $input->get('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (empty($cid)) {
			$this->setRedirect(JEMHelperRoute::getMyEventsRoute(),JText::_('COM_JEM_SELECT_ITEM_TO_UNPUBLISH'),'warning');
			return;
		}
		$model = $this->getModel('myevents');
		if(!$model->publish($cid, 0)) {
			$msg = $model->getError();
			$this->setRedirect(JEMHelperRoute::getMyEventsRoute(),$msg);
			return;
		}

		$total = count($cid);
		$msg 	= $total.' '.JText::_('COM_JEM_EVENT_UNPUBLISHED');

		$this->setRedirect(JEMHelperRoute::getMyEventsRoute(), $msg);
	}

	/**
	 * Logic to trash events
	 *
	 * @access public
	 * @return void
	 */
	function trash()
	{
		$cid = $input->get('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (empty($cid)) {
			$this->setRedirect(JEMHelperRoute::getMyEventsRoute(),JText::_('COM_JEM_SELECT_ITEM_TO_TRASH'),'warning');
			return;
		}

		$model = $this->getModel('myevents');
		if(!$model->publish($cid, -2)) {
			$msg = $model->getError();
			$this->setRedirect(JEMHelperRoute::getMyEventsRoute(),$msg);
			return;
		}

		$total = count($cid);
		$msg 	= $total.' '.JText::_('COM_JEM_EVENT_TRASHED');

		$this->setRedirect(JEMHelperRoute::getMyEventsRoute(), $msg);
	}
}
