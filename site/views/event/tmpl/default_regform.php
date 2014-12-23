<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

//the user is not registered allready -> display registration form
?>
<?php
if ($this->item->registra == 1)
{

if ($this->print == 0) {
?>


<?php
if ($this->item->maxplaces > 0 && ($this->item->booked >= $this->item->maxplaces) && !$this->item->waitinglist):
?>

<!-- Full, not possible to attend -->
<p></p>
<p></p>
<div class="center">

	<span class="label label-warning">
		<?php echo JText::_('COM_JEM_EVENT_FULL_NOTICE'); ?>
	</span>
	
</div>
<p></p>
	
	
<?php else: ?>
<form id="JEM" action="<?php echo JRoute::_('index.php?option=com_jem&view=event&id='.(int) $this->item->id); ?>"  name="adminForm" id="adminForm" method="post">
	<p>
		<?php if ($this->item->maxplaces && ($this->item->booked >= $this->item->maxplaces)): // full event ?>
		<div class="center">
		<span class="label label-warning"><?php echo JText::_('COM_JEM_EVENT_STATUS_FULL_WAITINGLIST');?></span>
		</div>
		
			<?php $text = JText::_('COM_JEM_EVENT_FULL_REGISTER_TO_WAITING_LIST'); ?>
		<?php else: ?>
			<?php $text = JText::_('COM_JEM_I_WILL_GO'); ?>
		<?php endif; ?>
	</p>
	
	<p></p>
	<div class="center">
		<div class="btn-wrapper input-append">
			<div class="btn btn_chkbox"><input type="checkbox" name="reg_check" onclick="check(this, document.getElementById('jem_send_attend'))" /></div>
			<input class="btn btn_button hasTooltip" type="submit" id="jem_send_attend" name="jem_send_attend" value="<?php echo JText::_( 'COM_JEM_REGISTER' ); ?>" disabled="disabled" title="<?php echo JHtml::tooltipText($text); ?>" />
		</div>
	</div>
	

<p>
	<input type="hidden" name="rdid" value="<?php echo $this->item->did; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="editevent.userregister" />
</p>
</form>
<?php endif;
}

}