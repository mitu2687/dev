<?php
class MastersController extends AppController{


	public $paginate=array( 'paramType' => 'querystring');
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('index','video','actions404');
	}
	public $components = array('Search.Prg');
	public $presetVars = array(
			array('field'=>'text','type'=>'value')
			);

	public function index(){
		C::write('debug',0);
		if(!empty($this->request->query['coo'])){
			$this->Cookie->delete("user_data");
			$this->redirect("/");
		}
		$this->Master->bindModel(array(
					'hasOne' => array('VideosTag', 
						'Tag' => array(
							'className' => 'Tag',
							'foreignKey' => false,
							'conditions' => array('Tag.id = VideosTag.tag_id')
							)
						)
					),false
				);
		$options=$this->paginate;
		$this->Prg->commonProcess();
		if(!empty($this->request->query['text'])){
			C::write("models","Master");
			$search_text=h($this->request->query);
			$options['conditions']=$this->Master->parseCriteria($search_text);
			C::write("search",$search_text['text']);
			if($this->Cookie->read("user_data")==null){
				$this->Cookie->write("user_data",array("search"=>array(C::read("search")=>C::read("search")),null,false,'30 day'));
			}else{
				$cookie=$this->Cookie->read("user_data");
				$this->Cookie->delete("user_data");
				$cookie['search']+=array(C::read("search")=>C::read("search"));
				$this->Cookie->write("user_data",$cookie,null,false,'30 day');
			}
		}else{
			$options['conditions']=array('Master.delete'=>0);
		}
		$options['limit']=28;
		$options['order']=array('Master.id'=>'desc');
		$options['group']='Master.id';
		$this->paginate=$options;
		$datas=$this->paginate();
		if(empty($datas)){
			$this->redirect("/masters/actions404/".C::read('search'));
		}
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

		$this->set(compact("datas","t","cate","cate2"));
		if($this->Cookie->check("user_data")){
			$this->set("cookie",$this->Cookie->read("user_data"));
		}
		$redirect=$this->Master->redirectHandler($this->request->query['page'],$this->request->here);
		switch(true){
			case $redirect==1;
			$this->redirect("http://eroparts.com",301);
			break;
			case $redirect==2;
			$this->redirect("http://eroparts.com?text=".C::read("search")."&page=".$this->request->query['page'],301);
			break;
			case $redirect==3;
			$this->redirect("http://eroparts.com/?page=".$this->request->query['page'],301);
			break;
		}

	}

	public function actions404($params){
		$info_text="キーワード[".$params."]の動画はありません";
		//カテゴリーを取得
		$category=ClassRegistry::init("Category");
		$order["order"]=array("Category.master_count"=>"desc");
		$order['conditions']=array("Category.master_count >="=>"10");
		$cate=$category->find("all",$order);
		$order['conditions']=array("Category.master_count <="=>"9");
		$cate2=$category->find("all",$order);
		if($this->Cookie->check("user_data")){
			$this->set("cookie",$this->Cookie->read("user_data"));
		}
		$this->set(compact("info_text","cate","cate2"));
	}

	public function video($params){
		C::write('debug',0);
		$data=$this->Master->findById($params);
		$embet=$this->GetUrlAbout->getEmbet($data,'Master');
		$conditions['conditions']=array("Category.id"=>$data['Category']['id'],"Master.delete"=>0);
		$conditions['limit']=12;
		$conditions['order']="rand()";
		$relative_video=$this->Master->find("all",$conditions);
		//カテゴリーを取得
		$category=ClassRegistry::init("Category");
		$order["order"]=array("Category.master_count"=>"desc");
		$order['conditions']=array("Category.master_count >="=>"10");
		$cate=$category->find("all",$order);
		$order['conditions']=array("Category.master_count <="=>"9");
		$cate2=$category->find("all",$order);

		//タグを取得
	/*	$tags=ClassRegistry::init("Tag");
		$options=array();
		$options['order']="rand()";
		$tags=$tags->find("all",$options);*/
		
		$img = $this->Master->getImage($data);

		$this->set(compact("data","embet","relative_video","cate","cate2","img"));
		if($this->Cookie->check("user_data")){
			$this->set("cookie",$this->Cookie->read("user_data"));
		}

	}

	public function admin_index(){
		$options=$this->paginate;
		$options['limit']=30;
		$options['conditions']=array('Master.delete'=>0);
		$this->paginate=$options;
		$data=$this->paginate();
		$this->set('datas',$data);
	}

	public function admin_edit($params){
		$data=$this->Master->findById($params);
		$this->set('data',$data);
		$embet=$this->GetUrlAbout->getEmbet($data,'Master');
		$this->set('embet',$embet);
		$cate = ClassRegistry::init('Category')->find('all');
		$this->set('cate',$cate);
		$tags=ClassRegistry::init('Tag')->find('all',array(
					'recursive'=>-2,
					));
		$this->set('tags',$tags);
	}

	public function admin_add($params){
		$post=$this->request->data;
		if(isset($post['newCate'])){
			$this->loadModel('Category');
			$this->Category->saveAll($post);
			$post['Master']['category_id']=$this->Category->getLastInsertID();
		}
		if(!isset($post['VideosTag'])){
			$this->Master->save($post);
		}else{
			$this->loadModel('VideosTag');
			$post['VideosTag']['video_id']=$params;
			$this->VideosTag->saveall($post);         
		}

		$this->Session->setFlash('更新が完了しました');
		$this->redirect("/admin/masters/edit/$params");
	}

	public function admin_delete($params){
		$condition=array('Master.id'=>$params);
		$fields=array('Master.delete'=>1);
		$this->Master->updateAll($fields,$condition);
		$this->Session->setFlash('削除しました。');
		$this->redirect('/admin/masters');
	}




}
