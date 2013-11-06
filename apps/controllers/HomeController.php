<?php

namespace Controllers;

class HomeController extends BaseController {
	
	/*
	 * Returns path to currently rendered view file or false in case it requested redirection
	 */
	public function Execute() {
		$params = $this->f3->get('PARAMS');
		$task = isset( $params['task'] ) ? strtolower( $params['task'] ) : '';
		
		switch( $task ) {
			case 'create_user':
				{
					$this->f3->set( 'SESSION.username', $this->f3->get( 'POST.username' ) );
					$this->f3->reroute( '/' );
					return false;
					break;
				}
			case 'reset' :
				{
					$this->f3->clear( 'SESSION.username' );					
				}
			default:
				{
					$this->f3->set( 'username', $this->f3->get( 'SESSION.username' ) );
					return 'views/home/main.htm';					
				}
		}
	}
}