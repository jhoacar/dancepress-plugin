<h1><?php _e('Manage Students');?></h1>
<a href="/wp-admin/admin.php?page=admin-managestudents&action=search&search="><?php _e('Active Students');?></a> |
<a href="/wp-admin/admin.php?page=admin-managestudents&action=deactivatedstudents"><?php _e('Deactivated Students');?></a><br>

<?php  if (isset($numStudents)):?>
	<h4><?php _e('Total Registered/Active Students: ');?><?=$numStudents?></h4>
<?php endif;?>

<?php
    if (isset($succmsg) && $succmsg!="") {
        echo "<p>".$succmsg."</p>";
    }
?>
<p><?php _e('View and edit registered students.');?></p>
<?php if (isset($showDeactivated) && $showDeactivated):?>
	<p class="error"><?php _e('Showing only inactive students');?></p>
<?php endif;?>
<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-managestudents" method="post">
	<input type="hidden" name="action" value="search"/>
	<?php if (isset($showDeactivated) && $showDeactivated):?>
		<input type="hidden" name="showDeactivated" value="1"/>
	<?php endif; ?>
	<input type="text" name="search" value=""/>
	<input type="submit" value="Find Students" class="button button-primary button-large"/><br/>
	(An empty search lists all students)
</form>

<?php  if (isset($students) && is_array($students) && !isset($numStudents)): ?>

<form id="dance-school-admin-students-bulk-action-form" action="<?php echo get_admin_url(); ?>admin.php?page=admin-managestudents" method="post">

	<div class="actions bulkactions">
		<select id="bulk-action" name="action">
			<option><?php _e('Bulk Actions');?></option>
			<option value="addrecommendedcourse-selectcourse"><?php _e('Add recommended course');?></option>
			<?php if ($_REQUEST['action'] != 'deactivatedstudents') { ?><option value="deactivate-account"><?php _e('Deactivate Account');?></option><?php } ?>
			<option value="enable-registration"><?php _e('Enable online registration (use for rec students)');?></option>
			<option value="disable-registration"><?php _e('Disable online registration (use for company students)');?></option>
			<option value="disable-schedule"><?php _e('Disable viewing schedule (company students only)');?></option>
			<option value="enable-schedule"><?php _e('Enable viewing schedule (company students only)');?></option>
		</select>
		<input type="submit" value="Apply" class="button action button-primary button-large" id="doaction" name="">
	</div>


<table cellspacing="0" cellpadding="0" border ="1" class="gridtable tablesortable">
<thead>
	<tr>
		<th><input type="checkbox" id="ds-selectall"></th>
		<th><?php _e('Firstname');?></th>
		<th><?php _e('Lastname');?></th>
		<th><?php _e('Profile');?></th>
		<th><?php _e('Date registered');?></th>
		<th><?php _e('D.O.B.');?></th>
		<th><?php _e('Gender');?></th>
		<th>
			<?php _e('Can register online?');?><br/>
			<?php _e('(Rec/Company)');?>
		</th>
		<th>
			<?php _e('Can view schedule?');?><br/>
			<?php _e('(Company only)');?>
		</th>
		<th><?php _e('Deactivate');?></th>
		<th></th>
	</tr>
</thead>
<tbody>
	<?php
        foreach ($students as $v):

    ?>
		<tr>
			<td><input type="checkbox" name="ids[]" value="<?=$v->id?>"/></td>

			<td>
				<?=$v->firstname;?>
			</td>
			<td>
				<?=$v->lastname;?>
			</td>
			<td><a href="/wp-admin/admin.php?page=admin-managestudents&action=edit&student_id=<?=$v->id?>"><?php _e('Edit Profile');?></a></td>
			<td>
				<?=$v->date_added;?>
			</td>
			<td>
				<?=$v->birthdate;?>
			</td>
			<td>
				<?=$v->gender;?>
			</td>
			<td>
				<?=$v->can_register_online === '1' ? 'yes'  : 'no'?>
			</td>
			<td>
				<?=$v->schedule_available === '1' ? 'yes'  : 'no'?>
			</td>
			<td>
				<?=$v->active ? '<a href="/wp-admin/admin.php?page=admin-managestudents&action=deactivatestudent&student_id='.$v->id.'">' . __('Deactivate') . '</a>' :
                '<a href="/wp-admin/admin.php?page=admin-managestudents&action=activatestudent&student_id='.$v->id.'">' . __('Activate') . '</a>'; ?>
			</td>

			<td>
				<a href="/wp-admin/admin.php?page=admin-managestudents&action=delete&id=<?=$v->id?>&search=<?=@$_REQUEST['search']?>" onclick="return confirm('<?php _e('Are you sure you want to delete this account holder? This will delete all details permanently. You may also need to delete the user from the Users panel if the account is validated.');?>');" class="dashicons dashicons-dismiss">&nbsp;</a>
			</td>
		</tr>
	<?php
        endforeach;
    ?>
</tbody>
</table>
<?php  endif; ?>
</form>
