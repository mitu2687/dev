<?php
App::uses('AppController', 'Controller');
class VideosTagsController extends AppController{

	public function admin_remove(){
		$this->autoRender=false;
		if($this->RequestHandler->isAjax()){
			$ajax= $this->request->data;
			$tagId=ClassRegistry::init('Tag')->findByName($ajax['request']);
			$delId=$this->VideosTag->findByTagIdAndVideoId($tagId['Tag']['id'],$ajax['id']);
			if($this->VideosTag->delete($delId['VideosTag']['id'])){
				print "タグを解除しました。";
			}
		}
	}

}
