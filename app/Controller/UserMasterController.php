<?php
class UserMasterController extends AppController {

	public function myroom(){
		$name = $this->Cookie->read('Auth.User.username');
		$this->User = ClassRegistry::init('User');
		$condition= array('conditions'=>array(
					'User.username'=>$name,
					),
				'recursive' =>2,
				);
		$datas = $this->UserMaster->find('all',$condition);
		//画像ファイル数を取得
		$t=$this->GetUrlAbout->getImgFile($datas);
		$t=json_encode($t);
		//カテゴリーを取得
		$category=ClassRegistry::init("Category");
		$order["order"]=array("Category.master_count"=>"desc");
		$order['conditions']=array("Category.master_count >="=>"10");
		$cate=$category->find("all",$order);
		$order['conditions']=array("Category.master_count <="=>"9");
		$cate2=$category->find("all",$order);
		$this->set(compact('datas','cate','cate2','name','t'));
	}

	public function add(){
		$this->autoRender = false;
		$id = $this->request->query['id'];
		$this->User = ClassRegistry::init('User');
		$user = $this->User->findByUsername($this->Cookie->read('Auth.User.username'));
		$save =array('UserMaster'=>array(
					'user_id'=>$user['User']['id'],
					'master_id'=>$id,
					));
		$this->UserMaster->save($save);
		$this->Session->setFlash('動画をマイルームに追加しました');
		$this->redirect($this->referer());
	}

	public function delete(){
		$this->autoRender = false;
		$id = $this->request->query['id'];
		$this->UserMaster->delete(array('id'=>$id));
		$this->Session->setFlash('動画をマイルームに追加しました');
		$this->redirect($this->referer());
	}

}
