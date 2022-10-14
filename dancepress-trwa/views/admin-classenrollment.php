<style>
@media print {
  table {float: none !important; }
  div { float: none !important; }
  .page-break { page-break-inside: avoid; page-break-before: always; }
  .dont-print { display: none; }
}
</style>

<h1><?php _e('Class Enrollment');?></h1>
<script type="text/javascript">var g_data_classId = <?php echo $classId;?></script>
<input type="button" value="Print Attendance Sheet(s)" id="print-attendance" class="print-attendance button button-primary button-large"/>

<div id="attendance-printout" style="page-break-after: always">

	<div class="ds-enrollment">
		<ul style="font-weight: bold;">
			<li><h2><?=$students[0]->name?></h2></li>
			<li><?php _e('Classroom');?>: <?=$students[0]->classroom?></li>
			<li><?php _e('Day');?>: <?=$students[0]->weekday_name?></li>
			<li><?php _e('Time');?>: <?=$students[0]->starttime?>-<?=$students[0]->endtime?></li>
			<li><?php _e('Class target ages');?>: <?=$students[0]->ages?> years</li>
			<!--li>Average age: <?=$average_age?></li-->
			<li><?php _e('Enrollment');?>: <?=count($students)?></li>
		</ul>
		<div id="ds-enrollment-customage">
			<span class="bold">Average Age on date</span>
			<select id="customage-year" name="year">
				<?php for ($i = 2000; $i <= date('Y', time()); $i++): ?>
					<option value="<?php echo $i;?>" <?php if ($currentSelectedYear == $i) {
    echo 'selected="selected"';
}?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>
			<select id="customage-month" name="month">
				<?php for ($i = 1; $i <= 12; $i++): ?>
					<option value="<?php echo $i;?>" <?php if ($currentSelectedMonth == $i) {
    echo 'selected="selected"';
}?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>
			<select id="customage-day" name="day">
				<?php for ($i = 1; $i <= 31; $i++): ?>
					<option value="<?php echo $i;?>" <?php if ($currentSelectedDay == $i) {
    echo 'selected="selected"';
}?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>
			: <span id="calculatedaverage" class="bold"><?php echo $calculatedAverageAge;?></span>
		</div>
	</div>

	<table class="gridtable printable">
		<tr>
			<th><?php _e('Dancer');?></th>
			<th><?php _e('Birthdate');?></th>
			<th><?php _e('Gender');?></th>
			<th><?php _e('Medical');?></th>
			<?php  for ($i = 0; $i < 15; $i++):?>
				<th style="height: 100px;"></th>
			<?php endfor;?>
		</tr>
	<?php  foreach ($students as $s): ?>
		<tr>
			<td style="font-size:14pt;"><a href="/wp-admin/admin.php?page=admin-managestudents&action=edit&student_id=<?=$s->student_id?>"><?=$s->firstname . ' ' . $s->lastname?></a></td>
			<td style="font-size:14pt;"><?=$s->birthdate?></td>
			<td style="font-size:14pt;"><?=$s->gender?></td>
			<td style="font-size:14pt;"><?=@$s->medical?></td>
			<?php  for ($i = 0; $i < 15; $i++):?>
				<td style="font-size:20pt;">&#9744;</td>
			<?php endfor;?>
		</tr>
	<?php endforeach;?>

	</table>
</div>
<div style="page-break-after: always"></div>
