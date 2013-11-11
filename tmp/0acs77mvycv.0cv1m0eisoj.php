<div class="panel panel-success">
	<div class="panel-heading">
    	<h3 class="panel-title">Game Stats</h3>
  	</div>
  	<div class="panel-body">
  		<table class="table table-striped">
  			<tr id="trTurn">
  				<td style="width : 50px;">Turn</td>
  				<td data-turn="<?php echo $game_turn; ?>"><?php echo $game_turn; ?></td>
  			</tr>
  			<tr id="trTeam">
  				<td style="width : 50px;">Team</td>
  				<td>
  					<?php if ($game_team == '2'): ?>
  						
		  					<span class="label label-danger">Red</span>
  						
  						<?php else: ?>
		  					<span class="label label-primary">Blue</span>
  						
  					<?php endif; ?>
  				</td>
  			</tr>
  		</table>
	</div>
</div>

<?php if ($spectator == TRUE): ?>
	 
	<?php else: ?>
		<div class="panel panel-danger poll">
			<div class="panel-heading">
		    	<h3 class="panel-title">Poll</h3>
		  	</div>
		  	<div class="panel-body">
		  		<?php echo $this->render('views/game/poll/idle.htm',$this->mime,get_defined_vars()); ?>
			</div>
		</div>
		


<?php endif; ?>


<div class="panel panel-success">
	<div class="panel-heading">
    	<h3 class="panel-title">Teams</h3>
  	</div>
  	<div class="panel-body">
  		<table class="table table-striped table-teams">
  			<tr data-team="2">
  				<td style="width 60px;">Red: </td>
  				<td><?php echo $members['2']; ?></td>
  			</tr>
  			<tr data-team="1">
  				<td style="width 60px;">Blue: </td>
  				<td><?php echo $members['1']; ?></td>
  			</tr>
  		</table>
	</div>
</div>
<a href="<?php echo $BASE; ?>/game/leave/<?php echo $game_id; ?>" class="btn btn-info pull-right">Leave the Game</a>	