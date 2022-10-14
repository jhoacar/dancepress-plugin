<h1><?php 
_e( 'Add a Course' );
?></h1>
<p><?php 
_e( 'A course is made up of weekly classes held over a period of weeks or months. Create new courses here, and then edit them via the Course List menu.' );
?></p>

<style>
label[class="error"]{
color:red;
}
</style>

<form action="../wp-ad<?php 
_e( 'min' );
?>/ad<?php 
_e( 'min' );
?>.php?page=ad<?php 
_e( 'min' );
?>-addclass" id="addclass-form" method="post">
	<input type="hidden" name="action" value="savenew" />
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
				<td colspan="3"><input type="text" name="class_name" id="class_name" value=""></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Class Room' );
?></th>
				<td colspan="3"><input type="text" name="classroom" id="classroom" value=""></td>
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
    echo  '<option value="' . $row->id . '">' . $row->category_name . '</option>' ;
}
?>
				</select></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Age ' );
?></th>
				<td colspan="3"><input type="text" name="ages" id="ages" value=""></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Experience Required' );
?></th>
				<td colspan="3"><input type="text" name="experience" id="experience" value=""></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Is Competitive?' );
?></th>
				<td colspan="3">
					<label for="is_not_comp">Recreational:</label><input type="radio" id="is_not_comp" name="is_competitive" value="0" /><br/>
					<label for="is_comp">Competitive:</label><input type="radio" id="is_comp" name="is_competitive" value="1"/>
				</td>
			</tr>
			<?php 
?>
			<tr>
				<th><?php 
_e( 'Start Date ' );
?></th>
				<td colspan="3"><input type="text" name="startdate" id="startdate" value=""></td>
			</tr>
			<tr>
				<th><?php 
_e( 'End Date ' );
?></th>
				<td colspan="3"><input type="text" name="enddate" id="enddate" value=""></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Day of week held' );
?></th>
				<td colspan="3">
					<input type="radio" name="days[]" id="monday" value="1"><label for="monday">Monday</label><br/>
					<input type="radio" name="days[]" id="tuesday" value="2"><label for="tuesday">Tuesday</label><br/>
					<input type="radio" name="days[]" id="wednesday" value="3"><label for="wednesday">Wednesday</label><br/>
					<input type="radio" name="days[]" id="thursday" value="4"><label for="thursday">Thursday</label><br/><!--html error fixed-->
					<input type="radio" name="days[]" id="friday" value="5"><label for="friday">Friday</label><br/>
					<input type="radio" name="days[]" id="saturday" value="6"><label for="saturday">Saturday</label><br/>
					<input type="radio" name="days[]" id="sunday" value="7"><label for="sunday">Sunday</label><br/>
				</td>
			</tr>
			<tr>
				<th><?php 
_e( 'Start Time ' );
?></th>
				<td colspan="3"><input type="text" name="starttime" id="starttime" value=""></td>
			</tr>
			<tr>
				<th><?php 
_e( 'Class Duration ' );
?></th>
				<td>
					<select name="duration" id="duration">
						<option value="5">5 <?php 
_e( 'mins' );
?></option>
						<option value="10">10 <?php 
_e( 'mins' );
?></option>
						<option value="15">15 <?php 
_e( 'mins' );
?></option>
						<option value="20">20 <?php 
_e( 'mins' );
?></option>
						<option value="25">25 <?php 
_e( 'mins' );
?></option>
						<option value="30">30 <?php 
_e( 'mins' );
?></option>
						<option value="35">35 <?php 
_e( 'mins' );
?></option>
						<option value="40">40 <?php 
_e( 'mins' );
?></option>
						<option value="45">45 <?php 
_e( 'mins' );
?></option>
						<option value="50">50 <?php 
_e( 'mins' );
?></option>
						<option value="55">55 <?php 
_e( 'mins' );
?></option>
						<option value="60" selected>1 <?php 
_e( 'Hour' );
?></option>
						<option value="65">1 <?php 
_e( 'Hour' );
?> 5 <?php 
_e( 'mins' );
?></option>
						<option value="70">1 <?php 
_e( 'Hour' );
?> 10 <?php 
_e( 'mins' );
?></option>
						<option value="75">1 <?php 
_e( 'Hour' );
?> 15 <?php 
_e( 'mins' );
?></option>
						<option value="80">1 <?php 
_e( 'Hour' );
?> 20 <?php 
_e( 'mins' );
?></option>
						<option value="85">1 <?php 
