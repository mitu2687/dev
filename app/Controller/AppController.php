<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');


/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $uses=array(
		'Owner',
	);


	public $helpers = array('Html', 'Form', 'Session');
	public $components = array(
			'RequestHandler' => array('className' => 'MyRequestHandler'),
			'GetUrlAbout',
			'Cookie',
			'Session', 
			'Auth' => array(
				'loginAction' => array('controller' => 'users','action' => 'login'), //ログインを行なうaction
				'loginRedirect' => array('controller' => 'user_master', 'action' => 'myroom'), //ログイン後のページ
				'logoutRedirect' => array('controller' => 'masters', 'action' => 'index'), //ログアウト後のページ
				'authError'=>'ログインして下さい。',

				),
			);

	public function beforeFilter(){
		if($this->Cookie->check("Auth")){
			C::write("Auth",$this->Cookie->read("Auth"));
		}
		if($this->RequestHandler->isSmartPhone()){
			$this->layout='smart';
			C::write("smart","smart");
		}
		$this->GetUrlAbout->getRoot();
		$all_url=C::read("all_url");
		$this->set(compact("all_url"));
		$this->Auth->allow(array('controller' => 'masters', 'action' => 'index'));
		if(isset($this->request->params['admin'])){
			$this->layout = 'admin';
		$this->Auth->authenticate = array(
				'Form' => array(
					'userModel' => 'Owner',
					'fields' => array('username' => 'username','password'=>'password')
				)
			);
			$this->Auth->loginAction = array('controller' => 'users','action' => 'login', 'admin'=>true);
			$this->Auth->loginRedirect = array('controller' => 'videos', 'action' => 'index', 'admin'=>true);
			$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login', 'admin'=>true);
			AuthComponent::$sessionKey = "Auth.Owner";
		}else{
			AuthComponent::$sessionKey = "Auth.User";
		}
	}

}
