<?php

namespace app\index\controller;
use \think\View;
use \think\Cookie;
use \think\Model;
use \think\Url;
use \think\Request;
class YlnIndex extends Model
{
}
class YlnUser extends Model
{
    protected $createTime = 'regtime';
    protected $updateTime = 'lasttime';
}
class Index extends \think\Controller
{
    public function index()
    {
		if(!file_exists('install.lock')){
            echo "<script language='javascript' type='text/javascript'>";
            echo "window.location.href='index.php/install/install/install'";
            echo "</script>";
        }
        //数据库查询
        $webdate = YlnIndex::get(1);//根据用户识别码
        if(!Cookie::has('safecode')){
            $status='<a class="btn btn-primary btn-lg" href="index.php/index/index/login">登录</a>	<a class="btn btn-primary btn-lg" href="index.php/index/index/register">注册</a>';
        }else{
            $status='<a class="btn btn-primary btn-lg" href="index.php/home/home/home" role="button">用户中心</a>';
        }
        //模板渲染
        $view = new View();
        $view->assign('login','index.php/index/index/login');
        $view->assign('register','index.php/index/index/register');
        $view->assign('name',$webdate['name']);
        $view->assign('status',$status);
        $view->assign('num',$webdate['num']);
        $view->assign('total',$webdate['total']);
        $view->assign('copy',$webdate['copy']);
        return $view->fetch('index',[],['__PUBLIC__'=>'/public/static/index/style']);
    }
    public function login()
    {
        if(input('param.'))
        {
            $in_put = input('param.');
            $search = new YlnUser();
            $search_data = $search->where('name',$in_put['name'])->where('password',md5($in_put['password']))->find();
            if($search_data)
            {
                $randcode = random(20,'all');
                $user = YlnUser::get($search_data['Id']);
                $user->safecode = $randcode;
                $user->save();
                cookie(['prefix' => '', 'expire' => 36000]);
                cookie('safecode',$randcode,'36000');
                $this->success('登陆成功哦','../../home/home/home');
            }else{
                $this->error('用户名或者密码不对哦');
            }
        }
        $webdate = YlnIndex::get(1);//根据用户识别码
        //渲染模板
        $view = new View();
        $view->assign('name',$webdate['name']);
        $view->assign('copy',$webdate['copy']);
        return $view->fetch('login',[],['__PUBLIC__'=>'/public/static/index/login']);
    }
    public  function  register()
    {
        //获取请求变量
        if(input('param.'))
        {
        //输入数据
            $in_put = input('param.');
            $search = new YlnUser();
            $search_data = $search->whereOr('name',$in_put['name'])->whereOr('phone',$in_put['phone'])->whereOr('qq',$in_put['qq'])->find();
            if($search_data)
            {
              $this->error('用户存在了哦');
            }else{
                if(mb_strwidth($in_put['password']) < 6)
                {
                    $this->error('密码太短了哦，请输入大于6位的密码');
                }
                $ylnuser = new YlnUser;
                $ylnuser->data([
                    'name'  =>  $in_put['name'],
                    'mail' =>  $in_put['mail'],
                    'password' => md5($in_put['password']),
                    'phone' => $in_put['phone'],
                    'qq' => $in_put['qq'],
                    'city' => 'CHINA',
                    'birth' => '2000-01-01',
                    'reminder' => 0,
                    'status' => 1,
                    'safecode' => '00000000000000000000'
                ]);
                if($ylnuser->save())
                {
                   $this->success('注册成功，正在前往登录页面','login');
                }
            }
        }
        //获取数据库
        $webdate = YlnIndex::get(1);//根据用户识别码
        //渲染模板
        $view = new View();
        $view->assign('name',$webdate['name']);
        $view->assign('copy',$webdate['copy']);
        return $view->fetch('register',[],['__PUBLIC__'=>'/public/static/index/login']);
    }
}
