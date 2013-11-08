<?php if ($whole_page): ?>
	
<html lang="en">
	<head>
		<?php echo $this->render('tmpl/head.htm',$this->mime,get_defined_vars()); ?>
	</head>
	
	<body>
		<div class="lg-col-1 container container-<?php echo $controller; ?> container-<?php echo $controller; ?>-<?php echo $view; ?>">
			<?php echo $this->render('tmpl/header.htm',$this->mime,get_defined_vars()); ?>
	
<?php endif; ?>
			<?php if ($page_body) echo $this->render($page_body,$this->mime,get_defined_vars()); ?>

<?php if ($whole_page): ?>

			<?php echo $this->render('tmpl/footer.htm',$this->mime,get_defined_vars()); ?>		
		</div>
	</body>
</html>
<?php endif; ?>