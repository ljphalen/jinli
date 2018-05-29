<?php
namespace App\Controller;
use Swoole;

class post extends Swoole\Controller
{
    public $is_ajax = true;

    function index()
    {
        return array('json' => 'swoole');
    }
}