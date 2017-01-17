<?php
namespace Common\Model;
use Think\Model;
/**
 * 文章内容model操作
 */
class NewsModel extends Model{
    private $_db='';
    public function __construct(){
        $this->_db= M('news');
    }
    
    public function insert($data=array()){
        if(!is_array($data)|| !$data){
            return 0;
        }
        $data['create_time']=time();
        $data['username']=getLoginUsername();
        return  $this->_db->add($data);
    }

    public function select($data = array(), $limit = 100) {

        $conditions = $data;
        $list = $this->_db->where($conditions)->order('news_id desc')->limit($limit)->select();
        return $list;
    }
    //得到所有文章
    /**
     * @param $data
     * @param $page
     * @param int $pageSize
     */
    public function getNews($data, $page, $pageSize=10){
        $conditions=$data;
        if(isset($data['title']) && $data['title']){
            //实现标题模糊搜索
            $conditions['title']=array('like','%'.$data['title'].'%');
        }
        if(isset($data['catid']) && $data['catid'] ){
            $conditions['catid']=intval($data['catid']);
        }
        $offset=($page-1)*$pageSize;
        $list=$this->_db->where($conditions)
            ->order('listorder desc,news_id desc')
            ->limit($offset,$pageSize)
            ->select();
        return $list;
    }

    // 统计文章数量
    public function getNewsCount($data=array()){
        $conditions=$data;
        if(isset($data['title']) && $data['title']){
            $conditions['title']=array('like','%'.$data['title'].'%');
        }
        if(isset($data['catid']) && $data['catid'] ){
            $conditions['catid']=intval($data['catid']);
        }
        return $this->_db->where($conditions)->count();
    }

    //通过id查找文章内容
    public function find($id){
        if(is_numeric($id)) {
            $data = $this->_db->where('news_id='.$id)->find();
            return $data;
        }
        return '';
    }

    public function updateById($id,$data){
        if(!$id || !is_numeric($id)){
            throw_exception("ID不合法");
        }
        if(!$data || !is_array($data)){
            throw_exception("更新数据不合法");
        }
        return $this->_db->where('news_id='.$id)->save($data);
    }

    public function updateStatusById($id,$status){
        if(!$id || !is_numeric($id)){
            throw_exception("ID不合法");
        }
        if(!is_numeric($status)){
            throw_exception("status不能为非数字");
        }
        $data['status']=$status;
        return $this->_db->where('news_id='.$id)->save($data);
    }

    public function updateNewsListorderById($id,$listorder){
        if(!$id || !is_numeric($id)){
            throw_exception("ID不合法");
        }
        $data=array('listorder'=>intval($listorder));
        return $this->_db->where('news_id='.$id)->save($data);
    }

    public function getNewsByNewsIdIn($newsId){
        if(!is_array($newsId)){
            throw_exception('参数不合法');
        }
        $data=array(
            'news_id'=>array('in',implode(',',$newsId)),
        );
        return $this->_db->where($data)->select();
    }

    //获取排行的数据
    public function getRank($data = array(), $limit = 100) {
        $list = $this->_db->where($data)->order('count desc,news_id desc ')->limit($limit)->select();
        return $list;
    }
    public function updateCount($id, $count) {
        if(!$id || !is_numeric($id)) {
            throw_exception("ID 不合法");

        }
        if(!is_numeric($count)) {
            throw_exception("count不能为非数字");
        }

        $data['count'] = $count;
        return $this->_db->where('news_id='.$id)->save($data);

    }

    //获取最大阅读数的文章
    public function maxcount() {
        $data = array(
            'status' => 1,
        );
        return $this->_db->where($data)->order('count desc')->limit(1)->find();
    }

}