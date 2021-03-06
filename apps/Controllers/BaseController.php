<?php

namespace Controllers;

abstract class BaseController {

	protected $f3; // instance of F3 framework main class
	
	protected $db; // instance of the DB class
	
	public $output_type = \Tools::OUTPUT_TEMPLATE; // which template preprocessor should be used to render the current view ('template' / 'view')
	public $output_format = \Tools::OUTPUT_FORMAT_NORMAL; // type of the output
		
	
	public $view = 'main'; // which view is being displayed ( 'main' is default )

	/*
	 * Constructor in which I connect to the DB 
	 * 
	 */
	function __construct( $f3, $db ) {

 		$this->f3 = $f3;
        $this->db = $db;
	}	

	/*
	 * Returns path to currently rendered view file or false in case it requested redirection
	 */
	abstract public function Execute();
}