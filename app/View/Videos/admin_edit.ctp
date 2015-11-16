<style>
#message{
width: 30%;
height: 280px;
z-index: 1000;
margin: 0 auto;
position: relative;
top: 428px;
display:none;
}

.tags{
cursor:pointer;
}

.tagsOk{
    position: relative;
    top: 15px;
    z-index: 2000;
background:#333;
}

.opac{
opacity:0.5;
}
<?php if(C::read("smart")==""):?>
#tagsBox{
position:relative;
top:35px;
}
<?php else:?>
.tagList{
margin-bottom:10px;
}
<?php endif;?>
</style>
<div class="container" id="tagsBox">
</div>

<div class="sp-bottom">
<a class="btn btn-default" href="/admin/videos">戻る</a>
<a class="btn btn-default" href="/admin/videos/delete/<?php echo $data['Video']['id'];?>">削除</a>
</div>

<div id="message" style="" class="bg-primary">
<?php if($msg=$this->Session->flash()):?>
<?php echo $msg;?>
<?php endif;?>
</div>
<?php echo $msg;?>
<?php print_r(C::read('error'));?>
<div class="row">
<div class="col-md-8 col-ms-8">
<iframe src="<?php echo $embet;?>" width=300 height=240 frameborder=0></iframe>
</div>
<div class="col-md-4 col-ms-4">
<?php if(!empty($files)):?>
<p class="alert alert-danger text-center">
画像を正しくダウンロードできませんでした
<br>再取得を行ってください。
</p>
<a href="../retry/<?php echo $data['Video']['id'] ;?>" class="btn btn-primary">再取得</a>
<?php endif;?>
</div>
<div class="col-md-12 col-ms-12">
 <?php echo $this->Form->create('Video',array(
	 'action'=>'add/'.$data["Video"]["id"],
	 'class'=>'table table-bordered panel panel-default',
	 'style'=>"padding:10px;")
 ); ?>
      <div class="form-group ">
       <?php echo $this->Form->input('id',array('type'=>'hidden','class'=>"form-control",'value'=>$data['Video']['id'])); ?>
       <label >動画URL</label>
       <span><?php echo $data['Video']['video']; ?></span>

     </div>
     <div class="form-group">
      <label >動画タイトル</label>
      <?php echo $this->Form->input('text',array('class'=>"form-control",'value'=>$data['Video']['text'])); ?>
    </div>
    <div class="form-group">
      <label >動画カテゴリー</label>
<span  class="h5" ><?php echo $data['Category']['name'];?></span>
<span class="btn btn-default sp-bottom" id="newCategory">新規カテゴリー追加</span>
<span class="btn btn-default sp-bottom" id="listCategory">カテゴリーを選択</span>
<a href="/admin/categories/edit/<?php echo $data['Video']['id'];?>" class="btn btn-default sp-bottom" >カテゴリーを編集</a>


<div style="" id="boxCategory">
<label class="sp-left">新規カテゴリーを追加する</label>
<input id="texts" type="text" value="" name="">
<div id="newCate">

</div>
</div>

<div id="selectCategory" >
<label class="sp-left">カテゴリーを選択する</label>
      <select id="VideoCategory_id" name="data[Video][category_id]">
        <?php foreach($cate as $cates) :?>
        <?php if($data['Video']['category_id']==$cates['Category']['id'] ):?>
        <option  selected value="<?php echo $cates['Category']['id'] ;?>"><?php echo $cates['Category']['name'] ;?></option>
      <?php else:?>
      <option  value="<?php echo $cates['Category']['id'] ;?>"><?php echo $cates['Category']['name'] ;?></option>
    <?php endif;?>
  <?php endforeach;?>
</select>
<div id="listCate">

</div>
</div>

</div>

<div class="form-group">
 <label >修正/無修正</label>
 <select name="data[Video][redirect_flag]">  
   <?php $array=array('修正','無修正');?>
   <?php for($i=0; $i<=1; $i++):?>
   <?php if($data['Video']['redirect_flag']==$i):?>
   <option  selected value="<?php echo $i;?>"><?php echo $array[$i];?></option>
 <?php else:?>
 <option  value="<?php echo $i;?>"><?php echo $array[$i];?></option>
<?php endif;?>
<?php endfor;?>
</select>
</div>

<div class="form-group">
  <label >タグ</label>
<span id="taglist">
  <?php foreach($data['Tag'] as $tag) :?>
  <span class="tags sp-left-sm"><?php echo $tag['name'] ;?></span>
<?php endforeach;?>
</span>

<span class="btn btn-default sp-bottom" id="push">新規タグ追加</span>
<span class="btn btn-default sp-bottom" id="box">タグを選択</span>
<a href="/admin/tags/edit/<?php echo $data['Video']['id'];?>" class="btn btn-default sp-bottom" >タグを編集</a>

<div style="" id="box1">
<label class="sp-left">新規タグを追加する</label>
<span>tag名を入れる：</span>
<input id="text" type="text" value="" name="">
<span>tag名の頭を入れる(ひらがな)：</span>
<input id="sortTag" type="text" value="" name="">
<div id="new">

