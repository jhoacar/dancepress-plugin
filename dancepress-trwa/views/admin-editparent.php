<a href="/wp-admin/admin.php?page=admin-manageparents"><?php 
_e( 'Back to Client Management page' );
?></a>

<?php 

if ( isset( $_GET['show_unconfirmed'] ) ) {
    ?>
<div style="color: red;"> <b>Note:</b> <?php 
    _e( 'You are viewing an incomplete/unconfirmed registration. Values cannot be changed.' );
    ?></div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('input, select').attr('disabled', 'disabled'); //Disable
	});
</script>
<?php 
}

?>

<h1><?php 
_e( 'Actions' );
?></h1>

<?php 
?>

<h1><?php 
_e( 'Manage Client Account Holders' );
?></h1>

<!--a href="/wp-admin/admin.php?page=admin-manageparents&student_id=<=$data['student'][0]->id?>">View Student's Parents</a><br/><br/-->
<div id="ds-parent">
	<form action="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-manageparents" id="editparents-form" method="post">
		<input type="hidden" name="action" value="updateparent"/>
		<input type="hidden" name="parent_id" value="<?php 
echo  $parents[0]->id ;
?>"/>
		<?php 
if ( isset( $succmsg ) && $succmsg != "" ) {
    echo  "<p>" . $succmsg . "</p>" ;
}
?>


		<table cellspacing="0" cellpadding="0" border ="1" class="gridtable">

			<?php 
foreach ( $parents as $k => $parent ) {
    ?>
				<?php 
    
    if ( $k == 0 ) {
        ?>
					<tr>
						<td><?php 
        _e( 'First name' );
        ?></td>
						<td><input type="text" name="firstname" value="<?php 
        echo  $parent->firstname ;
        ?>"></td>
					</tr>
					<tr>
						<td><?php 
        _e( 'Last name' );
        ?></td>
						<td><input type="text" name="lastname" value="<?php 
        echo  $parent->lastname ;
        ?>"></td>
					</tr>
					<tr>
						<td><?php 
        _e( 'Email/Wordpress Username' );
        ?><br/>
							Changing this field will also change user's login details to match
						</td>
						<td>
							<input type="text" name="email" value="<?php 
        echo  $parent->email ;
        ?>">
							<input type="hidden" name="emailold" value="<?php 
        echo  $parent->email ;
        ?>">
						</td>
					</tr>

					<tr>
						<td><?php 
        _e( 'Address ' );
        ?>1</td>
						<td><input type="text" name="address1" value="<?php 
        echo  @$parent->address1 ;
        ?>"/></td>
					</tr>
					<tr>
						<td><?php 
        _e( 'Address ' );
        ?>2</td>
						<td><input type="text" name="address2" value="<?php 
        echo  @$parent->address2 ;
        ?>"/></td>
					</tr>
					<tr>
						<td><?php 
        _e( 'City' );
        ?></td>
						<td><input type="text" name="city" value="<?php 
        echo  @$parent->city ;
        ?>"/></td>
					</tr>
					<tr>
						<td><?php 
        _e( 'Postal Code' );
        ?></td>
						<td><input type="text" name="postal_code" value="<?php 
        echo  @$parent->postal_code ;
        ?>"/></td>
					</tr>
					<tr>
						<td><?php 
        _e( 'Phone Primary' );
        ?></td>
						<td><input type="text" name="phone_primary" value="<?php 
        echo  @$parent->phone_primary ;
        ?>"/></td>
					</tr>
					<tr>
						<td><?php 
        _e( 'Phone Secondary' );
        ?></td>
						<td><input type="text" name="phone_secondary" value="<?php 
        echo  @$parent->phone_secondary ;
        ?>"/></td>
					</tr>
					<?php 
        foreach ( $parent->meta as $mk => $mv ) {
            ?>
						<?php 
            
            if ( !property_exists( $parent, $mk ) ) {
                //get rid of redundant entries in meta due to lazy coding.
                ?>
							<tr>
								<td><?php 
                echo  ucfirst( str_replace( '_', ' ', $mk ) ) ;
                ?></td>
								<td><input type="text" name="meta[<?php 
                echo  $mk ;
                ?>]" value="<?php 
                echo  $mv ;
                ?>"/></td>
							</tr>
						<?php 
            }
            
            ?>
					<?php 
        }
        ?>
					<tr>
						<td><?php 
        _e( 'Custom Field' );
        ?><br/>
						Field name:<br/><input type="text" name="custom_meta_key" value=""/></td>
						<td><?php 
        _e( 'Field value' );
        ?>:<br/> <input type="text" name="custom_meta_value" value=""/></td>
					</tr>
				<?php 
    }
    
    ?>
			<?php 
}
?>
				<tr>
					<td colspan="2">
						<input type="submit" value="Save changes" class="button button-primary button-large"/>
					</td>
				</tr>
			</table>

		<?php 
?>
	</form>
</div>

<div id="ds_students">
	<h2><?php 
_e( 'Students' );
?></h2>
	<div>
		<div class="childlist">
			<?php 
foreach ( $data['children'] as $child ) {
    ?>
				<a href="/wp-admin/admin.php?page=admin-managestudents&action=edit&student_id=<?php 
    echo  $child->student_id ;
    echo  ( isset( $_GET['show_unconfirmed'] ) ? '&show_unconfirmed=1' : '' ) ;
    ?>"><?php 
    echo  $child->firstname ;
    ?> <?php 
    echo  $child->lastname ;
    ?></a><br>
			<?php 
}
?>
		</div>
		<div style="margin-top: 20px; font-weight: bold;">
			<a href="/wp-admin/admin.php?page=admin-manageparents&action=addchild&parent_id=<?php 
echo  $parent->id ;
?>"><?php 

if ( count( $data['children'] ) ) {
    ?> <?php 
    _e( 'Add an additional student' );
    ?> <?php 
} else {
    ?> <?php 
    _e( 'Add a student' );
    ?> <?php 
}

?></a>
		</div>
	</div>
</div>
