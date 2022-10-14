	<div class="printhide">
		<h1><?php _e('Custom List');?></h1>
		<input type="button" value="Print Class Lists" id="print-attendance" class="print-attendance button button-primary button-large"/>
	</div>
			<div id="attendance-printout">
	<?php
    $classId = false;
    foreach ($classDetails as $k => $item):
        if ($item->class_id != $classId):
        $classId = $item->class_id;
    ?>
		<?= $k > 0 ? '</table><div style="page-break-after: always"></div></div>' : '';?>
	<div class="customlist">
		<h2><?=$item->class_name?></h2>

		<ul style="font-weight: bold;">
			<li><?php _e('Classroom: ');?><?=$item->classroom?></li>
			<li><?php _e('Day: ');?><?=$item->class_weekday_name?></li>
			<li><?php _e('Time: ');?><?=$item->class_starttime?>-<?=$item->class_endtime?></li>
			<li><?php _e('Class target ages: ');?><?=$item->class_ages?> years</li>
		</ul>
		<table class="gridtable printable">
			<tr>
				<?php  foreach ($item as $field_name => $fv):
                    if (strpos($field_name, 'class') === false && strpos($field_name, '_id') === false):
                ?>
					<th><?=ucwords(str_replace('_', ' ', $field_name))?></th>
				<?php
                    endif;
                    $firstrow = 0;
                endforeach ;?>
			</tr>
	<?php
        endif;
    ?>

			<tr>
				<?php  foreach ($item as $field_name => $fv):

                    if (strpos($field_name, 'class') === false && strpos($field_name, '_id') === false):
                        if ($field_name == 'student_measurements'):
                ?>
					<td style="padding: 0; border: 0px;">
						<table class="gridtable student-measurements" style="border: 0;">
							<?php if ($field_name == 'student_measurements' && $firstrow == 0): ?>
							<thead>
								<tr>
									<th><?php _e('Bust');?></th>
									<th><?php _e('Waist');?></th>
									<th><?php _e('Hips');?></th>
									<th><?php _e('Inseam');?></th>
									<th><?php _e('Girth');?></th>
									<th><?php _e('Other');?></th>
								</tr>
							</thead>
							<?php
                                $firstrow = 1;
                            endif ?>
							<tbody>
								<tr>
									<td><input type="text" size="4"/></td>
									<td><input type="text" size="4"/></td>
									<td><input type="text" size="4"/></td>
									<td><input type="text" size="4"/></td>
									<td><input type="text" size="4"/></td>
									<td><input type="text" size="4"/></td>
								</tr>
							</tbody>
						</table>
					</td>
				<?php
                        else:
                ?>
						<td><?=$fv?></td>
				<?php
                        endif;
                    endif;
                endforeach ;?>
			</tr>
	<?php
    endforeach;
    ?>
	</table>
