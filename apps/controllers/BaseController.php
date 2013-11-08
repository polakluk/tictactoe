<?php

namespace Controllers;

abstract class BaseController {

	protected $f3; // instance of F3 framework main class
	
	protected $db; // instance of the DB class
	
	public $output_type = 'template'; // which template preprocessor should be used to render the current view ('template' / 'view')
	
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