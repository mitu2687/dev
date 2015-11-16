<?php
class SitemapController extends AppController {

	public $uses=array("Master");
	public $helpers = array('Time');

	public function index(){
		$this->layout="xml/default";
		$master=$this->Master->find("all");
		$this->set(compact("master"));
		$this->RequestHandler->respondAs('xml');

	}

}
