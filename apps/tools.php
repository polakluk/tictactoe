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
		
		last = ''; // so I dont have to bother with commas
}