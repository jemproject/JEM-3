<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @todo add check if CB does exists and if so perform action
 */
defined('_JEXEC') or die;

$user		= JFactory::getUser();
$userId		= $user->get('id');
$params		= $this->item->params;
?>

<?php
if (!($this->formhandler == 2) && !($this->formhandler == 1)) {
?>

<div class="container-fluid">
<div class="row">
<div class="col-md-7">
	<dl>
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

		<?php
		if ($this->item->booked > 0){ ?>
		<dt class=""><?php echo JText::_('COM_JEM_REGISTERED_USERS').':';?></dt>
		<dd class=""><?php echo $this->item->booked; ?></dd>
		<?php } ?>
	</dl>
</div>
<div class="col-md-5">
</div>

</div>
</div><!-- end container-fluid -->
<?php } ?>

<!-- Attending users -->
<?php
$type_attendee = $params->get('event_attendeelist_visiblefor','0');

if ($type_attendee == 0) {
# visible for guest
	$check = $params->get('event_show_name_attendee','1') && $user->get('guest') == true;
}

if ($type_attendee == 1) {
# visible for registered
	$check = $params->get('event_show_name_attendee','1') && $userId == true;
}

if ($type_attendee == 2) {
	# visible for registered + guest
	$check = $params->get('event_show_name_attendee','1') && $user->get('guest') == true || $params->get('event_show_name_attendee','2') && $userId == true;
}


if ( $check && $this->registers|| JFactory::getUser()->authorise('core.manage') && $this->registers) :
?>

<div class="container-fluid">
<div class="row userbox">

<!-- output names -->
	<span class="register label label-info"><?php echo JText::_('COM_JEM_REGISTERED_USERS'); ?></span>
	<ul class="user ">


<?php
// define variables before the foreach

# Community Builder
if ($this->settings->get('event_comunsolution','0')==1) {
	static $CB_loaded;
	
	if (!$CB_loaded) {
		if ((!file_exists(JPATH_SITE.'/libraries/CBLib/CBLib/Core/CBLib.php')) || (!file_exists(JPATH_ADMINISTRATOR.'/components/com_comprofiler/plugin.foundation.php'))) {
			echo 'CB not installed'; return;
		}
	
		include_once(JPATH_ADMINISTRATOR.'/components/com_comprofiler/plugin.foundation.php' );
	}
}


# Kunena
if ($this->settings->get('event_comunsolution','0')==2) {
	$kconfig = $this->KunenaConfig;

	if ($kconfig->get('username')) {
		$name = 'username';
	} else {
		$name = 'name';
	}
	//$width = '60';
	//$height = '60';

	if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_kunena/api.php')) {
		// echo 'Kunena not installed!';
	} else {
		include_once(JPATH_ADMINISTRATOR.'/components/com_kunena/api.php' );
	}
}

//  loop trough the registerdata
foreach ($this->registers as $register) :

	// no community component is set so only show the name according to global setting
	if ($this->settings->get('event_comunsolution','0')==0) {
		$name = $this->settings->get('global_regname','1') ? 'name' : 'username';
		echo "<li><span class='username'>".$register->$name."</span></li>";
	}

	// Community Builder
	if ($this->settings->get('event_comunsolution','0')==1) :
		$cbUserCreate	= CBuser::getInstance( (int) $register->uid, false );
		$name 		= $cbUserCreate->getField( 'formatname', null, 'html', 'none', 'list', 0, true );

		# name with avatar + link
		if ($this->settings->get('event_comunoption','0')==1) {
			$cbUser = CBuser::getInstance($register->uid);
			if (!$cbUser) {
				$cbUser = CBuser::getInstance(null);
			}
			$avatar = $cbUser->getField( 'avatar', null, 'html', 'none', 'list' );
			echo "<li><a href='".JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$register->uid )."'>".$avatar."<span class='username'>".$name."</span></a></li>";
		}

		# name with link
		if ($this->settings->get('event_comunoption','0')==0) {
			echo "<li><span class='username'><a href='".JRoute::_( 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user='.$register->uid )."'>".$name." </a></span></li>";
		}
	endif;

	// Kunena
	if ($this->settings->get('event_comunsolution','0')==2) {
		$user	= KunenaFactory::getUser($register->uid);
		$avatar = $user->getAvatarImage('', '', '');

		# name with avatar + link
		if ($this->settings->get('event_comunoption','0')==1) {
			echo "<li><a href='".JRoute::_('index.php?option=com_kunena&view=user&userid='.$register->uid )."'>".$avatar."<span class='username'>".$register->$name."</span></a></li>";
		}

		# name with link
		if ($this->settings->get('event_comunoption','0')==0) {
			echo "<li><span class='username'><a href='".JRoute::_('index.php?option=com_kunena&view=user&userid='.$register->uid )."'>".$register->$name." </a></span></li>";
		}
	}

//end loop through attendees
endforeach;
?>

	</ul>

</div></div>
<?php endif; ?>
<br>
<div class="clearfix"></div>


<div class="container-fluid">
		<div class="row">
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
