<div id="admin-options">
	<h1><?php 
_e( 'Options' );
?></h1>

	<h2 class="nav-tab-wrapper">
		<a href="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-options&tab=tab_one" class="nav-tab <?php 
echo  ( $active_tab == 'tab_one' || !$active_tab ? 'nav-tab-active' : '' ) ;
?>"><?php 
_e( 'Front end integration' );
?></a>
		<a href="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-options&tab=tab_two" class="nav-tab <?php 
echo  ( $active_tab == 'tab_two' ? 'nav-tab-active' : '' ) ;
?>"><?php 
_e( 'Sessions' );
?></a>
		<a href="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-options&tab=tab_three" class="nav-tab <?php 
echo  ( $active_tab == 'tab_three' ? 'nav-tab-active' : '' ) ;
?>"><?php 
_e( 'Contact Options' );
?></a>
		<a href="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-options&tab=tab_four" class="nav-tab <?php 
echo  ( $active_tab == 'tab_four' ? 'nav-tab-active' : '' ) ;
?>"><?php 
_e( 'Payments and Taxes' );
?></a>
		<a href="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-options&tab=tab_five" class="nav-tab <?php 
echo  ( $active_tab == 'tab_five' ? 'nav-tab-active' : '' ) ;
?>"><?php 
_e( 'Email' );
?></a>
		<a href="<?php 
echo  get_admin_url() ;
?>admin.php?page=admin-options&tab=tab_six" class="nav-tab <?php 
echo  ( $active_tab == 'tab_six' ? 'nav-tab-active' : '' ) ;
?>"><?php 
_e( 'Miscellaneous' );
?></a>
	</h2>

