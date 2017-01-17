<?php
/**
 * 后台Index相关
 */
namespace Admin\Controller;

use Think\Controller;

class IndexController extends Controller
{
    // 系统首页
    public function index()
    {
        // 如果还没有登录，跳转到登录页面
        if (empty(session('adminUser'))) {
            // $this->redirect('/admin.php?m=admin&c=login','',3,'Please login...');
            //redirect('/admin.php?m=admin&c=login', 3, 'Please login...');
            redirect('/admin.php?m=admin&c=login');
        }

        $news = D('News')->maxcount();
        $newscount = D('News')->getNewsCount(array('status'=>1));
        $positionCount = D('Position')->getCount(array('status'=>1));
        $adminCount = D("Admin")->getLastLoginUsers();

        $this->assign('news', $news);
        $this->assign('newscount', $newscount);
        $this->assign('positioncount', $positionCount);
        $this->assign('admincount', $adminCount);
        $this->display();
    }

    public function main()
    {
        $this->display();
    }
}