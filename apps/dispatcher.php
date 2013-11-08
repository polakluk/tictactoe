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
        $this->db = new DB\SQL( $this->f3->get('db_dns') . $this->f3->get('db_name'), $this->f3->get('db_user'), $this->f3->get('db_pass') );
		
		if( !$this->f3->exists( 'SESSION.msgs' ) ) {
			$this->f3->set( 'SESSION.msgs', array() );
		}
	}
	
	/*
	 * This method renders home page
	 */
	public function DisplayHome() {
		$this->display( 'Home' );
	}

	/*
	 * This method renders a certain view
	 */
	public function DisplayView() {
		$this->display( $this->f3->get('PARAMS.view') );
	}

	/*
	 * Handles displaying content on the site
	 */	
	private function display( $view ) {
		$name = '\\Controllers\\'.ucwords( $view ).'Controller';
		
		$c = new $name( $this->f3, $this->db );
		
		$render = $c->Execute();
		if( $render === false ) { // we carried out the task and the controller is requesting rediretion
			return;
		} else { // nah, we'll show everything right now
			$this->f3->set( 'page_body', $render );
			if( $c->output_type == 'template' ) {
				echo \Template::instance()->render( $this->f3->get('template') );				
			} else { // View
				echo \View::instance()->render( $this->f3->get('template') );				
			}
			$this->f3->set( 'SESSION.msgs', array() );
		}		
	}
}