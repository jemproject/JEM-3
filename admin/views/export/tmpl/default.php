<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

?>
<script type="text/javascript">
    function selectAll()
    {
        selectBox = document.getElementById("cid");

        for (var i = 0; i < selectBox.options.length; i++){
             selectBox.options[i].selected = true;
        }
    }

    function unselectAll()
    {
        selectBox = document.getElementById("cid");

        for (var i = 0; i < selectBox.options.length; i++){
             selectBox.options[i].selected = false;
        }
    }
</script>


<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
	
	<div class="row-fluid">	
				<div class="span6">
	
	<fieldset class="form-horizontal">
			<legend><?php echo JText::_('COM_JEM_EXPORT_LEGEND_SELECTION');?></legend>
			
		<div class="control-group">
			<div class="control-label"><label class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_JEM_EXPORT_ADD_CATEGORYCOLUMN'); ?>">
		<?php echo JText::_('COM_JEM_EXPORT_ADD_CATEGORYCOLUMN'); ?></label></div>
		<div class="controls"><?php
				$categorycolumn = array();
				$categorycolumn[] = JHtml::_('select.option', '0', JText::_('JNO'));
				$categorycolumn[] = JHtml::_('select.option', '1', JText::_('JYES'));
				$categorycolumn = JHtml::_('select.genericlist', $categorycolumn, 'categorycolumn', array('size'=>'1','class'=>'inputbox'), 'value', 'text', '1');
				echo $categorycolumn;?>
		</div></div>
		
		<div class="control-group">
			<div class="control-label"><label for="dates"><?php echo JText::_('COM_JEM_DATE').':'; ?></label></div>
			<div class="controls"><?php echo JHtml::_('calendar', date("Y-m-d"), 'dates', 'dates', '%Y-%m-%d', array('class' => 'inputbox validate-date')); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><label for="enddates"><?php echo JText::_('COM_JEM_ENDDATE').':'; ?></label></div>
			<div class="controls"><?php echo JHtml::_('calendar', date("Y-m-d"), 'enddates', 'enddates', '%Y-%m-%d', array('class' => 'inputbox validate-date')); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><label for="cid"><?php echo JText::_('COM_JEM_CATEGORY').':'; ?></label></div>
			<div class="controls"><?php echo $this->categories; ?>
			<input class="btn" name="selectall" value="<?php echo JText::_('COM_JEM_EXPORT_SELECT_ALL_CATEGORIES'); ?>" onclick="selectAll();"><br />
			<input class="btn" name="unselectall" value="<?php echo JText::_('COM_JEM_EXPORT_UNSELECT_ALL_CATEGORIES'); ?>" onclick="unselectAll();">
			</div>
		</div>
		<div class="control-group">	
			<div class="control-label"><label></label></div>
			<div class="controls">
			
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.export';return true;"></input>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.exportsql';return true;"></input>
				</div>
				
			</div>
		
		</div>
		<div class="control-group">	
			<div class="control-label"><label></label></div>
			
		</div>
	</fieldset>
		
	</div><div class="span6">
	
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('COM_JEM_EXPORT_LEGEND_TABLES');?></legend>

		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_ATTACHMENTS'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_attachments';return true;"></input></div>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_sql_attachments';return true;"></input>
				</div>
		</div>	
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_CATEGORIES'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_categories';return true;"></input></div>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_sql_categories';return true;"></input>
				</div>
		</div>
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_CATSEVENTRELATIONS'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_cats_event_relations';return true;"></input></div>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_sql_cats_event_relations';return true;"></input>
				</div>
		</div>
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_EVENTS'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_events';return true;"></input></div>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_sql_events';return true;"></input>
				</div>
		</div>
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_GROUPS'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_groups';return true;"></input></div>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_sql_groups';return true;"></input>
				</div>
		</div>
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_RECURRENCEMASTER'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_recurrence_master';return true;"></input></div>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_sql_recurrence_master';return true;"></input>
				</div>
		</div>
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_RECURRENCE'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_recurrence';return true;"></input></div>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_sql_recurrence';return true;"></input>
				</div>
		</div>
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_REGISTER'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_register';return true;"></input></div>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_sql_register';return true;"></input>
				</div>
		</div>
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_SETTINGS'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_settings';return true;"></input></div>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_sql_settings';return true;"></input>
				</div>
		</div>
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_VENUES'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="csvexport" value="<?php echo JText::_('COM_JEM_EXPORT_FILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_venues';return true;"></input></div>
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.table_sql_venues';return true;"></input>
				</div>
		</div>
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('COM_JEM_EXPORT_TABLE_DUMP'); ?></label></div>
			<div class="controls">
				<div class="input-append">
					<input class="btn" type="submit" id="sqlexport" value="<?php echo JText::_('COM_JEM_EXPORT_SQLFILE'); ?>" onclick="document.getElementsByName('task')[0].value='export.tabledump';return true;"></input>
				</div>
			</div>
		</div>
		
		</fieldset>
		</div>
		</div>
		
	
	<input type="hidden" name="option" value="com_jem" />
	<input type="hidden" name="view" value="export" />
	<input type="hidden" name="controller" value="export" />
	<input type="hidden" name="task" value="" />
</form>