<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * CatOptions Field class.
 */
class JFormFieldCatOptions extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 */
	protected $type = 'CatOptions';

	protected function getInput()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';

		$frontedit = $this->element['frontedit'];

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Selected Categories
		$currentid = JFactory::getApplication()->input->getInt('a_id');
		$categories = self::getCategories($currentid);

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query = 'SELECT DISTINCT catid FROM #__jem_cats_event_relations WHERE itemid = '. $db->quote($currentid);

		$db->setQuery($query);
		$selectedcats = $db->loadColumn();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $selectedcats,$this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
		}
		else
			// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $selectedcats,$this->id);
		}

		return implode($html);
	}

	protected function getOptions() {

		$db			= JFactory::getDbo();

		$frontedit = $this->element['frontedit'];
		if ($frontedit) {
			$options	= $this->getCategories();
		} else {
			$options	= JEMCategories::getCategoriesTree();
		}

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage);
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	/**
	 * logic to get the categories
	 *
	 * @access public
	 * @return void
	 */
	function getCategories($id=false)
	{
		$user		= JFactory::getUser();
		$jemsettings = JEMHelper::config();
		$userid		= (int) $user->get('id');
		$superuser	= JEMUser::superuser();
		$levels 	= $user->getAuthorisedViewLevels();
		$settings 	= JemHelper::globalattribs();
		$guestcat	= $settings->get('guest_category','0');

		$valguest	= JEMUser::validate_guest();
		if (!$valguest) {
			$where = ' WHERE c.published = 1 AND c.access IN (' . implode(',', $levels) . ')';

			//get the ids of the categories the user maintaines
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$query = 'SELECT g.group_id'
					. ' FROM #__jem_groupmembers AS g'
					. ' WHERE g.member = '.$userid
					;
					$db->setQuery($query);
					$catids = $db->loadColumn();

					$query = 'SELECT gr.id'
							. ' FROM #__jem_groups AS gr'
									. ' LEFT JOIN #__jem_groupmembers AS g ON g.group_id = gr.id'
											. ' WHERE g.member = ' . (int) $user->get('id')
											. ' AND ' .$db->quoteName('gr.addevent') . ' = 1 '
													. ' AND g.member NOT LIKE 0';
					$db->setQuery($query);
					$groupnumber = $db->loadColumn();
					$categories = implode(' OR c.groupid = ', $groupnumber);

					//build ids query
					if ($categories) {
						//check if user is allowed to submit events in general, if yes allow to submit into categories
						//which aren't assigned to a group. Otherwise restrict submission into maintained categories only
						if (JEMUser::validate_user($jemsettings->evdelrec, $jemsettings->delivereventsyes)) {
							$where .= ' AND c.groupid = 0 OR c.groupid = '.$categories;
						} else {
							$where .= ' AND c.groupid = '.$categories;
						}
					} else {
						$where .= ' AND c.groupid = 0';
					}

					//administrators or superadministrators have access to all categories, also maintained ones
					if($superuser) {
						$where = ' WHERE c.published = 1';
					}
		} else {
			# specified category
			$where = ' WHERE c.id = '. $guestcat;
		}

		//get the maintained categories and the categories whithout any group
		//or just get all if somebody have edit rights
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query = 'SELECT c.*,c.id as value,catname as text'
				. ' FROM #__jem_categories AS c'
				. $where
				. ' ORDER BY c.ordering'
				;
		$db->setQuery($query);

		//	$this->_category = array();
		//	$this->_category[] = JHtml::_('select.option', '0', JText::_( 'COM_JEM_SELECT_CATEGORY' ) );
		//	$this->_categories = array_merge( $this->_category, $this->_db->loadObjectList() );

		$mitems = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseNotice(500, $db->getErrorMsg());
		}

		if (!$mitems)
		{
			$mitems = array();
			$children = array();

			$parentid = $mitems;
		}
		else
		{
			$mitems_temp = $mitems;

			$children = array();
			// First pass - collect children
			foreach ($mitems as $v)
			{
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}

			$parentid = intval($mitems[0]->parent_id);
		}

		//get list of the items
		$list = JEMCategories::treerecurse($parentid, '', array(), $children, 9999, 0, 0);

		return $list;
	}
}
