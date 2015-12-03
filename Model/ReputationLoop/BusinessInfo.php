<?php

namespace Model\ReputationLoop;

/**
 * Overloaded method for accessing Business Info data
 *
 * @author arstropica
 *        
 */
class BusinessInfo {
	
	/**
	 *
	 * @var array
	 */
	protected $data;
	
	/**
	 *
	 * @var array
	 */
	protected $ratings;
	
	/**
	 * Constructor
	 *
	 * @param array $data        	
	 *
	 * @return BusinessInfo
	 */
	public function __construct($data = [], $ratings = []) {
		$this->data = $data;
		$this->ratings = $ratings;
	}
	
	/**
	 * Magic Method for Method Calls
	 *
	 * @param string $name        	
	 * @param array $args        	
	 *
	 * @return mixed
	 */
	public function __call($name, $args = []) {
		$result = null;
		$key = false;
		
		if (strpos ( $name, 'get' ) === 0) {
			$key = $this->fromUCC ( substr ( $name, 3 ) );
		}
		
		if ($key && array_key_exists ( $key, $this->data )) {
			$_data = $this->data [$key];
			if (isset ( $args [1] ) && is_array ( $args [1] ) && count ( 
					$args [1] ) > 0) {
				for($i = 0; $i < count ( $args [1] ); $i ++) {
					if (array_key_exists ( $args [1] [$i], $_data )) {
						$_data = $_data [$args [1] [$i]];
					} else {
						$_data = [ ];
					}
				}
			}
			$result = $_data;
		}
		
		if (! $result && isset ( $args [0] )) {
			$result = $args [0];
		}
		
		return $result;
	}
	
	/**
	 * Return Array Version of data
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->data;
	}
	
	/**
	 * Get Ratings
	 */
	public function getRatings() {
		return $this->ratings;
	}
	
	/**
	 * Convert underscore to CamelCase
	 *
	 * @param string $string        	
	 */
	private function toUCC($string) {
		return preg_replace ( '/(?:^|_)(.?)/e', "strtoupper('$1')", $string );
	}
	
	/**
	 * Convert CamelCase to under_score
	 *
	 * @param string $string        	
	 */
	private function fromUCC($string) {
		return strtolower ( 
				preg_replace ( '/([a-z])([A-Z])/', '$1_$2', $string ) );
	}
}
