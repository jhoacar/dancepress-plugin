<h1><?php 
_e( 'List of Courses' );
?></h1>

<form action="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-listclass" id="editclass-form" method="post">
<?php 
if ( isset( $succmsg ) && $succmsg != "" ) {
    echo  "<p>" . $succmsg . "</p>" ;
}
?>
<div>
	<a href="<?php 
echo  esc_url( home_url( '/' ) ) ;
?>wp-admin/admin.php?page=admin-addclass">Add a new course</a> |
	<a href="<?php 
echo  esc_url( home_url( '/' ) ) ;
?>wp-admin/admin.php?page=admin-addclasscategory">Add course category</a> |
	<a href="<?php 
echo  esc_url( home_url( '/' ) ) ;
?>wp-admin/admin.php?page=admin-listclasscategories">Manage course categories</a>
</div>

<div class="actions bulkactions">
	<select name="action">
		<option><?php 
_e( 'Bulk Actions' );
?></option>
		<option value="print"><?php 
_e( 'Print selected class attendance lists' );
?></option>
		<option value="print-advanced"><?php 
_e( 'Print advanced class data lists & measurements' );
?></option>
	<?php 
?>
	</select>
	<input type="submit" value="Apply" class="button action button-primary button-large" id="doaction" name="" onclick="return confirm('Are you sure you want to complete this action? Deletions and updates cannot be undone.');">
</div>


<table cellspacing="0" cellpadding="0" border ="1" class="gridtable tablesortable">
<thead>
	<tr>
		<th><input type="checkbox" id="ds-selectall"></th>
		<th><?php 
_e( 'Class Name' );
?></th>
		<th><?php 
_e( 'Email' );
?></th>
		<th><?php 
_e( 'Category' );
?></th>
		<th><?php 
_e( 'Class Room' );
?></th>
		<th><?php 
_e( 'Enrollment' );
?></th>
		<th><?php 
_e( 'Weekday' );
?></th>
		<th><?php 
_e( 'Start Date' );
?></th>
		<th><?php 
_e( 'End Date' );
?></th>
		<th><?php 
_e( 'Start Time' );
?></th>
		<th><?php 
_e( 'End Time' );
?></th>
	<?php 
?>
		<th><?php 
_e( 'Edit All' );
?></th>
		<th><?php 
_e( 'Edit Selected' );
?></th>
		<th></th>
	</tr>
</thead>
<tbody>
<?php 
for ( $i = 0 ;  $i < count( $data ) ;  $i++ ) {
    ?>
<tr>
	<td><input type="checkbox" name="ids[]" value="<?php 
    echo  $data[$i]->id ;
    ?>"/></td>
	<td><?php 
    echo  $data[$i]->name ;
    ?></td>
	<td><a href="/wp-admin/admin.php?page=admin-sendemail&action=email-class&class_id=<?php 
    echo  $data[$i]->id ;
    ?>" class="dashicons dashicons-email-alt"></a></td>
	<td><?php 
    echo  $data[$i]->category_name ;
    ?></td>
	<td><?php 
    echo  $data[$i]->classroom ;
    ?></td>
	<td><a href="/wp-admin/admin.php?page=admin-editclass&id=<?php 
    echo  $data[$i]->id ;
    ?>&enrollment=1"><?php 
    echo  $data[$i]->enrollment ;
    ?></a></td>
	<td><?php 
    echo  $data[$i]->weekday_name ;
    ?></td>
	<td><?php 
    echo  $data[$i]->startdate ;
    ?></td>
	<td><?php 
    echo  $data[$i]->enddate ;
    ?></td>
	<td><?php 
    echo  date( "h:i A", strtotime( "2000-01-01 {$data[$i]->starttime}" ) ) ;
    ?></td>
	<td><?php 
    echo  date( "h:i A", strtotime( "2000-01-01 {$data[$i]->endtime}" ) ) ;
    ?></td>
	<?php 
    ?>
	<td><a href="/wp-admin/admin.php?page=admin-editclass&id=<?php 
    echo  $data[$i]->id ;
    ?>"><?php 
    _e( 'Edit All' );
    ?></a></td>
	<td><a href="/wp-admin/admin.php?page=admin-editclass&id=<?php 
    echo  $data[$i]->id ;
    ?>&editchildren=1"><?php 
    _e( 'Edit' );
    ?></a></td>
	<td><a title="Delete" alt="Delete" class="dashicons dashicons-dismiss" onclick="return confirm('<?php 
    _e( 'Are you sure you want to delete this course? This will delete all details permanently.' );
    ?>');" href="/wp-admin/admin.php?page=admin-listclass&action=delete&id=<?php 
    echo  $data[$i]->id ;
    ?>">&nbsp;</a></td>
</tr>
<?php 
}
?>
</tbody>
</table>
</form>