</div>
</div>

<div>
<label class="">
<?php $quality=C::read('quality');?>
<?php echo $quality[$data['Video']['quality']];?>
</label>
<select class="sp-left" name="data[Video][quality]">
<option <?php if($data['Video']['quality']==0){echo 'selected';}  ?> value="">画質を選択</option>
<?php foreach(C::read("quality") as $key=> $all):?>
<?php if($key==0){continue;}?>
<option <?php if($key==$data['Video']['quality']){echo 'selected';}  ?> value="<?php echo $key ;?>"><?php echo $all ;?></option>
<?php endforeach;?>
</select>
</div>
</div>

<div class="row">
  <div class=" col-md-4">
    <label>
      作成日時
      </label>
      <span><?php echo $data['Video']['created'] ;?></span>
      <input type="hidden" name="data[Video][created]" value="<?php echo $data['Video']['created'] ;?>">
      </div>
      <div class=" col-md-4">
    <label>
      更新日時
    </label>
    <span><?php echo $data['Video']['modified'] ;?></span>
  </div>
</div>
<button type="submit" class="btn btn-default">Submit</button>
<input type="hidden" name="data[Video][id]" value="<?php echo $data['Video']['id'] ;?>">
<?php if(isset($master['Master'])) :?>
<input type="hidden" name="data[Master][id]" value="<?php echo $master['Master']['id'] ;?>">
<input type="hidden" name="data[Master][created]" value="<?php echo $master['Master']['created'] ;?>">
<?php endif;?>
<input type="hidden" name="data[Video][created]" value="<?php echo $data['Video']['created'] ;?>">
<?php echo $this->Form->end(); ?>

<form method="post" action="/admin/videos/master/<?php echo $data['Video']['id'];?>">
<button type="submit" class="btn btn-success sp-bottom-ls">この動画を確定する</button>
<input type="hidden" name="data[Master][id]" value="<?php echo $data['Video']['id'] ;?>">
<input type="hidden" name="data[Master][video]" value="<?php echo $data['Video']['video'] ;?>">
<input type="hidden" name="data[Master][text]" value="<?php echo $data['Video']['text'] ;?>">
<input type="hidden" name="data[Master][site_id]" value="<?php echo $data['Video']['site_id'] ;?>">
<input type="hidden" name="data[Master][category_id]" value="<?php echo $data['Video']['category_id'] ;?>">
<input type="hidden" name="data[Master][min]" value="<?php echo $data['Video']['min'] ;?>">
<input type="hidden" name="data[Master][quality]" value="<?php echo $data['Video']['quality'] ;?>">
<?php if(!empty($master)):?>
<input type="hidden" name="data[Master][created]" value="<?php echo $master['Master']['created'] ;?>">
<?php endif;?>
<input type="hidden" name="data[Video][created]" value="<?php echo $data['Video']['created'] ;?>">
</form>
</div>
</div>
<?php echo $this->Html->script('jQuery.basic.js'."?".time()) ;?>
<script type="text/javascript">
$(function() {
 $("#box1").css("display", "none");
 
    // 「id="jQueryPush"」がクリックされた場合
    $("#push").click(function(){
	    // 「id="jQueryBox"」の表示、非表示を切り替える
	    $("#box1").toggle(300);
	    $("#select").attr({'name':""});
	    $("#text").attr({'name':"data[Tag][name]"});
	    $("#sortTag").attr({'name':"data[Tag][sort]"});
	    $("#push1").css("display", "none");
	    if(!$("#new input").hasClass("addHidden")){
	    $("#new").append("<input class='addHidden' type='hidden' value=<?php echo $data['Video']['id'];?> name='data[VideosTag][video_id]'>");
	    $("#new").append("<input class='addHidden' type='hidden' name='data[new]' value=1>");        
	    }
	    });


 $("#boxCategory").css("display", "none");
 $("#selectCategory").css("display", "none");
 
    // 「id="jQueryPush"」がクリックされた場合
    $("#newCategory").click(function(){
        // 「id="jQueryBox"」の表示、非表示を切り替える
        $("#boxCategory").toggle(300);
        $("#selectCategory").attr({'name':""});
        $("#texts").attr({'name':"data[Category][name]"});
        $("#selectCategory").css("display", "none");
        $("#newCate").append("<input type='hidden' name='data[newCate]' value=1>");        
        $("#VideoCategory_id").removeAttr("name");
    });
    $("#listCategory").click(function(){
        // 「id="jQueryBox"」の表示、非表示を切り替える
        $("#selectCategory").toggle(300);
        $("#texts").attr({'name':""});
        $("#VideosCategory_id").attr({'name':"data[Category][id]"});
        $("#boxCategory").css("display", "none");
        $("#newCate").empty();
    });

$(".tagList").addTags(<?php echo $data['Video']['id'];?>);
$(".tags").tagsDel(<?php echo $data['Video']['id'];?>);
$("#box").tagBox(
<?php echo json_encode($tags);?>
);
});
</script>
