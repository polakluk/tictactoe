	<div class="page-header text-center">
	<h1>Tic Tac Toe - Multiplayer <small>created by Lukáš Polák</small></h1>		
	</div>

<?php foreach (($SESSION['msgs']?:array()) as $msg): ?>
	<div class="alert alert-<?php echo $msg['1']; ?> alert-dismissable">
	  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	  <?php echo $msg['0']; ?>
	</div>
<?php endforeach; ?>