_e( 'Hour' );
?> 25 <?php 
_e( 'mins' );
?></option>
						<option value="90">1 <?php 
_e( 'Hour' );
?> 30 <?php 
_e( 'mins' );
?></option>
						<option value="95">1 <?php 
_e( 'Hour' );
?> 35 <?php 
_e( 'mins' );
?></option>
						<option value="100">1 <?php 
_e( 'Hour' );
?> 40 <?php 
_e( 'mins' );
?></option>
						<option value="105">1 <?php 
_e( 'Hour' );
?> 45 <?php 
_e( 'mins' );
?></option>
						<option value="110">1 <?php 
_e( 'Hour' );
?> 50 <?php 
_e( 'mins' );
?></option>
						<option value="115">1 <?php 
_e( 'Hour' );
?> 55 <?php 
_e( 'mins' );
?></option>
						<option value="120">2 <?php 
_e( 'Hours' );
?></option>
						<option value="125">2 <?php 
_e( 'Hours' );
?> 5 <?php 
_e( 'mins' );
?></option>
						<option value="130">2 <?php 
_e( 'Hours' );
?> 10 <?php 
_e( 'mins' );
?></option>
						<option value="135">2 <?php 
_e( 'Hours' );
?> 15 <?php 
_e( 'mins' );
?></option>
						<option value="140">2 <?php 
_e( 'Hours' );
?> 20 <?php 
_e( 'mins' );
?></option>
						<option value="145">2 <?php 
_e( 'Hours' );
?> 25 <?php 
_e( 'mins' );
?></option>
						<option value="150">2 <?php 
_e( 'Hours' );
?> 30 <?php 
_e( 'mins' );
?></option>
						<option value="155">2 <?php 
_e( 'Hours' );
?> 35 <?php 
_e( 'mins' );
?></option>
						<option value="160">2 <?php 
_e( 'Hours' );
?> 40 <?php 
_e( 'mins' );
?></option>
						<option value="165">2 <?php 
_e( 'Hours' );
?> 45 <?php 
_e( 'mins' );
?></option>
						<option value="170">2 <?php 
_e( 'Hours' );
?> 50 <?php 
_e( 'mins' );
?></option>
						<option value="175">2 <?php 
_e( 'Hours' );
?> 55 <?php 
_e( 'mins' );
?></option>
						<option value="180">3 <?php 
_e( 'Hours' );
?></option>
						<option value="185">3 <?php 
_e( 'Hours' );
?> 5 <?php 
_e( 'mins' );
?></option>
						<option value="190">3 <?php 
_e( 'Hours' );
?> 10 <?php 
_e( 'mins' );
?></option>
						<option value="195">3 <?php 
_e( 'Hours' );
?> 15 <?php 
_e( 'mins' );
?></option>
						<option value="200">3 <?php 
_e( 'Hours' );
?> 20 <?php 
_e( 'mins' );
?></option>
						<option value="205">3 <?php 
_e( 'Hours' );
?> 25 <?php 
_e( 'mins' );
?></option>
						<option value="210">3 <?php 
_e( 'Hours' );
?> 30 <?php 
_e( 'mins' );
?></option>
						<option value="215">3 <?php 
_e( 'Hours' );
?> 35 <?php 
_e( 'mins' );
?></option>
						<option value="220">3 <?php 
_e( 'Hours' );
?> 40 <?php 
_e( 'mins' );
?></option>
						<option value="225">3 <?php 
_e( 'Hours' );
?> 45 <?php 
_e( 'mins' );
?></option>
						<option value="230">3 <?php 
_e( 'Hours' );
?> 50 <?php 
_e( 'mins' );
?></option>
						<option value="235">3 <?php 
_e( 'Hours' );
?> 55 <?php 
_e( 'mins' );
?></option>
						<option value="240">4 <?php 
_e( 'hours' );
?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php 
_e( 'Description' );
?></th>
				<td colspan="3"><textarea cols="50" rows="10"  name="description" id="description" ></textarea></td>
			</tr>
		<?php 
?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="4"><input type="submit" name="submit" value="<?php 
_e( 'Submit' );
?>" class="button button-primary button-large"></th>
			</tr>
		</tfoot>
	</table>
</form>
<script type="text/javascript">

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
	format:'H:i',
	step:5
});

jQuery('#endtime').datetimepicker({
	datepicker:false,
	format:'H:i',
	step:5
});
</script>
