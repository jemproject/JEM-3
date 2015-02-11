<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$params		= $this->item->params;
$images 	= json_decode($this->item->datimage);
$canEdit	= $this->item->params->get('access-edit');
$user		= JFactory::getUser();
$attribs 	= json_decode($this->item->attribs);

JHtml::_('behavior.modal', 'a.flyermodal');
$mapType = $this->mapType;
?>
<?php if ($params->get('access-view')){?>
<div id="jem" class="event_id<?php echo $this->item->did; ?> jem_event<?php echo $this->pageclass_sfx;?>" itemscope itemtype="http://schema.org/Event">
<div class="topbox">
	<div class="btn-group pull-right hidden-phone">
		<?php
			if ($this->print) {
				echo JemOutput::printbutton($this->print_link, $this->params);
			} else {
		?>
		<div class="button_flyer icons">
		<?php
			echo JemOutput::submitbutton($this->addeventlink, $this->params);
			echo JemOutput::addvenuebutton($this->addvenuelink, $this->params, $this->jemsettings);
			if ($params->get('event_show_email_icon',1)) {echo JemOutput::mailbutton($this->item->slug, 'event', $this->params);}
			if ($params->get('event_show_print_icon',1)) { echo JemOutput::printbutton($this->print_link, $this->params);}
		?>
		</div>
		<?php } ?>
	</div>
</div>

<div class="info_container">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

<!-- Event -->
	<h2 class="jem">
	<?php
		echo JText::_('COM_JEM_EVENT');
		echo JemOutput::editbutton($this->item, $params, $attribs, $this->allowedtoeditevent, 'editevent');
		?>
	</h2>

	<?php if ($this->img_position == 0) { ?>
		<?php if ($this->dimage) { ?>
		<div class="image imagetop">
			<?php echo JemOutput::flyer($this->item, $this->dimage, 'event'); ?>
		</div>
		<?php } ?>
	<?php } ?>

<!-- EVENT-INFO -->
<?php if ($this->img_position == 1) { ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-7">
<?php } ?>

	<dl class="event_info">
		<?php if ($params->get('event_show_detailstitle',1)) : ?>
			<dt class="title"><?php echo JText::_('COM_JEM_TITLE').':'; ?></dt>
		<dd class="title" itemprop="name"><?php echo $this->escape($this->item->title); ?></dd>
		<?php
		endif;
		?>

		<?php
		$date = JemOutput::eventDateTime($this->item,true,true,true,true);

		$startDateTime	= false;
		$endDateTime	= false;
		$combinedDateTime = false;

		if (isset($date['startDateTime'])) {
			$startDateTime = $date['startDateTime'];
		}
		if (isset($date['endDateTime'])) {
			$endDateTime	= $date['endDateTime'];
		}
		if (isset($date['combinedDateTime'])) {
			$combinedDateTime	= $date['combinedDateTime'];
		}

		echo JemOutput::formatSchemaOrgDateTime($this->item->dates, $this->item->times,$this->item->enddates, $this->item->endtimes);

		if ($startDateTime && $endDateTime && !$combinedDateTime) {
		?>

		<dt class="when"><?php echo JText::_('COM_JEM_DATE_START').':'; ?></dt>
		<dd class="when">
			<?php
				echo $startDateTime;
			?>
		</dd>
		<dt class="when"><?php echo JText::_('COM_JEM_DATE_END').':'; ?></dt>
		<dd class="when">
			<?php
				echo $endDateTime;
			?>
		</dd>
		<?php } ?>


		<?php
		if (!$startDateTime && $endDateTime) {
		?>
		<dt class="when"><?php echo JText::_('COM_JEM_DATE_END').':'; ?></dt>
		<dd class="when">
			<?php
				echo $endDateTime;
			?>
		</dd>
		<?php } ?>

		<?php
		if (($startDateTime && !$endDateTime) || $combinedDateTime) {
		?>

		<dt class="when"><?php echo JText::_('COM_JEM_DATE').':'; ?></dt>
		<dd class="when">
			<?php
			if ($combinedDateTime) {
				echo $combinedDateTime;
			} else {
				echo $startDateTime;
			}
			?>
		</dd>
		<?php } ?>


		<?php if ($this->item->locid != 0) : ?>
			<dt class="where"><?php echo JText::_('COM_JEM_WHERE').':'; ?></dt>
		<dd class="where">
				<?php if (($params->get('event_show_detlinkvenue') == 1) && (!empty($this->item->url))) : ?>
					<a target="_blank" href="<?php echo $this->item->url; ?>"><?php echo $this->escape($this->item->venue); ?></a> -
				<?php elseif ($params->get('event_show_detlinkvenue') == 2) : ?>
					<a
				href="<?php echo JRoute::_(JemHelperRoute::getVenueRoute($this->item->venueslug)); ?>"><?php echo $this->item->venue; ?></a> -
				<?php elseif ($params->get('event_show_detlinkvenue') == 0) :
					echo $this->escape($this->item->venue).' - ';
				endif;

				if ($this->item->city && $this->item->state) {
					echo $this->escape($this->item->city).', '.$this->escape($this->item->state);
				} else {
					if ($this->item->city) {
						echo $this->escape($this->item->city);
					}
				}
				 ?>
			</dd>
		<?php endif;
			$n = count($this->categories);
		?>

		<dt class="category"><?php echo $n < 2 ? JText::_('COM_JEM_CATEGORY') : JText::_('COM_JEM_CATEGORIES'); ?>:</dt>
		<dd class="category">
			<?php
			$i = 0;
			foreach ($this->categories as $category) :
			?>
				<a
				href="<?php echo JRoute::_(JemHelperRoute::getCategoryRoute($category->catslug)); ?>">
					<?php echo $this->escape($category->catname); ?>
				</a>
			<?php
				$i++;
				if ($i != $n) :
					echo ', ';
				endif;
			endforeach;
			?>
		</dd>

		<?php
		for($cr = 1; $cr <= 10; $cr++) {
			$currentRow = $this->item->{'custom'.$cr};
			if(preg_match('%^https?://[^\s]+$%', $currentRow)) {
				$currentRow = '<a href="'.$this->escape($currentRow).'" target="_blank">'.$this->escape($currentRow).'</a>';
 			}
			if($currentRow) {
		?>
				<dt class="custom<?php echo $cr; ?>"><?php echo JText::_('COM_JEM_EVENT_CUSTOM_FIELD'.$cr).':'; ?></dt>
		<dd class="custom<?php echo $cr; ?>"><?php echo $currentRow; ?></dd>
		<?php
			}
		}
		?>

		<?php if ($params->get('event_show_hits')) : ?>
		<dt class="hits"><?php echo JText::_('COM_JEM_EVENT_HITS_LABEL'); ?></dt>
		<dd class="hits"><?php echo JText::sprintf('COM_JEM_EVENT_HITS', $this->item->hits); ?></dd>
		<?php endif; ?>


<!-- AUTHOR -->
		<?php if ($params->get('event_show_author') && !empty($this->item->author)) : ?>
		<dt class="createdby"><?php echo JText::_('COM_JEM_EVENT_CREATED_BY_LABEL'); ?></dt>
		<dd class="createdby">
		<?php $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author; ?>
		<?php echo JText::sprintf('COM_JEM_EVENT_CREATED_BY', $author); ?>
		</dd>
		<?php endif; ?>
		</dl>

		<?php if ($this->img_position == 1) { ?>
		</div><div class="col-md-5">
			<?php if ($this->dimage) { ?>
			<div class="image imageright">
				<?php echo JemOutput::flyer($this->item, $this->dimage, 'event'); ?>
			</div>
			<?php } ?>
		</div></div></div>
		<?php } ?>


<!-- DESCRIPTION -->
		<?php if ($params->get('event_show_description','1') && ($this->item->fulltext != '' && $this->item->fulltext != '<br />' || $this->item->introtext != '' && $this->item->introtext != '<br />')) { ?>
		<h2 class="description"><?php echo JText::_('COM_JEM_EVENT_DESCRIPTION'); ?></h2>
		<div class="description event_desc" itemprop="description">

		<?php //optional teaser intro text for guests ?>
		<?php if ($params->get('event_show_noauth') == true and  $user->get('guest') ) { ?>

		<?php echo $this->item->introtext; ?>
		<?php //Optional link to let them register to see the whole event. ?>
		<?php if ($params->get('event_show_readmore') && $this->item->fulltext != null) {
		$link1 = JRoute::_('index.php?option=com_users&view=login');
		$link = new JUri($link1);?>
		<p class="readmore">
		<a href="<?php echo $link; ?>">
		<?php
		if ($params->get('event_alternative_readmore') == false) {
			echo JText::_('COM_JEM_EVENT_REGISTER_TO_READ_MORE');
			} elseif ($readmore = $params->get('alternative_readmore')) {
			echo $readmore;
			}

		if ($params->get('event_show_readmore_title', 0) != 0) {
			    echo JHtml::_('string.truncate', ($this->item->title), $params->get('event_readmore_limit'));
			} elseif ($params->get('event_show_readmore_title', 0) == 0) {
			} else {
			echo JHtml::_('string.truncate', ($this->item->title), $params->get('event_readmore_limit'));
		} ?></a>
		</p>
		<?php }
			} else {
			echo $this->item->text;
			}
		?>
		</div>
	<?php } ?>


<!--  Contact -->
       <?php if ($params->get('event_show_contact') && !empty($this->item->conid )) : ?>

        <h2 class="contact">
         			<?php echo JText::_('COM_JEM_CONTACT') ; ?>
         		</h2>

        		<dl>
        		<dt class="con_name"><?php echo JText::_('COM_JEM_NAME').':'; ?></dt>
        			<dd class="con_name">
          <?php        $contact = $this->item->conname;
        if ($params->get('event_link_contact') == true):
        $needle = 'index.php?option=com_contact&view=contact&id=' . $this->item->conid;
        $menu = JFactory::getApplication()->getMenu();
        $item = $menu->getItems('link', $needle, true);
        $cntlink2 = !empty($item) ? $needle . '&Itemid=' . $item->id : $needle;
        ?>
        			<?php
			echo JText::sprintf('COM_JEM_EVENT_CONTACT', JHtml::_('link', JRoute::_($cntlink2), $contact));
 			else:
			echo JText::sprintf('COM_JEM_EVENT_CONTACT', $contact);
			endif;
 			?>
 		</dd>

 		<?php if ($this->item->contelephone) : ?>
		<dt class="con_telephone"><?php echo JText::_('COM_JEM_TELEPHONE').':'; ?></dt>
 		<dd class="con_telephone">
		<?php echo $this->escape($this->item->contelephone); ?>
 		</dd>
		<?php endif; ?>
		</dl>
        <?php endif ?>

	<?php $this->attachments = $this->item->attachments; ?>
	<?php echo $this->loadTemplate('attachments'); ?>
	<!--  	Venue  -->
	<?php if ($this->item->locid != 0) : ?>
	<p></p>

		<div>
		<h2 class="location">
			<?php
			echo JText::_('COM_JEM_VENUE') ;
			$itemid = $this->item ? $this->item->id : 0 ;
			echo JemOutput::editbutton($this->item, $params, $attribs, $this->allowedtoeditvenue, 'editvenue');
			?>
		</h2>

<!-- image -->
		<?php if ($this->img_position == 0) { ?>
			<?php if ($this->limage) { ?>
			<div class="image imagetop">
				<?php echo JemOutput::flyer($this->item, $this->limage, 'venue'); ?>
			</div>
			<?php } ?>
		<?php } ?>

<!-- VENUE-INFO -->
<?php if ($this->img_position == 1) { ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-7">
<?php } ?>
		<dl class="location_dl" itemprop="location" itemscope itemtype="http://schema.org/PostalAddress">
			<dt class="venue"><?php echo JText::_('COM_JEM_LOCATION').':'; ?></dt>
			<dd class="venue">
			<?php echo "<a href='".JRoute::_(JemHelperRoute::getVenueRoute($this->item->venueslug))."'>".$this->escape($this->item->venue)."</a>"; ?>

			<?php if (!empty($this->item->url)) : ?>
				&nbsp; - &nbsp;
				<a target="_blank" href="<?php echo $this->item->url; ?>"> <?php echo JText::_('COM_JEM_WEBSITE'); ?></a>
			<?php endif; ?>
			</dd>
		<?php if ($params->get('event_show_detailsadress','1')) : ?>
				<?php if ($this->item->street) : ?>
				<dt class="venue_street"><?php echo JText::_('COM_JEM_STREET').':'; ?></dt>
			<dd class="venue_street" itemprop="streetAddress">
					<?php echo $this->escape($this->item->street); ?>
				</dd>
				<?php endif; ?>

				<?php if ($this->item->postalCode) : ?>
				<dt class="venue_postalCode"><?php echo JText::_('COM_JEM_ZIP').':'; ?></dt>
			<dd class="venue_postalCode" itemprop="postalCode">
					<?php echo $this->escape($this->item->postalCode); ?>
				</dd>
				<?php endif; ?>

				<?php if ($this->item->city) : ?>
				<dt class="venue_city"><?php echo JText::_('COM_JEM_CITY').':'; ?></dt>
			<dd class="venue_city" itemprop="addressLocality">
					<?php echo $this->escape($this->item->city);?>
				</dd>
				<?php endif; ?>

				<?php if ($this->item->state) : ?>
				<dt class="venue_state"><?php echo JText::_('COM_JEM_STATE').':'; ?></dt>
			<dd class="venue_state" itemprop="addressRegion">
					<?php echo $this->escape($this->item->state); ?>
				</dd>
				<?php endif; ?>

				<?php if ($this->item->country) : ?>
				<dt class="venue_country"><?php echo JText::_('COM_JEM_COUNTRY').':'; ?></dt>
			<dd class="venue_country">
					<?php echo $this->item->countryimg ? $this->item->countryimg : $this->item->country; ?>
					<meta itemprop="addressCountry"
					content="<?php echo $this->item->country; ?>" />
			</dd>
				<?php endif; ?>

				<div id="venue_contactdetails">
			<?php if ($this->item->phone) : ?>
			<dt class="venue_phone"><?php echo JText::_('COM_JEM_PHONE').':'; ?></dt>
			<dd class="venue_phone">
				<?php echo $this->escape($this->item->phone); ?>
			</dd>
			<?php endif; ?>
			<?php if ($this->item->fax) : ?>
			<dt class="venue_fax"><?php echo JText::_('COM_JEM_FAX').':'; ?></dt>
			<dd class="venue_fax">
				<?php echo $this->escape($this->item->fax); ?>
			</dd>
			<?php endif; ?>
			<?php if ($this->item->email) : ?>
			<dt class="venue_email"><?php echo JText::_('COM_JEM_EMAIL').':'; ?></dt>
			<dd class="venue_email">
				<?php echo $this->escape($this->item->email); ?>
			</dd>
			<?php endif; ?>
		</div>

				<?php
		for($cr = 1; $cr <= 10; $cr++) {
			$currentRow = $this->item->{'venue'.$cr};
			if(preg_match('%^https?://[^\s]+$%', $currentRow)) {
				$currentRow = '<a href="'.$this->escape($currentRow).'" target="_blank">'.$this->escape($currentRow).'</a>';
 			}
			if($currentRow) {
		?>
				<dt class="custom<?php echo $cr; ?>"><?php echo JText::_('COM_JEM_VENUE_CUSTOM_FIELD'.$cr).':'; ?></dt>
			<dd class="custom<?php echo $cr; ?>"><?php echo $currentRow; ?></dd>
		<?php
			}
		}
		?>
				<?php if ($params->get('event_show_mapserv')== 1) : ?>
					<?php echo JemOutput::mapicon($this->item,'event',$params); ?>
				<?php endif; ?>
			<?php endif; ?>
		</dl>

	<?php if ($this->img_position == 1) { ?>
	</div><div class="col-md-5">
<!-- image -->
	<?php if ($this->limage) { ?>
		<div class="image imageright">
		<?php echo JemOutput::flyer($this->item, $this->limage, 'venue'); ?>
	</div>
	<?php } ?>
	</div></div></div>
	<?php } ?>
			<?php if ($params->get('event_show_mapserv')== 2) : ?>
				<?php echo JemOutput::mapicon($this->item,'event',$params); ?>
			<?php endif; ?>

			<?php if ($params->get('event_show_mapserv')== 3) : ?>

			<input type="hidden" id="latitude" value="<?php echo $this->item->latitude;?>">
			<input type="hidden" id="longitude" value="<?php echo $this->item->longitude;?>">

			<input type="hidden" id="venue" value="<?php echo $this->item->venue;?>">
			<input type="hidden" id="street" value="<?php echo $this->item->street;?>">
			<input type="hidden" id="city" value="<?php echo $this->item->city;?>">
			<input type="hidden" id="state" value="<?php echo $this->item->state;?>">
			<input type="hidden" id="postalCode" value="<?php echo $this->item->postalCode;?>">
			<input type="hidden" id="mapType" value="<?php echo $this->mapType;?>">
				<?php echo JemOutput::mapicon($this->item,'event',$params); ?>
			<?php endif; ?>
<!-- DESCRIPTION -->

		<?php if ($params->get('event_show_locdescription','1') && $this->item->locdescription != ''
			&& $this->item->locdescription != '<br />') : ?>

			<h2 class="location_desc"><?php echo JText::_('COM_JEM_VENUE_DESCRIPTION'); ?></h2>
		<div class="description location_desc" itemprop="description">
				<?php echo $this->item->locdescription; ?>
			</div>
		<?php endif; ?>


		<?php $this->attachments = $this->item->vattachments; ?>
	<?php echo $this->loadTemplate('attachments'); ?>

		</div>
	<?php endif; ?>



	<?php if ($this->item->registra == 1) : ?>
		<h2 class="register"><?php echo JText::_('COM_JEM_REGISTRATION'); ?></h2>
			<?php echo $this->loadTemplate('attendees'); ?>
	<?php endif; ?>

	</div>

<!-- call dispatcher -->
	<?php echo $this->item->pluginevent->onEventEnd; ?>

	<div id="iCal" class="iCal">
	<?php
		if ($params->get('event_show_ical_icon',1)) {
			echo JemOutput::icalbutton($this->item->slug, 'event');
		}
		?>
	</div>
	<div class="poweredby">
		<?php echo JemOutput::footer(); ?>
	</div>
</div>
<?php } ?>
