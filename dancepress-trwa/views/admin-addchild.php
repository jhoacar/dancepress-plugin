<h1>Add student for <?php echo $data['parent']->firstname?> <?php echo $data['parent']->lastname;?></h1>
<?php
//print_r($data);
echo '<script type="text/javascript" src="'.site_url(). '/wp-includes/js/jquery/jquery.js?ver=1.10.2"></script>';
echo '<script type="text/javascript" src="'.site_url(). '/wp-content/plugins/dancepress-trwa/js/jquery.validate.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="'.site_url(). '/wp-content/plugins/dancepress-trwa/css/jquery.datetimepicker.css"></link>';
echo '<script type="text/javascript" src="'.site_url(). '/wp-content/plugins/dancepress-trwa/js/jquery.datetimepicker.js"></script>';
?>
<form action="" method="post" id="childprofile-form">
	<input type="hidden" name="parent_id" value="<?php echo $data['parent']->id;?>"/>
	<table id="ds_reg_stage2" class="gridtable">
		<tbody>
			<tr>
				<td>
					<?php _e('First name');?>*
				</td>
				<td>
					<input type="text" name="firstname" value="" class="req"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Last name');?>*
				</td>
				<td>
					<input type="text" name="lastname" value="" class="req"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Birth date');?>*
				</td>
				<td>
					<input type="text" name="birthdate" id="birthdate" value="" class="req"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Gender');?>*
				</td>
				<td>
					<select class="gender_selector" name="gender">
						<option value="female"><?php _e('Female');?></option>
						<option value="male"><?php _e('Male');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php _e('Medical');?></td>
				<td><input type="text" name="meta[medical]" value=""/></td>
			</tr>
			<tr>
				<td><?php _e('Acro years');?></td>
				<td><input type="text" name="meta[acro_years]" value=""/></td>
			</tr>
			<tr>
				<td><?php _e('Ballet years');?></td>
				<td><input type="text" name="meta[ballet_years]" value=""/></td>
			</tr>
			<tr>
				<td><?php _e('Jazz years');?></td>
				<td><input type="text" name="meta[jazz_years]" value=""/></td>
			</tr>
			<tr>
				<td><?php _e('Tap years');?></td>
				<td><input type="text" name="meta[tap_years]" value=""/></td>
			</tr>
			<tr>
				<td><?php _e('Address (if different from parent/guardian)');?></td>
				<td><input type="text" name="meta[address1]" value=""/></td>
			</tr>
			<tr>
				<td><?php _e('Address2 (if different from parent/guardian)');?></td>
				<td><input type="text" name="meta[address2]" value=""/></td>
			</tr>
			<tr>
				<td><?php _e('City (if different from parent/guardian)');?></td>
				<td><input type="text" name="meta[city]" value=""/></td>
			</tr>
			<tr>
				<td><?php _e('Postal code (if different from parent/guardian)');?></td>
				<td><input type="text" name="meta[postal_code]" value=""/></td>
			</tr>
			<tr>
				<td><?php _e('Custom field name');?>:<br><input type="text" name="custom_meta_key" value=""/></td>
				<td><input name="custom_meta_value" type="text"></td>
			</tr>

		<tr>
			<th></th>
			<th><input type="submit" name="submit" value="Save" class="button button-primary button-large"></th>
		</tr>

		</tbody>
	</table>
<script>
  /*function deleteParent()
  {
	if(confirm('Are you sure you want to delete profile?'))
	{
		jQuery('#action').val('delete');
		jQuery("#parentprofile-form").submit();
		return true;
	}
	else
	return false;
  }*/
  // When the browser is ready...

 jQuery( document ).ready(function() {

    // Setup form validation on the #register-form element
    jQuery("#childprofile-form").validate({

        // Specify the validation rules
        rules: {
            firstname: "<?php _e('required');?>",
            lastname: "<?php _e('required');?>",
			birthdate: "<?php _e('required');?>"
        },

        // Specify the validation error messages
        messages: {
            firstname: "<?php _e('Please enter First Name');?>",
            lastname: "<?php _e('Please enter Last Name');?>",
			birthdate: "<?php _e('Please enter Birth date');?>",
        },

        submitHandler: function(form) {
            form.submit();
        }
    });

  });
 jQuery('#birthdate').datetimepicker({

		timepicker:false,
		format:'Y/m/d',
		formatDate:'Y/m/d',


	});
  </script>
</form>
