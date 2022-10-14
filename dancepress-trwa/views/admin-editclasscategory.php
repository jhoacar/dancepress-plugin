<h1><?php _e('Edit Class to the database');?></h1>
<?php
echo '<script type="text/javascript" src="'.site_url(). '/wp-content/plugins/dancepress-trwa/js/jquery.validate.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="'.site_url(). '/wp-content/plugins/dancepress-trwa/css/jquery.datetimepicker.css"></link>';
echo '<script type="text/javascript" src="'.site_url(). '/wp-content/plugins/dancepress-trwa/js/jquery.datetimepicker.js"></script>';

?>
<style>
label[class="error"]{
color:red;
}
</style>
<?php
if (isset($succmsg) && $succmsg!="") {
    echo "<p>".$succmsg."</p>";
}
?>
<form action="../wp-admin/admin.php?page=admin-editclasscategory&id=<?php echo $_REQUEST["id"];?>" id="addclass-form" method="post">
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="id" value="<?php echo $data[0]->id;?>" />
	<table class="form-table dance-table">
		<thead>
			<tr>
				<td class="manage-column column-name column-left"><?php _e('Field');?></td>
				<td class="manage-column column-name"><?php _e('Value');?></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="manage-column column-name"><?php _e('Class Category Name');?></td>
				<td><input type="text" name="category_name" id="category_name" value="<?php echo $data[0]->category_name;?>"></td>
			</tr>

		</tbody>
		<tfoot>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="<?php _e('Submit');?>" class="button button-primary button-large"></td>
			</tr>
		</tfoot>
	</table>
</form>
<script>

  // When the browser is ready...

  jQuery(function() {

    // Setup form validation on the #register-form element
    jQuery("#addclass-form").validate({

        // Specify the validation rules
        rules: {
            category_name: "<?php _e('required');?>"


        },

        // Specify the validation error messages
        messages: {
            category_name: "<?php _e('Please enter Class Category Name');?>"

        },

        submitHandler: function(form) {
            form.submit();
        }
    });

  });

  </script>
