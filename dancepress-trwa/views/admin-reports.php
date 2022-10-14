<div id="admin-reports">
	<h1><?php _e('Reports');?></h1>
	<a href="/wp-admin/admin.php?page=admin-manageparents&action=registrationsbydate"><?php _e('Registration Stats');?></a><br/><br/>
	<input type="button" class="print-report button button-primary button-large" id="print-report" value="Print Report">

	<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-reports" method="post">

		<fieldset>
			<legend><?php _e('Columns');?></legend>
			<p><?php _e('Column must be selected in order to "hide result if empty".');?></p>
			<fieldset>
				<legend><?php _e('Students');?></legend>
				<table>
					<tr>
						<th><?php _e('Column');?></th>
						<th><?php _e('Hide result if empty');?>?</th>
					</tr>
					<?php foreach ($data['studentsColumnMap'] as $ck => $c): ?>
						<tr>
							<td><input type="checkbox" id="report-<?php echo $ck;?>" name="<?php echo $ck;?>" <?php if (in_array($ck, $data['selectedColumns'])) {
    echo 'checked="checked"';
}?>><?php echo $c['title'];?></td>
							<td><input type="checkbox" name="<?php echo $ck;?>-hideempty" <?php if (in_array($ck, $data['selectedHideEmpty'])) {
    echo 'checked="checked"';
}?>/></td>
						</tr>
					<?php endforeach;?>
				</table>

				<br/><input type="checkbox" id="option-students-showdeactivated" name="option-students-showdeactivated" <?php if ($data['optionStudentShowDeactivated']) {
    echo 'checked="checked"';
}?>>Show deactivated

			</fieldset>

			<fieldset>
				<legend><?php _e('Parents');?></legend>
				<table>
					<tr>
						<th><?php _e('Column');?></th>
						<th><?php _e('Hide result if empty');?>?</th>
					</tr>
				<?php foreach ($data['parentsColumnMap'] as $ck => $c): ?>
						<tr>
							<td><input type="checkbox" id="report-<?php echo $ck;?>" name="<?php echo $ck;?>" <?php if (in_array($ck, $data['selectedColumns'])) {
    echo 'checked="checked"';
}?>><?php echo $c['title'];?></td>
							<td><input type="checkbox" name="<?php echo $ck;?>-hideempty" <?php if (in_array($ck, $data['selectedHideEmpty'])) {
    echo 'checked="checked"';
}?>/></td>
						</tr>
				<?php endforeach;?>
				</table>
			</fieldset>

			<fieldset>
				<legend><?php _e('Class Students');?></legend>
				<table>
					<tr>
						<th><?php _e('Column');?></th>
						<th><?php _e('Hide result if empty');?>?</th>
					</tr>
				<?php foreach ($data['classStudentsColumnMap'] as $ck => $c): ?>
						<tr>
							<td><input type="checkbox" id="report-<?php echo $ck;?>" name="<?php echo $ck;?>" <?php if (in_array($ck, $data['selectedColumns'])) {
    echo 'checked="checked"';
}?>><?php echo $c['title'];?></td>
							<td><input type="checkbox" name="<?php echo $ck;?>-hideempty" <?php if (in_array($ck, $data['selectedHideEmpty'])) {
    echo 'checked="checked"';
}?>/></td>
						</tr>
				<?php endforeach;?>
				</table>
			</fieldset>

			<br><input type="submit" value="Update columns" class="button button-primary button-large"/>
		</fieldset>
		<div id="report-table-wrapper">
			<table id="report-table" class="gridtable tablesortable">
				<thead>
					<tr>
						<td>
							<?php if (!$data['studentsParentsClasses']): ?>
								<?php _e('No results found using specified filter.');?> <input type="submit" value="Reset Filter"/>
							<?php else: ?>
								<input type="submit" value="<?php _e('Filter');?>" class="button button-primary button-large"/>
							<?php endif; ?>
						</td>
						<?php foreach ($headers as $h): ?>
							<td><?php echo $h;?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<th>

						</th>
						<?php if (is_array($titles)):?>
							<?php foreach ($titles as $t): ?>
								<th><?php echo $t;?></th>
							<?php endforeach; ?>
						<?php endif; ?>
					</tr>
				</thead>

				<tbody>
					<?php if (is_array($data['studentsParentsClasses'])): ?>
						<?php foreach ($data['studentsParentsClasses'] as $spc): ?>
							<tr><td><?php //print_r($spc); //enable for quick debug?></td>
								<?php foreach ($spc as $k => $v):?>
									<td>
										<?php //if(is_array($v)){ continue; }: //skip arrays, because that's metadata...?>
										<?php if ($k == 'ds_class_students_class_id'):?>
											<?php foreach ($classList as $cl):?>
												<?=$cl->id == $v ? $cl->name . ' (' . $cl->weekday_name . ')': '';?>
											<?php endforeach; ?>
										<?php elseif ($k == 'ds_parents_is_confirmed' || $k == 'ds_parents_active'):?>
											<?php echo ($v) ? 'Yes' : 'No' ; ?>
										<?php else:?>
											<?php echo $v;?>
										<?php endif; ?>
									</td>
								<?php endforeach;?>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

	</form>
</div>
