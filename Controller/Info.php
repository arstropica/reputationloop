<?php

namespace Controller;

use ReputationLoop\Controller;

/**
 * BusinessInfo Controller
 *
 * @author nci
 *        
 */
class Info extends Controller {
	/**
	 * Index Actopm
	 *
	 * @return void
	 */
	public function index() {
		// crickets...
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Slim\Slim::get()
	 */
	public function get($id = null) {
		$output = [ ];
		$title = 'Unknown Business';
		
		$output ['title'] = $title;
		
		// Spoof $id
		$id = 1;
		
		$model = $this->model;
		$model->init ( $id );
		
		/* @var $business_info \Model\ReputationLoop\BusinessInfo */
		$business_info = $model->getInfo ();
		if ($business_info) {
			$output ['data'] = $business_info->toArray ();
			$output ['title'] = $business_info->getBusinessName ( $title );
		}
		
		echo json_encode ( $output );
	}
}
