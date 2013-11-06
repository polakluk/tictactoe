<?php

namespace Controllers;

abstract class BaseController {

	protected $f3; // instance of F3 framework main class
	
	protected $db; // instance of the DB class
	
	/*
	 * Constructor in which I connect to the DB 
	 * 
	 */
	function __construct( $f3 ) {

 		$this->f3 = $f3;
        $this->db = new \DB\SQL( $this-> f3->get('db_dns') . $this->f3->get('db_name'), $this->f3->get('db_user'), $this->f3->get('db_pass') );
	}	

	/*
	 * Returns path to currently rendered view file or false in case it requested redirection
	 */
	abstract public function Execute();
}