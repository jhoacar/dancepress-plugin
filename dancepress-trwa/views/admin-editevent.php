<h1><?php _e('Manage Events');?></h1>
<?php
    if (isset($succmsg) && $succmsg!="") {
        echo "<p>".$succmsg."</p>";
    }
?>

<a href="/wp-admin/admin.php?page=admin-events">Event Manager</a> |
<a href="/wp-admin/admin.php?page=admin-events&action=add">Add Event</a>

<div id="ds-event">
	<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-events" id="editevent-form" method="post">
		<input type="hidden" name="action" value="updateevent"/>
		<input type="hidden" name="event_id" value="<?=$event->id?>"/>

		<table cellspacing="0" cellpadding="0" border ="1" class="gridtable">
			<tr>
				<td><?php _e('Name');?></td>
				<td><input type="text" name="name" value="<?=$event->name?>"></td>
			</tr>
			<tr>
				<td><?php _e('Venue');?></td>
				<td>
					<select name="venue_id" id="venue-id">
							<option value=""><?php _e('Select');?></option>
						<?php foreach ($venues as $venue): ?>
							<option value="<?=$venue->id?>" <?php if ($venue->id == $event->venue_id) :?>selected<?php endif ;?>><?=$venue->name?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php _e('Starts');?></td>
				<td><input type="text" name="starts" value="<?=$event->starts;?>" class="datetime"></td>
			</tr>
			<tr>
				<td><?php _e('Ends');?></td>
				<td><input type="text" name="ends" value="<?=$event->ends;?>" class="datetime"></td>
			</tr>
			<tr>
				<td><?php _e('Ticket Price ');?>($)</td>
				<td><input type="text" min="1" name="ticket_price" value="<?=$event->ticket_price;?>"></td>
			</tr>
			<tr>
				<td><?php _e('Max Tickets');?></td>
				<td><input type="number" min="1" name="max_tickets" value="<?=$event->max_tickets;?>"></td>
			</tr>
			<tr>
				<td><?php _e('Description');?></td>
				<td><textarea name="description" cols="50" rows="15"><?=$event->description;?></textarea></td>
			</tr>
			<tr>
				<td><?php _e('Image ');?><img alt="" src="<?=$event->image_url?>" class="event-image" <?php if (empty($event->image_url)) {?>style="display:none"<?php }?>> <input type="hidden" name="image_url" class="event-image-url" value="<?=$event->image_url?>"></td>
				<td><input type="button" id="" class="button add-event-image-button" value="Upload/Select Image"></td>
			</tr>

			<?php
            if (!empty($event->meta)) {
                foreach ($event->meta as $mk => $mv): ?>
					<tr>
						<td><?=ucfirst(str_replace('_', ' ', $mk)); ?></td>
						<td><input type="text" name="meta[<?=$mk?>]" value="<?=$mv; ?>"/></td>
					</tr>
				<?php  endforeach;
            }
            ?>
			<tr>
				<td>
					<?php _e('Custom Field name');?>:<br/> <input type="text" name="custom_meta_key" value=""/>
				</td>
				<td>
					<?php _e('Custom Field value');?>:<br/> <input type="text" name="custom_meta_value" value=""/>
				</td>
			</tr>


		</table>
		<input type="submit" name="submit" value="<?php _e('Save Event');?>" class="button button-primary button-large"/>
	</form>
</div>
<div class="clearfix"></div>
