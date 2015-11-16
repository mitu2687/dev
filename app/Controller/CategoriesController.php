<?php
class CategoriesController  extends  AppController{

	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('get_categories');
		C::write('debug',0);
$this->RequestHandler->setContent('json');
$this->RequestHandler->respondAs('application/json; charset=UTF-8');
	}

	public function admin_edit($params=null){
		c::write('param',$params);
		$data=$this->Category->find('all');
		$this->set("datas",$data);
		if(isset($this->request->data)){
			$post=$this->request->data;
			if($this->Category->saveAll($post)){
				$this->redirect("/admin/categories/edit/$params");
			}
		}
	}


	public function get_categories($params){
		$this->autoRender=false;
		if($this->RequestHandler->isAjax()){
			$order["order"]=array("Category.master_count"=>"desc");
			$order['fields']=array("name","master_count");
			if($params==1){
				$order['conditions']=array("Category.master_count >="=>"10");
			}else{
				$order['conditions']=array("Category.master_count <="=>"9");
			}
			$ajax=$this->Category->find("all",$order);
			return json_encode($ajax);
		}
	}
}
