<h1><?php 
_e( 'Edit All Classes in a Course' );
?></h1>
<?php 
echo  '<script type="text/javascript" src="' . site_url() . '/wp-content/plugins/dancepress-trwa/js/jquery.validate.js"></script>' ;
echo  '<link rel="stylesheet" type="text/css" href="' . site_url() . '/wp-content/plugins/dancepress-trwa/css/jquery.datetimepicker.css"></link>' ;
echo  '<script type="text/javascript" src="' . site_url() . '/wp-content/plugins/dancepress-trwa/js/jquery.datetimepicker.js"></script>' ;
?>
<style>
label[class="error"]{
color:red;
}
</style>
<?php 
if ( isset( $succmsg ) && $succmsg != "" ) {
    echo  "<p>" . $succmsg . "</p>" ;
}
?>
<form action="../wp-admin/admin.php?page=admin-editclassall&id=<?php 
echo  $_REQUEST["id"] ;
?>" id="addclass-form" method="post">
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="id" value="<?php 
echo  $data->id ;
?>" />
	<div class="updated fade"><strong>Note:</strong> Using this form will delete and recreate ALL classes in this course. To edit individual classes within this course, click the back button and select 'Edit Selected'</div>
	<!-- Removed Classes: wp-list-table fixed -->
	<table class="form-table dance-table">
		<thead>
			<tr>
				<th class="manage-column column-name column-left"><?php 
_e( 'Field' );
?></th>
				<th class="manage-column column-name" colspan="3"><?php 
_e( 'Value' );
?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="manage-column column-name"><?php 
_e( 'Class Name' );
?></th>
				<td colspan="3"><input type="text" name="class_name" id="class_name" value="<?php 
echo  $data->name ;
?>"></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Class Room' );
?></th>
				<td colspan="3"><input type="text" name="classroom" id="classroom" value="<?php 
echo  $data->classroom ;
?>"></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Class Categories' );
?></th>
				<td colspan="3"><select name="category_id" id="category_id" >
				<option value=""><?php 
_e( 'Select Category' );
?></option>
				<?php 
$ObjClassCategories = new \DancePressTRWA\Models\ClassCategories( $this->sessionCondition );
foreach ( $ObjClassCategories->getClassCategories() as $row ) {
    
    if ( $data->category_id == $row->id ) {
        echo  '<option value="' . $row->id . '" selected="selected">' . $row->category_name . '</option>' ;
    } else {
        echo  '<option value="' . $row->id . '" >' . $row->category_name . '</option>' ;
    }

}
?>
				</select></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Age Range' );
?><br/>
					<small><?php 
_e( 'Please enter as a number or range of numbers<br/> separated by a hyphen (eg. \'8\', \'10+\' or \'18-80\')' );
?>.</small>
				</th>
				<td colspan="3"><input type="text" name="ages" id="ages" value="<?php 
echo  $data->ages ;
?>"></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Experience Required (years)' );
?></th>
				<td colspan="3"><input type="number" name="experience" id="experience" value="<?php 
echo  $data->experience ;
?>" min="0" step="1"></td>
			</tr>
			<tr>
				<th>
					<?php 
_e( 'Is Competitive?' );
?><br>
					<?php 
?>
				</th>
				<td colspan="3">


					<label for="is_not_comp"><?php 
_e( 'Recreational' );
?>:</label><input type="radio" id="is_not_comp" name="is_competitive" value="0" <?php 
echo  ( !$data->is_competitive ? 'checked="checked"' : '' ) ;
?>/><br/>
					<label for="is_comp"><?php 
_e( 'Competitive' );
?>:</label><input type="radio" id="is_comp" name="is_competitive" value="1" <?php 
echo  ( $data->is_competitive ? 'checked="checked"' : '' ) ;
?>/>
				</td>
			</tr>
			<?php 
?>
			<tr>
				<th><?php 
_e( 'Start Date ' );
?></th>
				<td colspan="3"><input type="text" name="startdate" id="startdate" value="<?php 
echo  $data->startdate ;
?>"></td>
			</tr>
			<tr>
				<th><?php 
_e( 'End Date ' );
?></th>
				<td colspan="3"><input type="text" name="enddate" id="enddate" value="<?php 
echo  $data->enddate ;
?>"></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Day of week held' );
?></th>
				<td colspan="3">

					<input type="radio" name="days[]" id="monday" value="1" <?php 
