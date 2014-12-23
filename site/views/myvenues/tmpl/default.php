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

<div id="jem" class="jem_myvenues<?php echo $this->pageclass_sfx;?>">

<div class="topbox"></div>
<div class="clearfix"></div>
<div class="info_container">

	<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

	<!--table-->
	<?php echo $this->loadTemplate('venues');?>

	</div>
	
	
	<!--footer-->
	<div class="poweredby">
		<?php echo JEMOutput::footer( ); ?>
	</div>
</div>
