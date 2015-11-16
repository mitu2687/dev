<?php 
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class GetUrlAboutComponent extends Component{

	public function getEmbet($data,$model){
		$embet="";
		switch(true){
			case $data[$model]['site_id']==1:
				$pattern='/[\D]{5,}/';
				$data[$model]['video']=preg_replace($pattern,'',$data[$model]['video']);
				$num=strpos($data[$model]['video'],'/');
				$len=strlen($data[$model]['video']);
				$sum=$len-$num;
				$data[$model]['video']=substr($data[$model]['video'],-$len,-$sum);
				$embet="http://flashservice.xvideos.com/embedframe/{$data[$model]['video']} ";
				break;
			case $data[$model]['site_id']==2:
				$split=explode('/',$data[$model]['video']);
				$embet="http://xhamster.com/xembed.php?video={$split[4]}";
				break;
		}
		return $embet;
	}

	public function getImgFile($data, $c = 0){

		if(isset($data["Master"])){
			$alias=explode('-',$data['Master']['created']);
			$alias=$alias[0].'-'.$alias[1];
			$targetDir="img".DIRECTORY_SEPARATOR.$alias.DIRECTORY_SEPARATOR.$data['Master']['id'];
			$dir=new Folder($targetDir);
			$t=$dir->read();
		}else{
			foreach($data as  $key => $val){
				$alias=explode('-',$val['Master']['created']);
				$alias=$alias[0].'-'.$alias[1];
				$targetDir="img".DIRECTORY_SEPARATOR.$alias.DIRECTORY_SEPARATOR.$val['Master']['id'];
				$dir=new Folder($targetDir);

				$t[$val['Master']['id']]=$dir->read();

			}
			if(!empty($t)){
				foreach($t as $key=> $val){
					$t[$key]=$val[1];
				}
			}else{
				$t =array();
			}
		}
		return $t;
	}

	public function getFileInfo($files,$date,$master){
		$id=$master['Master']['id'];
		$site_id=$master['Master']['site_id'];
		$handle=array();
		switch(true){
			case $site_id==1:
				$num=array('first'=>10,'last'=>20);
				break;
			case $site_id==2:
				$num=array('first'=>10,'last'=>19);
				break;
		}
		for($i=$num['first']; $i<=$num['last']; $i++){
			$handle[$i]=$i;
		}
		foreach($files[1] as $key => $val){
			$target="eroparts/img/$date/$id/$val";
			$dir=new File($target);
			$name=$dir->name();
			if(in_array($name,$handle)){
				unset($handle[$name]);
			}
		}
		return $handle;
	}

	public function getRoot(){


		$cssUrl=Router::url();
		$cssUrl=explode("/",$cssUrl);
		C::write("all_url",$cssUrl);
		if(isset($cssUrl[2])){
			C::write("url",$cssUrl[2]);
		}else{
			C::write("url","masters");
		}
		return null;
	}


}
