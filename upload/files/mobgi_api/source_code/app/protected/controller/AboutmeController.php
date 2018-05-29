<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$AppCt=Doo::loadController("AppDooController");
class AboutmeController extends AppDooController {
    public function __construct(){
        parent::__construct();
    }
    
    public function index() {
        $this->myrender("aboutme/index", $this->data);
    }
}
