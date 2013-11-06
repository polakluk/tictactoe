<?php

namespace Controllers;

class RulesController extends BaseController {
	
	/*
	 * Returns path to currently rendered view file or false in case it requested redirection
	 */
	public function Execute() {
		return 'views/rules/main.htm';
	}
}
