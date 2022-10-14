<h1><?php _e('Add Recommended Course to students: Select Course to recommend');?></h1>
<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-managestudents&action=addrecommendedcourse-add" id="editclass-form" method="post">
	<input type="hidden" name="student_ids" value="<?php echo implode(',', $studentIds);?>"/>
	<input type="submit" value="<?php _e('Recommend')?>" class="button button-primary button-large"/><br/><br/>

	<table cellspacing="0" cellpadding="0" border ="1" class="gridtable tablesortable">
		<thead>
			<tr>
				<th><input type="checkbox" id="ds-selectall"></th>
				<th><?php _e('Class Name');?></th>
				<th><?php _e('Category');?></th>
				<th><?php _e('Class Room');?></th>
				<th><?php _e('Enrollment');?></th>
				<th><?php _e('Weekday');?></th>
				<th><?php _e('Start Date');?></th>
				<th><?php _e('End Date');?></th>
				<th><?php _e('Start Time');?></th>
				<th><?php _e('End Time');?></th>
				<th><?php _e('Online<br/>registration?');?></th>

			</tr>
		</thead>
		<tbody>
		<?php
            for ($i=0;$i<count($classes);$i++) :
        ?>
		<tr>
			<td><input type="checkbox" name="ids[]" value="<?=$classes[$i]->id?>"/></td>
			<td><?php echo $classes[$i]->name;?></td>
			<td><?php echo $classes[$i]->category_name;?></td>
			<td><?php echo $classes[$i]->classroom;?></td>
			<td><?php echo $classes[$i]->enrollment;?></td>
			<td><?php echo $classes[$i]->weekday_name;?></td>
			<td><?php echo $classes[$i]->startdate;?></td>
			<td><?php echo $classes[$i]->enddate;?></td>
			<td><?php echo $classes[$i]->starttime;?></td>
			<td><?php echo $classes[$i]->endtime;?></td>
			<td><?php $no = __('no'); $yes = __('yes');  echo $classes[$i]->is_registerable == 0 ? $no : $yes;?></td>
		</tr>
		<?php endfor; ?>
		</tbody>
	</table>
	<input type="submit" value="<?php _e('Recommend');?>" class="button button-primary button-large"/><br/><br/>
</form>
