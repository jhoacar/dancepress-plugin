<h1><?php 
_e( 'Manage Students' );
?></h1>
<?php 
if ( isset( $succmsg ) && $succmsg != "" ) {
    echo  "<p>" . $succmsg . "</p>" ;
}

if ( isset( $_GET['show_unconfirmed'] ) ) {
    ?>
<div style="color: red;"><?php 
    _e( ' <b>Note:</b> You are viewing an incomplete/unconfirmed registration. Values cannot be changed.' );
    ?></div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('input, select').attr('disabled', 'disabled'); //Disable
	});
</script>
<?php 
}

?>
<a href="/wp-admin/admin.php?page=admin-managestudents"><?php 
_e( 'Student Manager' );
?></a> | <a href="/wp-admin/admin.php?page=admin-manageparents&action=edit&parent_id=<?php 
echo  $data['student'][0]->parent_id ;
echo  ( isset( $_GET['show_unconfirmed'] ) ? '&show_unconfirmed=1' : '' ) ;
?>"><?php 
_e( 'View Parent/Guardian' );
?></a> |
<a href="/wp-admin/admin.php?page=admin-managestudents&action=datasheet&student_id=<?php 
echo  $data['student'][0]->id ;
?>"><?php 
_e( 'View Data Sheet' );
?></a><br/><br/>


<div>
	<?php 

if ( $data['student'][0]->active ) {
    ?>
		<form action="<?php 
    echo  get_admin_url() ;
    ?>admin.php?page=admin-managestudents&action=deactivatestudent" method="post">
			<input type="hidden" name="student_id" value="<?php 
    echo  $data['student'][0]->id ;
    ?>"/>
			<input type="submit" value="Deactivate Student" class="button button-primary button-large" onclick="return confirm('Are you sure you want to deactivate this student?');"/>
	<?php 
} else {
    ?>
		<form action="<?php 
    echo  get_admin_url() ;
    ?>admin.php?page=admin-managestudents&action=activatestudent" method="post">
			<input type="hidden" name="student_id" value="<?php 
    echo  $data['student'][0]->id ;
    ?>"/>
			<input type="submit" value="Activate Student" class="button button-primary button-large" onclick="return confirm('Are you sure you want to activate this student?');"/>

	<?php 
}

?>

		</form>
</div>
<div class="ds-grid">
    <?php 

if ( !$data['student'][0]->active ) {
    $disabled = " disabled='disabled'";
} else {
    $disabled = '';
}

?>

    <div id="ds-student">
    	<form action="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-managestudents" id="editstudent-form" method="post">
    		<input type="hidden" name="action" value="updatestudent"/>
    		<input type="hidden" name="student_id" value="<?php 
echo  $data['student'][0]->id ;
?>"/>

    		<table cellspacing="0" cellpadding="0" border ="1" class="gridtable">
    		<?php 
