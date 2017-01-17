<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/18
 * Time: 23:12
 */
namespace Admin\Controller;

class PositionController extends CommonController
{
    public function index(){
        $data=array(
            'status'=>1,
        );
        $positions=D("Position")->select($data);
        $this->assign('positions',$positions);
        $this->display();
    }
}
