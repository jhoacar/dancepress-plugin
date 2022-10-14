<h1><?php _e('Student Data');?></h1>





<div id="ds-student">
	<form action="" id="editstudent-form" method="post">
		<input type="hidden" name="student_id" value="<?=$data['student'][0]->id?>"/>
		<?php
            if (isset($succmsg) && $succmsg!="") {
                echo "<p>".$succmsg."</p>";
            }
        ?>

		<table cellspacing="0" cellpadding="0" border ="1" class="gridtable">
		<?php foreach ($data['student'] as $v):
            $address = json_decode($v->address_data);
        ?>
			<tr>
				<td><?php _e('First name');?></td>
				<td><input type="text" name="firstname" value="<?=$v->firstname;?>"></td>
			</tr>
			<tr>
				<td><?php _e('Last name');?></td>
				<td><input type="text" name="lastname" value="<?=$v->lastname;?>"></td>
			</tr>
			<tr>
				<td><?php _e('Date of Birth (YYYY-MM-DD)');?></td>
				<td><input type="text" name="birthdate" value="<?=$v->birthdate;?>"></td>
			</tr>
			<tr>
				<td><?php _e('Gender');?></td>
				<td>
					<select name="gender">
						<option <?=$v->gender=='female' ? 'selected' : '';?> value="female">female</option>
						<option <?=$v->gender=='male' ? 'selected' : '';?> value="male">male</option>
					</select>
				</td>
			</tr>
			<?php
            if (is_object($v->meta)):
                foreach ($v->meta as $mk => $mv): ?>
					<?php  if (!array_key_exists($mk, $v) && !is_object($mv)): //get rid of redundant entries in meta due to lazy coding.?>
						<?php  if ($mk == "same_address"):
                            if (is_numeric($mv)):
                                $mv = $mv == 1 ? 'yes' : 'no';
                            endif;
                        ?>
							<tr>
								<td><?php _e('Same Address as parent/guardian');?></td>
								<td><input type="text" name="meta[<?=$mk?>]" value="<?=$mv?>"/></td>
							</tr>
						<?php  else:?>
							<tr>
								<td><?=ucfirst(str_replace('_', ' ', $mk));?></td>
								<td><input type="text" name="meta[<?=$mk?>]" value="<?=$mv;?>"/></td>
							</tr>
						<?php  endif;?>
					<?php  endif;?>
				<?php  endforeach;?>
			<?php  endif; ?>

		<?php  endforeach; ?>

		</table>
	</form>
</div>

<div>
	<table class="gridtable">
		<tr>
			<td><?php _e('Costume');?></td>
			<td><textarea></textarea></td>
		</tr>
		<tr>
			<td><?php _e('Size');?></td>
			<td><textarea></textarea></td>
		</tr>
		<tr>
			<td><?php _e('Bust');?></td>
			<td><textarea></textarea></td>
		</tr>
		<tr>
			<td><?php _e('Waist');?></td>
			<td><textarea></textarea></td>
		</tr>
		<tr>
			<td><?php _e('Hips');?></td>
			<td><textarea></textarea></td>
		</tr>
		<tr>
			<td><?php _e('Inseam');?></td>
			<td><textarea></textarea></td>
		</tr>
		<tr>
			<td><?php _e('Girth');?></td>
			<td><textarea></textarea></td>
		</tr>

	</table>

</div>





<div class="clearfix"></div>
