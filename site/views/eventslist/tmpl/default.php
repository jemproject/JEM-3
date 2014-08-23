<?php
/**
 * @version 3.0.2
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;

$saveOrder	= $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_jem&task=events.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'eventList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>


<div id="jem" class="jem_eventslist<?php echo $this->pageclass_sfx;?>">
	
<div class="topbox">	
	<div class="btn-group pull-right">
	
	<?php 
	if ($this->print) { 
		echo JemOutput::printbutton($this->print_link, $this->params);
	} else {
		if ($this->settings->get('show_dropwdownbutton',1)) {
	?>
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span> <span class="caret"></span> </a>
			<ul id="dropdown" class="dropdown-menu">
				<li><?php echo JemOutput::submitbutton($this->dellink, $this->params);?></li>
				<li><?php echo JemOutput::addvenuebutton($this->addvenuelink, $this->params, $this->jemsettings);?></li>
				<li><?php echo JemOutput::archivebutton($this->params, $this->task);?></li>
				<li><?php echo JemOutput::printbutton($this->print_link, $this->params);?></li>
			</ul>
			
	<?php } } ?>
	</div>
</div>
<div class="clearfix"></div>
<!-- info -->	
<div class="info_container">	
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

	<div class="clearfix"></div>

	<?php if ($this->params->get('showintrotext')) : ?>
		<div class="description no_space floattext">
			<?php echo $this->params->get('introtext'); ?>
		</div>
	<?php endif; ?>

<!--table-->
	<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
		<?php echo $this->loadTemplate('events_table'); ?>

		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="view" value="eventslist" />
	</form>

	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	
<!--footer-->
	<?php if ($this->params->get('showfootertext')) : ?>
		<div class="description no_space floattext">
			<?php echo $this->params->get('footertext'); ?>
		</div>
	<?php endif; ?>
	
	<br/>

</div>	

	
	<div id="iCal" class="iCal">
			<?php echo JemOutput::icalbutton('', 'eventslist'); ?>
	</div>
	<div class="poweredby">
			<?php echo JemOutput::footer( ); ?>
	</div>
</div>