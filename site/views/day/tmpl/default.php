<?php
/**
 * @version 3.0.6
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

?>
<div id="jem" class="jem_day<?php echo $this->pageclass_sfx;?>">
<div class="topbox">
<div class="btn-group pull-right">	
	<?php 
	if ($this->print) { 
		echo JemOutput::printbutton($this->print_link, $this->params);
	} else {
	?>
	<div class="button_flyer icons">
		<?php
			echo JEMOutput::printbutton($this->print_link, $this->params );
		?>
	</div>
	<?php } ?>
</div></div>
<div class="clearfix"></div>
<!-- info -->
<div class="info_container">	
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<div class="clr"> </div>

	<?php if (isset($this->showdaydate)) : ?>
	<h2 class="jem">
	<?php echo $this->daydate; ?>
	</h2>
	<?php endif; ?>

	<!--table-->
	<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
		<?php echo $this->loadTemplate('table'); ?>
		<p>
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="view" value="day" />
		</p>
	</form>
</div>
	
	<!--footer-->
	<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

	<div class="poweredby">
	<?php echo JemOutput::footer( ); ?>
	</div>
</div>