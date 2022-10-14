<div id="admin-venues">
	<h1><?php _e('Manage Venues');?></h1>
	<a href="/wp-admin/admin.php?page=admin-venues&action=add"><?php _e('Add Venue');?></a><br/><br/>

	<p><?php _e('View and edit venues.');?></p>
	<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-venues" method="post">
		<input type="hidden" name="action" value="search"/>
		<input type="text" name="search" value=""/>
		<input type="submit" value="Find Venues" class="button button-primary button-large"/><br/>
		(An empty search lists all venues)
	</form>
	<?php  if (isset($venues) && is_array($venues) && !isset($numStudents)): ?>
		<form id="dance-school-admin-venues-bulk-action-form" action="<?php echo get_admin_url(); ?>admin.php?page=admin-venues" method="post">
			<div class="actions bulkactions">
				<select id="bulk-action" name="action">
					<option><?php _e('Bulk Actions');?></option>
					<option value="deletevenues"><?php _e('Delete venues');?></option>
				</select>
				<input type="submit" value="Apply" class="button action button-primary button-large" id="doaction" name="">
			</div>


		<table cellspacing="0" cellpadding="0" border ="1" class="gridtable tablesortable">
		<thead>
			<tr>
				<th><input type="checkbox" id="ds-selectall"></th>
				<th><?php _e('Name');?></th>
				<th><?php _e('Address');?></th>
				<th><?php _e('Phone');?></th>
				<th><?php _e('Email');?></th>
				<th><?php _e('Upcoming Events');?></th>
				<th><?php _e('Edit Selected');?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
                foreach ($venues as $v):
            ?>
				<tr>
					<td><input type="checkbox" name="ids[]" value="<?=$v->id?>"/></td>
					<td>
						<?=$v->name;?>
					</td>
					<td>
						<?=$v->address_data->address1;?> <?=$v->address_data->city;?> <?=$v->address_data->postal_code;?>
					</td>
					<td>
						<?=$v->address_data->phone;?>
					</td>
					<td>
						<?php echo !empty($v->meta->email) ? $v->meta->email : '';?>
					</td>
					<td>
						<a href="/wp-admin/admin.php?page=admin-events" title="Upcoming Events"><?=$v->total_upcoming_events;?></a>
					</td>
					<td>
						<a href="/wp-admin/admin.php?page=admin-venues&action=edit&venue_id=<?=$v->id?>" title="">Edit</a>
					</td>
					<td>
						<a href="/wp-admin/admin.php?page=admin-venues&action=delete&venue_id=<?=$v->id?>" onclick="return confirm('Are you sure you want to delete this venue? This will delete all details permanently.');" class="dashicons dashicons-dismiss">&nbsp;</a>
					</td>
				</tr>
			<?php
                endforeach;
            ?>
		</tbody>
		</table>

		</form>
	<?php  endif; ?>
</div>
