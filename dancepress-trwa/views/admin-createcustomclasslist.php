<h1><?php _e('Create Custom Class List');?></h1>

<form action="<?php echo get_admin_url(); ?>admin.php" method="get">
	<input type="hidden" name="page" value="admin-listclass"/>
	<input type="hidden" name="action" value="showadvancedclasslist"/>
	<?php  foreach ($ids as $id):?>
		<input type="hidden" name="ids[]" value="<?=$id?>"/>
	<?php  endforeach;?>
	<?php  // Note field names correlate exactly to database tables, fields and json data in fields, so can be used programatically?>
	<input type="checkbox" name="ds_parents[address_data][phone_primary]" value="1"><?php _e('Primary Phone');?><br>
	<input type="checkbox" name="ds_parents[address_data][phone_secondary]" value="1"><?php _e('Secondary Phone');?><br>
	<input type="checkbox" name="ds_parents[firstname]" value="1"><?php _e('Parent Firstname');?><br>
	<input type="checkbox" name="ds_parents[lastname]" value="1"><?php _e('Parent Lastname');?><br>
	<input type="checkbox" name="ds_parents[email]" value="1"><?php _e('Primary Email');?><br>
	<input type="checkbox" name="ds_parents[meta][email_additional]" value="1"><?php _e('Secondary Email');?><br>
	<input type="checkbox" name="ds_students[firstname]" value="1"><?php _e('Student Firstname');?><br>
	<input type="checkbox" name="ds_students[lastname]" value="1"><?php _e('Student Lastname');?><br>
	<input type="checkbox" name="ds_students[birthdate]" value="1"><?php _e('Student Birthdate');?><br>
	<input type="checkbox" name="ds_students[gender]" value="1"><?php _e('Student Gender');?><br>
	<input type="checkbox" name="ds_students[measurements]" value="1"><?php _e('Display student measurements form');?><br>
	<input type="submit" class="button button-primary button-large" value="Build Printout"/>
</form>

<div style="page-break-after: always"></div>
