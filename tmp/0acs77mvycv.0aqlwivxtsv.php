<div class="panel panel-success">
	<div class="panel-heading">
    	<h3 class="panel-title">Game #<?php echo $game_id; ?></h3>
  	</div>
	<table class="table table-bordered text-center table-desk" data-enabled="1" data-game="<?php echo $game_id; ?>">
		<?php foreach (($game_table?:array()) as $num_row=>$row): ?>
			<tr class="game">
				<?php foreach (($row?:array()) as $num_col=>$col): ?>
					<?php switch ($col): ?><?php case '1': ?>
							<td data-col="<?php echo $num_col; ?>" data-row="<?php echo $num_row; ?>" >
								<img src="<?php echo $BASE; ?>/media/images/blue.png" alt="blue" />
							</td>
						<?php if (TRUE) break; ?><?php case '0': ?>
							<td data-col="<?php echo $num_col; ?>" data-row="<?php echo $num_row; ?>">
								<img src="<?php echo $BASE; ?>/media/images/red.png" alt="red" />
							</td>
						<?php if (TRUE) break; ?><?php default: ?>
							<td class="empty" data-col="<?php echo $num_col; ?>" data-row="<?php echo $num_row; ?>">
								&nbsp;
							</td>
						<?php break; ?><?php endswitch; ?>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
