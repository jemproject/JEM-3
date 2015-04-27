<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();

if ($app->isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

require_once JPATH_ROOT . '/components/com_jem/helpers/route.php';

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework', true);

$function  = $app->input->getCmd('function', 'jSelectEvent');
//$listOrder = $this->escape($this->state->get('list.ordering'));
//$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<div id="jem" class="jem_eventslist_modal_<?php echo $this->pageclass_sfx;?>">
<form action="<?php echo JRoute::_('index.php?option=com_jem&view=eventslist&layout=modal&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->lists['search'];?>" class="inputbox input-medium" onchange="this.form.submit();" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom">
					<span class="icon-search"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.getElementById('filter_search').value='';this.form.submit();">
					<span class="icon-remove"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="btn-group pull-right">
		<?php echo $this->pagination->getLimitBox();?>
		</div>
			<div class="clearfix"></div>
		</div>
		<hr class="hr-condensed" />
		<div class="filters pull-left">
			<?php echo $this->lists['filter'].'&nbsp;'; ?>
		</div>

	</fieldset>

	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_JEM_DATE', 'a.dates', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_JEM_TIME_START', 'a.times', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_JEM_VENUE', 'l.venue', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_JEM_CITY', 'l.city', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JText::_('COM_JEM_CATEGORY'); ?>
				</th>
				<th class="center" width="1%" nowrap="nowrap">
					<?php echo JText::_('JSTATUS'); ?>
				</th>
				<th width="15%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="1%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->rows as $i => $item) :

		//Prepare date
		$displaydate = JemOutput::formatLongDateTime($item->dates, null, $item->enddates, null);
		// Insert a break between date and enddate if possible
		$displaydate = str_replace(" - ", " -<br />", $displaydate);

		//Prepare time
		if (!$item->times) {
			$displaytime = '-';
		} else {
			$displaytime = JemOutput::formattime($item->times);
		}

		if ($item->language && JLanguageMultilang::isEnabled())
			{
				$tag = strlen($item->language);
				if ($tag == 5)
				{
					$lang = substr($item->language, 0, 2);
				}
				elseif ($tag == 6)
				{
					$lang = substr($item->language, 0, 3);
				}
				else {
					$lang = "";
				}
			}
			elseif (!JLanguageMultilang::isEnabled())
			{
				$lang = "";
			}
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape(JemHelperRoute::getEventRoute($item->slug)); ?>', '<?php echo $this->escape(JemHelperRoute::getEventRoute($item->slug)); ?>', '<?php echo $this->escape($lang); ?>',null);">
						<?php echo $this->escape($item->title); ?></a>
				</td>
				<td>
				<?php
					// Format date
					echo JemOutput::formatLongDateTime($item->dates, null, $item->enddates, null);
				?>
				</td>
				<td>
				<?php
					// Prepare time
					if (!$item->times) {
						$displaytime = '-';
					} else {
						$time = strftime( $this->jemsettings->formattime, strtotime( $item->times ));
						$displaytime = $time.' '.$this->jemsettings->timename;
					}
					echo $displaytime;
				?>
				</td>
				<td><?php echo $item->venue ? $this->escape($item->venue) : '-'; ?></td>
				<td><?php echo $item->city ? $this->escape($item->city) : '-'; ?></td>
				<td>
				<?php
					# we're referring to the helper due to the multi-cat feature
					echo implode(", ",JemOutput::getCategoryList($item->categories, false));
				?>
				</td>
				<td class="center">
				<?php echo JHtml::_('jgrid.published', $item->published, $i,'events.',false,'cb'); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="function" value="<?php echo $this->escape($function); ?>" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
</div>
