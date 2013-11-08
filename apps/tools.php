<?php

abstract class Tools{
	
	const
		// output preprocessors
		OUTPUT_TEMPLATE = 0,
		OUTPUT_VIEW = 1,
	
		// output formats
		OUTPUT_FORMAT_NORMAL = 0, // the whole page
		OUTPUT_FORMAT_RAW = 1, // return return result from controller
		OUTPUT_FORMAT_VIEW = 2; // display only content of the current view
}