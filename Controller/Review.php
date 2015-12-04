<?php

namespace Controller;

use ReputationLoop\Controller;
use Slim\Slim;

/**
 * Review Controller
 *
 * @author nci
 *        
 */
class Review extends Controller {
	/**
	 * Index Action
	 * 
	 * @param number $id        	
	 *
	 * @return void
	 */
	public function index($id = null) {
		$output = [ ];
		$reviews = false;
		$count = 0;
		
		// Spoof $id
		$id = 1;
		
		$model = $this->model;
		$model->init ( $id );
		
		$data = $model->getReviews ();
		if ($data) {
			if (isset ( $data ['reviews'] )) {
				/* @var $reviews \Model\ReputationLoop\Reviews */
				$reviews = $data ['reviews'];
			}
			if (isset ( $data ['count'] )) {
				$count = $data ['count'];
			}
		}
		
		if ($reviews) {
			$output ['data'] = $reviews->toArray ();
			$output ['count'] = $count;
		}
		
		echo json_encode ( $output );
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
		
		$data = $this->model->get ( $id );
		if ($data && isset ( $data ['business_info'] )) {
			/* $business_info \Model\ReputationLoop\BusinessInfo */
			$business_info = $data ['business_info'];
			$output ['data'] = $business_info->toArray ();
			$output ['title'] = $business_info->getBusinessName ( $title );
		}
		
		echo json_encode ( $output );
	}
	
	/**
	 * Get Post
	 *
	 * @return boolean|array
	 */
	protected function getPost() {
		$post = false;
		$app = Slim::getInstance ();
		$request = $app->request ();
		$body = $request->getBody ();
		if ($body) {
			$post = @json_decode ( $body, true );
		}
		return $post;
	}
}

