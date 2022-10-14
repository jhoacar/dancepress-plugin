<h1><?php _e('Manage Venues');?></h1>
<?php
    if (isset($succmsg) && $succmsg!="") {
        echo "<p>".$succmsg."</p>";
    }
?>

<a href="/wp-admin/admin.php?page=admin-venues"><?php _e('Venue Manager');?></a>

<div id="ds-venue">
	<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-venues" id="addvenue-form" method="post">
		<input type="hidden" name="action" value="addvenue"/>
		<table cellspacing="0" cellpadding="0" border ="1" class="gridtable">
			<tr>
				<td><?php _e('Name');?></td>
				<td><input type="text" name="name" value="<?=$name?>"></td>
			</tr>
			<tr>
				<td><?php _e('Address 1');?></td>
				<td><input type="text" name="address1" value="<?=$address1?>"></td>
			</tr>
			<tr>
				<td><?php _e('Address 2');?></td>
				<td><input type="text" name="address2" value="<?=$address2?>"></td>
			</tr>
			<tr>
				<td><?php _e('City');?></td>
				<td><input type="text" name="city" value="<?=$city?>"></td>
			</tr>
			<tr>
				<td><?php _e('Postal Code');?></td>
				<td><input type="text" name="postal_code" value="<?=$postal_code?>"></td>
			</tr>
			<tr>
				<td><?php _e('Phone');?></td>
				<td><input type="text" name="phone" value="<?=$phone?>"></td>
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
		<input type="submit" name="<?php _e('submit');?>" value="Save Venue" class="button button-primary button-large"/>
	</form>
</div>
<div class="clearfix"></div>
