<?php
class MoneysController extends AppController{
	public $paginate=array( 'paramType' => 'querystring');

	public function index(){


		$options=$this->paginate;
		$options['limit']=25;
		$options['order']=array('Money.created'=>'DESC');



		$this->paginate=$options;
		$data=$this->paginate();
		$this->set('datas',$data);



		$fiels['fields']=array('SUM(Money.money) as sum');
		$sum=$this->Money->find('first',$fiels);
		$this->set('sum',$sum);
	}
}
