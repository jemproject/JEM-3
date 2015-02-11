<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_jem.category');
$saveOrder	= $listOrder=='ordering';

$params		= (isset($this->state->params)) ? $this->state->params : new JObject();
$settings	= $this->settings;

$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
$canChange	= $user->authorise('core.edit.state') && $canCheckin;

$options = array(
		'onActive' => 'function(title, description){
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
    }',
		'onBackground' => 'function(title, description){
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
    }',
		'opacityTransition' => true,
		'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
		'useCookie' => true, // this must not be a string. Don't use quotes.
);
JHtml::_('bootstrap.tooltip');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jem&view=events'); ?>" method="post" name="adminForm" id="adminForm">
		
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'date-tab1','useCookie' => '1')); ?>
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'date-tab1', JText::_('COM_JEM_DATES_SINGLEDATE_TAB', true)); ?>

	<fieldset id="filter-bar"></fieldset>
	<div class="clr"> </div>
	<table class="table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="1%" class="center"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE', 'a.date', $listDirn, $listOrder ); ?></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE_NAME', 'a.date_name', $listDirn, $listOrder ); ?></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE_HOLIDAY', 'a.holiday', $listDirn, $listOrder ); ?></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE_CALENDAR', 'a.calendar', $listDirn, $listOrder ); ?></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE_ENABLED', 'a.enabled', $listDirn, $listOrder ); ?></th>
				<th width="1%" class="center"><?php echo JText::_('COM_JEM_ID'); ?></th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan="20">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>

		<tbody id="seach_in_here">
			<?php
			
			foreach ($this->items as $i => $row) :
			
			if (!$row->date_range) {
			
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<td class=""><a href="<?php echo 'index.php?option=com_jem&amp;task=date.edit&amp;id='.$row->id;?>"><?php echo $this->escape($row->date);?></a></td>
				<td class=""><?php echo $this->escape($row->date_name);?></td>
				<td class=""><?php echo $this->escape($row->holiday);?></td>
				<td class=""><?php echo $this->escape($row->calendar);?></td>
				<td class=""><?php 
				//echo $this->escape($row->enabled);
				echo JHtml::_('jemhtml.dateenabled', $row->enabled, $i, $canChange);
				
				?></td>
				<td class="center"><?php echo $this->escape($row->id); ?></td>
			</tr>
			<?php 
			}
			endforeach; 
			?>
		</tbody>
	</table>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	
	
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'date-tab2', JText::_('COM_JEM_DATES_MULTIDATE_TAB', true)); ?>	
	<fieldset id="filter-bar"></fieldset>
	<div class="clr"> </div>
	<table class="table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="1%" class="center"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE_START_RANGE', 'a.date_startdate_range', $listDirn, $listOrder ); ?></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE_END_RANGE', 'a.date_enddate_range', $listDirn, $listOrder ); ?></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE_NAME', 'a.date_name', $listDirn, $listOrder ); ?></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE_HOLIDAY', 'a.holiday', $listDirn, $listOrder ); ?></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE_CALENDAR', 'a.calendar', $listDirn, $listOrder ); ?></th>
				<th class="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_DATES_DATE_ENABLED', 'a.enabled', $listDirn, $listOrder ); ?></th>
				<th width="1%" class="center"><?php echo JText::_('COM_JEM_ID'); ?></th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan="20">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>

		<tbody id="seach_in_here">
			<?php
			
			foreach ($this->items as $i => $row) :

			if ($row->date_range) {
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<td><a href="<?php echo 'index.php?option=com_jem&amp;task=date.edit&amp;id='.$row->id;?>"><?php echo $this->escape($row->date_startdate_range);?></a></td>
				<td><?php echo $this->escape($row->date_enddate_range);?></td>
				<td class=""><?php echo $this->escape($row->date_name);?></td>
				<td class=""><?php echo $this->escape($row->holiday);?></td>
				<td class=""><?php echo $this->escape($row->calendar);?></td>
				<td class=""><?php 
				//echo $this->escape($row->enabled);
				echo JHtml::_('jemhtml.dateenabled', $row->enabled, $i, $canChange);
				?></td>
				<td class="center"><?php echo $this->escape($row->id); ?></td>
			</tr>
			<?php 
			}
			endforeach; 
			?>
		</tbody>
	</table>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.endTabSet');?>
	

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
