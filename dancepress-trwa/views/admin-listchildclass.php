<h1><?php _e('Edit Single Classes in this Course');?></h1>

<form action="" id="editclass-form" method="post">
<input type="hidden" name="action" id="action" value="" />
<?php
if (isset($succmsg) && $succmsg!="") {
    echo "<p>".$succmsg."</p>";
}
?>
<table cellspacing="0" cellpadding="0" border ="1" class="gridtable">
<tr>
	<th><?php _e('Class Name');?></th>
	<th><?php _e('Class Room');?></th>
	<th><?php _e('Category');?></th>
	<th><?php _e('Start Date');?></th>
	<th><?php _e('End Date');?></th>
	<th><?php _e('Start Time');?></th>
	<th><?php _e('End Time');?></th>
	<th><?php _e('Edit Selected');?></th>
	<th><?php _e('Delete');?></th>
</tr>
<?php
foreach ($data as $class) {
    ?>
<tr>
	<td><?php echo $class->name; ?></td>
	<td><?php echo $class->classroom; ?></td>
	<td><?php echo $class->category_name; ?></td>
	<td><?php echo $class->startdate; ?></td>
	<td><?php echo $class->enddate; ?></td>
	<td><?php echo $class->starttime; ?></td>
	<td><?php echo $class->endtime; ?></td>
	<td><a href="../wp-admin/admin.php?page=admin-editclass&id=<?php echo $class->id; ?>&editchild=1"><?php _e('Edit'); ?></a></td>
	<td><input type="checkbox" name="deleteclass[]" value="<?php echo $class->id; ?>"> </td>
</tr>
<?php
}?>

</table>
<p><input type="button" name="Delete" id="delete" value="Delete" ></p>
</form>
