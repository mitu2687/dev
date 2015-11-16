<?php
class UsersController extends AppController {
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('login','add','logout');
	}
	public function index(){

	}
	public function login(){
		if(empty($this->data)){
			if($this->Cookie->check("Auth")){
				$auth=$this->Cookie->read("Auth");
				if($this->Auth->login($auth)){
					return $this->redirect(array(
								'controller'=>$this->Auth->loginRedirect['controller'],
								'action'=>$this->Auth->loginRedirect['action']
								));
				}
			}
		}
		if(!empty($this->data)){
			if ($this->request->is('post')) {
				if($this->Auth->login()) {
					$cookie=h($this->request->data);
					$this->Cookie->write("Auth",$cookie,null,false,'30 day');
					//ログイン後のURLが不安定なため、普通に書く。
					$this->redirect(array(
								'controller'=>$this->Auth->loginRedirect['controller'], 
								'action'=>$this->Auth->loginRedirect['action'])
						       );
				}else{
					$this->Session->setFlash(__('user or pass が正しくありません!'), 'default', array(), 'auth');
				}
			}
		}
	}
	public function logout(){
		$this->Cookie->destroy();
		$this->redirect($this->Auth->logout());
	}
	public function add() {
		if ($this->request->is('post')) {
			$data=h($this->request->data);
			//created_date の追加
			$data['User']['created_date'] = date('Y-m-d H:i:s');

			$this->User->set($data);
			if($this->User->validates()){
				$this->User->create();
				if ($this->User->save($data)) {
					if($this->Auth->login($data)){
						$this->Cookie->write("Auth",$data,null,false,'30 day');
						return $this->redirect(array(
									'controller'=>$this->Auth->loginRedirect['controller'],
									'action'=>$this->Auth->loginRedirect['action']
									));
					}
				} else {
					$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
				}
			}else{
				$this->log( $this->User->validationErrors, LOG_DEBUG);
			}
		}
	}




	public function admin_index(){
	}
	public function admin_login(){
		parent::beforeFilter();
		if($this->Cookie->check('Auth')){
			if($this->Cookie->read('Auth.User.username')=='mitu'){

				$this->login(); //すでに作成したUser用のlogin()を使いまわす。
			}else{

				$this->logout(); //すでに作成したUser用のlogin()を使いまわす。
			}
		}else{

			$this->login(); //すでに作成したUser用のlogin()を使いまわす。
		}
	}
	public function admin_logout(){
		$this->logout(); //すでに作成したUser用のlogout()を使いまわす。
	}


	public function admin_add() {
		parent::beforeFilter();
		if ($this->request->is('post')) {

			//created_date の追加
			$this->request->data['Owner']['created_date'] = date('Y-m-d H:i:s');

			$this->Owner->create();
			$this->request->data['Owner']=array(
					'username'=>$this->request->data['User']['username'],
					'password'=>$this->request->data['User']['password'],
					);
			if ($this->Owner->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'login'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}

}
