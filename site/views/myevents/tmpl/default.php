<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

?>
<div id="jem" class="jem_myevents<?php echo $this->pageclass_sfx;?>">

<div class="topbox">
<div id="toolbar" class="btn-toolbar button_flyer">
	<?php echo JemOutput::publishbutton();?>
	<?php echo JemOutput::unpublishbutton();?>
	<?php echo JemOutput::trashbutton();?>
</div>
</div>
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

	<div class="clearfix"></div>
	<br>
	<div class="info_container">

	<!--table-->
	<?php echo $this->loadTemplate('events');?>

	</div>

	<!--footer-->
	<div class="poweredby">
		<?php echo JemOutput::footer( ); ?>
	</div>
</div>