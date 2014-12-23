<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * 
 * @todo add Itemid parameter to action link
 */
defined('_JEXEC') or die;
?>
<?php
//the user is allready registered. Let's check if he can unregister from the event


if ($this->print == 0) {

if ($this->item->unregistra == 0) :

	//no he is not allowed to unregister
	echo JText::_( 'COM_JEM_ALLREADY_REGISTERED' );

else:

	//he is allowed to unregister -> display form
	?>
	
	
	<form id="JEM" action="<?php echo JRoute::_('index.php?option=com_jem&view=event&id='.(int) $this->item->id); ?>" method="post">
		<p>
			<?php if ($this->isregistered == 2): ?>
			
			
			<div class="center"><p>
		<span class="label label-warning"><?php echo JText::_('COM_JEM_EVENT_STATUS_WAITINGLIST');?></span>
		</p></div>
				<?php $text = JText::_( 'COM_JEM_WAITINGLIST_UNREGISTER_BOX' ); ?>
			<?php else: ?>
			<div class="center"><p>
			<p>
		<span class="label label-warning"><?php echo JText::_('COM_JEM_EVENT_STATUS_ATTENDING');?></span>
		</p></div>
				<?php $text = JText::_( 'COM_JEM_UNREGISTER_BOX' ); ?>
			<?php endif;?>
		<p></p>
		<div class="center">
			<div class="btn-wrapper input-append">
				<div class="btn btn_chkbox"><input type="checkbox" name="reg_check" onclick="check(this, document.getElementById('jem_send_attend'))" /></div>
				<input class="btn btn_button hasTooltip" type="submit" id="jem_send_attend" name="jem_send_attend" value="<?php echo JText::_('COM_JEM_UNREGISTER'); ?>" disabled="disabled" title="<?php echo JHtml::tooltipText($text); ?>" />
		</div>
		</div>

		<p></p>
			<input type="hidden" name="rdid" value="<?php echo $this->item->did; ?>" />
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="task" value="editevent.delreguser" />
	</form>
	<?php
endif;
}