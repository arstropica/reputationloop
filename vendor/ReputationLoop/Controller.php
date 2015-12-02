<?php
namespace ReputationLoop;

use Slim;

class Controller extends Slim\Slim
{

    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var object
     */
    protected $model;

    public function __construct()
    {
        $settings = require ("../settings.php");
        if (isset($settings['data'])) {
            $this->data = $settings['data'];
        }
        if (isset($settings['model'])) {
            $this->model = $settings['model'];
        }
        parent::__construct($settings);
    }

    public function render($template, $data = array(), $status = null)
    {
        // Allowed Extensions
        $allowed = [
            'twig',
            'php'
        ];
        
        if (! preg_match('/' . preg_quote(implode('|', array_map(function ($ext) {
            return "." . $ext;
        }, $allowed))) . '/i', $template)) {
            foreach ($allowed as $ext) {
                if (file_exists('../View/' . $template . '.' . $ext)) {
                    $template = $template . '.' . $ext;
                    break;
                }
            }
        }
        parent::render($template, $data, $status);
    }
}
