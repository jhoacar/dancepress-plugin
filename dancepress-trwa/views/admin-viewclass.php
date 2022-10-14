<h1><?php echo $data[0]->name;?></h1>


	<table class="form-table dance-table">

		<tbody>
			<tr>
				<th class="manage-column column-name"><?php _e('Class Name');?></th>
				<td><?php echo $data[0]->name;?></td>
			</tr>
			<tr>
				<th><?php _e('Class Room');?></th>
				<td><?php echo $data[0]->classroom;?></td>
			</tr>
			<tr>
				<th><?php _e('Class Categories');?></th>
				<td><?php echo $data[0]->category_name;?></td>
			</tr>

			<tr>
				<th><?php _e('Start Date ');?></th>
				<td><?php echo $data[0]->startdate;?></td>
			</tr>
			<tr>
				<th><?php _e('End Date ');?></th>
				<td><?php echo $data[0]->enddate;?></td>
			</tr>
			<tr>
				<th><?php _e('Start Time ');?></th>
				<td><?php echo $data[0]->starttime;?></td>
			</tr>
			<tr>
				<th><?php _e('End Time ');?></th>
				<td><?php echo $data[0]->endtime;?></td>
			</tr>
			<tr>
				<th><?php _e('Description');?></th>
				<td><?php echo $data[0]->description;?></td>
			</tr>

		</tbody>

	</table>
