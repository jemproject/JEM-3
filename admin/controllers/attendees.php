<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Controller: Attendees
 */
class JemControllerAttendees extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra task
		$this->registerTask('add', 	'edit');
		$this->registerTask('apply', 'save');
	}

	/**
	 * Delete attendees
	 *
	 * @return true on sucess
	 * @access private
	 *
	 */
	function remove()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$jinput 	= JFactory::getApplication()->input;
		$cid 		= $jinput->get('cid',array(),'array');
		$eventid 	= $jinput->getInt('eid');

		$total 		= count($cid);

		$model 		= $this->getModel('attendees');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseError(500, JText::_('COM_JEM_SELECT_ITEM_TO_DELETE'));
		}

		if(!$model->remove($cid)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$cache = JFactory::getCache('com_jem');
		$cache->clean();

		$msg = $total.' '.JText::_('COM_JEM_ATTENDEES_REGISTERED_USERS_DELETED');

		$this->setRedirect('index.php?option=com_jem&view=attendees&eid='.$eventid, $msg);
	}

	function export()
	{
		$app = JFactory::getApplication();

		$model = $this->getModel('attendees');
		$datas = $model->getItems();

		header('Content-Type: text/csv');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: attachment; filename=attendees.csv');
		header('Pragma: no-cache');

		$export = '';
		$col = array();

		for($i=0; $i < count($datas); $i++)
		{
			$data = $datas[$i];

			$col[] = str_replace("\"", "\"\"", $data->name);
			$col[] = str_replace("\"", "\"\"", $data->username);
			$col[] = str_replace("\"", "\"\"", $data->email);
			$col[] = str_replace("\"", "\"\"", JHtml::_('date',$data->uregdate, JText::_('DATE_FORMAT_LC2')));
			$col[] = str_replace("\"", "\"\"", $data->uid);

			for($j = 0; $j < count($col); $j++)
			{
				$export .= "\"" . $col[$j] . "\"";

				if($j != count($col)-1)
				{
					$export .= ";";
				}
			}
			$export .= "\r\n";
			$col = '';
		}

		echo $export;
		$app->close();
	}

	/**
	 * redirect to events page
	 */
	function back()
	{
		$this->setRedirect('index.php?option=com_jem&view=events');
	}

	function toggle()
	{
		$jinput 	= JFactory::getApplication()->input;
		$cid 		= $jinput->get('cid',array(),'array');
		$eventid 	= $jinput->get('eid');


		$model 		= $this->getModel('attendee');
		$res =		 $model->toggle($cid[0]);

		$register_data = $model->getItem($cid[0]);

		$type = 'message';

		if ($res)
		{
			JPluginHelper::importPlugin('jem');
			$dispatcher = JEventDispatcher::getInstance();
			$res = $dispatcher->trigger('onUserOnOffWaitinglist', array($cid[0]));

			if ($register_data->waiting)
			{
				$msg = JText::_('COM_JEM_ADDED_TO_ATTENDING');
			}
			else
			{
				$msg = JText::_('COM_JEM_ADDED_TO_WAITING');
			}
		}
		else
		{
			$msg = JText::_('COM_JEM_WAITINGLIST_TOGGLE_ERROR').': '.$model->getError();
			$type = 'error';
		}
		$this->setRedirect('index.php?option=com_jem&view=attendees&eid='.$eventid, $msg, $type);
		$this->redirect();
	}


	/**
	 * logic to create the edit attendee view
	 *
	 * @access public
	 * @return void
	 *
	 */
	function edit()
	{
		// Check for request forgeries.
		// JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'attendee');
		$jinput->set('hidemainmenu', '1');

		/*
		$model 	= $this->getModel('attendee');

		$user	= JFactory::getUser();

		// Error if checkedout by another administrator
		if ($model->isCheckedOut($user->get('id'))) {
			$this->setRedirect('index.php?option=com_jem&view=attendees', JText::_('COM_JEM_EDITED_BY_ANOTHER_ADMIN'));
		}
		$model->checkout();
		*/

		parent::display();
	}

	/**
	 * Proxy for getModel.
	 */
	public function getModel($name = 'Attendee', $prefix = 'JEMModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