<?php 
switch ( $active_tab ) {
    case false:
    case 'tab_one':
        
        if ( !dancepress_fs()->is_plan( 'pro' ) || !dancepress_fs()->is__premium_only() ) {
            ?>
				<h2><?php 
            _e( 'Front end integration' );
            ?></h2>
				Front end integration, e-commerce and the parent portal are available in the Pro and Enterprise editions. <a href="<?php 
            echo  get_admin_url() ;
            ?>admin.php?page=dancepress_menu-pricing">Upgrade to a free trial now</a>.
	<?php 
        }
        
        break;
        // end tab one
    // end tab one
    case 'tab_two':
        //start tab 2: sessions
        ?>
	<h2><?php 
        _e( 'Sessions' );
        ?></h2>
	<p>
		This is the list of sessions. Select the default session below, in the Generic section - all site activity will occur in this session.
	</p>
	<p>
		Different sessions allow you to keep data from different years or periods separate, and to archive past data and keep it for reference in future.
	</p>
	<form action="<?php 
        echo  get_admin_url() ;
        ?>admin.php?page=admin-options&action=savesession&tab=tab_two" method="post">
		<table class="gridtable">
			<?php 
        foreach ( $sessions as $s ) {
            ?>
				<tr>
					<td><?php 
            echo  $s->id ;
            ?>:</td>
					<td>
						<?php 
            echo  $s->name ;
            ?>
					</td>
				</tr>
			<?php 
        }
        ?>
			<tr>
				<td><?php 
        _e( 'Add New Session:' );
        ?></td>
				<td><input type="text" name="newsession"/></td>
			</tr>
		</table>
	<input type="submit" value="Save" class="button-primary"/>
	</form>
	<hr/>
	<h2><?php 
        _e( 'Generic - Everyone' );
        ?></h2>
	<p><?php 
        _e( 'The session all your site users and clients will use on public pages (eg for new registrations and all current data)' );
        ?></p>
	<form action="<?php 
        echo  get_admin_url() ;
        ?>admin.php?page=admin-options&action=save&tab=tab_two" method="post">

		<table  class="gridtable">
			<?php 
        foreach ( $possible as $pk => $p ) {
            ?>
				<tr>
					<td><?php 
            echo  $possible[$pk] ;
            ?></td>
					<td>
					<?php 
            
            if ( $pk == DANCEPRESSTRWA_SESSION_OPTION ) {
                ?>
						<select name="<?php 
                echo  $pk ;
                ?>">
							<option value=""><?php 
                _e( 'Select' );
                ?></option>
							<?php 
                foreach ( $sessions as $s ) {
                    ?>
								<option value="<?php 
                    echo  $s->id ;
                    ?>"
								<?php 
                    if ( $s->id == $current[$pk] ) {
                        echo  'selected="selected"' ;
                    }
                    ?>
								><?php 
                    echo  $s->name ;
                    ?></option>
							<?php 
                }
                ?>
						</select>
					<?php 
            } else {
                ?>
						<input type="text" name="<?php 
                echo  $pk ;
                ?>" value="<?php 
                echo  $current[$pk] ;
                ?>"/>
					<?php 
            }
            
            ?>
				</tr>
			<?php 
        }
        ?>
		</table>

		<br><input type="submit" value="Save" class="button-primary"/>
	</form>

	<hr/>
	<h2><?php 
        _e( 'Your Working Session' );
        ?></h2>
	<p><?php 
        _e( 'To work on or view data from a session other that the default current session, select it here. This is useful for working with archived data.' );
        ?></p>
	<form action="<?php 
        echo  get_admin_url() ;
        ?>admin.php?page=admin-options&action=saveuser&tab=tab_two" method="post">

		<table  class="gridtable">
			<?php 
        foreach ( $possibleUser as $pk => $p ) {
            ?>
				<tr>
					<td><?php 
            echo  $possibleUser[$pk] ;
            ?></td>
					<td>
					<?php 
            
            if ( $pk == DANCEPRESSTRWA_SESSION_OPTION ) {
                ?>
						<select name="<?php 
                echo  $pk ;
                ?>">
							<option value=""><?php 
                _e( 'Select' );
                ?></option>
							<?php 
                foreach ( $sessions as $s ) {
                    ?>
								<option value="<?php 
                    echo  $s->id ;
                    ?>"
								<?php 
                    if ( $s->id == $currentUser[$pk] ) {
                        echo  'selected="selected"' ;
                    }
                    ?>
								><?php 
                    echo  $s->name ;
                    ?></option>
							<?php 
                }
                ?>
						</select>
					<?php 
            } else {
                ?>
						<input type="text" name="<?php 
                echo  $pk ;
                ?>" value="<?php 
                echo  $currentUser[$pk] ;
                ?>"/>
					<?php 
            }
            
            ?>
				</tr>
			<?php 
        }
        ?>
		</table>

		<br><input type="submit" value="Save" class="button-primary"/>

	</form>
	<hr/>
	<h2><?php 
        _e( 'Copy Session Classes' );
        ?></h2>
	<p><?php 
        _e( 'This tool will copy classes from an archived session to the current default session.' );
        ?></p>
	<form action="<?php 
        echo  get_admin_url() ;
        ?>admin.php?page=admin-options&action=copyclasses&tab=tab_two" method="post">
		<table  class="gridtable">
			<?php 
        foreach ( $possibleUser as $pk => $p ) {
            ?>

				<?php 
            foreach ( $sessions as $s ) {
                ?>
					<?php 
                
                if ( $s->id == $currentUser[$pk] ) {
                    ?>
						<input type="hidden" name="copytoid" value="<?php 
                    echo  $s->id ;
                    ?>"/>
					<?php 
                }
                
                ?>
				<?php 
            }
            ?>
				<tr>
					<td><?php 
            _e( 'Classes from session:' );
            ?></td>
					<td>

						<select name="copyfromid">
							<?php 
            foreach ( $sessions as $s ) {
                ?>
								<?php 
                
                if ( $s->id != $currentUser[$pk] ) {
                    ?>
									<option value="<?php 
                    echo  $s->id ;
                    ?>" <?php 
                    echo  ( @$otheroptions['dstrwa_country'] == '<?php echo $s->id;?>' ? 'selected' : '' ) ;
                    ?>><?php 
                    echo  $s->name ;
                    ?></option>
								<?php 
                }
                
                ?>
							<?php 
            }
            ?>
						</select>
						will be copied to <?php 
            echo  $currentSession ;
            ?> session.
				</tr>
			<?php 
        }
        ?>
		</table>
		<b>Note: Please allow a couple of minutes for the copy to complete. Only click the save button once and do not reload the page.</b>
		<br><input type="submit" value="Save" class="button-primary" onclick="return confirm('Are you sure you want to copy this data? You should generally only copy to empty sessions. Copying into a working session could cause major disruption to your existing data!! You have been warned!')"/>
	</form>
	<hr/>
	<h2><?php 
        _e( 'Copy Session Parents and Students' );
        ?></h2>
	<p><?php 
        _e( "This tool will copy parents and students from another session in bulk. All parents and students will be marked as 'inactive' by default. Parents who never completed registration, or students who are marked as inactive in the old session will not be copied." );
        ?></p>
	<form action="<?php 
        echo  get_admin_url() ;
        ?>admin.php?page=admin-options&action=copyparentsstudents&tab=tab_two" method="post">
		<table  class="gridtable">
			<?php 
        foreach ( $possibleUser as $pk => $p ) {
            ?>

				<?php 
            foreach ( $sessions as $s ) {
                ?>
					<?php 
                
                if ( $s->id == $currentUser[$pk] ) {
                    ?>
						<input type="hidden" name="copytoid" value="<?php 
                    echo  $s->id ;
                    ?>"/>
					<?php 
                }
                
                ?>
				<?php 
            }
            ?>
				<tr>
					<td><?php 
            _e( 'Parents and students from session:' );
            ?></td>
					<td>

						<select name="copyfromid">
							<?php 
            foreach ( $sessions as $s ) {
                ?>
								<?php 
                
                if ( $s->id != $currentUser[$pk] ) {
                    ?>
									<option value="<?php 
                    echo  $s->id ;
                    ?>" <?php 
                    echo  ( @$otheroptions['dstrwa_country'] == '<?php echo $s->id;?>' ? 'selected' : '' ) ;
                    ?>><?php 
                    echo  $s->name ;
                    ?></option>
								<?php 
                }
                
                ?>
							<?php 
            }
            ?>
						</select>
						will be copied to <?php 
            echo  $currentSession ;
            ?> session.
				</tr>
			<?php 
        }
        ?>
		</table>
		<b>Note: Please allow a couple of minutes for the copy to complete. Only click the save button once and do not reload the page.</b>
		<br><input type="submit" value="Save" class="button-primary" onclick="return confirm('Are you sure you want to copy this data? You should generally only copy to empty sessions. Copying into a working session could cause major disruption to your existing data!! You have been warned!')"/>
	</form>
<?php 
        break;
        // end tab_two - sessions
    // end tab_two - sessions
    case "tab_three":
        //start tab_three contact options
        ?>
	<br>
	<form action="<?php 
        echo  get_admin_url() ;
        ?>admin.php?page=admin-options&action=updatecontactoptions&tab=tab_three" method="post">
		<table  class="gridtable options-table">
			<tr>
				<td><?php 
        _e( 'Address' );
        ?></td>
				<td><input type="text" name="ds_contact_address" value="<?php 
        echo  ( $contactoptions['ds_contact_address'] ? $contactoptions['ds_contact_address'] : '' ) ;
        ?>"/></td>
			</tr>

			<tr>
				<td><?php 
        _e( 'Province/State' );
        ?></td>
				<td><input type="text" name="ds_contact_address_province" value="<?php 
        echo  ( $contactoptions['ds_contact_address_province'] ? $contactoptions['ds_contact_address_province'] : '' ) ;
        ?>"/></td>
			</tr>

			<tr>
				<td><?php 
        _e( 'Country' );
        ?></td>
				<td>
					<select name='ds_contact_address_country'>
						<option value="">Select</option>
						<?php 
        foreach ( $countries as $country ) {
            ?>
							<option value="<?php 
            echo  $country->id ;
            ?>" <?php 
            if ( $contactoptions['ds_contact_address_country'] == $country->id ) {
                echo  'selected' ;
            }
            ?>><?php 
            echo  $country->name ;
            ?></option>
						<?php 
        }
        ?>
					</select>
				</td>
			</tr>

			<tr>
				<td><?php 
        _e( 'Postal/Zip Code' );
        ?></td>
				<td><input type="text" name="ds_contact_address_postal_code" value="<?php 
        echo  ( $contactoptions['ds_contact_address_postal_code'] ? $contactoptions['ds_contact_address_postal_code'] : '' ) ;
        ?>"/></td>
			</tr>

			<tr>
				<td><?php 
        _e( 'Telephone' );
        ?></td>
				<td><input type="phone" name="ds_contact_telephone" value="<?php 
        echo  ( $contactoptions['ds_contact_telephone'] ? $contactoptions['ds_contact_telephone'] : '' ) ;
        ?>"/></td>
			</tr>

			<tr>
				<td><?php 
        _e( 'Email' );
        ?></td>
				<td><input type="email" name="ds_contact_email" value="<?php 
        echo  ( $contactoptions['ds_contact_email'] ? $contactoptions['ds_contact_email'] : '' ) ;
        ?>"/></td>
			</tr>
		</table>
		<br><input type="submit" value="Save" class="button-primary"/>
	</form>


<?php 
        break;
        //end tab_three contact options
    //end tab_three contact options
    case "tab_four":
        
        if ( !dancepress_fs()->is__premium_only() || !dancepress_fs()->is_plan( 'pro' ) ) {
            ?>
					<br>
					<h2><?php 
            _e( 'Payment Configuration' );
            ?></h2>
					<a href="<?php 
            echo  get_admin_url() ;
            ?>admin.php?page=dancepress_menu-pricing">Upgrade to gain access to payments, billing and tax features.</a>
			<?php 
        }
        
        break;
        //end tab_four fees and taxes options
    //end tab_four fees and taxes options
    case "tab_five":
        //start tab_five fees and taxtes options
        ?>
		<br>
		<h2><?php 
        _e( 'Email Configuration' );
        ?></h2>
	<?php 
        
        if ( !dancepress_fs()->is__premium_only() || !dancepress_fs()->is_plan( 'pro' ) ) {
            ?>
			<a href="<?php 
            echo  get_admin_url() ;
            ?>admin.php?page=dancepress_menu-pricing">Upgrade to gain access to email features.</>
		<?php 
        }
        
        break;
    case "tab_six":
        ?>
		<br>
		<form action="<?php 
        echo  get_admin_url() ;
        ?>admin.php?page=admin-options&action=updateotheroptions&tab=tab_six" method="post">
			<table  class="gridtable options-table">
				<tr>
					<td><?php 
        _e( 'Default State/Province/County' );
        ?></td>
					<td><input type="text" name="dstrwa_province" value="<?php 
        echo  ( isset( $otheroptions['dstrwa_province'] ) ? $otheroptions['dstrwa_province'] : '' ) ;
        ?>"></td>
				</tr>
				<tr>
					<td><?php 
        _e( 'Default Country' );
        ?></td>
					<td>
						<select name="dstrwa_country">
							<option value=""><?php 
        _e( 'Select' );
        ?></option>
							<option value="Afghanistan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Afghanistan' ? 'selected' : '' ) ;
        ?>>Afghanistan</option>
							<option value="Åland Island" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Åland Island' ? 'selected' : '' ) ;
        ?>>Åland Islands</option>
							<option value="Albania" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Albania' ? 'selected' : '' ) ;
        ?>>Albania</option>
							<option value="Algeria" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Algeria' ? 'selected' : '' ) ;
        ?>>Algeria</option>
							<option value="American Samoa" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'American Samoa' ? 'selected' : '' ) ;
        ?>>American Samoa</option>
							<option value="Andorra" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Andorra' ? 'selected' : '' ) ;
        ?>>Andorra</option>
							<option value="Angola" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Angola' ? 'selected' : '' ) ;
        ?>>Angola</option>
							<option value="Anguilla" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Anguilla' ? 'selected' : '' ) ;
        ?>>Anguilla</option>
							<option value="Antarctica" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Antarctica' ? 'selected' : '' ) ;
        ?>>Antarctica</option>
							<option value="Antigua and Barbuda" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Antigua and Barbuda' ? 'selected' : '' ) ;
        ?>>Antigua and Barbuda</option>
							<option value="Argentina" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Argentina' ? 'selected' : '' ) ;
        ?>>Argentina</option>
							<option value="Armenia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Armenia' ? 'selected' : '' ) ;
        ?>>Armenia</option>
							<option value="Aruba" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Aruba' ? 'selected' : '' ) ;
        ?>>Aruba</option>
							<option value="Australia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Australia' ? 'selected' : '' ) ;
        ?>>Australia</option>
							<option value="Austria" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Austria' ? 'selected' : '' ) ;
        ?>>Austria</option>
							<option value="Azerbaijan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Azerbaijan' ? 'selected' : '' ) ;
        ?>>Azerbaijan</option>
							<option value="Bahamas" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Bahamas' ? 'selected' : '' ) ;
        ?>>Bahamas</option>
							<option value="Bahrain" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Bahrain' ? 'selected' : '' ) ;
        ?>>Bahrain</option>
							<option value="Bangladesh" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Bangladesh' ? 'selected' : '' ) ;
        ?>>Bangladesh</option>
							<option value="Barbados" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Barbados' ? 'selected' : '' ) ;
        ?>>Barbados</option>
							<option value="Belarus" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Belarus' ? 'selected' : '' ) ;
        ?>>Belarus</option>
							<option value="Belgium" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Belgium' ? 'selected' : '' ) ;
        ?>>Belgium</option>
							<option value="Belize" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Belize' ? 'selected' : '' ) ;
        ?>>Belize</option>
							<option value="Benin" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Benin' ? 'selected' : '' ) ;
        ?>>Benin</option>
							<option value="Bermuda" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Bermuda' ? 'selected' : '' ) ;
        ?>>Bermuda</option>
							<option value="Bhutan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Bhutan' ? 'selected' : '' ) ;
        ?>>Bhutan</option>
							<option value="Bolivia, Plurinational State of" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Bolivia, Plurinational State of' ? 'selected' : '' ) ;
        ?>>Bolivia, Plurinational State of</option>
							<option value="Bonaire, Sint Eustatius and Saba" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Bonaire, Sint Eustatius and Saba' ? 'selected' : '' ) ;
        ?>>Bonaire, Sint Eustatius and Saba</option>
							<option value="Bosnia and Herzegovina" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Bosnia and Herzegovina' ? 'selected' : '' ) ;
        ?>>Bosnia and Herzegovina</option>
							<option value="Botswana" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Botswana' ? 'selected' : '' ) ;
        ?>>Botswana</option>
							<option value="Bouvet Island" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Bouvet Island' ? 'selected' : '' ) ;
        ?>>Bouvet Island</option>
							<option value="Brazil" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Brazil' ? 'selected' : '' ) ;
        ?>>Brazil</option>
							<option value="British Indian Ocean Territory" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'British Indian Ocean Territory' ? 'selected' : '' ) ;
        ?>>British Indian Ocean Territory</option>
							<option value="Brunei Darussalam" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Brunei Darussalam' ? 'selected' : '' ) ;
        ?>>Brunei Darussalam</option>
							<option value="Bulgaria" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Bulgaria' ? 'selected' : '' ) ;
        ?>>Bulgaria</option>
							<option value="Burkina Faso" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Burkina Faso' ? 'selected' : '' ) ;
        ?>>Burkina Faso</option>
							<option value="Burundi" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Burundi' ? 'selected' : '' ) ;
        ?>>Burundi</option>
							<option value="Cambodia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Cambodia' ? 'selected' : '' ) ;
        ?>>Cambodia</option>
							<option value="Cameroon" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Cameroon' ? 'selected' : '' ) ;
        ?>>Cameroon</option>
							<option value="Canada" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Canada' ? 'selected' : '' ) ;
        ?>>Canada</option>
							<option value="Cape Verde" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Cape Verde' ? 'selected' : '' ) ;
        ?>>Cape Verde</option>
							<option value="Cayman Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Cayman Islands' ? 'selected' : '' ) ;
        ?>>Cayman Islands</option>
							<option value="Central African Republic" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Central African Republic' ? 'selected' : '' ) ;
        ?>>Central African Republic</option>
							<option value="Chad" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Chad' ? 'selected' : '' ) ;
        ?>>Chad</option>
							<option value="Chile" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Chile' ? 'selected' : '' ) ;
        ?>>Chile</option>
							<option value="China" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'China' ? 'selected' : '' ) ;
        ?>>China</option>
							<option value="Christmas Island" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Christmas Island' ? 'selected' : '' ) ;
        ?>>Christmas Island</option>
							<option value="Cocos (Keeling) Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Cocos (Keeling) Islands' ? 'selected' : '' ) ;
        ?>>Cocos (Keeling) Islands</option>
							<option value="Colombia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Colombia' ? 'selected' : '' ) ;
        ?>>Colombia</option>
							<option value="Comoros" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Comoros' ? 'selected' : '' ) ;
        ?>>Comoros</option>
							<option value="Congo" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Congo' ? 'selected' : '' ) ;
        ?>>Congo</option>
							<option value="Congo, the Democratic Republic of the" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Congo, the Democratic Republic of the' ? 'selected' : '' ) ;
        ?>>Congo, the Democratic Republic of the</option>
							<option value="Cook Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Cook Islands' ? 'selected' : '' ) ;
        ?>>Cook Islands</option>
							<option value="Costa Rica" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Costa Rica' ? 'selected' : '' ) ;
        ?>>Costa Rica</option>
							<option value="Côte d'Ivoire" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Côte d\'Ivoire' ? 'selected' : '' ) ;
        ?>>Côte d'Ivoire</option>
							<option value="Croatia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Croatia' ? 'selected' : '' ) ;
        ?>>Croatia</option>
							<option value="Cuba" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Cuba' ? 'selected' : '' ) ;
        ?>>Cuba</option>
							<option value="CW" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'CW' ? 'selected' : '' ) ;
        ?>>Curaçao</option>
							<option value="Cyprus" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Cyprus' ? 'selected' : '' ) ;
        ?>>Cyprus</option>
							<option value="Czech Republic" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Czech Republic' ? 'selected' : '' ) ;
        ?>>Czech Republic</option>
							<option value="Denmark" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Denmark' ? 'selected' : '' ) ;
        ?>>Denmark</option>
							<option value="Djibouti" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Djibouti' ? 'selected' : '' ) ;
        ?>>Djibouti</option>
							<option value="Dominica" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Dominica' ? 'selected' : '' ) ;
        ?>>Dominica</option>
							<option value="Dominican Republic" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Dominican Republic' ? 'selected' : '' ) ;
        ?>>Dominican Republic</option>
							<option value="Ecuador" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Ecuador' ? 'selected' : '' ) ;
        ?>>Ecuador</option>
							<option value="Egypt" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Egypt' ? 'selected' : '' ) ;
        ?>>Egypt</option>
							<option value="El Salvador" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'El Salvador' ? 'selected' : '' ) ;
        ?>>El Salvador</option>
							<option value="Equatorial Guinea" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Equatorial Guinea' ? 'selected' : '' ) ;
        ?>>Equatorial Guinea</option>
							<option value="Eritrea" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Eritrea' ? 'selected' : '' ) ;
        ?>>Eritrea</option>
							<option value="Estonia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Estonia' ? 'selected' : '' ) ;
        ?>>Estonia</option>
							<option value="Ethiopia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Ethiopia' ? 'selected' : '' ) ;
        ?>>Ethiopia</option>
							<option value="Falkland Islands (Malvinas)" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Falkland Islands (Malvinas)' ? 'selected' : '' ) ;
        ?>>Falkland Islands (Malvinas)</option>
							<option value="Faroe Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Faroe Islands' ? 'selected' : '' ) ;
        ?>>Faroe Islands</option>
							<option value="Fiji" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Fiji' ? 'selected' : '' ) ;
        ?>>Fiji</option>
							<option value="Finland" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Finland' ? 'selected' : '' ) ;
        ?>>Finland</option>
							<option value="France" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'France' ? 'selected' : '' ) ;
        ?>>France</option>
							<option value="French Guiana" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'French Guiana' ? 'selected' : '' ) ;
        ?>>French Guiana</option>
							<option value="French Polynesia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'French Polynesia' ? 'selected' : '' ) ;
        ?>>French Polynesia</option>
							<option value="French Southern Territories" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'French Southern Territories' ? 'selected' : '' ) ;
        ?>>French Southern Territories</option>
							<option value="Gabon" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Gabon' ? 'selected' : '' ) ;
        ?>>Gabon</option>
							<option value="Gambia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Gambia' ? 'selected' : '' ) ;
        ?>>Gambia</option>
							<option value="Georgia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Georgia' ? 'selected' : '' ) ;
        ?>>Georgia</option>
							<option value="Germany" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Germany' ? 'selected' : '' ) ;
        ?>>Germany</option>
							<option value="Ghana" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Ghana' ? 'selected' : '' ) ;
        ?>>Ghana</option>
							<option value="Gibraltar" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Gibraltar' ? 'selected' : '' ) ;
        ?>>Gibraltar</option>
							<option value="Greece" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Greece' ? 'selected' : '' ) ;
        ?>>Greece</option>
							<option value="Greenland" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Greenland' ? 'selected' : '' ) ;
        ?>>Greenland</option>
							<option value="Grenada" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Grenada' ? 'selected' : '' ) ;
        ?>>Grenada</option>
							<option value="Guadeloupe" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Guadeloupe' ? 'selected' : '' ) ;
        ?>>Guadeloupe</option>
							<option value="Guam" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Guam' ? 'selected' : '' ) ;
        ?>>Guam</option>
							<option value="Guatemala" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Guatemala' ? 'selected' : '' ) ;
        ?>>Guatemala</option>
							<option value="Guernsey" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Guernsey' ? 'selected' : '' ) ;
        ?>>Guernsey</option>
							<option value="Guinea" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Guinea' ? 'selected' : '' ) ;
        ?>>Guinea</option>
							<option value="Guinea-Bissau" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Guinea-Bissau' ? 'selected' : '' ) ;
        ?>>Guinea-Bissau</option>
							<option value="Guyana" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Guyana' ? 'selected' : '' ) ;
        ?>>Guyana</option>
							<option value="Haiti" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Haiti' ? 'selected' : '' ) ;
        ?>>Haiti</option>
							<option value="Heard Island and McDonald Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Heard Island and McDonald Islands' ? 'selected' : '' ) ;
        ?>>Heard Island and McDonald Islands</option>
							<option value="Holy See (Vatican City State)" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Holy See (Vatican City State)' ? 'selected' : '' ) ;
        ?>>Holy See (Vatican City State)</option>
							<option value="Honduras" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Honduras' ? 'selected' : '' ) ;
        ?>>Honduras</option>
							<option value="Hong Kong" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Hong Kong' ? 'selected' : '' ) ;
        ?>>Hong Kong</option>
							<option value="Hungary" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Hungary' ? 'selected' : '' ) ;
        ?>>Hungary</option>
							<option value="Iceland" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Iceland' ? 'selected' : '' ) ;
        ?>>Iceland</option>
							<option value="India" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'India' ? 'selected' : '' ) ;
        ?>>India</option>
							<option value="Indonesia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Indonesia' ? 'selected' : '' ) ;
        ?>>Indonesia</option>
							<option value="Iran, Islamic Republic of" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Iran, Islamic Republic of' ? 'selected' : '' ) ;
        ?>>Iran, Islamic Republic of</option>
							<option value="Iraq" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Iraq' ? 'selected' : '' ) ;
        ?>>Iraq</option>
							<option value="Ireland" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Ireland' ? 'selected' : '' ) ;
        ?>>Ireland</option>
							<option value="Isle of Man" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Isle of Man' ? 'selected' : '' ) ;
        ?>>Isle of Man</option>
							<option value="Israel" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Israel' ? 'selected' : '' ) ;
        ?>>Israel</option>
							<option value="Italy" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Italy' ? 'selected' : '' ) ;
        ?>>Italy</option>
							<option value="Jamaica" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Jamaica' ? 'selected' : '' ) ;
        ?>>Jamaica</option>
							<option value="Japan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Japan' ? 'selected' : '' ) ;
        ?>>Japan</option>
							<option value="Jersey" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Jersey' ? 'selected' : '' ) ;
        ?>>Jersey</option>
							<option value="Jordan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Jordan' ? 'selected' : '' ) ;
        ?>>Jordan</option>
							<option value="Kazakhstan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Kazakhstan' ? 'selected' : '' ) ;
        ?>>Kazakhstan</option>
							<option value="Kenya" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Kenya' ? 'selected' : '' ) ;
        ?>>Kenya</option>
							<option value="Kiribati" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Kiribati' ? 'selected' : '' ) ;
        ?>>Kiribati</option>
							<option value="Korea, Democratic People's Republic of" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Korea, Democratic People\'s Republic of' ? 'selected' : '' ) ;
        ?>>Korea, Democratic People's Republic of</option>
							<option value="Korea, Republic of" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Korea, Republic of' ? 'selected' : '' ) ;
        ?>>Korea, Republic of</option>
							<option value="Kuwait" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Kuwait' ? 'selected' : '' ) ;
        ?>>Kuwait</option>
							<option value="Kyrgyzstan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Kyrgyzstan' ? 'selected' : '' ) ;
        ?>>Kyrgyzstan</option>
							<option value="Lao People's Democratic Republic" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Lao People\'s Democratic Republic' ? 'selected' : '' ) ;
        ?>>Lao People's Democratic Republic</option>
							<option value="Latvia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Latvia' ? 'selected' : '' ) ;
        ?>>Latvia</option>
							<option value="Lebanon" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Lebanon' ? 'selected' : '' ) ;
        ?>>Lebanon</option>
							<option value="Lesotho" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Lesotho' ? 'selected' : '' ) ;
        ?>>Lesotho</option>
							<option value="Liberia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Liberia' ? 'selected' : '' ) ;
        ?>>Liberia</option>
							<option value="Libya" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Libya' ? 'selected' : '' ) ;
        ?>>Libya</option>
							<option value="Liechtenstein" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Liechtenstein' ? 'selected' : '' ) ;
        ?>>Liechtenstein</option>
							<option value="Lithuania" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Lithuania' ? 'selected' : '' ) ;
        ?>>Lithuania</option>
							<option value="Luxembourg" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Luxembourg' ? 'selected' : '' ) ;
        ?>>Luxembourg</option>
							<option value="Macao" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Macao' ? 'selected' : '' ) ;
        ?>>Macao</option>
							<option value="Macedonia, the former Yugoslav Republic of" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Macedonia, the former Yugoslav Republic of' ? 'selected' : '' ) ;
        ?>>Macedonia, the former Yugoslav Republic of</option>
							<option value="Madagascar" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Madagascar' ? 'selected' : '' ) ;
        ?>>Madagascar</option>
							<option value="Malawi" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Malawi' ? 'selected' : '' ) ;
        ?>>Malawi</option>
							<option value="Malaysia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Malaysia' ? 'selected' : '' ) ;
        ?>>Malaysia</option>
							<option value="Maldives" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Maldives' ? 'selected' : '' ) ;
        ?>>Maldives</option>
							<option value="Mali" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Mali' ? 'selected' : '' ) ;
        ?>>Mali</option>
							<option value="Malta" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Malta' ? 'selected' : '' ) ;
        ?>>Malta</option>
							<option value="Marshall Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Marshall Islands' ? 'selected' : '' ) ;
        ?>>Marshall Islands</option>
							<option value="Martinique" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Martinique' ? 'selected' : '' ) ;
        ?>>Martinique</option>
							<option value="Mauritania" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Mauritania' ? 'selected' : '' ) ;
        ?>>Mauritania</option>
							<option value="Mauritius" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Mauritius' ? 'selected' : '' ) ;
        ?>>Mauritius</option>
							<option value="Mayotte" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Mayotte' ? 'selected' : '' ) ;
        ?>>Mayotte</option>
							<option value="Mexico" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Mexico' ? 'selected' : '' ) ;
        ?>>Mexico</option>
							<option value="Micronesia, Federated States of" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Micronesia, Federated States of' ? 'selected' : '' ) ;
        ?>>Micronesia, Federated States of</option>
							<option value="Moldova, Republic of" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Moldova, Republic of' ? 'selected' : '' ) ;
        ?>>Moldova, Republic of</option>
							<option value="Monaco" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Monaco' ? 'selected' : '' ) ;
        ?>>Monaco</option>
							<option value="Mongolia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Mongolia' ? 'selected' : '' ) ;
        ?>>Mongolia</option>
							<option value="Montenegro" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Montenegro' ? 'selected' : '' ) ;
        ?>>Montenegro</option>
							<option value="Montserrat" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Montserrat' ? 'selected' : '' ) ;
        ?>>Montserrat</option>
							<option value="Morocco" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Morocco' ? 'selected' : '' ) ;
        ?>>Morocco</option>
							<option value="Mozambique" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Mozambique' ? 'selected' : '' ) ;
        ?>>Mozambique</option>
							<option value="Myanmar" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Myanmar' ? 'selected' : '' ) ;
        ?>>Myanmar</option>
							<option value="Namibia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Namibia' ? 'selected' : '' ) ;
        ?>>Namibia</option>
							<option value="Nauru" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Nauru' ? 'selected' : '' ) ;
        ?>>Nauru</option>
							<option value="Nepal" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Nepal' ? 'selected' : '' ) ;
        ?>>Nepal</option>
							<option value="Netherlands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Netherlands' ? 'selected' : '' ) ;
        ?>>Netherlands</option>
							<option value="New Caledonia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'New Caledonia' ? 'selected' : '' ) ;
        ?>>New Caledonia</option>
							<option value="New Zealand" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'New Zealand' ? 'selected' : '' ) ;
        ?>>New Zealand</option>
							<option value="Nicaragua" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Nicaragua' ? 'selected' : '' ) ;
        ?>>Nicaragua</option>
							<option value="Niger" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Niger' ? 'selected' : '' ) ;
        ?>>Niger</option>
							<option value="Nigeria" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Nigeria' ? 'selected' : '' ) ;
        ?>>Nigeria</option>
							<option value="Niue" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Niue' ? 'selected' : '' ) ;
        ?>>Niue</option>
							<option value="Norfolk Island" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Norfolk Island' ? 'selected' : '' ) ;
        ?>>Norfolk Island</option>
							<option value="Northern Mariana Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Northern Mariana Islands' ? 'selected' : '' ) ;
        ?>>Northern Mariana Islands</option>
							<option value="Norway" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Norway' ? 'selected' : '' ) ;
        ?>>Norway</option>
							<option value="Oman" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Oman' ? 'selected' : '' ) ;
        ?>>Oman</option>
							<option value="Pakistan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Pakistan' ? 'selected' : '' ) ;
        ?>>Pakistan</option>
							<option value="Palau" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Palau' ? 'selected' : '' ) ;
        ?>>Palau</option>
							<option value="Palestinian Territory, Occupied" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Palestinian Territory, Occupied' ? 'selected' : '' ) ;
        ?>>Palestinian Territory, Occupied</option>
							<option value="Panama" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Panama' ? 'selected' : '' ) ;
        ?>>Panama</option>
							<option value="Papua New Guinea" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Papua New Guinea' ? 'selected' : '' ) ;
        ?>>Papua New Guinea</option>
							<option value="Paraguay" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Paraguay' ? 'selected' : '' ) ;
        ?>>Paraguay</option>
							<option value="Peru" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Peru' ? 'selected' : '' ) ;
        ?>>Peru</option>
							<option value="Philippines" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Philippines' ? 'selected' : '' ) ;
        ?>>Philippines</option>
							<option value="Pitcairn" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Pitcairn' ? 'selected' : '' ) ;
        ?>>Pitcairn</option>
							<option value="Poland" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Poland' ? 'selected' : '' ) ;
        ?>>Poland</option>
							<option value="Portugal" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Portugal' ? 'selected' : '' ) ;
        ?>>Portugal</option>
							<option value="Puerto Rico" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Puerto Rico' ? 'selected' : '' ) ;
        ?>>Puerto Rico</option>
							<option value="Qatar" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Qatar' ? 'selected' : '' ) ;
        ?>>Qatar</option>
							<option value="Réunion" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Réunion' ? 'selected' : '' ) ;
        ?>>Réunion</option>
							<option value="Romania" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Romania' ? 'selected' : '' ) ;
        ?>>Romania</option>
							<option value="Russian Federation" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Russian Federation' ? 'selected' : '' ) ;
        ?>>Russian Federation</option>
							<option value="Rwanda" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Rwanda' ? 'selected' : '' ) ;
        ?>>Rwanda</option>
							<option value="Saint Barthélemy" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Saint Barthélemy' ? 'selected' : '' ) ;
        ?>>Saint Barthélemy</option>
							<option value="Saint Helena, Ascension and Tristan da Cunha" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Saint Helena, Ascension and Tristan da Cunha' ? 'selected' : '' ) ;
        ?>>Saint Helena, Ascension and Tristan da Cunha</option>
							<option value="Saint Kitts and Nevis" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Saint Kitts and Nevis' ? 'selected' : '' ) ;
        ?>>Saint Kitts and Nevis</option>
							<option value="Saint Lucia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Saint Lucia' ? 'selected' : '' ) ;
        ?>>Saint Lucia</option>
							<option value="Saint Martin (French part)" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Saint Martin (French part)' ? 'selected' : '' ) ;
        ?>>Saint Martin (French part)</option>
							<option value="Saint Pierre and Miquelon" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Saint Pierre and Miquelon' ? 'selected' : '' ) ;
        ?>>Saint Pierre and Miquelon</option>
							<option value="Saint Vincent and the Grenadines" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Saint Vincent and the Grenadines' ? 'selected' : '' ) ;
        ?>>Saint Vincent and the Grenadines</option>
							<option value="Samoa" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Samoa' ? 'selected' : '' ) ;
        ?>>Samoa</option>
							<option value="San Marino" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'San Marino' ? 'selected' : '' ) ;
        ?>>San Marino</option>
							<option value="Sao Tome and Principe" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Sao Tome and Principe' ? 'selected' : '' ) ;
        ?>>Sao Tome and Principe</option>
							<option value="Saudi Arabia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Saudi Arabia' ? 'selected' : '' ) ;
        ?>>Saudi Arabia</option>
							<option value="Senegal" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Senegal' ? 'selected' : '' ) ;
        ?>>Senegal</option>
							<option value="Serbia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Serbia' ? 'selected' : '' ) ;
        ?>>Serbia</option>
							<option value="Seychelles" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Seychelles' ? 'selected' : '' ) ;
        ?>>Seychelles</option>
							<option value="Sierra Leone" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Sierra Leone' ? 'selected' : '' ) ;
        ?>>Sierra Leone</option>
							<option value="Singapore" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Singapore' ? 'selected' : '' ) ;
        ?>>Singapore</option>
							<option value="Sint Maarten (Dutch part)" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Sint Maarten (Dutch part)' ? 'selected' : '' ) ;
        ?>>Sint Maarten (Dutch part)</option>
							<option value="Slovakia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Slovakia' ? 'selected' : '' ) ;
        ?>>Slovakia</option>
							<option value="Slovenia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Slovenia' ? 'selected' : '' ) ;
        ?>>Slovenia</option>
							<option value="Solomon Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Solomon Islands' ? 'selected' : '' ) ;
        ?>>Solomon Islands</option>
							<option value="Somalia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Somalia' ? 'selected' : '' ) ;
        ?>>Somalia</option>
							<option value="South Africa" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'South Africa' ? 'selected' : '' ) ;
        ?>>South Africa</option>
							<option value="South Georgia and the South Sandwich Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'South Georgia and the South Sandwich Islands' ? 'selected' : '' ) ;
        ?>>South Georgia and the South Sandwich Islands</option>
							<option value="South Sudan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'South Sudan' ? 'selected' : '' ) ;
        ?>>South Sudan</option>
							<option value="Spain" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Spain' ? 'selected' : '' ) ;
        ?>>Spain</option>
							<option value="Sri Lanka" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Sri Lanka' ? 'selected' : '' ) ;
        ?>>Sri Lanka</option>
							<option value="Sudan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Sudan' ? 'selected' : '' ) ;
        ?>>Sudan</option>
							<option value="Suriname" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Suriname' ? 'selected' : '' ) ;
        ?>>Suriname</option>
							<option value="Svalbard and Jan Mayen" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Svalbard and Jan Mayen' ? 'selected' : '' ) ;
        ?>>Svalbard and Jan Mayen</option>
							<option value="Swaziland" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Swaziland' ? 'selected' : '' ) ;
        ?>>Swaziland</option>
							<option value="Sweden" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Sweden' ? 'selected' : '' ) ;
        ?>>Sweden</option>
							<option value="Switzerland" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Switzerland' ? 'selected' : '' ) ;
        ?>>Switzerland</option>
							<option value="Syrian Arab Republic" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Syrian Arab Republic' ? 'selected' : '' ) ;
        ?>>Syrian Arab Republic</option>
							<option value="Taiwan, Province of China" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Taiwan, Province of China' ? 'selected' : '' ) ;
        ?>>Taiwan, Province of China</option>
							<option value="Tajikistan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Tajikistan' ? 'selected' : '' ) ;
        ?>>Tajikistan</option>
							<option value="Tanzania, United Republic of" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Tanzania, United Republic of' ? 'selected' : '' ) ;
        ?>>Tanzania, United Republic of</option>
							<option value="Thailand" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Thailand' ? 'selected' : '' ) ;
        ?>>Thailand</option>
							<option value="Timor-Leste" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Timor-Leste' ? 'selected' : '' ) ;
        ?>>Timor-Leste</option>
							<option value="Togo" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Togo' ? 'selected' : '' ) ;
        ?>>Togo</option>
							<option value="Tokelau" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Tokelau' ? 'selected' : '' ) ;
        ?>>Tokelau</option>
							<option value="Tonga" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Tonga' ? 'selected' : '' ) ;
        ?>>Tonga</option>
							<option value="Trinidad and Tobago" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Trinidad and Tobago' ? 'selected' : '' ) ;
        ?>>Trinidad and Tobago</option>
							<option value="Tunisia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Tunisia' ? 'selected' : '' ) ;
        ?>>Tunisia</option>
							<option value="Turkey" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Turkey' ? 'selected' : '' ) ;
        ?>>Turkey</option>
							<option value="Turkmenistan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Turkmenistan' ? 'selected' : '' ) ;
        ?>>Turkmenistan</option>
							<option value="Turks and Caicos Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Turks and Caicos Islands' ? 'selected' : '' ) ;
        ?>>Turks and Caicos Islands</option>
							<option value="Tuvalu" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Tuvalu' ? 'selected' : '' ) ;
        ?>>Tuvalu</option>
							<option value="Uganda" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Uganda' ? 'selected' : '' ) ;
        ?>>Uganda</option>
							<option value="Ukraine" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Ukraine' ? 'selected' : '' ) ;
        ?>>Ukraine</option>
							<option value="United Arab Emirates" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'United Arab Emirates' ? 'selected' : '' ) ;
        ?>>United Arab Emirates</option>
							<option value="United Kingdom" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'United Kingdom' ? 'selected' : '' ) ;
        ?>>United Kingdom</option>
							<option value="United States" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'United States' ? 'selected' : '' ) ;
        ?>>United States</option>
							<option value="United States Minor Outlying Islands" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'United States Minor Outlying Islands' ? 'selected' : '' ) ;
        ?>>United States Minor Outlying Islands</option>
							<option value="Uruguay" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Uruguay' ? 'selected' : '' ) ;
        ?>>Uruguay</option>
							<option value="Uzbekistan" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Uzbekistan' ? 'selected' : '' ) ;
        ?>>Uzbekistan</option>
							<option value="Vanuatu" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Vanuatu' ? 'selected' : '' ) ;
        ?>>Vanuatu</option>
							<option value="Venezuela, Bolivarian Republic of" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Venezuela, Bolivarian Republic of' ? 'selected' : '' ) ;
        ?>>Venezuela, Bolivarian Republic of</option>
							<option value="Viet Nam" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Viet Nam' ? 'selected' : '' ) ;
        ?>>Viet Nam</option>
							<option value="Virgin Islands, British" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Virgin Islands, British' ? 'selected' : '' ) ;
        ?>>Virgin Islands, British</option>
							<option value="Virgin Islands, U.S." <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Virgin Islands, U.S.' ? 'selected' : '' ) ;
        ?>>Virgin Islands, U.S.</option>
							<option value="Wallis and Futuna" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Wallis and Futuna' ? 'selected' : '' ) ;
        ?>>Wallis and Futuna</option>
							<option value="Western Sahara" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Western Sahara' ? 'selected' : '' ) ;
        ?>>Western Sahara</option>
							<option value="Yemen" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Yemen' ? 'selected' : '' ) ;
        ?>>Yemen</option>
							<option value="Zambia" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Zambia' ? 'selected' : '' ) ;
        ?>>Zambia</option>
							<option value="Zimbabwe" <?php 
        echo  ( @$otheroptions['dstrwa_country'] == 'Zimbabwe' ? 'selected' : '' ) ;
        ?>>Zimbabwe</option>
						</select>
					</td>
					</tr>

					<tr>
						<td><?php 
        _e( 'Default City' );
        ?></td>
						<td><input type="text" name="dstrwa_city" value="<?php 
        echo  ( isset( $otheroptions['dstrwa_city'] ) ? $otheroptions['dstrwa_city'] : '' ) ;
        ?>"></td>
					</tr>
					<tr>
						<td><?php 
        _e( 'Max students per class (0 disables). Set to 100 at installation)' );
        ?></td>
						<td><input type="number" name="dstrwa_class_limit" value="<?php 
        echo  ( isset( $otheroptions['dstrwa_class_limit'] ) && (int) $otheroptions['dstrwa_class_limit'] ? (int) $otheroptions['dstrwa_class_limit'] : DANCEPRESSTRWA_CLASS_LIMIT ) ;
        ?>" min="0" step="1"></td>
					</tr>
				<?php 
        ?>
			</table>
			<br><input type="submit" value="Save" class="button-primary"/>
		</form>

	<?php 
        break;
    default:
        // code...
        break;
}
?>


</div>
