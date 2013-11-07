<?php

namespace Models;

abstract class BaseModel{
	
	protected $f3;
	protected $db;
	
	/*
	 * Constructor takes all the important objects for the model
	 */
	function __construct( $f3, $db ) {
		$this->f3 = $f3;
		$this->db = $db;
	}
	
	/*
	 * Gets a simple item from this model based on its ID
	 */
	abstract public function GetItem( $id );

	/*
	 * Gets a list of items based on the conditions
	 */
	abstract public function GetList( $conditions = '' );
}