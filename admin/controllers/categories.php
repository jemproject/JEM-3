<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;



/**
 * Controller: Categories
 */
class JemControllerCategories extends JControllerAdmin
{
	protected $text_prefix = 'COM_JEM_CATEGORIES';

	/**
	 * Proxy for getModel
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 *
	 * @return	object	The model.
	 */
	function getModel($name = 'Category', $prefix = 'JEMModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return	bool	False on failure or error, true on success.
	 */
	public function rebuild()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$extension = JFactory::getApplication()->input->getCmd('com_jem');
		$this->setRedirect(JRoute::_('index.php?option=com_jem&view=categories', false));

		// Initialise variables.
		$model = $this->getModel();

		if ($model->rebuild()) {
			// Rebuild succeeded.
			$this->setMessage(JText::_('COM_JEM_CATEGORIES_REBUILD_SUCCESS'));
			return true;
		} else {
			// Rebuild failed.
			$this->setMessage(JText::_('COM_JEM_CATEGORIES_REBUILD_FAILURE'));
			return false;
		}
	}


 	/**
 	 * Logic to delete categories
 	 *
 	 * @access public
 	 * @return void
 	 *
 	 */
 	function remove()
 	{
 		$jinput	= JFactory::getApplication()->input;
 		$cid	= $jinput->post->get('cid', array(),'array');

 		if (!is_array($cid) || count($cid) < 1) {
 			JError::raiseWarning(500, JText::_('COM_JEM_SELECT_ITEM_TO_DELETE'));
 		}

 		$model = $this->getModel('category');

 		$msg = $model->delete($cid);

 		$cache = JFactory::getCache('com_jem');
 		$cache->clean();

 		$this->setRedirect('index.php?option=com_jem&view=categories', $msg);
 	}

}
