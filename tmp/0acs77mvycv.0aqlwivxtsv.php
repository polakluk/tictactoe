<div class="panel panel-success">
	<div class="panel-heading">
    	<h3 class="panel-title">Game #<?php echo $game_id; ?></h3>
  	</div>
	<table class="table table-bordered text-center table-desk">
		<?php foreach (($game_table?:array()) as $row): ?>
			<tr class="game">
				<?php foreach (($row?:array()) as $col): ?>
					<?php switch ($col): ?><?php case '1': ?>
							<td>
								<img src="<?php echo $BASE; ?>/media/images/blue.png" alt="blue" />
							</td>
						<?php if (TRUE) break; ?><?php case '0': ?>
							<td>
								<img src="<?php echo $BASE; ?>/media/images/red.png" alt="red" />
							</td>
						<?php if (TRUE) break; ?><?php default: ?>
							<td>
								&nbsp;
							</td>
						<?php break; ?><?php endswitch; ?>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
