	<div class="page-header text-center">
	<h1>Game <small><?php echo $game_username; ?>
		
		<?php if ($spectator): ?>
			 
			<?php else: ?>
				<?php if ($player_team == 2): ?>
					
						<span class="label label-danger">Red</span>
					
					<?php else: ?>
						<span class="label label-primary">Blue</span>
					
				<?php endif; ?>
			
		<?php endif; ?>
		</small></h1>
	</div>

	<div class="container" style="padding: 0 30px 0px 0;">

		<div class="row">
				<div class="col-lg-6">
					<?php echo Base::instance()->raw($desk); ?>
					<?php echo $this->render('views/game/msgs.htm',$this->mime,get_defined_vars()); ?>
				</div>
				
				<div class="col-lg-4 col-lg-offset-2">
					<?php echo $this->render('views/game/right_col.htm',$this->mime,get_defined_vars()); ?>
				</div>
		</div>

	</div>