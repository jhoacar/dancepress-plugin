<h1><?php _e('List of Course Categories');?></h1>

<form action="" id="editclass-form" method="post">
<input type="hidden" name="action" id="action" value="" />
<?php
if (isset($succmsg) && $succmsg!="") {
    echo "<p>".$succmsg."</p>";
}
?>
<table cellspacing="0" cellpadding="0" border ="1" class="gridtable">
<tr>
	<th><?php _e('Category Name');?></th>

	<th><?php _e('Edit');?></th>
	<th><?php _e('Delete');?></th>
</tr>
<?php

foreach ($data as $key => $value):
    if (is_numeric($key)):
?>
	<tr>
		<td><?php echo ucfirst($value->category_name);?></td>
		<td><a href="../wp-admin/admin.php?page=admin-editclasscategory&id=<?php echo $value->id;?>"><?php _e('Edit');?></a></td>
		<td><input type="checkbox" name="deleteclasscategory[]" value="<?php echo $value->id;?>"> </td>
	</tr>
<?php
    endif;
endforeach; ?>

</table>
<p><input type="button" name="Delete" id="delete" value="Delete" >&nbsp;<input type="button" name="Add" id="add" value="Add" onclick="window.location.href='../wp-admin/admin.php?page=admin-addclasscategory';" ></p>
</form>
<script language="javascript">
jQuery( document ).ready(function() {
   jQuery('#delete').click(function () {
		if(jQuery("input:checked").length > 0)
		{
			if(confirm("<?php _e('Are you sure you want to delete the class categories?');?>"))
			{
				jQuery('#action').val('delete');
				jQuery('#editclass-form').submit();
			}
		}
		else
		{
			alert("Please select at least one class to delete.");
			return false;
		}
	});
});
</script>
