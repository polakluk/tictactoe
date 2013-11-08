<?php

namespace Models;

abstract class BaseModel{
	
	protected $f3; // instance of the base F3 class
	protected $db; // instance of the db class
	protected $state = array(); // inner state of the model
	
	/*
	 * Constructor takes all the important objects for the model
	 */
	function __construct( $f3, & $db ) {
		$this->f3 = $f3;
		$this->db = &$db;
		
		$this->ResetModel();
	}
	/*
	 * Resets inner state of the model
	 */
	abstract public function ResetModel();
	
	/*
	 * Sets inner values for the model
	 */
	public function SetState( $new_state ) {
		
		foreach( $new_state as $key => $val ) {
			$this->state[$key] = $val;
		}
	}
	
	/*
	 * Gets a simple item from this model based on its ID
	 */
	abstract public function GetItem( $id );

	/*
	 * Gets a list of items based on the conditions
	 */
	abstract public function GetList();
}