<html lang="en">
	<head>
		<?php echo $this->render('tmpl/head.htm',$this->mime,get_defined_vars()); ?>
	</head>
	
	<body>
		<?php echo $this->render('tmpl/header.htm',$this->mime,get_defined_vars()); ?>
		<?php if ($page_body) echo $this->render($page_body,$this->mime,get_defined_vars()); ?>
		<?php echo $this->render('tmpl/footer.htm',$this->mime,get_defined_vars()); ?>		
	</body>
</html>