foreach ( $data['student'] as $v ) {
    $address = json_decode( $v->address_data );
    ?>
    			<?php 
    
    if ( $disabled ) {
        ?>
    			<tr>
    				<td colspan="2"><h2 class="error"><?php 
        _e( 'This student has been disabled.' );
        ?></h2></td>
    			</tr>
    			<?php 
    }
    
    ?>
    			<tr>
    				<td><?php 
    _e( 'First name' );
    ?></td>
    				<td><input type="text" name="firstname" value="<?php 
    echo  $v->firstname ;
    ?>" <?php 
    echo  $disabled ;
    ?>></td>
    			</tr>
    			<tr>
    				<td><?php 
    _e( 'Last name' );
    ?></td>
    				<td><input type="text" name="lastname" value="<?php 
    echo  $v->lastname ;
    ?>" <?php 
    echo  $disabled ;
    ?>></td>
    			</tr>
    			<tr>
    				<td><?php 
    _e( 'Date of Birth (YYYY-MM-DD)' );
    ?></td>
    				<td><input type="text" name="birthdate" value="<?php 
    echo  $v->birthdate ;
    ?>" <?php 
    echo  $disabled ;
    ?>></td>
    			</tr>
    			<tr>
    				<td><?php 
    _e( 'Gender' );
    ?></td>
    				<td>
    					<select name="gender">
    						<option <?php 
    echo  ( $v->gender == 'female' ? 'selected' : '' ) ;
    ?> value="female" <?php 
    echo  $disabled ;
    ?>>female</option>
    						<option <?php 
    echo  ( $v->gender == 'male' ? 'selected' : '' ) ;
    ?> value="male">male</option>
    					</select>
    				</td>
    			</tr>
    			<?php 
    
    if ( $v->is_company_student ) {
        ?>
    			<tr>
    				<td><?php 
        _e( 'Schedule Available' );
        ?></td>
    				<td>
    					<select name="schedule_available">
    						<option <?php 
        echo  ( $v->schedule_available == 1 ? 'selected' : '' ) ;
        ?> value="1">Yes</option>
    						<option <?php 
        echo  ( $v->schedule_available == 0 ? 'selected' : '' ) ;
        ?> value="0">No</option>
    					</select>
    				</td>
    			</tr>
    			<?php 
    }
    
    ?>

    			<?php 
    
    if ( is_object( $v->meta ) ) {
        foreach ( $v->meta as $mk => $mv ) {
            ?>
    					<?php 
            
            if ( property_exists( $v, $mk ) && !is_object( $mv ) ) {
                //get rid of redundant entries in meta due to poor coding.
                ?>
    						<?php 
                
                if ( $mk == "same_address" ) {
                    if ( is_numeric( $mv ) ) {
                        $mv = ( $mv == 1 ? 'yes' : 'no' );
                    }
                    ?>
    							<tr>
    								<td><?php 
                    _e( 'Same Address as parent/guardian' );
                    ?></td>
    								<td><input type="text" name="meta[<?php 
                    echo  $mk ;
                    ?>]" value="<?php 
                    echo  $mv ;
                    ?>" <?php 
                    echo  $disabled ;
                    ?>/></td>
    							</tr>
    						<?php 
                } elseif ( $mk == 'medical' ) {
                    ?>
    							<tr>
    								<td><?php 
                    echo  ucfirst( str_replace( '_', ' ', $mk ) ) ;
                    ?></td>
    								<td>
    									<input type="radio" class="medicalbool" name="meta[medicalbool]" value="1" <?php 
                    echo  ( $mv ? 'checked="checked"' : '' ) ;
                    ?>/>Yes
    									<input type="radio" class="medicalbool" name="meta[medicalbool]" value="0" <?php 
                    echo  ( $mv ? '' : 'checked="checked"' ) ;
                    ?>/>No<br>
    									<input type="text" id="medical" name="meta[<?php 
                    echo  $mk ;
                    ?>]" value="<?php 
                    echo  $mv ;
                    ?>" <?php 
                    echo  $disabled ;
                    ?>/ <?php 
                    echo  ( $mv ? '' : 'style="display:none"' ) ;
                    ?>/>
    								</td>
    							</tr>

    						<?php 
                } elseif ( $mk != 'medicalbool' ) {
                    ?>
    							<tr>
    								<td><?php 
                    echo  ucfirst( str_replace( '_', ' ', $mk ) ) ;
                    ?></td>
    								<td><input type="text" name="meta[<?php 
                    echo  $mk ;
                    ?>]" value="<?php 
                    echo  $mv ;
                    ?>" <?php 
                    echo  $disabled ;
                    ?>/></td>
    							</tr>
    						<?php 
                }
                
                ?>
    					<?php 
            }
            
            ?>
    				<?php 
        }
        ?>
    			<?php 
    }
    
    ?>
    			<tr>
    				<td>
    				<?php 
    _e( 'Custom Field name:' );
    ?><br/> <input type="text" name="custom_meta_key" value="" <?php 
    echo  $disabled ;
    ?>/>
    			</td>
    			<td>
    				<?php 
    _e( 'Custom Field value' );
    ?>:<br/> <input type="text" name="custom_meta_value" value="" <?php 
    echo  $disabled ;
    ?>/></td>
    			</tr>
    		<?php 
}
?>

    		</table>
    		<input type="submit" name="submit" value="<?php 
_e( 'Save Student' );
?>" class="button button-primary button-large" <?php 
echo  $disabled ;
?>/>
    	</form>
    </div>

    <div class="ds-classes">
        <?php 
foreach ( $data['classes'] as $c ) {
    ?>
        <div class="ds-class">
        	<a href="/wp-admin/admin.php?page=admin-editclass&id=<?php 
    echo  $c->id ;
    ?>&enrollment=1"><?php 
    echo  $c->name ;
    ?> (<?php 
    echo  $c->ages ;
    ?>)<br/></a>
        	<?php 
    echo  $c->weekday_name ;
    ?><br/>
        	<?php 
    echo  $c->starttime ;
    ?> - <?php 
    echo  $c->endtime ;
    ?><br/><br/>
        	<a href="/wp-admin/admin.php?page=admin-managestudents&action=removeclass&class_student_id=<?php 
    echo  $c->class_student_id ;
    ?>" onclick="return confirm('<?php 
    _e( 'Deleting student from this class will also recalculate parent\'s fee schedule. Note: parent will not be automatically notified of new fees, and any refunds will need to be processed through Stripe or by cheque' );
    ?>.')"><?php 
    _e( 'Remove Class' );
    ?></a>
        </div>
        <?php 
}
?>
        <?php 
//end of pro only
?>

        <div class="ds-class">
        	<form action="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-managestudents&action=addclasses" id="addstudentclasses-form" method="post">
        	<!--input type="hidden" name="action" value="assignclass" /-->
        	<input type="hidden" name="student_id" value="<?php 
echo  $data['student'][0]->id ;
?>" />
        	<table class="gridtable">
        		<tr>
        			<th></th>
        			<th><input type="submit" name="submit" value="Add classes" class="button button-primary button-large"></th>
        		</tr>
        		<?php 
foreach ( $addClasses as $row ) {
    ?>
        		<tr>
        			<th class="manage-column column-name"><?php 
    echo  $row->name ;
    ?></th>
        			<td>
        				<select name="classday[<?php 
    echo  $row->id ;
    ?>]" id="classday[<?php 
    echo  $row->id ;
    ?>]" >
        					<option value=""><?php 
    _e( 'Select Weekday' );
    ?></option>
        					<option value="<?php 
    echo  @$row->week_day ;
    ?>"><?php 
    echo  @$row->weekday_name ;
    ?></option>
        				</select>
        			</td>
        		</tr>
        		<?php 
}
?>
        		<tr>
        			<th></th>
        			<th><input type="submit" name="submit" value="Add classes" class="button button-primary button-large"
        			<?php 
?>></th>
        		</tr>
        	</table>
        </form>
        </div>
    </div>
</div>

<div class="clearfix"></div>
