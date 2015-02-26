<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

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
<form action="<?php echo JRoute::_('index.php?option=com_jem&view=help'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
		<div id="sidebar" class="span3">
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search input-append">
					<label for="helpsearch" class="element-invisible"><?php echo JText::_('COM_JEM_SEARCH'); ?></label>
					<input type="text" name="helpsearch" id="helpsearch" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->help_search); ?>" class="input-small hasTooltip" title="<?php echo JHtml::tooltipText('COM_JEM_SEARCH'); ?>" />
					<button type="submit" class="btn hasTooltip" title="<?php JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i></button>
					<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="f=document.adminForm;f.helpsearch.value='';f.submit()">
						<i class="icon-remove"></i></button>
				</div>
			</div>
		<div class="clearfix"></div>
		<div class="sidebar-nav">
			<ul class="nav nav-list">
				<li><a href="<?php echo 'components/com_jem/help/'.$this->langTag.'/intro.html'; ?>" target='helpFrame'><?php echo JText::_('COM_JEM_HOME'); ?></a></li>
				<li><a href="<?php echo 'components/com_jem/help/'.$this->langTag.'/helpsite/gethelp.html'; ?>" target='helpFrame'><?php echo JText::_('COM_JEM_GET_HELP'); ?></a></li>
				<li><a href="<?php echo 'components/com_jem/help/'.$this->langTag.'/helpsite/givehelp.html'; ?>" target='helpFrame'><?php echo JText::_('COM_JEM_GIVE_HELP'); ?></a></li>
				<li><a href="<?php echo 'components/com_jem/help/'.$this->langTag.'/helpsite/credits.html'; ?>" target='helpFrame'><?php echo JText::_('COM_JEM_CREDITS_JEM'); ?></a></li>
				<li><a href="<?php echo 'components/com_jem/help/'.$this->langTag.'/helpsite/creditsel.html'; ?>" target='helpFrame'><?php echo JText::_('COM_JEM_CREDITS_EL'); ?></a></li>
				<li><?php echo JHtml::_('link', 'http://www.gnu.org/licenses/gpl-2.0.html', JText::_('COM_JEM_LICENSE'), array('target' => 'helpFrame')) ?></li>
				<hr class="hr-condensed" />
					<li class="nav-header"><?php echo JText::_('COM_JEM_SCREEN_HELP'); ?></li>
					<?php foreach ($this->toc as $k => $v): ?>
						<li>
							<?php echo JHtml::Link('components/com_jem/help/'.$this->langTag.'/'.$k, $v, array('target' => 'helpFrame'));?>
						</li>
					<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="span9">
			<iframe name="helpFrame" height="2100px" src="<?php echo 'components/com_jem/help/'.$this->langTag.'/intro.html'; ?>" class="helpFrame table table-bordered"></iframe>
		</div>
	</div>
<input class="textarea" type="hidden" name="option" value="com_jem" />
</form>

<?php
//keep session alive
JHtml::_('behavior.keepalive');
?>