<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Controller: Attendee
 */
class JemControllerAttendee extends JControllerForm
{
	/**
	 * Constructor
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}


	/**
	 * redirect to events page
	 */
	function back()
	{
		$this->setRedirect('index.php?option=com_jem&view=events');
	}


	/**
	 * Gets the URL arguments to append to a list redirect.
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToListAppend()
	{
		$jinput 	= JFactory::getApplication()->input;

		$tmpl		= $jinput->get('tmpl');
		$eventid	= $jinput->getInt('eid');
		$append = '';

		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if ($eventid) {
			$append .= '&eid='.$eventid;
		}

		return $append;
	}


	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$jinput 	= JFactory::getApplication()->input;

		$tmpl   = $jinput->get('tmpl');
		$id		= $jinput->get('id');
		$layout = $jinput->getString('layout', 'edit');
		$eventid = $jinput->get('eid');
		$append = '';

		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout)
		{
			$append .= '&layout=' . $layout;
		}

		if ($recordId && $eventid)
		{
			$append .= '&' . $urlVar . '=' . $recordId.'&e' . $urlVar . '=' . $eventid;
		}

		if (is_null($recordId))
		{
			$append .= '&e' . $urlVar . '=' . $id;
		}

		return $append;
	}
}
