<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Controller-Editvenue
 */
class JEMControllerEditvenue extends JControllerForm
{
	protected $view_item = 'editvenue';
	protected $view_list = 'venues';
	protected $context = 'editvenue';

	/**
	 * Method to add a new record.
	 *
	 * @return	boolean	True if the event can be added, false if not.
	 */
	public function add()
	{
		if (!parent::add()) {
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	An array of input data.
	 *
	 * @return	boolean
	 */
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$jinput 	= JFactory::getApplication()->input;
		$allow		= null;

		
		$settings 	= JemHelper::globalattribs();

		if (JEMUser::addVenue($settings)) {
			return true;
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else {
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 * @todo: check if the user is allowed to edit/save
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$asset		= 'com_jem.venue.'.$recordId;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) {
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', $asset)) {

			// Now test the owner is the user.
			$ownerId	= (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId) {
				// Need to do a lookup from the model.
				$record		= $this->getModel()->getItem($recordId);

				if (empty($record)) {
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId) {
				return true;
			}
		}

		$record			= $this->getModel()->getItem($recordId);
		$jemsettings 	= JEMHelper::config();
		$maintainer 	= JEMUser::venuegroups('edit');
		$genaccess 		= JEMUser::editaccess($jemsettings->venueowner, $record->created_by, $jemsettings->venueeditrec, $jemsettings->venueedit);
		if ($maintainer || $genaccess) {
			return true;
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 *
	 * @return	Boolean	True if access level checks pass, false otherwise.
	 */
	public function cancel($key = 'a_id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	Boolean	True if access level check and checkout passes, false otherwise.
	 */
	public function edit($key = null, $urlVar = 'a_id')
	{

		$result = parent::edit($key, $urlVar);

		return $result;
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 * @param	array	$config	Configuration array for model. Optional.
	 *
	 * @return	object	The model.
	 *
	 */
	public function getModel($name = 'editvenue', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'a_id')
	{
		$jinput = JFactory::getApplication()->input;

		// Need to override the parent method completely.
		$tmpl		= JFactory::getApplication()->input->getCmd('tmpl');
		$layout		= JFactory::getApplication()->input->getCmd('layout', 'edit');
		$append		= '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		$append .= '&layout=edit';

		if ($recordId) {
			$append .= '&'.$urlVar.'='.$recordId;
		}

		$itemId	= $jinput->getInt('Itemid');
		$return	= $this->getReturnPage();
		//$catId = $jinput->getInt('catid', null);

		if ($itemId) {
			$append .= '&Itemid='.$itemId;
		}

		//if($catId) {
		//	$append .= '&catid='.$catId;
		//}

		if ($return) {
			$append .= '&return='.base64_encode(urlencode($return));
		}

		return $append;
	}

	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return	string	The return URL.
	 */
	protected function getReturnPage()
	{
		$return = JFactory::getApplication()->input->get('return', null, 'base64');

		if (empty($return) || !JUri::isInternal(urldecode(base64_decode($return)))) {
			return JUri::base();
		}
		else {
			return urldecode(base64_decode($return));
		}
	}

	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{

	$task = $this->getTask();
	if ($task == 'save' || $task == 'apply') {
		$isNew = $model->getState('editvenue.new');
		$id    = $model->getState('editvenue.id');

		$enabled = JPluginHelper::isEnabled('jem','mailer');

		if ($enabled) {
			JPluginHelper::importPlugin('jem','mailer');
			$dispatcher = JEventDispatcher::getInstance();
			$dispatcher->trigger('onVenueEdited', array($id, $isNew));
		} else {
			JError::raiseNotice(100,JText::_('COM_JEM_GLOBAL_MAILERPLUGIN_DISABLED'));
		}
	}
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function save($key = null, $urlVar = 'a_id')
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$lang  = JFactory::getLanguage();
		$model = $this->getModel();
		$table = $model->getTable();
		$data  = $this->input->post->get('jform', array(), 'array');
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";

		$task = $this->getTask();

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		$recordId = $this->input->getInt($urlVar);

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy')
		{
			// Check-in the original row.
			if ($checkin && $model->checkin($data[$key]) === false)
			{
				// Check-in failed. Go back to the item and display a notice.
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
				$this->setMessage($this->getError(), 'error');

				$this->setRedirect(
						JRoute::_(
								'index.php?option=' . $this->option . '&view=' . $this->view_item
								. $this->getRedirectToItemAppend($recordId, $urlVar), false
						)
				);

				return false;
			}

			// Reset the ID and then treat the request as for Apply.
			$data[$key] = 0;
			$task = 'apply';
		}

		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
					JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_list
							. $this->getRedirectToListAppend(), false
					)
			);

			return false;
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
			if ($errors[$i] instanceof Exception)
			{
			$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
			}
			else
			{
				$app->enqueueMessage($errors[$i], 'warning');
				}
				}

				// Save the data in the session.
				$app->setUserState($context . '.data', $data);

				// Redirect back to the edit screen.
				$this->setRedirect(
				JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
				);

				return false;
			}

			if (!isset($validData['tags']))
			{
			$validData['tags'] = null;
			}

					// Attempt to save the data.
				if (!$model->save($validData))
				{

					// Save the data in the session.
						$app->setUserState($context . '.data', $validData);

						// Redirect back to the edit screen.
						$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
						$this->setMessage($this->getError(), 'error');

						$this->setRedirect(
								JRoute::_(
										'index.php?option=' . $this->option . '&view=' . $this->view_item
										. $this->getRedirectToItemAppend($recordId, $urlVar), false
								)
						);

						return false;
					}

					// Save succeeded, so check-in the record.
					if ($checkin && $model->checkin($validData[$key]) === false)
					{
						// Save the data in the session.
						$app->setUserState($context . '.data', $validData);

						// Check-in failed, so go back to the record and display a notice.
						$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
						$this->setMessage($this->getError(), 'error');

						$this->setRedirect(
								JRoute::_(
										'index.php?option=' . $this->option . '&view=' . $this->view_item
										. $this->getRedirectToItemAppend($recordId, $urlVar), false
								)
						);

						return false;
					}

					$this->setMessage(
							JText::_(
									($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
											? $this->text_prefix
											: 'JLIB_APPLICATION') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
							)
					);

					// Redirect the user and adjust session state based on the chosen task.
					switch ($task)
					{
						case 'apply':
							// Set the record data in the session.
							$recordId = $model->getState($this->context . '.id');
							$this->holdEditId($context, $recordId);
							$app->setUserState($context . '.data', null);
							$model->checkout($recordId);

							// Redirect back to the edit screen.
							$this->setRedirect(
									JRoute::_(
											'index.php?option=' . $this->option . '&view=' . $this->view_item
											. $this->getRedirectToItemAppend($recordId, $urlVar), false
									)
							);
							break;

						case 'save2new':
							// Clear the record id and data from the session.
							$this->releaseEditId($context, $recordId);
							$app->setUserState($context . '.data', null);

							// Redirect back to the edit screen.
							$this->setRedirect(
									JRoute::_(
											'index.php?option=' . $this->option . '&view=' . $this->view_item
											. $this->getRedirectToItemAppend(null, $urlVar), false
									)
							);
							break;

						default:
							// Clear the record id and data from the session.
							$this->releaseEditId($context, $recordId);
							$app->setUserState($context . '.data', null);


							$this->setRedirect($this->getReturnPage());

							/*
								// Redirect to the list screen.
							$this->setRedirect(
									JRoute::_(
											'index.php?option=' . $this->option . '&view=' . $this->view_list
											. $this->getRedirectToListAppend(), false
									)
							);
							*/
							break;
					}

					// Invoke the postSave method to allow for the child class to access the model.
					$this->postSaveHook($model, $validData);

					return true;
	}
}
