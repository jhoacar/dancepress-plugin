<h1><?php _e('Add Class Category');?></h1>
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
<form action="../wp-admin/admin.php?page=admin-addclasscategory" id="addclass-form" method="post">
	<input type="hidden" name="action" value="savenew" />
	<table class="gridtable">
		<tbody>
			<tr>
				<th class="manage-column column-name"><?php _e('Category Name');?></th>
				<td><input type="text" name="category_name" id="category_name" value=""></td>
			</tr>

		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th><input type="submit" name="submit" value="<?php _e('Submit');?>" class="button button-primary button-large"></th>
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
            class_name: "<?php _e('Please enter Class Category Name');?>"

        },

        submitHandler: function(form) {
            form.submit();
        }
    });

  });

  </script>
