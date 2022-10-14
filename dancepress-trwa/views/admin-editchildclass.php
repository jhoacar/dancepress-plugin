<h1><?php _e('Edit Single Class');?></h1>

<?php
if (isset($succmsg) && $succmsg!="") {
    echo "<p>".$succmsg."</p>";
}
?>
<form action="../wp-admin/admin.php?page=admin-editchildclass&id=<?php echo $_REQUEST["id"];?>" id="addclass-form" method="post">
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="id" value="<?php echo $data->id;?>" />
	<table class="form-table dance-table">
		<thead>
			<tr>
				<th class="manage-column column-name column-left"><?php _e('Field');?></th>
				<th class="manage-column column-name"><?php _e('Value');?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="manage-column column-name"><?php _e('Course Name');?><br/></th>
				<td><?php echo $data->name;?></td>
			</tr>
			<tr>
				<th><?php _e('Class Room');?></th>
				<td><input type="text" name="classroom" id="classroom" value="<?php echo $data->classroom;?>"></td>
			</tr>
			<tr>
				<th><?php _e('Date ');?></th>
				<td><input type="text" name="startdate" id="startdate" value="<?php echo $data->startdate;?>"></td>
			</tr>
			<tr>
				<th><?php _e('Start Time ');?></th>
				<td><input type="text" name="starttime" id="starttime" value="<?php echo $data->starttime;?>"></td>
			</tr>
			<tr>
				<th><?php _e('End Time ');?></th>
				<td><input type="text" name="endtime" id="endtime" value="<?php echo $data->endtime;?>"></td>
			</tr>
			<tr>
				<th><?php _e('Description');?></th>
				<td><textarea cols="50" rows="10"  name="description" id="description" ><?php echo $data->description;?></textarea></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th><input type="submit" name="submit" value="<?php _e('Submit')?>" class="button button-primary button-large"></th>
			</tr>
		</tfoot>
	</table>
</form>
