<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.modal','a.flyermodal');
?>
<div id="jem" class="jem_category<?php echo $this->pageclass_sfx;?>">
<div class="topbox">
	<div class="btn-group pull-right hidden-phone">
		<?php
			if ($this->print) {
				echo JemOutput::printbutton($this->print_link, $this->params);
			} else {
		?>
	<div class="button_flyer icons">
	<?php
		echo JemOutput::submitbutton($this->dellink, $this->params);
		echo JemOutput::addvenuebutton($this->addvenuelink, $this->params, $this->jemsettings);
		echo JemOutput::archivebutton($this->params, $this->task, $this->category->slug);
		echo JemOutput::mailbutton($this->category->slug, 'category', $this->params);
		echo JemOutput::printbutton($this->print_link, $this->params);
	?>
	</div>
	<?php } ?>
	</div>
</div>
<div class="clearfix"></div>
<!--info-->
<div class="info_container">

	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>


	<?php if ($this->vsettings->get('show_category_image','1'))  : ?>
	<h2 class="jem">
			<?php echo '&nbsp'; ?>
	</h2>
		<div class="image imagetop">
		<?php
		// flyer
		if (empty($this->category->image)) {
			$jemsettings = JEMHelper::config();
			$imgattribs['width'] = $jemsettings->imagewidth;
			$imgattribs['height'] = $jemsettings->imagehight;

			echo JHtml::_('image', 'com_jem/noimage.png', $this->category->catname, $imgattribs, true);
		}
		else {
			echo JemOutput::flyer($this->category, $this->cimage, 'category');
		}
		?>
		</div>
		<?php endif; ?>


		<h2 class="description"><?php echo JText::_('COM_JEM_EVENT_DESCRIPTION'); ?></h2>
		<p><?php echo $this->description; ?></p>

	<!--subcategories-->
	<?php
	if ($this->maxLevel != 0 && !empty($this->category->id) && !empty($this->children[$this->category->id])) {
?>
		<div class="cat-children">
		<?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
		<h3>
			<?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
		</h3>
		<?php endif; ?>
		<?php echo $this->loadTemplate('subcategories'); ?>
		</div>
		<?php }; ?>



	<form action="<?php echo $this->action; ?>" method="post" id="adminForm">
	<!--table-->
		<?php echo $this->loadTemplate('events_table'); ?>
		<input type="hidden" name="option" value="com_jem" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<input type="hidden" name="view" value="category" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
	</form>

</div>

	<!--pagination-->
	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

	<!-- iCal -->
	<div id="iCal" class="iCal">
		<?php echo JemOutput::icalbutton($this->category->id, 'category'); ?>
	</div>

	<!-- copyright -->
	<div class="poweredby">
		<?php echo JemOutput::footer( ); ?>
	</div>
</div>