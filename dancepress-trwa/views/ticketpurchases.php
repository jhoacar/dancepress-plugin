<?php include_once('parent-portal-menu.php'); ?>
<?php
    $option = new \DancePressTRWA\Models\Option();
    $taxPercent = $option->getTaxPercent();
?>
<h3><?php _e('Ticket Purchases');?></h3>
<?php
echo '<script type="text/javascript" src="'.site_url(). '/wp-includes/js/jquery/jquery.js?ver=1.10.2"></script>';
echo '<script type="text/javascript" src="'.site_url(). '/wp-content/plugins/dancepress-trwa/js/jquery.validate.js"></script>';

?>
		<table class="dbl-tbl">
			<tbody>
				<tr class="yyy removeable">
					<th colspan="7"><h4>
					<?php
                    if (!empty($parent)) {
                        echo $parent->firstname .' '. $parent->lastname.': Tiket Purchase History';
                    } else {
                        echo 'Parent Ticket Purchase History';
                    }
                    ?>
					</h4>
					</th>
				</tr>
				<tr>
					<th><?php _e('Event');?></th>
					<th><?php _e('Ticket Price');?></th>
					<th><?php _e('Quantity');?></th>
					<th><?php _e('Sub-Total');?></th>
					<th><?php _e('Tax (');?><?=$taxPercent?>%)</th>
					<th><?php _e('Total');?></th>
					<th><?php _e('Date Purchased');?></th>
				</tr>
				<?php if (!empty($ticket_purchases)) {?>
					<?php  foreach ($ticket_purchases as $k => $ticket_purchase): ?>
						<tr>
							<td><?=ucfirst($ticket_purchase->event->name);?></td>

							<td><?=$ticket_purchase->event->ticket_price?></td>

							<td><?=$ticket_purchase->quantity?></td>

							<td><?=$ticket_purchase->sub_total?></td>

							<td><?=$ticket_purchase->tax?></td>

							<td><?=$ticket_purchase->total?></td>

							<td><?=$ticket_purchase->date_purchased?></td>

						</tr>
					<?php endforeach;?>
				<?php }?>
			</tbody>
		</table>

<script type="text/javascript">
  // When the browser is ready...
 jQuery( document ).ready(function() {



  });
</script>
