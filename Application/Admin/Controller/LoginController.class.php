<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * use Common\Model 这块可以不需要使用，框架默认会加载里面的内容
 */
class LoginController extends Controller
{

    public function index()
    {
        // 如果已经登录，则直接跳转到登录页面
        if (session('adminUser')) {
            $this->redirect('/admin.php?m=admin&c=index');
        }
        // 如果当前没有启用模板主题则定位到： 当前模块/默认视图目录/当前控制器/当前操作.html
        return $this->display();
    }

    // 处理来自login.js的Ajax Post异步请求
    public function check()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if (! trim($username)) {
            return show(0, '用户名不能为空');
        }
        if (! trim($password)) {
            return show(0, '密码不能为空');
        }
        // D方法 实例化自己的Model
        $ret = D('Admin')->getAdminByUsername($username);
        if (! $ret) {
            return show(0, '该用户不存在');
        }
        if ($ret['status'] != 1) {
            return show(0, '该用户登录受限，请联系管理员');
        }

        if ($ret['password'] != getMd5Password($password)) {
            return show(0, '密码错误');
        }
        // 更新用户最后登录时间
        D("Admin")->updateByAdminId($ret['admin_id'],array('lastlogintime'=>time()));
        session('adminUser', $ret);
        return show(1, '登录成功');
    }

    // 注销登录
    public function loginout()
    {
        session('adminUser', null);
        $this->redirect('/admin.php?m=admin&c=login');
    }
}