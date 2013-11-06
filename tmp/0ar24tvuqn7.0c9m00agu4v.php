<div class="lg-col-1 container">
	<div class="page-header text-center">
	<h1>Current games<small><?php echo $game_username; ?></small></h1>
	</div>

	<div class="container" style="padding: 0 30px 0px 0;">

		<div class="row">

			<?php foreach (($desks?:array()) as $desk): ?>
				<div class="col-lg-6">
					<?php echo Base::instance()->raw($desk); ?>
				</div>    
			<?php endforeach; ?>

		</div>

	</div>	
	<a href="<?php echo $BASE; ?>" class="btn btn-info pull-right">Back to home</a>	
</div>