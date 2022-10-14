<h1><?php _e('Parent Management - Incomplete Registrations');?></h1>

<p><a href="/wp-admin/admin.php?page=admin-manageparents"><?php _e('Return to Parent Managment main page');?></a></p>

<p><?php _e('View incomplete registrations. Data will not always be complete and duplicate entries are likely. Incomplete registrations, client and student data should not be edited, but can be deleted. If the client wishes to complete the registration, please ask them to start over, or use a fresh admin registration.');?></p>

<?php  if (is_array($data)): ?>
<table cellspacing="0" cellpadding="0" border ="1" class="gridtable tablesortable">
<thead>
	<tr>
		<th><?php _e('Firstname');?></th>
		<th><?php _e('Lastname');?></th>
		<th><?php _e('Profile');?></th>
		<th><?php _e('Email');?></th>
		<th><?php _e('Date Added');?></th>
		<th><?php _e('Delete');?></th>
	</tr>
</thead>
<tbody>
<?php
    foreach ($data as $v):
?>
	<tr>
		<td><?=$v->firstname;?></td>
		<td><?=$v->lastname;?></td>
		<td><a class="dashicons dashicons-id" href="/wp-admin/admin.php?page=admin-manageparents&action=edit&parent_id=<?=$v->parent_id?>&show_unconfirmed=1"></a></td>
		<td><a class="dashicons dashicons-email-alt" href="mailto:<?=$v->email;?>" alt="<?=$v->email;?>" title="<?=$v->email;?>"></a></td>
		<td><?=$v->parent_added != '0000-00-00 00:00:00' ? $v->parent_added : $v->billing_added;?></td>
		<td><a href="javascript: void(0);" class="clickable dashicons dashicons-dismiss" onclick="if(confirm('<?php _e('Do you want to completely delete this incomplete registration?');?>')){ window.location='/wp-admin/admin.php?page=admin-manageparents&action=deleteparent&parent_id=<?=$v->parent_id?>'; }" ></a>
	</tr>
<?php
    endforeach;
?>
</tbody>
</table>
<?php  endif; ?>
</form>
