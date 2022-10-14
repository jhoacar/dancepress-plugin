<h1><?php _e('Manage Events');?></h1>
<?php
    if (isset($succmsg) && $succmsg!="") {
        echo "<p>".$succmsg."</p>";
    }
?>

<a href="/wp-admin/admin.php?page=admin-events"><?php _e('Event Manager');?></a>

<div id="ds-event">
	<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-events" id="addevent-form" method="post">
		<input type="hidden" name="action" value="addevent"/>

		<table cellspacing="0" cellpadding="0" border ="1" class="gridtable">
			<tr>
				<td>Name</td>
				<td><input type="text" name="name" value="<?=$name?>"></td>
			</tr>
			<tr>
				<td>Venue</td>
				<td>
					<select name="venue_id" id="venue-id">
						<option value=""><?php _e('Select');?></option>
						<?php foreach ($venues as $venue): ?>
							<option value="<?=$venue->id?>"><?=$venue->name?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php _e('Starts');?></td>
				<td><input type="text" name="starts" value="<?=$starts;?>" class="datetime"></td>
			</tr>
			<tr>
				<td><?php _e('Ends');?></td>
				<td><input type="text" name="ends" value="<?=$ends;?>" class="datetime"></td>
			</tr>
			<tr>
				<td><?php _e('Ticket Price ($)');?></td>
				<td><input type="text" min="1" name="ticket_price" value="<?=$ticket_price;?>"></td>
			</tr>
			<tr>
				<td><?php _e('Max Tickets');?></td>
				<td><input type="number" min="1" name="max_tickets" value="<?=@$max_tickets;?>"></td>
			</tr>
			<tr>
				<td><?php _e('Description');?></td>
				<td><textarea name="description" cols="50" rows="15"><?=$description;?></textarea></td>
			</tr>
			<tr>
				<td><?php _e('Image ');?><img alt="" src="<?=$image_url?>" class="event-image" <?php if (empty($image_url)) {?>style="display:none"<?php }?>> <input type="hidden" name="image_url" class="event-image-url" value=""></td>
				<td><input type="button" id="" class="button add-event-image-button" value="Upload/Select Image"></td>
			</tr>
			<tr>
				<td>
					<?php _e('Custom Field name:');?><br/> <input type="text" name="custom_meta_key" value=""/>
				</td>
				<td>
					<?php _e('Custom Field value:');?><br/> <input type="text" name="custom_meta_value" value=""/>
				</td>
			</tr>


		</table>
		<input type="submit" name="submit" value="<?php _e('Save Event');?>" class="button button-primary button-large"/>
	</form>
</div>
<div class="clearfix"></div>
