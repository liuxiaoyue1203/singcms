<?php

namespace Admin\Controller;
//use Think\Controller;

use Think\Exception;

class ContentController extends CommonController{
    
    public function index(){
        $conds=array();

        if($_GET['title']){
            $conds['title']=$_GET['title'];
            $this->assign('title', $conds['title']);
        }else{
            $this->assign('title','');
        }

        if($_GET['catid']){
            $conds['catid']=intval($_GET['catid']);
            $this->assign('catid', $conds['catid']);
        }else{
            $this->assign('catid', -1);
        }

        $page=$_REQUEST['p'] ?$_REQUEST['p']:1;
        $pageSize=6;
        $conds['status']=array('neq',-1);
        $news=D("News")->getNews($conds,$page,$pageSize);
        $count=D("News")->getNewsCount($conds);
        $res=new \Think\Page($count,$pageSize);
        $pageRes=$res->show();

        $positions=D('Position')->getNormalPositions();
        //print_r($positions);exit;
        $this->assign("pageRes",$pageRes);
        $this->assign("news",$news);
        $this->assign('positions',$positions);
        $this->assign('webSiteMenu',D("Menu")->getBarMenus());  //webSiteMenu 前端栏目
        return $this->display();
    }
    public function add(){
        if($_POST){
            //如果有post数据  提交到数据库
            if(!isset($_POST['title']) || !$_POST['title']){
                return show(0,'标题不存在');
            }
            if(!isset($_POST['small_title']) || !$_POST['small_title']){
                return show(0,'短标题不存在');
            }
            if(!isset($_POST['catid']) || !$_POST['catid']){
                return show(0,'文章栏目不存在');
            }
            if(!isset($_POST['keywords']) || !$_POST['keywords']){
                return show(0,'关键字不存在');
            }
            if(!isset($_POST['content']) || !$_POST['content']){
                return show(0,'Content不存在');
            }

            // 判断是add还是edit的save
            if($_POST['news_id']){
                return $this->save($_POST);
            }
           // $newsId= D("News");
           // print_r($newsId);
           // exit;
            $newsId= D("News")->insert($_POST);
            if($newsId){
                $newsContentData['content']=$_POST['content'];
                $newsContentData['news_id']=$newsId;
                $cId=D('NewsContent')->insert($newsContentData);
                if($cId){ 
                    return show(1,'新增成功');
                }else{
                    return show(1,'主表插入成功，副表插入失败');
                }
            }else{
                return show(0,'新增失败');
            }
        }else{
            $webSiteMenu=D("Menu")->getBarMenus();
            $titleFontColor=C('TITLE_FONT_COLOR');
            $copyFrom=C('COPY_FROM');
            //echo 'URL_MODEL:'.C('URL_MODEL').' ';
            //print_r($titleFontColor);exit;
            $this->assign('webSiteMenu',$webSiteMenu);
            $this->assign('titleFontColor',$titleFontColor);
            $this->assign('copyFrom',$copyFrom);
            return $this->display();
        }
    }

    public function edit(){
        $newsId=$_GET['id'];
        if(!$newsId){
            $this->redirect('/admin.php?c=content');
        }
        $news=D('News')->find($newsId);
        if(!$news){
            $this->redirect('/admin.php?c=content');
        }
        $newsContent=D('NewsContent')->find($newsId);
        if($newsContent){
            $news['content']=$newsContent['content'];
        }

        $webSiteMenu=D("Menu")->getBarMenus();
        $this->assign('webSiteMenu',$webSiteMenu);
        $this->assign('titleFontColor',C("TITLE_FONT_COLOR"));
        $this->assign('copyFrom',C("COPY_FROM"));
        $this->assign('news',$news);
        $this->display();
    }

    public function save($data){
        $newsId=$data['news_id'];
        unset($data['news_id']);
        try{
            $id=D('News')->updateById($newsId,$data);
            $newContentData['content']=$data['content'];
            $condId=D('NewsContent')->updateNewsById($newsId,$newContentData);
            if($id === false || $condId === false){
                return show(0,'更新失败');
            }
            return show(1,'更新成功');
        }catch(Exception $e){
            return show(0,$e->getMessage());
        }
    }

    public function setStatus(){
        try {
            if ($_POST) {
                $id = $_POST['id'];
                $status = $_POST['status'];
                if (!id) {
                    return show(0, 'ID不存在');
                }
                $res = D("News")->updateStatusById($id, $status);
                if ($res) {
                    return show(1, '操作成功');
                } else {
                    return show(0, '操作失败');
                }
            }
            return show(0,'没有提交的内容');
        }catch(Exception $e){
            return show(0,$e->getMessage());
        }
    }

    public function listorder(){
        $listorder=$_POST['listorder'];
        $jumpUrl=$_SERVER['HTTP_REFERER'];//前页的前一页的地址
        $errors=array();
        try {
            if ($listorder) {
                foreach ($listorder as $newsId => $v) {
                    //执行更新操作
                    $id = D('News')->updateNewsListorderById($newsId, $v);
                    if ($id === false) {
                        $errors[] = $newsId;
                    }
                }
                if ($errors) {
                    return show(0, '排序失败-', implode(',', $errors), array('jump_url' => $jumpUrl));
                }
                return show(1, '排序成功', array('jump_url' => $jumpUrl));
            }
        }catch(Exception $e){
            return show(0,$e->getMessage());
        }
        return show(0, '排序数据失败', array('jump_url' => $jumpUrl));
    }

    //推送数据
    public function push(){
        $jumpUrl=$_SERVER['HTTP_REFERER'];
        $positionId=intval($_POST['position_id']);
        $newsId=$_POST['push'];

        if(!$newsId || !is_array($newsId)){
            return show(0,'请选择推荐的文章ID进行推荐');
        }
        if(!$positionId){
            return show(0,'没有选择推荐位');
        }
        try {
            $news = D("News")->getNewsByNewsIdIn($newsId);
            if (!$news) {
                return show(0, '没有相关内容');
            }
            foreach ($news as $new) {
                $data = array(
                    'position_id' => $positionId,
                    'title' => $new['title'],
                    'thumb' => $new['thumb'],
                    'news_id' => $new['news_id'],
                    'status' => 1,
                    'create_time' => $new['create_time'],
                );
                $position = D("PositionContent")->insert($data);
            }
        }catch (Exception $e){
            show(0,$e->getMessage());
        }
        return show(1,'推荐成功',array('jump_url'=>$jumpUrl));
    }

}