	<div class="page-header text-center">
	<h1>Game <small><?php echo $game_username; ?></small></h1>
	</div>

	<div class="container" style="padding: 0 30px 0px 0;">

		<div class="row">
				<div class="col-lg-6">
					<?php echo Base::instance()->raw($desk); ?>
				</div>
				
				<div class="col-lg-4 col-lg-offset-2">
					<?php echo $this->render('views/game/right_col.htm',$this->mime,get_defined_vars()); ?>
				</div>
		</div>

	</div>
	<a href="<?php echo $BASE; ?>/game/leave/<?php echo $game_id; ?>" class="btn btn-info pull-right">Leave the Game</a>	