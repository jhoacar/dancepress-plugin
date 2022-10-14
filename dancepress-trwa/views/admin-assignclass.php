<h1>Assign Class to students</h1>
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

<form action="<?php echo $_SERVER['PHP_SELF'];?>?page=admin-assignclass" method="post">
	<table class="studentlist">
		<tr><th></th><th><?php _e('Firstname');?></th><th><?php _e('Lastname');?></th><th><?php _e('Classes');?></th></tr>

		<?php foreach ($data['studentsClasses'] as $student): ?>
			<tr>
				<td><input type="checkbox" name="studentids[]" value="<?php echo $student['id'];?>"/></td>
				<td>
					<?php echo $student['firstname'];?>
				</td>
				<td>
					<?php echo $student['lastname'];?>
				</td>
				<td>
					<?php foreach ($student['classes'] as $class): ?>
						<?php echo $class['name'];?> (<?php echo $data['weekdayMap'][$class['weekday']];?>)<br>
					<?php endforeach; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<div class="assign">
		Assign To:
		<select name="classdays">
			<?php foreach ($data['classes'] as $class): ?>
				<?php foreach ($class->weekdays as $weekday): ?>
					<option value="<?php echo $class->id;?>-<?php echo $weekday;?>">
						<?php echo $class->name;?> (<?php echo $data['weekdayMap'][$weekday];?>)
					</option>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</select>
		<input type="submit" value="assign"/>
	</div>
</form>
<div class="nav">

<?php if ($data['pageNumber'] > 1): ?>
	<a href="<?php echo $_SERVER['PHP_SELF'];?>?page=admin-assignclass&p=<?php echo($data['pageNumber'] - 1);?>">&lt;- <?php _e('Previous');?></a>
<?php endif;?>
	<a href="<?php echo $_SERVER['PHP_SELF'];?>?page=admin-assignclass&p=<?php echo($data['pageNumber'] + 1);?>"><?php _e('Next');?> -&gt;</a>
</div>



<!--form action="../wp-admin/admin.php?page=admin-assignclass" id="addclass-form" method="post">
	<input type="hidden" name="action" value="savenew" />
	<table class="form-table dance-table gridtable">
		<thead>
			<tr>
				<th class="manage-column column-name column-left"><?php _e('Field');?></th>
				<th class="manage-column column-name">Value</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="manage-column column-name">Class Name</th>
				<td><select name="class_id" id="class_id" >
				<option value="">Select Class</option>
				<?php
                $ObjClass = new \DancePressTRWA\Models\ClassManager($this->sessionCondition);
                foreach ($ObjClass->getAllClass() as $row) {
                    echo '<option value="'.$row->id.'">'.$row->name.'</option>';
                }
                ?>
				</select></td>
			</tr>
			<tr>
				<th class="manage-column column-name">Student Name</th>
				<td><select name="student_id" id="student_id" >
				<option value="">Select Student</option>
				<?php
                $ObjClass = new \DancePressTRWA\Models\ClassManager($this->sessionCondition);
                foreach ($ObjClass->getAllStudents() as $row) {
                    echo '<option value="'.$row->id.'">'.$row->firstname.' '.$row->lastname.'</option>';
                }
                ?>
				</select></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th><input type="submit" name="submit" value="Submit" class="button button-primary button-large"></th>
			</tr>
		</tfoot>
	</table>
</form-->
<script>

  // When the browser is ready...

  jQuery(function() {

    // Setup form validation on the #register-form element
    jQuery("#addclass-form").validate({

        // Specify the validation rules
        rules: {
            class_id: "required",
            student_id: "required"
			 },

        // Specify the validation error messages
        messages: {
            class_id: "Please select Class",
            student_id: "Please select student"


        },

        submitHandler: function(form) {
            form.submit();
        }
    });

  });

  </script>
