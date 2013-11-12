<?php

abstract class Tools{
	
	const
		// output preprocessors
		OUTPUT_TEMPLATE = 0,
		OUTPUT_VIEW = 1,
	
		// output formats
		OUTPUT_FORMAT_NORMAL = 0, // the whole page
		OUTPUT_FORMAT_RAW = 1, // return return result from controller
		OUTPUT_FORMAT_VIEW = 2, // display only content of the current view

		// move states
		MOVE_STATE_NONE = 0, // the move does not exist
		MOVE_STATE_DONE = 1, // the move already exist and it's decided
		MOVE_STATE_TMP = 2, // the move exists but it's not final yet
		
		// teams (written)
		TEAM_RED = 'red',
		TEAM_BLUE = 'blue',
		TEAN_SPEC = 'spectator',
		
		// teams (DB)
		TEAM_BLUE_SQL = 1,
		TEAM_RED_SQL = 2,
		
		// general constants
		CONNECT_FIELDS = 4,
		DESK_SIZE = 5,
		
		last = ''; // so I dont have to bother with commas

		/*
		 * Enqueues the message to queue of all messages on the site
		 */
		static public function EnqueueMessage( $msg, $type = 'info' ) {
			Base::instance()->push( 'SESSION.msgs', array( $msg, $type) );
		}
}