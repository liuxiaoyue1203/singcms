<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index($type=''){
        //右侧排行
        $rankNews=$this->getRank();
        //首页大图数据
        $topPicNews=D("PositionContent")->select(array('status'=>1,'position_id'=>2),1);
        //首页3个小图推荐
        $topSmailNews=D("PositionContent")->select(array('status'=>1,'position_id'=>3),3);

        $listNews = D("News")->select(array('status'=>1,'thumb'=>array('neq','')),30);

        //右侧广告位
        $advNews=D("PositionContent")->select(array('status'=>1,'position_id'=>5),3);

        $this->assign('result',array(
            'topPicNews'=>$topPicNews,
            'topSmailNews'=>$topSmailNews,
            'listNews'=>$listNews,
            'advNews'=>$advNews,
            'rankNews'=>$rankNews,
            'catId'=>0
        ));

        /**
         * 生成页面静态化
         */
        if($type == 'buildHtml') {
            $filename='index';
            if(file_exists(HTML_PATH.$filename.C("HTML_FILE_SUFFIX")))
                $fid = unlink(HTML_PATH.$filename.C("HTML_FILE_SUFFIX"));
            else
                $fid = ture;
            // thinkphp/library/think/controller.class.php
            // $filename 静态化文件名字  HTML_FILE_SUFFIX静态化文件后缀，定义在common/config.php
            // HTML_PATH 静态化文件路径，定义在index.php
            // 'Index/index' 要静态化的模板文件
            $this->buildHtml($filename,HTML_PATH,'Index/index');
            return $fid;
        }else {
            $this->display();
        }
    }

    public function build_html() {
        $id = $this->index('buildHtml');
        if($id == ture) {
            return show(1, '首页缓存生成成功');
        }else{
            return show(0,'原缓存删除失败');
        }
    }


    public function crontab_build_html() {
        if(APP_CRONTAB != 1) {
            die("the_file_must_exec_crontab");
        }
        $result = D("Basic")->select();
        if(!$result['cacheindex']) {
            die('系统没有设置开启自动生成首页缓存的内容');
        }
        $this->index('buildHtml');
    }

    // count.js
    public function getCount() {
        if(!$_POST) {
            return show(0, '没有任何内容');
        }
        $newsIds =  array_unique($_POST);
        try{
            $list = D("News")->getNewsByNewsIdIn($newsIds);
        }catch (Exception $e) {
            return show(0, $e->getMessage());
        }
        if(!$list) {
            return show(0, 'notdataa');
        }
        $data = array();
        foreach($list as $k=>$v) {
            $data[$v['news_id']] = $v['count'];
        }
        return show(1, 'success', $data);
    }
}