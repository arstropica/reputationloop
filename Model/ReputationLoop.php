<?php
namespace Model;

use Model\ReputationLoop\BusinessInfo;

/**
 * Model class consumes JSON data and provides iteration functionality
 *
 * @author arstropica
 *        
 */
class ReputationLoop
{

    /**
     *
     * @var BusinessInfo
     */
    protected $business_info;

    protected $reviews;

    /**
     *
     * @var string
     */
    protected $url;

    public function __construct()
    {}

    public function setUrl($url)
    {
        if ($url) {
            $this->url = $url;
        }
    }

    public function fetch($url)
    {
        $data = $this->getJson($url);
        
        if ($data) {
            if (isset($data['business_info'])) {
                $this->business_info = new BusinessInfo($data['business_info']);
            }
            if (isset($data['reviews'])) {
                $this->reviews = new BusinessInfo($data['reviews']);
            }
        }
        return $data;
    }

    /**
     * Get Business Info
     *
     * @param $id string            
     * @param $fields array            
     *
     * @return BusinessInfo
     */
    public function get($id, $fields = ['business_info', 'reviews'])
    {
        $url = $this->buildUrl($id);
        $this->setUrl($url);
        $this->fetch($url);
        $data = [];
        if ($fields) {
            foreach ($fields as $field) {
                if (isset($this->{$field})) {
                    $data[$field] = $this->{$field};
                }
            }
        }
        return $data;
    }

    public function getReviews($limit = 10, $filter = [], $offset = 0, $threshold = 0)
    {}

    private function getJson($url, $toArray = true)
    {
        $output = false;
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $json = curl_exec($ch);
        
        if (curl_errno($ch) === 0) {
            if ($json !== false) {
                
                $data = @json_decode($json, $toArray);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    $output = $data;
                }
            }
        }
        
        curl_close($ch);
        
        return $output;
    }

    private function buildUrl($id, $limit = 10, $filter = [], $offset = 0, $threshold = 0)
    {
        $sources = [
            'internal',
            'yelp',
            'google'
        ];
        $include = [];
        foreach ($sources as $source) {
            if (! $filter || in_array($source, $filter)) {
                $include[$source] = 1;
            } else {
                $include[$source] = 0;
            }
        }
        $apiKey = $this->getApiKey($id);
        return "http://test.localfeedbackloop.com/api?apiKey={$apiKey}&noOfReviews={$limit}&" . http_build_query($include) . "&offset={$offset}&threshold={$threshold}";
    }

    private function getApiKey($id)
    {
        // normally get API Key for ID from secure source
        return "61067f81f8cf7e4a1f673cd230216112";
    }
}

