<h1><?php _e('Add Clients');?></h1>

<p><?php _e('Use this area to add Clients (ie parents/guardians) and create billing agreements through the DancePress.');?></p>
<h3><?php _e('Notes');?></h3>
<ol>
	<li><?php _e('Clients\' user accounts will be created, and they will be notified of login details <b>if requested in the form below</b>');?>.</li>
	<li><?php _e('After creating a client, edit the new client and create students and assign classes through the "Client Management" page.');?></li>
	<li><?php _e('You can create billing agreements through DancePress, but charges will not be automatically made until a Stripe customer is created.');?></li>
</ol>

<form action="<?php echo get_admin_url(); ?>admin.php?page=admin-addclients" method="post" class="registration" id="admin-addclients-form">
	<input type="hidden" name="action" value="savenewclient"/>
	<table class="gridtable">
		<tbody>
			<tr>
				<td colspan="2">
					<h3><?php _e('Account Holder Details');?></h3>
				</td>
			</tr>
			<tr>
				<td style="width: 400px;">
					<?php _e('First name*');?>
				</td>
				<td>
					<input type="text" name="parent[firstname]" id="newclient-firstname" value="" class="req"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Last name*');?>
				</td>
				<td>
					<input type="text" name="parent[lastname]" id="newclient-lastname" value="" class="req"/>
					<span class="duplicate" id="newclient-lastname-duplicate">Warning: Duplicate first/last name</span>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Email address*');?>
                    </td>
				 <td>
					    <input type="text" name="parent[email]" id="newclient-email" value="" class="req email"/>
						<span class="duplicate" id="newclient-email-duplicate">Error: Duplicate email. A user with this email address already exists.</span>
				 </td>
			</tr>
			<tr>
				<td>
					<?php _e('Optional additional email');?>
                    </td>
				 <td>
					    <input type="text" name="parent[email_additional]" id="newclient-additionalemail" value="" class="email"/>
						<span class="duplicate" id="newclient-additionalemail-duplicate">Warning: Duplicate email</span>
				 </td>
			</tr>
			<tr>
				<td>
					<?php _e('Street Address');?>
				</td>
				<td>
					<input type="text" name="parent[address1]"  id="newclient-address" value=""/>
					<span class="duplicate" id="newclient-address-duplicate">Warning: Duplicate street address</span>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Street Address 2');?>
				</td>
				<td>
					<input type="text" name="parent[address2]" value=""/>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('City');?>
				</td>
				<td>
					<?php $city = get_option('dstrwa_city');?>
					<input type="text" name="parent[city]" value="<?=$city?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Postal code');?>
				</td>
				<td>
					<input type="text" name="parent[postal_code]" value="" />
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('State/Province/County');?>
				</td>
				<td>
					<input type="text" name="parent[province]" value="<?=get_option('dstrwa_province')?>" class=""/>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Country');?>
				</td>
				<td>
					<?php $country = get_option('dstrwa_country');?>
					<select name="parent[country]">
						<option value="">Select</option>
						<option value="Afghanistan" <?=$country == 'Afghanistan' ? 'selected' : '' ?>>Afghanistan</option>
						<option value="Åland Island" <?=$country == 'Åland Island' ? 'selected' : '' ?>>Åland Islands</option>
						<option value="Albania" <?=$country == 'Albania' ? 'selected' : '' ?>>Albania</option>
						<option value="Algeria" <?=$country == 'Algeria' ? 'selected' : '' ?>>Algeria</option>
						<option value="American Samoa" <?=$country == 'American Samoa' ? 'selected' : '' ?>>American Samoa</option>
						<option value="Andorra" <?=$country == 'Andorra' ? 'selected' : '' ?>>Andorra</option>
						<option value="Angola" <?=$country == 'Angola' ? 'selected' : '' ?>>Angola</option>
						<option value="Anguilla" <?=$country == 'Anguilla' ? 'selected' : '' ?>>Anguilla</option>
						<option value="Antarctica" <?=$country == 'Antarctica' ? 'selected' : '' ?>>Antarctica</option>
						<option value="Antigua and Barbuda" <?=$country == 'Antigua and Barbuda' ? 'selected' : '' ?>>Antigua and Barbuda</option>
						<option value="Argentina" <?=$country == 'Argentina' ? 'selected' : '' ?>>Argentina</option>
						<option value="Armenia" <?=$country == 'Armenia' ? 'selected' : '' ?>>Armenia</option>
						<option value="Aruba" <?=$country == 'Aruba' ? 'selected' : '' ?>>Aruba</option>
						<option value="Australia" <?=$country == 'Australia' ? 'selected' : '' ?>>Australia</option>
						<option value="Austria" <?=$country == 'Austria' ? 'selected' : '' ?>>Austria</option>
						<option value="Azerbaijan" <?=$country == 'Azerbaijan' ? 'selected' : '' ?>>Azerbaijan</option>
						<option value="Bahamas" <?=$country == 'Bahamas' ? 'selected' : '' ?>>Bahamas</option>
						<option value="Bahrain" <?=$country == 'Bahrain' ? 'selected' : '' ?>>Bahrain</option>
						<option value="Bangladesh" <?=$country == 'Bangladesh' ? 'selected' : '' ?>>Bangladesh</option>
						<option value="Barbados" <?=$country == 'Barbados' ? 'selected' : '' ?>>Barbados</option>
						<option value="Belarus" <?=$country == 'Belarus' ? 'selected' : '' ?>>Belarus</option>
						<option value="Belgium" <?=$country == 'Belgium' ? 'selected' : '' ?>>Belgium</option>
						<option value="Belize" <?=$country == 'Belize' ? 'selected' : '' ?>>Belize</option>
						<option value="Benin" <?=$country == 'Benin' ? 'selected' : '' ?>>Benin</option>
						<option value="Bermuda" <?=$country == 'Bermuda' ? 'selected' : '' ?>>Bermuda</option>
						<option value="Bhutan" <?=$country == 'Bhutan' ? 'selected' : '' ?>>Bhutan</option>
						<option value="Bolivia, Plurinational State of" <?=$country == 'Bolivia, Plurinational State of' ? 'selected' : '' ?>>Bolivia, Plurinational State of</option>
						<option value="Bonaire, Sint Eustatius and Saba" <?=$country == 'Bonaire, Sint Eustatius and Saba' ? 'selected' : '' ?>>Bonaire, Sint Eustatius and Saba</option>
						<option value="Bosnia and Herzegovina" <?=$country == 'Bosnia and Herzegovina' ? 'selected' : '' ?>>Bosnia and Herzegovina</option>
						<option value="Botswana" <?=$country == 'Botswana' ? 'selected' : '' ?>>Botswana</option>
						<option value="Bouvet Island" <?=$country == 'Bouvet Island' ? 'selected' : '' ?>>Bouvet Island</option>
						<option value="Brazil" <?=$country == 'Brazil' ? 'selected' : '' ?>>Brazil</option>
						<option value="British Indian Ocean Territory" <?=$country == 'British Indian Ocean Territory' ? 'selected' : '' ?>>British Indian Ocean Territory</option>
						<option value="Brunei Darussalam" <?=$country == 'Brunei Darussalam' ? 'selected' : '' ?>>Brunei Darussalam</option>
						<option value="Bulgaria" <?=$country == 'Bulgaria' ? 'selected' : '' ?>>Bulgaria</option>
						<option value="Burkina Faso" <?=$country == 'Burkina Faso' ? 'selected' : '' ?>>Burkina Faso</option>
						<option value="Burundi" <?=$country == 'Burundi' ? 'selected' : '' ?>>Burundi</option>
						<option value="Cambodia" <?=$country == 'Cambodia' ? 'selected' : '' ?>>Cambodia</option>
						<option value="Cameroon" <?=$country == 'Cameroon' ? 'selected' : '' ?>>Cameroon</option>
						<option value="Canada" <?=$country == 'Canada' ? 'selected' : '' ?>>Canada</option>
						<option value="Cape Verde" <?=$country == 'Cape Verde' ? 'selected' : '' ?>>Cape Verde</option>
						<option value="Cayman Islands" <?=$country == 'Cayman Islands' ? 'selected' : '' ?>>Cayman Islands</option>
						<option value="Central African Republic" <?=$country == 'Central African Republic' ? 'selected' : '' ?>>Central African Republic</option>
						<option value="Chad" <?=$country == 'Chad' ? 'selected' : '' ?>>Chad</option>
						<option value="Chile" <?=$country == 'Chile' ? 'selected' : '' ?>>Chile</option>
						<option value="China" <?=$country == 'China' ? 'selected' : '' ?>>China</option>
						<option value="Christmas Island" <?=$country == 'Christmas Island' ? 'selected' : '' ?>>Christmas Island</option>
						<option value="Cocos (Keeling) Islands" <?=$country == 'Cocos (Keeling) Islands' ? 'selected' : '' ?>>Cocos (Keeling) Islands</option>
						<option value="Colombia" <?=$country == 'Colombia' ? 'selected' : '' ?>>Colombia</option>
						<option value="Comoros" <?=$country == 'Comoros' ? 'selected' : '' ?>>Comoros</option>
						<option value="Congo" <?=$country == 'Congo' ? 'selected' : '' ?>>Congo</option>
						<option value="Congo, the Democratic Republic of the" <?=$country == 'Congo, the Democratic Republic of the' ? 'selected' : '' ?>>Congo, the Democratic Republic of the</option>
						<option value="Cook Islands" <?=$country == 'Cook Islands' ? 'selected' : '' ?>>Cook Islands</option>
						<option value="Costa Rica" <?=$country == 'Costa Rica' ? 'selected' : '' ?>>Costa Rica</option>
						<option value="Côte d'Ivoire" <?=$country == 'Côte d\'Ivoire' ? 'selected' : '' ?>>Côte d'Ivoire</option>
						<option value="Croatia" <?=$country == 'Croatia' ? 'selected' : '' ?>>Croatia</option>
						<option value="Cuba" <?=$country == 'Cuba' ? 'selected' : '' ?>>Cuba</option>
						<option value="CW" <?=$country == 'CW' ? 'selected' : '' ?>>Curaçao</option>
						<option value="Cyprus" <?=$country == 'Cyprus' ? 'selected' : '' ?>>Cyprus</option>
						<option value="Czech Republic" <?=$country == 'Czech Republic' ? 'selected' : '' ?>>Czech Republic</option>
						<option value="Denmark" <?=$country == 'Denmark' ? 'selected' : '' ?>>Denmark</option>
						<option value="Djibouti" <?=$country == 'Djibouti' ? 'selected' : '' ?>>Djibouti</option>
						<option value="Dominica" <?=$country == 'Dominica' ? 'selected' : '' ?>>Dominica</option>
						<option value="Dominican Republic" <?=$country == 'Dominican Republic' ? 'selected' : '' ?>>Dominican Republic</option>
						<option value="Ecuador" <?=$country == 'Ecuador' ? 'selected' : '' ?>>Ecuador</option>
						<option value="Egypt" <?=$country == 'Egypt' ? 'selected' : '' ?>>Egypt</option>
						<option value="El Salvador" <?=$country == 'El Salvador' ? 'selected' : '' ?>>El Salvador</option>
						<option value="Equatorial Guinea" <?=$country == 'Equatorial Guinea' ? 'selected' : '' ?>>Equatorial Guinea</option>
						<option value="Eritrea" <?=$country == 'Eritrea' ? 'selected' : '' ?>>Eritrea</option>
						<option value="Estonia" <?=$country == 'Estonia' ? 'selected' : '' ?>>Estonia</option>
						<option value="Ethiopia" <?=$country == 'Ethiopia' ? 'selected' : '' ?>>Ethiopia</option>
						<option value="Falkland Islands (Malvinas)" <?=$country == 'Falkland Islands (Malvinas)' ? 'selected' : '' ?>>Falkland Islands (Malvinas)</option>
						<option value="Faroe Islands" <?=$country == 'Faroe Islands' ? 'selected' : '' ?>>Faroe Islands</option>
						<option value="Fiji" <?=$country == 'Fiji' ? 'selected' : '' ?>>Fiji</option>
						<option value="Finland" <?=$country == 'Finland' ? 'selected' : '' ?>>Finland</option>
						<option value="France" <?=$country == 'France' ? 'selected' : '' ?>>France</option>
						<option value="French Guiana" <?=$country == 'French Guiana' ? 'selected' : '' ?>>French Guiana</option>
						<option value="French Polynesia" <?=$country == 'French Polynesia' ? 'selected' : '' ?>>French Polynesia</option>
						<option value="French Southern Territories" <?=$country == 'French Southern Territories' ? 'selected' : '' ?>>French Southern Territories</option>
						<option value="Gabon" <?=$country == 'Gabon' ? 'selected' : '' ?>>Gabon</option>
						<option value="Gambia" <?=$country == 'Gambia' ? 'selected' : '' ?>>Gambia</option>
						<option value="Georgia" <?=$country == 'Georgia' ? 'selected' : '' ?>>Georgia</option>
						<option value="Germany" <?=$country == 'Germany' ? 'selected' : '' ?>>Germany</option>
						<option value="Ghana" <?=$country == 'Ghana' ? 'selected' : '' ?>>Ghana</option>
						<option value="Gibraltar" <?=$country == 'Gibraltar' ? 'selected' : '' ?>>Gibraltar</option>
						<option value="Greece" <?=$country == 'Greece' ? 'selected' : '' ?>>Greece</option>
						<option value="Greenland" <?=$country == 'Greenland' ? 'selected' : '' ?>>Greenland</option>
						<option value="Grenada" <?=$country == 'Grenada' ? 'selected' : '' ?>>Grenada</option>
						<option value="Guadeloupe" <?=$country == 'Guadeloupe' ? 'selected' : '' ?>>Guadeloupe</option>
						<option value="Guam" <?=$country == 'Guam' ? 'selected' : '' ?>>Guam</option>
						<option value="Guatemala" <?=$country == 'Guatemala' ? 'selected' : '' ?>>Guatemala</option>
						<option value="Guernsey" <?=$country == 'Guernsey' ? 'selected' : '' ?>>Guernsey</option>
						<option value="Guinea" <?=$country == 'Guinea' ? 'selected' : '' ?>>Guinea</option>
						<option value="Guinea-Bissau" <?=$country == 'Guinea-Bissau' ? 'selected' : '' ?>>Guinea-Bissau</option>
						<option value="Guyana" <?=$country == 'Guyana' ? 'selected' : '' ?>>Guyana</option>
						<option value="Haiti" <?=$country == 'Haiti' ? 'selected' : '' ?>>Haiti</option>
						<option value="Heard Island and McDonald Islands" <?=$country == 'Heard Island and McDonald Islands' ? 'selected' : '' ?>>Heard Island and McDonald Islands</option>
						<option value="Holy See (Vatican City State)" <?=$country == 'Holy See (Vatican City State)' ? 'selected' : '' ?>>Holy See (Vatican City State)</option>
						<option value="Honduras" <?=$country == 'Honduras' ? 'selected' : '' ?>>Honduras</option>
						<option value="Hong Kong" <?=$country == 'Hong Kong' ? 'selected' : '' ?>>Hong Kong</option>
						<option value="Hungary" <?=$country == 'Hungary' ? 'selected' : '' ?>>Hungary</option>
						<option value="Iceland" <?=$country == 'Iceland' ? 'selected' : '' ?>>Iceland</option>
						<option value="India" <?=$country == 'India' ? 'selected' : '' ?>>India</option>
						<option value="Indonesia" <?=$country == 'Indonesia' ? 'selected' : '' ?>>Indonesia</option>
						<option value="Iran, Islamic Republic of" <?=$country == 'Iran, Islamic Republic of' ? 'selected' : '' ?>>Iran, Islamic Republic of</option>
						<option value="Iraq" <?=$country == 'Iraq' ? 'selected' : '' ?>>Iraq</option>
						<option value="Ireland" <?=$country == 'Ireland' ? 'selected' : '' ?>>Ireland</option>
						<option value="Isle of Man" <?=$country == 'Isle of Man' ? 'selected' : '' ?>>Isle of Man</option>
						<option value="Israel" <?=$country == 'Israel' ? 'selected' : '' ?>>Israel</option>
						<option value="Italy" <?=$country == 'Italy' ? 'selected' : '' ?>>Italy</option>
						<option value="Jamaica" <?=$country == 'Jamaica' ? 'selected' : '' ?>>Jamaica</option>
						<option value="Japan" <?=$country == 'Japan' ? 'selected' : '' ?>>Japan</option>
						<option value="Jersey" <?=$country == 'Jersey' ? 'selected' : '' ?>>Jersey</option>
						<option value="Jordan" <?=$country == 'Jordan' ? 'selected' : '' ?>>Jordan</option>
						<option value="Kazakhstan" <?=$country == 'Kazakhstan' ? 'selected' : '' ?>>Kazakhstan</option>
						<option value="Kenya" <?=$country == 'Kenya' ? 'selected' : '' ?>>Kenya</option>
						<option value="Kiribati" <?=$country == 'Kiribati' ? 'selected' : '' ?>>Kiribati</option>
						<option value="Korea, Democratic People's Republic of" <?=$country == 'Korea, Democratic People\'s Republic of' ? 'selected' : '' ?>>Korea, Democratic People's Republic of</option>
						<option value="Korea, Republic of" <?=$country == 'Korea, Republic of' ? 'selected' : '' ?>>Korea, Republic of</option>
						<option value="Kuwait" <?=$country == 'Kuwait' ? 'selected' : '' ?>>Kuwait</option>
						<option value="Kyrgyzstan" <?=$country == 'Kyrgyzstan' ? 'selected' : '' ?>>Kyrgyzstan</option>
						<option value="Lao People's Democratic Republic" <?=$country == 'Lao People\'s Democratic Republic' ? 'selected' : '' ?>>Lao People's Democratic Republic</option>
						<option value="Latvia" <?=$country == 'Latvia' ? 'selected' : '' ?>>Latvia</option>
						<option value="Lebanon" <?=$country == 'Lebanon' ? 'selected' : '' ?>>Lebanon</option>
						<option value="Lesotho" <?=$country == 'Lesotho' ? 'selected' : '' ?>>Lesotho</option>
						<option value="Liberia" <?=$country == 'Liberia' ? 'selected' : '' ?>>Liberia</option>
						<option value="Libya" <?=$country == 'Libya' ? 'selected' : '' ?>>Libya</option>
						<option value="Liechtenstein" <?=$country == 'Liechtenstein' ? 'selected' : '' ?>>Liechtenstein</option>
						<option value="Lithuania" <?=$country == 'Lithuania' ? 'selected' : '' ?>>Lithuania</option>
						<option value="Luxembourg" <?=$country == 'Luxembourg' ? 'selected' : '' ?>>Luxembourg</option>
						<option value="Macao" <?=$country == 'Macao' ? 'selected' : '' ?>>Macao</option>
						<option value="Macedonia, the former Yugoslav Republic of" <?=$country == 'Macedonia, the former Yugoslav Republic of' ? 'selected' : '' ?>>Macedonia, the former Yugoslav Republic of</option>
						<option value="Madagascar" <?=$country == 'Madagascar' ? 'selected' : '' ?>>Madagascar</option>
						<option value="Malawi" <?=$country == 'Malawi' ? 'selected' : '' ?>>Malawi</option>
						<option value="Malaysia" <?=$country == 'Malaysia' ? 'selected' : '' ?>>Malaysia</option>
						<option value="Maldives" <?=$country == 'Maldives' ? 'selected' : '' ?>>Maldives</option>
						<option value="Mali" <?=$country == 'Mali' ? 'selected' : '' ?>>Mali</option>
						<option value="Malta" <?=$country == 'Malta' ? 'selected' : '' ?>>Malta</option>
						<option value="Marshall Islands" <?=$country == 'Marshall Islands' ? 'selected' : '' ?>>Marshall Islands</option>
						<option value="Martinique" <?=$country == 'Martinique' ? 'selected' : '' ?>>Martinique</option>
						<option value="Mauritania" <?=$country == 'Mauritania' ? 'selected' : '' ?>>Mauritania</option>
						<option value="Mauritius" <?=$country == 'Mauritius' ? 'selected' : '' ?>>Mauritius</option>
						<option value="Mayotte" <?=$country == 'Mayotte' ? 'selected' : '' ?>>Mayotte</option>
						<option value="Mexico" <?=$country == 'Mexico' ? 'selected' : '' ?>>Mexico</option>
						<option value="Micronesia, Federated States of" <?=$country == 'Micronesia, Federated States of' ? 'selected' : '' ?>>Micronesia, Federated States of</option>
						<option value="Moldova, Republic of" <?=$country == 'Moldova, Republic of' ? 'selected' : '' ?>>Moldova, Republic of</option>
						<option value="Monaco" <?=$country == 'Monaco' ? 'selected' : '' ?>>Monaco</option>
						<option value="Mongolia" <?=$country == 'Mongolia' ? 'selected' : '' ?>>Mongolia</option>
						<option value="Montenegro" <?=$country == 'Montenegro' ? 'selected' : '' ?>>Montenegro</option>
						<option value="Montserrat" <?=$country == 'Montserrat' ? 'selected' : '' ?>>Montserrat</option>
						<option value="Morocco" <?=$country == 'Morocco' ? 'selected' : '' ?>>Morocco</option>
						<option value="Mozambique" <?=$country == 'Mozambique' ? 'selected' : '' ?>>Mozambique</option>
						<option value="Myanmar" <?=$country == 'Myanmar' ? 'selected' : '' ?>>Myanmar</option>
						<option value="Namibia" <?=$country == 'Namibia' ? 'selected' : '' ?>>Namibia</option>
						<option value="Nauru" <?=$country == 'Nauru' ? 'selected' : '' ?>>Nauru</option>
						<option value="Nepal" <?=$country == 'Nepal' ? 'selected' : '' ?>>Nepal</option>
						<option value="Netherlands" <?=$country == 'Netherlands' ? 'selected' : '' ?>>Netherlands</option>
						<option value="New Caledonia" <?=$country == 'New Caledonia' ? 'selected' : '' ?>>New Caledonia</option>
						<option value="New Zealand" <?=$country == 'New Zealand' ? 'selected' : '' ?>>New Zealand</option>
						<option value="Nicaragua" <?=$country == 'Nicaragua' ? 'selected' : '' ?>>Nicaragua</option>
						<option value="Niger" <?=$country == 'Niger' ? 'selected' : '' ?>>Niger</option>
						<option value="Nigeria" <?=$country == 'Nigeria' ? 'selected' : '' ?>>Nigeria</option>
						<option value="Niue" <?=$country == 'Niue' ? 'selected' : '' ?>>Niue</option>
						<option value="Norfolk Island" <?=$country == 'Norfolk Island' ? 'selected' : '' ?>>Norfolk Island</option>
						<option value="Northern Mariana Islands" <?=$country == 'Northern Mariana Islands' ? 'selected' : '' ?>>Northern Mariana Islands</option>
						<option value="Norway" <?=$country == 'Norway' ? 'selected' : '' ?>>Norway</option>
						<option value="Oman" <?=$country == 'Oman' ? 'selected' : '' ?>>Oman</option>
						<option value="Pakistan" <?=$country == 'Pakistan' ? 'selected' : '' ?>>Pakistan</option>
						<option value="Palau" <?=$country == 'Palau' ? 'selected' : '' ?>>Palau</option>
						<option value="Palestinian Territory, Occupied" <?=$country == 'Palestinian Territory, Occupied' ? 'selected' : '' ?>>Palestinian Territory, Occupied</option>
						<option value="Panama" <?=$country == 'Panama' ? 'selected' : '' ?>>Panama</option>
						<option value="Papua New Guinea" <?=$country == 'Papua New Guinea' ? 'selected' : '' ?>>Papua New Guinea</option>
						<option value="Paraguay" <?=$country == 'Paraguay' ? 'selected' : '' ?>>Paraguay</option>
						<option value="Peru" <?=$country == 'Peru' ? 'selected' : '' ?>>Peru</option>
						<option value="Philippines" <?=$country == 'Philippines' ? 'selected' : '' ?>>Philippines</option>
						<option value="Pitcairn" <?=$country == 'Pitcairn' ? 'selected' : '' ?>>Pitcairn</option>
						<option value="Poland" <?=$country == 'Poland' ? 'selected' : '' ?>>Poland</option>
						<option value="Portugal" <?=$country == 'Portugal' ? 'selected' : '' ?>>Portugal</option>
						<option value="Puerto Rico" <?=$country == 'Puerto Rico' ? 'selected' : '' ?>>Puerto Rico</option>
						<option value="Qatar" <?=$country == 'Qatar' ? 'selected' : '' ?>>Qatar</option>
						<option value="Réunion" <?=$country == 'Réunion' ? 'selected' : '' ?>>Réunion</option>
						<option value="Romania" <?=$country == 'Romania' ? 'selected' : '' ?>>Romania</option>
						<option value="Russian Federation" <?=$country == 'Russian Federation' ? 'selected' : '' ?>>Russian Federation</option>
						<option value="Rwanda" <?=$country == 'Rwanda' ? 'selected' : '' ?>>Rwanda</option>
						<option value="Saint Barthélemy" <?=$country == 'Saint Barthélemy' ? 'selected' : '' ?>>Saint Barthélemy</option>
						<option value="Saint Helena, Ascension and Tristan da Cunha" <?=$country == 'Saint Helena, Ascension and Tristan da Cunha' ? 'selected' : '' ?>>Saint Helena, Ascension and Tristan da Cunha</option>
						<option value="Saint Kitts and Nevis" <?=$country == 'Saint Kitts and Nevis' ? 'selected' : '' ?>>Saint Kitts and Nevis</option>
						<option value="Saint Lucia" <?=$country == 'Saint Lucia' ? 'selected' : '' ?>>Saint Lucia</option>
						<option value="Saint Martin (French part)" <?=$country == 'Saint Martin (French part)' ? 'selected' : '' ?>>Saint Martin (French part)</option>
						<option value="Saint Pierre and Miquelon" <?=$country == 'Saint Pierre and Miquelon' ? 'selected' : '' ?>>Saint Pierre and Miquelon</option>
						<option value="Saint Vincent and the Grenadines" <?=$country == 'Saint Vincent and the Grenadines' ? 'selected' : '' ?>>Saint Vincent and the Grenadines</option>
						<option value="Samoa" <?=$country == 'Samoa' ? 'selected' : '' ?>>Samoa</option>
						<option value="San Marino" <?=$country == 'San Marino' ? 'selected' : '' ?>>San Marino</option>
						<option value="Sao Tome and Principe" <?=$country == 'Sao Tome and Principe' ? 'selected' : '' ?>>Sao Tome and Principe</option>
						<option value="Saudi Arabia" <?=$country == 'Saudi Arabia' ? 'selected' : '' ?>>Saudi Arabia</option>
						<option value="Senegal" <?=$country == 'Senegal' ? 'selected' : '' ?>>Senegal</option>
						<option value="Serbia" <?=$country == 'Serbia' ? 'selected' : '' ?>>Serbia</option>
						<option value="Seychelles" <?=$country == 'Seychelles' ? 'selected' : '' ?>>Seychelles</option>
						<option value="Sierra Leone" <?=$country == 'Sierra Leone' ? 'selected' : '' ?>>Sierra Leone</option>
						<option value="Singapore" <?=$country == 'Singapore' ? 'selected' : '' ?>>Singapore</option>
						<option value="Sint Maarten (Dutch part)" <?=$country == 'Sint Maarten (Dutch part)' ? 'selected' : '' ?>>Sint Maarten (Dutch part)</option>
						<option value="Slovakia" <?=$country == 'Slovakia' ? 'selected' : '' ?>>Slovakia</option>
						<option value="Slovenia" <?=$country == 'Slovenia' ? 'selected' : '' ?>>Slovenia</option>
						<option value="Solomon Islands" <?=$country == 'Solomon Islands' ? 'selected' : '' ?>>Solomon Islands</option>
						<option value="Somalia" <?=$country == 'Somalia' ? 'selected' : '' ?>>Somalia</option>
						<option value="South Africa" <?=$country == 'South Africa' ? 'selected' : '' ?>>South Africa</option>
						<option value="South Georgia and the South Sandwich Islands" <?=$country == 'South Georgia and the South Sandwich Islands' ? 'selected' : '' ?>>South Georgia and the South Sandwich Islands</option>
						<option value="South Sudan" <?=$country == 'South Sudan' ? 'selected' : '' ?>>South Sudan</option>
						<option value="Spain" <?=$country == 'Spain' ? 'selected' : '' ?>>Spain</option>
						<option value="Sri Lanka" <?=$country == 'Sri Lanka' ? 'selected' : '' ?>>Sri Lanka</option>
						<option value="Sudan" <?=$country == 'Sudan' ? 'selected' : '' ?>>Sudan</option>
						<option value="Suriname" <?=$country == 'Suriname' ? 'selected' : '' ?>>Suriname</option>
						<option value="Svalbard and Jan Mayen" <?=$country == 'Svalbard and Jan Mayen' ? 'selected' : '' ?>>Svalbard and Jan Mayen</option>
						<option value="Swaziland" <?=$country == 'Swaziland' ? 'selected' : '' ?>>Swaziland</option>
						<option value="Sweden" <?=$country == 'Sweden' ? 'selected' : '' ?>>Sweden</option>
						<option value="Switzerland" <?=$country == 'Switzerland' ? 'selected' : '' ?>>Switzerland</option>
						<option value="Syrian Arab Republic" <?=$country == 'Syrian Arab Republic' ? 'selected' : '' ?>>Syrian Arab Republic</option>
						<option value="Taiwan, Province of China" <?=$country == 'Taiwan, Province of China' ? 'selected' : '' ?>>Taiwan, Province of China</option>
						<option value="Tajikistan" <?=$country == 'Tajikistan' ? 'selected' : '' ?>>Tajikistan</option>
						<option value="Tanzania, United Republic of" <?=$country == 'Tanzania, United Republic of' ? 'selected' : '' ?>>Tanzania, United Republic of</option>
						<option value="Thailand" <?=$country == 'Thailand' ? 'selected' : '' ?>>Thailand</option>
						<option value="Timor-Leste" <?=$country == 'Timor-Leste' ? 'selected' : '' ?>>Timor-Leste</option>
						<option value="Togo" <?=$country == 'Togo' ? 'selected' : '' ?>>Togo</option>
						<option value="Tokelau" <?=$country == 'Tokelau' ? 'selected' : '' ?>>Tokelau</option>
						<option value="Tonga" <?=$country == 'Tonga' ? 'selected' : '' ?>>Tonga</option>
						<option value="Trinidad and Tobago" <?=$country == 'Trinidad and Tobago' ? 'selected' : '' ?>>Trinidad and Tobago</option>
						<option value="Tunisia" <?=$country == 'Tunisia' ? 'selected' : '' ?>>Tunisia</option>
						<option value="Turkey" <?=$country == 'Turkey' ? 'selected' : '' ?>>Turkey</option>
						<option value="Turkmenistan" <?=$country == 'Turkmenistan' ? 'selected' : '' ?>>Turkmenistan</option>
						<option value="Turks and Caicos Islands" <?=$country == 'Turks and Caicos Islands' ? 'selected' : '' ?>>Turks and Caicos Islands</option>
						<option value="Tuvalu" <?=$country == 'Tuvalu' ? 'selected' : '' ?>>Tuvalu</option>
						<option value="Uganda" <?=$country == 'Uganda' ? 'selected' : '' ?>>Uganda</option>
						<option value="Ukraine" <?=$country == 'Ukraine' ? 'selected' : '' ?>>Ukraine</option>
						<option value="United Arab Emirates" <?=$country == 'United Arab Emirates' ? 'selected' : '' ?>>United Arab Emirates</option>
						<option value="United Kingdom" <?=$country == 'United Kingdom' ? 'selected' : '' ?>>United Kingdom</option>
						<option value="United States" <?=$country == 'United States' ? 'selected' : '' ?>>United States</option>
						<option value="United States Minor Outlying Islands" <?=$country == 'United States Minor Outlying Islands' ? 'selected' : '' ?>>United States Minor Outlying Islands</option>
						<option value="Uruguay" <?=$country == 'Uruguay' ? 'selected' : '' ?>>Uruguay</option>
						<option value="Uzbekistan" <?=$country == 'Uzbekistan' ? 'selected' : '' ?>>Uzbekistan</option>
						<option value="Vanuatu" <?=$country == 'Vanuatu' ? 'selected' : '' ?>>Vanuatu</option>
						<option value="Venezuela, Bolivarian Republic of" <?=$country == 'Venezuela, Bolivarian Republic of' ? 'selected' : '' ?>>Venezuela, Bolivarian Republic of</option>
						<option value="Viet Nam" <?=$country == 'Viet Nam' ? 'selected' : '' ?>>Viet Nam</option>
						<option value="Virgin Islands, British" <?=$country == 'Virgin Islands, British' ? 'selected' : '' ?>>Virgin Islands, British</option>
						<option value="Virgin Islands, U.S." <?=$country == 'Virgin Islands, U.S.' ? 'selected' : '' ?>>Virgin Islands, U.S.</option>
						<option value="Wallis and Futuna" <?=$country == 'Wallis and Futuna' ? 'selected' : '' ?>>Wallis and Futuna</option>
						<option value="Western Sahara" <?=$country == 'Western Sahara' ? 'selected' : '' ?>>Western Sahara</option>
						<option value="Yemen" <?=$country == 'Yemen' ? 'selected' : '' ?>>Yemen</option>
						<option value="Zambia" <?=$country == 'Zambia' ? 'selected' : '' ?>>Zambia</option>
						<option value="Zimbabwe" <?=$country == 'Zimbabwe' ? 'selected' : '' ?>>Zimbabwe</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Phone (primary)*');?>
				</td>
				<td>
					<input type="text" name="parent[phone_primary]" id="newclient-phoneprimary" value="" value="" class="req phone"/>
					<span class="duplicate" id="newclient-phoneprimary-duplicate">Warning: Duplicate</span>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Phone (secondary)');?>
				</td>
				<td>
					<input type="text" name="parent[phone_secondary]" id="newclient-phonesecondary" value="" class="phone"/>
					<span class="duplicate" id="newclient-phonesecondary-duplicate"><?php _e('Warning: Duplicate');?></span>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e('Where did you hear about ');?><?=get_bloginfo('name');?>?
				</td>
				<td>
					<select name="wherehear" class="">
						<option value="">Select</option>
						<option value="recommendation"><?php _e('Recommended by a friend');?></option>
						<option value="advertisment"><?php _e('Advertisement (print)');?></option>
						<option value="search_engine"><?php _e('Search Engine (eg Google)');?></option>
						<option value="other"><?php _e('Other');?></option>
					</select>
				</td>
			</tr>
			<tr class="">
				<td>
					<?php _e('Emergency Contact');?><br/>
					(<?php _e('Someone who can be contacted in an emergency if you are not available)');?>
				</td>
				<td>
					<input type="text" name="parent[emergency_contact]" value="" class=""/>
				</td>
			</tr>
			<tr class="">
				<td>
					<?php _e('Emergency Contact Phone');?>
				</td>
				<td>
					<input type="text" name="parent[emergency_contact_phone]" value="" class="phone"/>
				</td>
			</tr>
			<tr class="">
				<td>
					<?php _e('Emergency Contact\'s relationship to dancer(s)');?>
				</td>
				<td>
					<input type="text" name="parent[emergency_contact_relationship]" value=""/>
				</td>
			</tr>
            <tr class="">
				<td>
					<?php _e('Notify User of new account and send registration information:');?>
				</td>
				<td>
					<input type="checkbox" name="sendnotifications" value="1"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input id="db-submit" type="button" id="db-submit" value="<?php _e('Create client');?>" class="button button-primary button-large"/>
				</td>
			</tr>

		</tbody>
	</table>


</form>
