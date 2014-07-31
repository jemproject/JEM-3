<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * 
 * @todo add check if CB does exists and if so perform action
 */

defined('_JEXEC') or die;
$user				= JFactory::getUser();
$userId				= $user->get('id');
?>

<?php
if (!($this->formhandler == 2) || !($this->formhandler == 1)) { ?>

<div class="row-fluid">
<div class="span12">
<div class="span7">


<div class="dl">
	<dl class="">
		<?php 
		if ($this->item->maxplaces > 0 ) {?>
			<dt class=""><?php echo JText::_('COM_JEM_MAX_PLACES').':';?></dt>
			<dd class=""><?php echo $this->item->maxplaces; ?></dd>
			<dt class=""><?php echo JText::_('COM_JEM_BOOKED_PLACES').':';?></dt>
			<dd class=""><?php echo $this->item->booked; ?></dd>
		<?php } ?>
		<?php if ($this->item->maxplaces > 0): ?>
			<dt class=""><?php echo JText::_('COM_JEM_AVAILABLE_PLACES').':';?></dt>
			<dd>
			<?php 
			$places = $this->item->maxplaces-$this->item->booked;
			if ($places < 0) {
				$places = 0;
			}
			echo $places;
			?></dd>
		<?php
			endif;
		?>
		
		<?php if ($this->item->waiters > 0){ ?>
		<dt class=""><?php echo JText::_('COM_JEM_EVENT_WAITERS').':';?></dt>
		<dd class=""><?php echo $this->item->waiters; ?></dd>
		<?php } ?>
		
		<?php if ($this->registers > 0){ ?>
		<dt class=""><?php echo JText::_('COM_JEM_REGISTERED_USERS').':';?></dt>
		<dd class=""><?php echo count($this->registers); ?></dd>
		<?php } ?>
		
	</dl>
</div>
</div><!-- end span7 -->


<div class="span5">
</div>

</div><!-- end span 12 -->
</div><!-- end span row-fluid -->
<?php } ?>

<!-- Attending users -->

<?php
//only set style info if users already have registered and user is allowed to see it
if ($this->registers && $userId) :
?>



<div class="row-fluid">
<div class="span12 userbox">

<!-- output names -->
	<span class="register label label-info"><?php echo JText::_('COM_JEM_REGISTERED_USERS'); ?></span>
	<ul class="user ">
	

<?php
//loop through attendees
foreach ($this->registers as $register) :
	$text = '';
	// is a plugin catching this ?
	//TODO: remove markup..the plugin should handle this to improve flexibility
	if ($res = $this->dispatcher->trigger('onAttendeeDisplay', array( $register->uid, &$text ))) :
		echo '<li>'.$text.'</li>';
	endif;
	//if CB
	if ($this->settings->get('event_comunsolution','0')==1) :
	
		if ($this->settings->get('event_comunoption','0')==1) :
			//User has avatar
			if(!empty($register->avatar)) :
				$avatarname = $register->avatar;
				if (strpos($avatarname,'gallery/') !== false) {
    				$useravatar = JHtml::image('components/com_comprofiler/images/'.$register->avatar,$register->name);
				}
				else
				{
					$useravatar = JHtml::image('images/comprofiler/'.'tn'.$register->avatar,$register->name);
				}

				echo "<li><a href='".JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$register->uid )."'>".$useravatar."<span class='username'>".$register->name."</span></a></li>";

			//User has no avatar
			else :
				   $nouseravatar = JHtml::image('components/com_comprofiler/images/english/tnnophoto.jpg',$register->name);
				echo "<li><a href='".JRoute::_( 'index.php?option=com_comprofiler&task=userProfile&user='.$register->uid )."'>".$nouseravatar."<span class='username'>".$register->name."</span></a></li>";
			endif;
		endif;

		//only show the username with link to profile
		if ($this->settings->get('event_comunoption','0')==0) :
			echo "<li><span class='username'><a href='".JRoute::_( 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user='.$register->uid )."'>".$register->name." </a></span></li>";
		endif;

	//if CB end - if not CB than only name
	endif;

	//no communitycomponent is set so only show the username
	if ($this->settings->get('event_comunsolution','0')==0) :
		echo "<li><span class='username'>".$register->name."</span></li>";
	endif;

//end loop through attendees
endforeach;
?>

	</ul>
	
</div></div>
<?php endif; ?>

<div class="clearfix"></div>


<div class="row-fluid">
		<div class="span12">
		<?php
if ($this->print == 0) {
switch ($this->formhandler) {

	case 1:
		//echo '<span class="label-danger">'.JText::_('COM_JEM_TOO_LATE_REGISTER').'</span>';
		echo '';
	break;

	case 2:
		
		$html = array();
		$html[] = '<div class="center">';
		$html[] = '<span class="label label-warning">'.JText::_('COM_JEM_LOGIN_FOR_REGISTER').'</span>';
		$html[] = '</div>';
		
		echo implode("\n", $html);
		
		
	break;

	case 3:
		echo $this->loadTemplate('unregform');
	break;

	case 4:
		echo $this->loadTemplate('regform');
	break;
}
}
?></div>
</div>
