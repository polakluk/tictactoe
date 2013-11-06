<?php

class Dispatcher {
	
	protected $f3; // instance of F3 framework main class
	
	protected $db; // instance of the DB class
	
	/*
	 * Constructor in which I connect to the DB 
	 * 
	 */
	function __construct() {

 		$this->f3 = Base::instance();
        $this->db = new DB\SQL( $this-> f3->get('db_dns') . $this->f3->get('db_name'), $this->f3->get('db_user'), $this->f3->get('db_pass') );
	}
	
	
	/*
	 * This method renders home page
	 */
	public function display_home() {
		$controller = new \Controllers\HomeController( $this->f3 );
		
		$this->f3->set( 'page_body', $controller->Execute() );
		echo \Template::instance()->render( 'tmpl/layout.htm' );
	}

	/*
	 * This method renders a certain view
	 */
	public function display_view() {
		$name = '\\Controllers\\'.ucwords( $this->f3->get('PARAMS.view') ).'Controller';
		
		$c = new $name( $this->f3 );
		
		$render = $c->Execute();
		if( $render === false ) { // we carried out the task and the controller is requesting rediretion
			return;
		} else { // nah, we'll show everything right now
			$this->f3->set( 'page_body', $render );
			echo \Template::instance()->render( 'tmpl/layout.htm' );			
		}
	}
}