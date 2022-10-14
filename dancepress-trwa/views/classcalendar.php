<?php include_once('parent-portal-menu.php'); ?>
<h1><?php _e('Class Schedule');?></h1>
<?php if ($is_full_schedule) :?>
	<h3>Complete School Schedule</h3>
<?php else:?>
	<h3>Student Class Schedule</h3>
<?php endif;?>
<?php if (empty($student->is_company_student) || $student->schedule_available || $is_full_schedule) : ?>
	<?php  foreach ($classes as $c): ?>
	<div class="ds-class">
		<strong><?php _e('Course name:');?></strong> <?=$c->title?> (Ages <?=$c->ages?>)<br/>
		<strong><?php _e('Days and Times:');?></strong>
		<ul class="ds-classlist">
			<?php foreach ($c->weekday_name as $day):?>
				<li><?=$day . ', ' . date("g:i a", strtotime($c->start))?> - <?=date("g:i a", strtotime($c->end))?></li>
			<?php endforeach; ?>
		</ul>
		<strong><?php _e('Course runs:');?></strong> <?=date("Y-m-d", strtotime($c->start))?> <?php _e('to');?> <?=date("Y-m-d", strtotime($c->end))?><br/>
		<strong><?php _e('Classroom:');?></strong> <?=$c->classroom?><br/>
	</div>
	<?php  endforeach ;?>
<?php else : ?>
	<div>
		<?php _e('This student\'s class schedule is currently not available.');?>'
	</div>
<?php endif; ?>
