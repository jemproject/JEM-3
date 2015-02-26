<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Controller
 */
class JemController extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Display the view
	 */
	function display($cachable = false, $urlparams = false)
	{
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$jinput 	= JFactory::getApplication()->input;

		// Set the default view name and format.
		$id				= $jinput->getInt('a_id');
		$viewName 		= $jinput->getCmd('view', 'eventslist');
		$viewFormat 	= $document->getType();
		$layoutName 	= $jinput->getCmd('layout', 'edit');

		// Check for edit form.
		if ($viewName == 'editevent' && !$this->checkEditId('com_jem.edit.editevent', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		// Check for edit form.
		if ($viewName == 'editvenue' && !$this->checkEditId('com_jem.edit.editvenue', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		if ($view = $this->getView($viewName, $viewFormat)) {
			// Do any specific processing by view.
			switch ($viewName) {
				case 'attendees':
				case 'calendar':
				case 'categories':
				case 'categoriesdetailed':
				case 'category':
				case 'day':
				case 'editevent':
				case 'editvenue':
				case 'event':
				case 'eventslist':
				case 'myattendances':
				case 'myevents':
				case 'myvenues':
				case 'search':
				case 'venue':
				case 'venues':
				case 'weekcal':
					$model = $this->getModel($viewName);
					break;
				default:
					$model = $this->getModel('eventslist');
					break;
			}

			$view->setModel($model, true);
			$view->setLayout($layoutName);

			// Push document object into the view.
			$view->document = $document;

			$view->display();
		}
	}

	/**
	 * for attachment downloads
	 *
	 */
	function getfile()
	{
		$jinput = JFactory::getApplication()->input;
		$id 	= $jinput->getInt('file');

		$path = JEMAttachment::getAttachmentPath($id);

		$mime = JEMHelper::getMimeType($path);

		$doc = JFactory::getDocument();
		$doc->setMimeEncoding($mime);
		header('Content-Disposition: attachment; filename="'.basename($path).'"');
		if ($fd = fopen ($path, "r"))
		{
			$fsize = filesize($path);
			header("Content-length: $fsize");
			header("Cache-control: private"); //use this to open files directly
			while(!feof($fd)) {
				$buffer = fread($fd, 2048);
				echo $buffer;
			}
		}
		fclose ($fd);
		return;
	}

	/**
	 * Delete attachment
	 *
	 * @return true on sucess
	 * @access private
	 *
	 */
	function ajaxattachremove()
	{
		$jinput	= JFactory::getApplication()->input;
		$id	 	= $jinput->request->getInt('id', 0);

		$res = JEMAttachment::remove($id);
		if (!$res) {
			echo 0;
			jexit();
		}

		$cache = JFactory::getCache('com_jem');
		$cache->clean();

		echo 1;
		jexit();
	}
}
