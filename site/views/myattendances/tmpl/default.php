<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
?>

<div id="jem" class="jem_myattendances<?php echo $this->pageclass_sfx;?>">

<div class="topbox"></div>
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<!--table-->
	<div class="clearfix"></div>
	<div class="info_container">
	<?php echo $this->loadTemplate('attendances');?>
	</div>

	<!--footer-->
	<div class="poweredby">
		<?php echo JemOutput::footer( ); ?>
	</div>
</div>
