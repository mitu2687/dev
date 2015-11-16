<?php
App::uses('AppController', 'Controller');
App::uses('AppShell','Console/Command');
App::uses('VideosShell','Console/Command');
App::import('Vendor','simple_html_dom');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class VideosController extends AppController{

	public $paginate=array( 'paramType' => 'querystring');
	public $components = array('Search.Prg');
	public $presetVars = array(
			array('field'=>'text','type'=>'value')
			);

	public function admin_index(){
		C::write('debug',2);
		$this->Video->bindModel(array(
					'hasOne' => array('VideosTag', 
						'Tag' => array(
							'className' => 'Tag',
							'foreignKey' => false,
							'conditions' => array('Tag.id = VideosTag.tag_id')
							)
						)
					),false
				);
		$this->loadModel('Master');
		$options=$this->paginate;
		$this->Prg->commonProcess();
		if(!empty($this->request->query['text'])){
			$text=$this->request->query['text'];
			C::write("models","Video");
			C::write("search",$text);
			$options['conditions']=$this->Master->parseCriteria($this->request->query);
		}else{
			$options['conditions']=array('Video.delete'=>0);
		}
		$options['limit']=30;
		$options['group']='Video.id';
		$this->paginate=$options;
		$data=$this->paginate();
		$this->set('datas',$data);
		$count=$this->Master->find('count',array(
					"conditions"=>array(
						"delete"=>0
						)
					)
				);
		$stockCount=$this->Video->find('count',array(
					"conditions"=>array(
						"delete"=>0
						)
					)
				);
		$this->set('count',$count);
		$this->set('stockCount',$stockCount);
	}

	public function admin_edit($params){
		$data=$this->Video->findById($params);
		$this->set('data',$data);
		$embet=$this->GetUrlAbout->getEmbet($data,'Video');
		$this->set('embet',$embet);
		$cate = ClassRegistry::init('Category')->find('all');
		$this->set('cate',$cate);
		$tags=ClassRegistry::init('Tag')->find('all',array(
					'recursive'=>-2,
					'order'=>array("Tag.sort"=>"asc")
					));
		$this->set('tags',$tags);

		$master = ClassRegistry::init('Master')->findById($params);
		$this->set('master',$master);
		//画像ファイルを取得
		if(!empty($master)){
			$date=explode("-",$master['Master']['created']);
			$files=$this->GetUrlAbout->getImgFile($master);
			$date=$date[0]."-".$date[1];
			$files=$this->GetUrlAbout->getFileInfo($files,$date,$master);
			$this->Cookie->write('list',$files);
			$this->set("files",$files);
		}else{
			$date=explode("-",$data['Video']['created']);
			$this->set("files","");
		}
		$date=$date[0]."-".$date[1];
		$this->set('date',$date);
	}


	public function admin_retry($params){
		$this->autoRender=false;
		$list=$this->Cookie->read('list');
		$this->Video->getImage($params,$list);
		$this->Session->setFlash("画像を取得しました。");
		$this->redirect("/admin/videos/edit/$params");
	}

	public function admin_add($params){
		$post=$this->request->data;
		if(isset($post['Master'])){
			$this->loadModel('Master');
			$master=array(
					'id'=>$post['Master']['id'],
					'created'=>$post['Master']['created'],
					'text'=>$post['Video']['text'],
					'category_id'=>$post['Video']['category_id'],
					'quality'=>$post['Video']['quality']
				     );
			$this->Master->save($master);
		}
		if(isset($post['newCate'])){
			$this->loadModel('Category');
			$this->Category->saveAll($post);
			$post['Video']['category_id']=$this->Category->getLastInsertID();
		}
		if(!isset($post['VideosTag'])){
			$this->Video->save($post);
		}else{
			$this->loadModel('VideosTag');
			$post['VideosTag']['master_id']=$params;
			$this->VideosTag->saveall($post);         
		}

		$this->Session->setFlash('更新が完了しました');
		$this->redirect("/admin/videos/edit/$params");
	}

	public function admin_master($params){
		$post=$this->request->data;
		$error=$this->Video->getImage($params);
		$this->loadModel('Master');
		if($this->Master->save($post)){
			$this->Session->setFlash('更新が完了しました');
			$data['Video']['delete']=1;
			$data['Video']['created']=$post['Video']['created'];
			$data['Video']['id']=$params;
			$this->Video->save($data);
			if(!is_null($error)){
				C::write('error',$error);
			}
			$this->redirect("/admin/videos/edit/$params");
		}else{
			$this->Session->setFlash('データ挿入に失敗しました。');
		}
	}

	public function admin_delete($params){
		$condition=array('Video.id'=>$params);
		$fields=array('Video.delete'=>1);
		$this->Video->updateAll($fields,$condition);
		$this->Session->setFlash('削除しました。');
		$this->redirect('/admin/videos');
	}


	public function admin_addTags(){
		$this->autoRender=false;
		if($this->RequestHandler->isAjax()){
			$ajax= $this->request->data;
			$tagId=ClassRegistry::init('Tag')->findByName($ajax['request']);
			$data=array(
					"Video"=>array(
						'id'=>$ajax["id"]
						),
					"Master"=>array(
						'id'=>$ajax["id"]
						),
					"Tag"=>array(
						'id'=>$tagId["Tag"]["id"]
						),
				   );
			if($this->Video->saveAll($data)){
				$this->loadModel("VideosTag");
				$lastId=$this->VideosTag->getLastInsertID();
				$this->VideosTag->updateAll(
						array("VideosTag.master_id"=>$ajax['id']),
						array("VideosTag.id"=>$lastId)
						);
				print $ajax['request']."タグを追加しました";
			}
		}
	}

	public function admin_getVideo2(){
		C::write('debug',0);
		$this->autoRender=false;
		for($i=1; $i<=8; $i++){
			$text=$this->Video->getVideo2($i);
		}
		$this->Session->setFlash("動画を取得しました。");
		$this->redirect('/admin/videos');
	}

	public function admin_getHam(){
		C::write('debug',0);
		$this->autoRender=false;
		for($i=1; $i<=8; $i++){
			$text=$this->Video->getVideo2($i,"ham");
		}
		$this->Session->setFlash("動画を取得しました。");
		$this->redirect('/admin/videos');
	}

}
