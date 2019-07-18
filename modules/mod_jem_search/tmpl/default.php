<?php
/**
 * @package JEM
 * @subpackage JEM - Module-Basic
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
?>

<ul class="jemmod<?php echo $params->get('moduleclass_sfx'); ?>">
<?php if (count($items)) : ?>

<?php foreach ($list as $item) : ?>
	<li class="jemmod<?php echo $params->get('moduleclass_sfx'); ?>">
		<?php if ($params->get('linkdet') == 1) : ?>
		<a href="<?php echo $item->link; ?>" class="jemmod<?php echo $params->get('moduleclass_sfx'); ?>">
			<?php echo $item->dateinfo; ?>
		</a>
		<?php else :
			echo $item->dateinfo;
		endif;
		?>

		<br />

		<?php if ($params->get('showtitloc') == 0 && $params->get('linkloc') == 1) : ?>
			<a href="<?php echo $item->venueurl; ?>" class="jemmod<?php echo $params->get('moduleclass_sfx'); ?>">
				<?php echo $item->text; ?>
			</a>
		<?php elseif ($params->get('showtitloc') == 1 && $params->get('linkdet') == 2) : ?>
			<a href="<?php echo $item->link; ?>" class="jemmod<?php echo $params->get('moduleclass_sfx'); ?>">
				<?php echo $item->text; ?>
			</a>
		<?php
			else :
				echo $item->text;
			endif;
		?>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