echo  ( isset( $data->days[1] ) ? 'checked="checked"' : '' ) ;
?>><label for="monday">Monday</label><br/>
					<input type="radio" name="days[]" id="tuesday" value="2" <?php 
echo  ( isset( $data->days[2] ) ? 'checked="checked"' : '' ) ;
?>><label for="tuesday">Tuesday</label><br/>
					<input type="radio" name="days[]" id="wednesday" value="3" <?php 
echo  ( isset( $data->days[3] ) ? 'checked="checked"' : '' ) ;
?>><label for="wednesday">Wednesday</label><br/>
					<input type="radio" name="days[]" id="thursday" value="4" <?php 
echo  ( isset( $data->days[4] ) ? 'checked="checked"' : '' ) ;
?>><label for="thursday">Thursday</label><br/>
					<input type="radio" name="days[]" id="friday" value="5" <?php 
echo  ( isset( $data->days[5] ) ? 'checked="checked"' : '' ) ;
?>><label for="friday">Friday</label><br/>
					<input type="radio" name="days[]" id="saturday" value="6" <?php 
echo  ( isset( $data->days[6] ) ? 'checked="checked"' : '' ) ;
?>><label for="saturday">Saturday</label><br/>
					<input type="radio" name="days[]" id="sunday" value="7" <?php 
echo  ( isset( $data->days[7] ) ? 'checked="checked"' : '' ) ;
?>><label for="sunday">Sunday</label><br/>
				</td>
			</tr>
			<tr>
				<th><?php 
_e( 'Start Time ' );
?></th>
				<td colspan="3"><input type="text" name="starttime" id="starttime" value="<?php 
echo  $data->starttime ;
?>"></td>
			</tr>
			<tr>
				<th><?php 
_e( 'End Time ' );
?></th>
				<td colspan="3"><input type="text" name="endtime" id="endtime" value="<?php 
echo  $data->endtime ;
?>"></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Description' );
?></th>
				<td colspan="3"><textarea cols="50" rows="10"  name="description" id="description" ><?php 
echo  $data->description ;
?></textarea></td>
			</tr>
		<?php 
?>
		<tr>
			<th class="manage-column column-name"><?php 
_e( 'Costume Fee ' );
?>(optional)</th>
			<td colspan="3">$<input type="number" name="costume_fee" id="costume_fee" value="<?php 
echo  $data->costume_fee ;
?>" min="0" step="0.01"></td>
		</tr>
		</tbody>
		<tfoot>
			<tr>
				<th  colspan="4"><input type="submit" name="submit" value="Submit" class="button button-primary button-large">&nbsp;&nbsp;<input type="button" name="back" value="Back" onclick="window.location.href='../wp-admin/admin.php?page=admin-listclass';" class="button button-primary button-large"></th><!--added back button-->
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
            class_name: "<?php 
_e( 'required' );
?>",
            classroom: "<?php 
_e( 'required' );
?>",
			category_id: "<?php 
_e( 'required' );
?>",
            startdate: "<?php 
_e( 'required' );
?>",
			 enddate: "<?php 
_e( 'required' );
?>",
			starttime: "<?php 
_e( 'required' );
?>",
            endtime: "<?php 
_e( 'required' );
?>"

        },

        // Specify the validation error messages
        messages: {
            class_name: "<?php 
_e( 'Please enter Class Name' );
?>",
            classroom: "<?php 
_e( 'Please enter Classroom' );
?>",
			  category_id: "<?php 
_e( 'Please select class category' );
?>",
            teacher_id: "<?php 
_e( 'Please select Class Teacher' );
?>",
			  startdate: "<?php 
_e( 'Please enter Start Date' );
?>",
			 enddate: "<?php 
_e( 'Please enter End Date' );
?>",
			starttime: "<?php 
_e( 'Please enter Start Time' );
?>",
            endtime: "<?php 
_e( 'Please enter End Time' );
?>"

        },

        submitHandler: function(form) {
            form.submit();
        }
    });

  });
 jQuery('#startdate').datetimepicker({

	timepicker:false,
	format:'Y/m/d',
	formatDate:'Y/m/d',


});
jQuery('#enddate').datetimepicker({

	timepicker:false,
	format:'Y/m/d',
	formatDate:'Y/m/d',

});
jQuery('#starttime').datetimepicker({
	datepicker:false,
	format:'h:i A',
	formatTime:'h:i A',
	step:5
});
jQuery('#endtime').datetimepicker({
	datepicker:false,
	format:'h:i A',
	formatTime:'h:i A',
	step:5
});
  </script>
