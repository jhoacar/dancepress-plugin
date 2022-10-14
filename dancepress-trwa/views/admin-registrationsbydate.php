<h1><?php _e('Student Registrations By Date');?></h1>
<!--script type="text/javascript">var g_data_classId = <?php echo $classId;?></script-->

<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-manageparents&action=registrationsbydate" method="post">
	From: 	<select id="start-year" name="startyear">
				<?php for ($i = 2013; $i <= date('Y', time()); $i++): ?>
					<option value="<?php echo $i;?>" <?php if ($currentSelectedStartYear == $i) {
    echo 'selected="selected"';
}?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>
			<select id="start-month" name="startmonth">
				<?php for ($i = 1; $i <= 12; $i++): ?>
					<option value="<?php echo $i;?>" <?php if ($currentSelectedStartMonth == $i) {
    echo 'selected="selected"';
}?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>
			<select id="start-day" name="startday">
				<?php for ($i = 1; $i <= 31; $i++): ?>
					<option value="<?php echo $i;?>" <?php if ($currentSelectedStartDay == $i) {
    echo 'selected="selected"';
}?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>

	To: 	<select id="end-year" name="endyear">
				<?php for ($i = 2013; $i <= date('Y', time()); $i++): ?>
					<option value="<?php echo $i;?>" <?php if ($currentSelectedEndYear == $i) {
    echo 'selected="selected"';
}?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>
			<select id="end-month" name="endmonth">
				<?php for ($i = 1; $i <= 12; $i++): ?>
					<option value="<?php echo $i;?>" <?php if ($currentSelectedEndMonth == $i) {
    echo 'selected="selected"';
}?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>
			<select id="end-day" name="endday">
				<?php for ($i = 1; $i <= 31; $i++): ?>
					<option value="<?php echo $i;?>" <?php if ($currentSelectedEndDay == $i) {
    echo 'selected="selected"';
}?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>

			<input type="submit" value="update" class="button button-primary button-large"><br/><a href="/wp-admin/admin.php?page=admin-manageparents&action=registrationsbydate"><?php _e('Clear');?></a>
</form>
<br/>
<h2><?php _e('Registrations between ');?><?=$currentSelectedStartYear . '/' .  $currentSelectedStartMonth . '/' . $currentSelectedStartDay  . ' and ' . $currentSelectedEndYear . '/' . $currentSelectedEndMonth . '/' . $currentSelectedEndDay?></h2>
<h4>
All Students: <?php echo $students['all'];?><br/>
Recreational Students: <?php echo $students['recreational'];?><br/>
Competitive Students: <?php echo $students['competitive'];?><br/>
<br/>
Online Payments: <?php echo $payments[0]->online_count;?><br/>
Cheque Payments: <?php echo $payments[0]->cheques_count;?><br/>
Advance Payments: <?php echo $payments[0]->advance_count;?><br/>
</h4>
<!--div style="page-break-after: always"></div-->


<h2><?php _e('Dropped classes between ');?><?=$currentSelectedStartYear . '/' .  $currentSelectedStartMonth . '/' . $currentSelectedStartDay  . ' and ' . $currentSelectedEndYear . '/' . $currentSelectedEndMonth . '/' . $currentSelectedEndDay?></h2>

<table class="gridtable tablesortable">
	<tr><th><?php _e('Class');?></th><th><?php _e('Student');?></th><th><?php _e('Date');?></th><th><?php _e('Total drops in this class');?></th></tr>
	<?php if (!count($droppedClassDetails)):?>
	<tr><td><?php _e('No classes dropped during this time');?></td></tr>
	<?php endif; ?>
	<?php foreach ($droppedClassDetails as $c): ?>
		<tr>
			<td>
				<?php echo $c->name;?>
			</td>

			<td>
				<a href="/wp-admin/admin.php?page=admin-managestudents&action=edit&student_id=<?=$c->student_id?>"><?=$c->firstname?> <?=$c->lastname?></a>
			</td>
			<td><?=date('d-M-Y', $c->timestamp)?>

			</td>
			<td>
				<?php echo $c->droppedCount; ?>
			</td>
		</tr>

	<?php endforeach; ?>
</table>
