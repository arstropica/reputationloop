<?php
namespace Controller;

use ReputationLoop\Controller;

class Api extends Controller
{

    public function index()
    {
        // crickets...
    }

    public function get($id = null)
    {
        $output = [];
        $title = 'Unknown Business';
        
        $output['title'] = $title;
        
        // Spoof $id
        $id = 1;
        
        $data = $this->model->get($id);
        if ($data && isset($data['business_info'])) {
            /* $business_info \Model\ReputationLoop\BusinessInfo */
            $business_info = $data['business_info'];
            $output['data'] = $business_info->toArray();
            $output['title'] = $business_info->getBusinessName($title);
        }
        
        echo json_encode($output);
    }
}

?>