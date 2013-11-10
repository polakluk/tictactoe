<div class="panel panel-success">
	<div class="panel-heading">
    	<h3 class="panel-title">Game #<?php echo $game_id; ?></h3>
  	</div>
  	<div class="panel-body">
  		<h3 id="<?php echo $game_id; ?>">
  			Turn <?php echo $game_turn; ?>
	  		<?php if ($game_team == 'red'): ?>
	  			
		  			<span class="label label-danger">red</span>
	  			
	  			<?php else: ?>
		  			<span class="label label-primary">blue</span>
	  			
	  		<?php endif; ?>
  		</h3>  			
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
		
			<tr>
				<td colspan="3">
			  		<a href="<?php echo $BASE; ?>/game/join/<?php echo $game_id; ?>" class="btn btn-success pull-right">
						<?php if ($join_game): ?>
							Join game
							<?php else: ?>Spectate game
						<?php endif; ?>
			  		</a>				
				</td>
			</tr>
	</table>
</div>
