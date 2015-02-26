<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Controller: Dates
 */
class JemControllerDates extends JControllerAdmin
{
	protected $text_prefix = 'COM_JEM_DATES';

	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	ContentControllerArticles
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('disabledate',	'setstatusdate');
	}


	/**
	 * Proxy for getModel.
	 *
	 */
	public function getModel($name = 'Date', $prefix = 'JEMModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}


	/**
	 * Method to enable/disable dates
	 *
	 */
	function setstatusdate()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$jinput	= JFactory::getApplication()->input;
		$ids	= $jinput->get('cid', array(),'array');
		$values	= array('setstatusdate' => 1, 'disabledate' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_jem.date.'.(int) $id)) {
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->setstatusdate($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_jem&view=dates');
	}
}
