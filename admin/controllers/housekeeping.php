<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Controller: Housekeeping
 */
class JemControllerHousekeeping extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * logic to massdelete unassigned images
	 *
	 * @access public
	 * @return void
	 */
	function delete()
	{
		$task = JFactory::getApplication()->input->getCmd('task');
		$model = $this->getModel('housekeeping');

		if ($task == 'cleaneventimg') {
			$total = $model->delete($model::EVENTS);
		} elseif ($task == 'cleanvenueimg') {
			$total = $model->delete($model::VENUES);
		} elseif ($task == 'cleancategoryimg') {
			$total = $model->delete($model::CATEGORIES);
		}

		$link = 'index.php?option=com_jem&view=housekeeping';
		$msg = JText::sprintf('COM_JEM_HOUSEKEEPING_IMAGES_DELETED', $total);

		$this->setRedirect($link, $msg);
	}


	/**
	 * Cleanup images
	 */
	function CleanupImages() {
		$jinput	= JFactory::getApplication()->input;
		$task 	= $jinput->getCmd('task');
		$model 	= $this->getModel('housekeeping');

		$total = $model->CleanupImages();
		$total_cnt = count($total);
		$thumb_cnt = count(array_filter($total));
		$normal = $total_cnt-$thumb_cnt;
		
		$link = 'index.php?option=com_jem&view=housekeeping';
		$msg = JText::sprintf('COM_JEM_HOUSEKEEPING_IMAGES_MOVED', $thumb_cnt, $normal);
		$this->setRedirect($link, $msg);
	}
	
	
	/**
	 * Remove images
	 */
	function rmObsImages() {
		$jinput	= JFactory::getApplication()->input;
		$task 	= $jinput->getCmd('task');
		$model 	= $this->getModel('housekeeping');
		$model->rmObsImages();
	
		$link = 'index.php?option=com_jem&view=housekeeping';
		$msg = JText::_('COM_JEM_HOUSEKEEPING_IMAGES_REMOVED');
		$this->setRedirect($link, $msg);
	}


	/**
	 * logic to truncate table cats_relations
	 *
	 * @access public
	 * @return void
	 *
	 */
	function cleanupCatsEventRelations()
	{
		$model = $this->getModel('housekeeping');
		$model->cleanupCatsEventRelations();

		$link = 'index.php?option=com_jem&view=housekeeping';
		$msg = JText::_('COM_JEM_HOUSEKEEPING_CLEANUP_CATSEVENT_RELS_DONE');

		$this->setRedirect($link, $msg);
	}

	/**
	 * Truncates JEM tables with exception of settings table
	 */
	public function truncateAllData() {
		$model = $this->getModel('housekeeping');
		$model->truncateAllData();

		$link = 'index.php?option=com_jem&view=housekeeping';
		$msg = JText::_('COM_JEM_HOUSEKEEPING_TRUNCATE_ALL_DATA_DONE');

		$this->setRedirect($link, $msg);
	}

	/**
	 * Triggerarchive + Recurrences
	 *
	 * @access public
	 * @return void
	 *
	 */
	function triggerarchive()
	{
		JEMHelper::cleanup(1);

		$link = 'index.php?option=com_jem&view=housekeeping';
		$msg = JText::_('COM_JEM_HOUSEKEEPING_AUTOARCHIVE_DONE');

		$this->setRedirect($link, $msg);
	}
}
