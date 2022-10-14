<?php include_once('parent-portal-menu.php'); ?>
<h3><?php echo $data->name;?></h3>
<a href="parent-portal/dance-school-plugin-page/"><?php _e('Return to Previous Page');?></a>

	<table class="form-table dance-table">

		<tbody>
			<tr>
				<th class="manage-column column-name"><?php _e('Class Name');?></th>
				<td><?php echo $data->name;?></td>
			</tr>
			<tr>
				<th><?php _e('Class Room');?></th>
				<td><?php echo $data->classroom;?></td>
			</tr>
			<tr>
				<th><?php _e('Class Categories');?></th>
				<td><?php echo $data->category_name;?></td>
			</tr>
			<tr>
				<th><?php _e('Start Date ');?></th>
				<td><?php echo $data->startdate;?></td>
			</tr>
			<tr>
				<th><?php _e('End Date ');?></th>
				<td><?php echo $data->enddate;?></td>
			</tr>
			<tr>
				<th><?php _e('Start Time ');?></th>
				<td><?php echo $data->starttime;?></td>
			</tr>
			<tr>
				<th><?php _e('End Time ');?></th>
				<td><?php echo $data->endtime;?></td>
			</tr>
			<tr>
				<th><?php _e('Description');?></th>
				<td><?php echo $data->description;?></td>
			</tr>

		</tbody>

	</table>
