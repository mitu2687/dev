<?php
class TagsController extends AppController{


	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('get_tag');
		C::write('debug',0);
$this->RequestHandler->setContent('json');
$this->RequestHandler->respondAs('application/json; charset=UTF-8');
	}
	public function admin_edit($params=null){
		c::write('param',$params);
		$data=$this->Tag->find('all',array("order"=>array('id'=>'desc')));
		$this->set("datas",$data);
		if(isset($this->request->data)){
			$post=$this->request->data;
			foreach($post as $val){
				$this->Tag->create();
				$this->Tag->save($val);
				$this->redirect("/admin/tags/edit/$params");
			}
		}
	}

	public function admin_delete($params){
		$this->Tag->delete($params);
		$this->redirect("/admin/tags/edit/$params");
	}

	public function get_tag(){
		if($this->RequestHandler->isAjax()){
			$this->Tag->unbindModel(
					array(
						"hasAndBelongsToMany"=>array("Video","Master")
					     ),false
					);
			$ajax=$this->Tag->find("all",array(
						"order"=>"rand()",
						"fields"=>array("Tag.name"),
						)
					);
			$this->autoRender=false;
			return  json_encode($ajax);
		}

	}

}
