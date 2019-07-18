<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Controller-Attendees
 */
class JEMControllerAttendees extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * redirect to events page
	 */
	function back()
	{
		$this->setRedirect(JRoute::_(JEMHelperRoute::getMyEventsRoute(), false));
		$this->redirect();
	}

	/**
	 * removetask
	 */
	function attendeeremove()
	{
		$jinput = JFactory::getApplication()->input;

		$cid = $jinput->post->get('cid', array(0), 'array');
		$id  = $jinput->getInt('id');
		$fid = $jinput->getInt('Itemid');
		$total = count($cid);

		$model = $this->getModel('attendees');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseError(500, JText::_('COM_JEM_SELECT_ITEM_TO_DELETE'));
		}

		if(!$model->remove($cid)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$cache = JFactory::getCache('com_jem');
		$cache->clean();

		$msg = $total.' '.JText::_('COM_JEM_REGISTERED_USERS_DELETED');

		$this->setRedirect(JRoute::_('index.php?option=com_jem&view=attendees&id='.$id.'&Itemid='.$fid, false), $msg);
	}

	/**
	 * toggletask
	 */
	function attendeetoggle()
	{
		$jinput = JFactory::getApplication()->input;

		$id = $jinput->getInt('id');
		$fid = $jinput->getInt('Itemid');

		$model = $this->getModel('attendee');
		$model->setId($id);

		$attendee = $model->getData();
		$res = $model->toggle();

		$type = 'message';

		if ($res)
		{
			JPluginHelper::importPlugin('jem');
			$dispatcher = JEventDispatcher::getInstance();
			$res = $dispatcher->trigger('onUserOnOffWaitinglist', array($id));

			if ($attendee->waiting) {
				$msg = JText::_('COM_JEM_ADDED_TO_WAITING');
			} else {
				$msg = JText::_('COM_JEM_ADDED_TO_ATTENDING');
			}
		}
		else
		{
			$msg = JText::_('COM_JEM_WAITINGLIST_TOGGLE_ERROR').': '.$model->getError();
			$type = 'error';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jem&view=attendees&id='.$attendee->event.'&Itemid='.$fid, false), $msg, $type);
		$this->redirect();
	}

	/**
	 * Export
	 */
	function export()
	{
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$params = $app->getParams();
		$enableemailadress = $params->get('enableemailaddress', 0);

		$model = $this->getModel('attendees');

		$datas = $model->getData();

		header('Content-Type: text/x-csv');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Disposition: attachment; filename=attendees.csv');
		header('Pragma: no-cache');

		$export = '';

		for ($i=0; $i < count($datas); $i++)
		{
			$col = array();
			$data = $datas[$i];
			$col[] = str_replace("\"", "\"\"", $data->username);
			if ($enableemailadress == 1){
				$col[] = str_replace("\"", "\"\"", $data->email);
			}
			$col[] = str_replace("\"", "\"\"", JHtml::_('date',$data->uregdate, JText::_('DATE_FORMAT_LC2')));
			for ($j = 0; $j < count($col); $j++){
				$export .= "\"" . $col[$j] . "\"";
				if ($j != count($col)-1){
					$export .= ";";
				}
			}
			$export .= "\r\n";
		}

		echo $export;

		$app->close();
	}
}
