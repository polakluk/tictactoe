	<div class="container" style="padding: 0 30px 0px 0px;">

		<div class="row">
			<div class="col-lg-6">

				<div class="panel panel-info">
				  <div class="panel-heading">
				    <h3 class="panel-title">Welcome!</h3>
				  </div>
				  <div class="panel-body">
				  	<?php if ($username): ?>
				  		
				  			<div class="well-lg">
							  	<form action="<?php echo $BASE; ?>/home/create_user" method="POST" class="form-horizontal" role="form">
								  	<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Your name</span>
											<input type="text" class="form-control" disabled="true" value="<?php echo $username; ?>" name="username" id="username" />
											<div class="input-group-btn">
												<a href="<?php echo $BASE; ?>/home/reset" class="btn btn-danger" alt="Change">Change name</a>
												<a href="<?php echo $BASE; ?>/showroom" class="btn btn-info" alt="Go">Go!</a>
										  	</div>
					 					</div>
					 				</div>
					 			</form>
					 		</div>
				  		
				  		
				  		<?php else: ?>
				  			<div class="well-lg">
							  	<form action="<?php echo $BASE; ?>/home/create_user" method="POST" class="form-horizontal" role="form">
								  	<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Your name</span>
											<input type="text" class="form-control" name="username" placeholder="Your name ..."  id="username"/>
											<div class="input-group-btn">
												<button class="btn btn-info" type="button" id="go_button">Go!</button>
										  	</div>
					 					</div>
								  	</div>
							  	</form>
						  	</div>
				  		
				  	<?php endif; ?>
				  </div>
				</div>

			</div>
			
			<div class="col-lg-6">

				<div class="panel panel-success">
				  <div class="panel-heading">
				    <h3 class="panel-title">Check out!</h3>
				  </div>
				  <div class="panel-body text-center">
					<div class="btn-group btn-group-justified">
						<a href="<?php echo $BASE; ?>/showroom/spectator" class="btn btn-success btn-lg">See Games</a>
						<a href="<?php echo $BASE; ?>/rules" class="btn btn-danger btn-lg">See Rules</a>
					</div>
				  </div>
				</div>
						
			</div>
		</div>

	</div>