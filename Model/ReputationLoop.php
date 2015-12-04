<?php

namespace Model;

use Model\ReputationLoop\BusinessInfo;
use Model\ReputationLoop\Reviews;

/**
 * Model class consumes JSON data and provides iteration functionality
 *
 * @author arstropica
 *        
 */
class ReputationLoop {
	
	/**
	 *
	 * @var BusinessInfo
	 */
	protected $business_info;
	
	/**
	 *
	 * @var Reviews
	 */
	protected $reviews;
	
	/**
	 *
	 * @var string
	 */
	protected $url;
	
	/**
	 *
	 * @var Integer
	 */
	private $id;
	
	/**
	 * Set id variable
	 *
	 * @param Integer $id        	
	 */
	public function init($id) {
		$this->id = $id;
	}
	
	/**
	 * Set API Url
	 *
	 * @param string $url        	
	 */
	public function setUrl($url) {
		if ($url) {
			$this->url = $url;
		}
	}
	
	/**
	 * Fetch API Response
	 *
	 * @param string $url        	
	 */
	public function fetch($url) {
		$data = $this->getJson ( $url );
		
		if ($data) {
			if (isset ( $data ['business_info'] )) {
				$this->business_info = new BusinessInfo ( 
						$data ['business_info'] );
			}
			if (isset ( $data ['reviews'] )) {
				$this->reviews = new Reviews ( $data ['reviews'] );
			}
		}
		return $data;
	}
	
	/**
	 * Get Business Info
	 *
	 * @return BusinessInfo
	 */
	public function getInfo($id = null) {
		$data = [ ];
		if (! $this->id && ! $id) {
			return $data;
		}
		if ($id) {
			$this->init ( $id );
		}
		$url = $this->buildUrl ( $this->id );
		$this->setUrl ( $url );
		$this->fetch ( $url );
		$data = [ ];
		if (isset ( $this->business_info ) && $this->business_info instanceof BusinessInfo) {
			$data = $this->business_info;
		}
		return $data;
	}
	
	/**
	 * Get Business Reviews
	 *
	 * @param number $threshold        	
	 *
	 * @return Reviews
	 */
	public function getReviews($threshold = 0) {
		$data = [ ];
		if (! $this->id)
			return $data;
		$url = $this->buildUrl ( $this->id, $threshold );
		
		$this->setUrl ( $url );
		$this->fetch ( $url );
		if (isset ( $this->reviews ) && $this->reviews instanceof Reviews) {
			$data ['reviews'] = $this->reviews;
		}
		if (isset ( $this->business_info ) && $this->business_info instanceof BusinessInfo) {
			$data ['count'] = $this->business_info->getTotalRating ( 0, 
					[ 
							'total_no_of_reviews' 
					] );
		}
		return $data;
	}
	
	/**
	 * Pull JSON from API Endpoint
	 *
	 * @param string $url        	
	 * @param boolean $toArray        	
	 */
	private function getJson($url, $toArray = true) {
		$output = false;
		$ch = curl_init ();
		
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 5 );
		$json = curl_exec ( $ch );
		
		if (curl_errno ( $ch ) === 0) {
			if ($json !== false) {
				
				$data = @json_decode ( $json, $toArray );
				
				if (json_last_error () === JSON_ERROR_NONE) {
					$output = $data;
				}
			}
		}
		
		curl_close ( $ch );
		
		return $output;
	}
	
	/**
	 * Build URL string
	 *
	 * @param integer $id        	
	 * @param number $threshold        	
	 *
	 * @return string
	 */
	private function buildUrl($id, $threshold = 0) {
		$apiKey = $this->getApiKey ( $id );
		return "http://test.localfeedbackloop.com/api?apiKey={$apiKey}&noOfReviews=-1&&offset=0&threshold={$threshold}";
	}
	
	/**
	 * Get API Key from ID
	 *
	 * @param integer $id        	
	 *
	 * @return string
	 */
	private function getApiKey($id) {
		// normally get API Key for ID from secure source
		return "61067f81f8cf7e4a1f673cd230216112";
	}
}

