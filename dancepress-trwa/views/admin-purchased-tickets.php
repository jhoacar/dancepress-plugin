<div id="admin-venues">
	<h1><?php _e('Purchased Tickets');?></h1>
	<a href="/wp-admin/admin.php?page=admin-events"><?php _e('All Events');?></a> |
	<a href="/wp-admin/admin.php?page=admin-events&action=past"><?php _e('Past Events');?></a> |
	<a href="/wp-admin/admin.php?page=admin-events&action=current"><?php _e('Current Events');?></a> |
	<a href="/wp-admin/admin.php?page=admin-events&action=upcoming"><?php _e('Upcoming Events');?></a> |
	<a href="/wp-admin/admin.php?page=admin-events&action=add"><?php _e('Add Event');?></a><br/>

	<!--
	<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-events" method="post">
		<input type="hidden" name="action" value="search"/>
		<input type="text" name="search" value=""/>
		<input type="submit" value="Find Events" class="button button-primary button-large"/><br/>
		(An empty search lists all events)
	</form>
	-->
	<br/>

	<?php  if (isset($purchases) && is_array($purchases)): ?>
		<form id="" action="<?php echo get_admin_url(); ?>admin.php?page=" method="post">
			<div class="actions bulkactions">
				<select id="bulk-action" name="action">
					<option><?php _e('Bulk Actions');?></option>
				</select>
				<input type="submit" value="Apply" class="button action button-primary button-large" id="doaction" name="">
			</div>

		<table cellspacing="0" cellpadding="0" border ="1" class="gridtable tablesortable">
		<thead>
			<tr>
				<th><input type="checkbox" id="ds-selectall"></th>
				<th><?php _e('Event');?></th>
				<th><?php _e('Venue');?></th>
				<th><?php _e('Parent');?></th>
				<th><?php _e('Qualtity');?></th>
				<th><?php _e('Sub-Total');?></th>
				<th><?php _e('Tax');?></th>
				<th><?php _e('Total');?></th>
				<th><?php _e('Date Purchased');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
                foreach ($purchases as $purchased_tickets):
            ?>
				<tr>
					<td></td>
					<td><?=$purchased_tickets->event->name?></td>
					<td><?=$purchased_tickets->event->venue->name?></td>
					<td><a href="/wp-admin/admin.php?page=admin-manageparents&action=edit&parent_id=<?=$purchased_tickets->parent->id?>"><?=$purchased_tickets->parent->firstname?> <?=$purchased_tickets->parent->lastname?></a></td>	
					<td><?=$purchased_tickets->quantity?></td>
					<td><?=$purchased_tickets->sub_total?></td>
					<td><?=$purchased_tickets->tax?></td>
					<td><?=$purchased_tickets->total?></td>
					<td><?=$purchased_tickets->date_purchased?></td>
				</tr>
			<?php
                endforeach;
            ?>
		</tbody>
		</table>

		</form>
	<?php  endif; ?>
</div>
