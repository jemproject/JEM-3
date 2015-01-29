<?php
/**
 * @version 3.0.6
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined( '_JEXEC' ) or die;


/**
 * Controller: Venues
 */
class JemControllerVenues extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 *
	 */
	protected $text_prefix = 'COM_JEM_VENUES';


	/**
	 * Proxy for getModel.
	 *
	 */
	public function getModel($name = 'Venue', $prefix = 'JEMModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}


	/**
	 * logic for remove venues
	 *
	 * @access public
	 * @return void
	 *
	 */
	function remove()
	{
		$jinput = JFactory::getApplication()->input;
		$cid 	= $jinput->get('cid',  array(),'array');

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JEM_SELECT_AN_ITEM_TO_DELETE' ) );
		}

		$model = $this->getModel('venues');

		$msg = $model->remove($cid);

		$cache = JFactory::getCache('com_jem');
		$cache->clean();

		$this->setRedirect( 'index.php?option=com_jem&view=venues', $msg );
	}
}
