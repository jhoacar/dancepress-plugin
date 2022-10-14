<div id="admin-venues">
	<h1><?php _e('Manage Events');?></h1>
	<a href="/wp-admin/admin.php?page=admin-events"><?php _e('All Events');?></a> |
	<a href="/wp-admin/admin.php?page=admin-events&action=past"><?php _e('Past Events');?></a> |
	<a href="/wp-admin/admin.php?page=admin-events&action=current"><?php _e('Current Events');?></a> |
	<a href="/wp-admin/admin.php?page=admin-events&action=upcoming"><?php _e('Upcoming Events');?></a> |
	<a href="/wp-admin/admin.php?page=admin-events&action=add"><?php _e('Add Event');?></a><br/>

	<p><?php _e('View and edit events.');?></p>

	<?php if (!empty($_GET['action']) && $_GET['action'] == 'past'):?>
		<p class="error"><?php _e('Showing only past events');?></p>
	<?php elseif (!empty($_GET['action']) && $_GET['action'] == 'current'): ?>
		<p class="error"><?php _e('Showing only current events');?></p>
	<?php elseif (!empty($_GET['action']) && $_GET['action'] == 'upcoming'):?>
		<p class="error"><?php _e('Showing only upcoming events');?></p>
	<?php endif;?>

	<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-events" method="post">
		<input type="hidden" name="action" value="search"/>
		<input type="text" name="search" value=""/>
		<input type="submit" value="Find Events" class="button button-primary button-large"/><br/>
		(An empty search lists all events)
	</form>

	<?php  if (isset($events) && is_array($events)): ?>
		<form id="dance-school-admin-events-bulk-action-form" action="<?php echo get_admin_url(); ?>admin.php?page=admin-events" method="post">
			<div class="actions bulkactions">
				<select id="bulk-action" name="action">
					<option><?php _e('Bulk Actions');?></option>
					<option value="deleteevents"><?php _e('Delete events');?></option>
				</select>
				<input type="submit" value="Apply" class="button action button-primary button-large" id="doaction" name="">
			</div>

		<table cellspacing="0" cellpadding="0" border ="1" class="gridtable tablesortable">
		<thead>
			<tr>
				<th><input type="checkbox" id="ds-selectall"></th>
				<th><?php _e('Name');?></th>
				<th><?php _e('Venue');?></th>
				<th><?php _e('Starts');?></th>
				<th><?php _e('Ends');?></th>
				<th><?php _e('Tickets Sold');?></th>
				<th><?php _e('Tickets Available');?></th>
				<th><?php _e('Edit Selected');?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
                foreach ($events as $event):
            ?>
				<tr>
					<td><input type="checkbox" name="ids[]" value="<?=$event->id?>"/></td>
					<td>
						<?=$event->name;?>
					</td>
					<td>
						<?=$event->venue_name;?>
					</td>
					<td>
						<?=$event->starts;?>
					</td>
					<td>
						<?=$event->ends;?>
					</td>
					<td>
						<?=$event->tickets_sold;?>
						<?php if (!empty($event->tickets_sold)) {?>
							(<a href="/wp-admin/admin.php?page=admin-events&action=list-purchased-tickets&event_id=<?=$event->id?>" title="Tickets Sold"><?php _e('View Tickets');?></a>)
						<?php }?>
					</td>
					<td>
						<?=$event->tickets_available;?>
					</td>
					<td>
						<a href="/wp-admin/admin.php?page=admin-events&action=edit&event_id=<?=$event->id?>" title=""><?php _e('Edit');?></a>
					</td>
					<td>
						<a href="/wp-admin/admin.php?page=admin-events&action=delete&event_id=<?=$event->id?>" onclick="return confirm('<?php _e('Are you sure you want to delete this event? This will delete all details permanently.');?>');" class="dashicons dashicons-dismiss">&nbsp;</a>
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
