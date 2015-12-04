<?php

namespace Controller;

use ReputationLoop\Controller;

/**
 * Main Controller
 *
 * @author nci
 *        
 */
class Main extends Controller {
	/**
	 * Index Action
	 *
	 * @return void
	 */
	public function index() {
		$title = 'Unknown Business';
		
		// Default $id
		$id = 1;
		
		$model = $this->model;
		$model->init ( $id );
		
		/* $business_info \Model\ReputationLoop\BusinessInfo */
		$business_info = $model->getInfo ();
		if ($business_info) {
			$title = $business_info->getBusinessName ( $title );
		}
		
		$this->render ( "index", 
				array (
						"title" => $title,
						"description" => $title 
				) );
	}
	
	/**
	 * Error Action
	 *
	 * @return void
	 */
	public function error404() {
		$this->render ( 'error', array (), 404 );
	}
}
