<?php
namespace Controller;

use ReputationLoop\Controller;

class Main extends Controller
{

    public function index()
    {
        $this->render("index");
    }

    public function test($title)
    {
        $this->render("test", array(
            "title" => $title,
            "name" => "Test"
        ));
    }

    public function error404()
    {
        $this->render('error', array(), 404);
    }
}
