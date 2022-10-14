<h1><?php 
_e( 'Client/Parent/Guardian Management' );
?></h1>
<a href="<?php 
echo  home_url() ;
?>/wp-admin/admin.php?page=admin-addclients"><?php 
_e( 'Add Clients' );
?></a> |
<a href="<?php 
echo  home_url() ;
?>/wp-admin/admin.php?page=admin-manageparents&action=paymentspending"><?php 
_e( 'Pending/Overdue Payments' );
?></a> |
<a href="<?php 
echo  home_url() ;
?>/wp-admin/admin.php?page=admin-manageparents&action=incompleteregistrations"><?php 
_e( 'Incomplete Registrations' );
?></a> |
<a href="<?php 
echo  home_url() ;
?>/wp-admin/admin.php?page=admin-manageparents&action=deactivatedusers"><?php 
_e( 'Deactivated' );
?></a> |
<a href="<?php 
echo  home_url() ;
?>/wp-admin/admin.php?page=admin-manageparents&action=registrationsbydate"><?php 
_e( 'Registration Stats' );
?></a><br>

<?php 
if ( isset( $succmsg ) && $succmsg != "" ) {
    echo  "<p>" . $succmsg . "</p>" ;
}
?>
<p><?php 
_e( 'View and edit registered clients.' );
?></p>

<form action="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-manageparents&action=search" method="post" class="searchbox">
	<input type="text" name="search" value="<?php 
echo  ( isset( $_REQUEST['search '] ) ? $_REQUEST['search '] : '' ) ;
?>"/>
	<input type="submit" value="Find Clients" class="button button-primary button-large"/><br/>
	Show <select name="limit">
		<?php 
for ( $i = 100 ;  $i < 500 ;  $i = $i + 100 ) {
    ?>
			<option value=<?php 
    echo  $i ;
    ?> <?php 
    echo  ( isset( $_REQUEST['limit'] ) && $_REQUEST['limit'] == $i ? 'selected="selected"' : '' ) ;
    ?>><?php 
    echo  $i ;
    ?></options>
		<?php 
}
?>
			<option value="all" <?php 
echo  ( isset( $_REQUEST['limit'] ) && $_REQUEST['limit'] == 'all' ? 'selected="selected"' : '' ) ;
?>>All</option>
	</select> results.
	<br>
	<input type="checkbox" name="searchdeactivated" value="1" <?php 
echo  ( isset( $_REQUEST['searchdeactivated'] ) ? 'checked="checked"' : '' ) ;
?>/> Search deactivated <br>
	(An empty search lists all clients)
</form>
<br><br>
<?php 

if ( is_array( $data ) && count( $data['parents'] ) ) {
    ?>
<form id="dance-school-admin-bulk-parents-action-form" action="<?php 
    echo  get_admin_url() ;
    ?>admin.php?page=admin-manageparents" method="post">
	<input type="hidden" name="search" value="<?php 
    echo  ( isset( $search ) ? $search : '' ) ;
    ?>" />
	<div class="actions bulkactions">
		<select id="bulk-action" name="action">
			<option><?php 
    _e( 'Bulk Actions' );
    ?></option>
			<option value="validate"><?php 
    _e( 'Validate and send login details' );
    ?></option>
			<option value="validateandclasslist"><?php 
    _e( 'Validate, send login details, billing and class lists' );
    ?></option>
			<option value="billingandclasslist"><?php 
    _e( 'Send billing and class lists' );
    ?></option>
			<option value="classlistonly"><?php 
    _e( 'Send class lists/enrollments only (non-competitive)' );
    ?></option>
			<option value="classlistonlycomp"><?php 
    _e( 'Send class lists/enrollments only (competitive)' );
    ?></option>
	<?php 
    ?>
			<option value="change_schedule_availability_menu"><?php 
    _e( 'Change Students\' Schedule Availability' );
    ?></option>
		</select>
		<input type="submit" value="Apply" class="button action button-primary button-large" id="doaction" name="">
	</div>

	<?php 
    
    if ( !empty($parents) ) {
        ?>
	<table cellspacing="0" cellpadding="0" border ="1" class="gridtable tablesortable">
	<thead>
		<tr>
			<th><input type="checkbox" id="ds-selectall"></th>
			<th><?php 
        _e( 'Firstname' );
        ?></th>
			<th><?php 
        _e( 'Lastname' );
        ?></th>
			<th><?php 
        _e( 'Profile' );
        ?></th>
			<th><?php 
        _e( 'Students' );
        ?></th>
		<?php 
        ?>
			<th><?php 
        _e( 'Date Added' );
        ?></th>
			<th><?php 
        _e( 'Status' );
        ?></th>
			<th><?php 
        _e( 'Delete' );
        ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
        foreach ( $parents as $v ) {
            ?>
		<tr>
			<td><input type="checkbox" name="ids[]" value="<?php 
            echo  $v->id ;
            ?>"/></td>
			<td><?php 
            echo  $v->firstname ;
            ?></td>
			<td>
				<?php 
            echo  $v->lastname ;
            ?>
			</td>
			<td>
				<a href="/wp-admin/admin.php?page=admin-manageparents&action=edit&parent_id=<?php 
            echo  $v->id ;
            ?>" class="dashicons dashicons-id" alt="Edit profile" title="Edit profile">&nbsp;</a>
				<a href="mailto:<?php 
            echo  $v->email ;
            ?>" class="dashicons dashicons-email-alt" alt="Send email" title="Send email">&nbsp;</a>
			</td>
			<td>
			<?php 
            foreach ( $v->children as $c ) {
                ?>
				<a href="/wp-admin/admin.php?page=admin-managestudents&action=edit&student_id=<?php 
                echo  $c->student_id ;
                ?>"><?php 
                echo  $c->firstname ;
                ?> <?php 
                echo  $c->lastname ;
                ?></a> (<?php 
                echo  $c->birthdate ;
                ?>)<br>
			<?php 
            }
            ?>
			</td>
	<?php 
            ?>
			<td>
				<?php 
            echo  $v->date_added ;
            ?>
			</td>
			<td>
				<?php 
            
            if ( $v->active ) {
                ?>
					active / <a href="/wp-admin/admin.php?page=admin-manageparents&action=deactivate&id=<?php 
                echo  $v->id ;
                ?>">deactivate</a>
				<?php 
            } else {
                ?>
					inactive / <a href="/wp-admin/admin.php?page=admin-manageparents&action=activate&id=<?php 
                echo  $v->id ;
                ?>">activate</a><br/>

					<?php 
                echo  ( $v->deactivated ? 'Date deactivated: ' . $v->deactivated : '' ) ;
                ?>
				<?php 
            }
            
            ?>
			</td>
			<td>
				<a href="/wp-admin/admin.php?page=admin-manageparents&action=delete&id=<?php 
            echo  $v->id ;
            ?>&search=<?php 
            echo  ( isset( $_REQUEST['search'] ) ? $_REQUEST['search'] : '' ) ;
            ?>" onclick="return confirm('Are you sure you want to delete this account holder. This will delete all details permanently. You may also need to delete the user from the Users panel if the account is validated.');" class="dashicons dashicons-dismiss" alt="Delete" title="Delete">&nbsp;</a>
			</td>

		</tr>
	<?php 
        }
        ?>
	</tbody>
	</table>
	<?php 
    }
    
    ?>
	<?php 
}

?>
</form>

<script>

</script>
