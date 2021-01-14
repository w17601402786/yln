<?php
namespace app\home\controller;
use \think\Cookie;
use \think\View;
use \think\Model;
use \GatewayClient\Gateway;
class YlnIndex extends Model
{
    protected $resultSetType = 'collection';
}
class YlnUser extends Model
{
    protected $resultSetType = 'collection';
}
class YlnComment extends Model
{
    protected $resultSetType = 'collection';
    protected $createTime = 'time';
    protected $updateTime = false;
}
class YlnBlog extends Model
{
    protected $resultSetType = 'collection';
    protected $createTime = 'time';
    protected $updateTime = false;
}
class YlnChatData extends Model
{
    protected $resultSetType = 'collection';
    protected $createTime = 'time';
    protected $updateTime = false;
}

class Home extends \think\Controller
{
    public function home()
    {
         $cookie=Cookie::get('safecode');
         if(!$cookie)
         {
             $this->error('不好意思哦，请先登陆一下吧','../../index/index/login');
         }
         $data=YlnUser::getBySafecode($cookie);
         if(!$data)
         {
             $this->error('别改COOKIE了，赶紧去登录吧','../../index/index/login');
         }
        $webdate = YlnIndex::get(1);//根据用户识别码
        $view = new View();
        $view->assign('web',$webdate);
        $view->assign('user',$data);
        return $view->fetch('home',[],['__PUBLIC__'=>'/public/static']);
    }
    public function friend()
    {
        $friend_data=YlnUser::where('en_searched','1')->paginate(3);
        $page=$friend_data->render();
        $view=new View();
        $view->assign('friend',$friend_data);
        $view->assign('page',$page);
        return $view->fetch('friend',[],['__PUBLIC__'=>'/public/static']);
    }
    public function loginout()
    {
        $cookie=Cookie::get('safecode');
        if(!$cookie)
        {
            $this->error('不好意思哦，请先登陆一下吧','../../index/index/login');
        }
        $delete=Cookie::delete('safecode');
        $this->success('已退出账户，如有需要请重新登录哦', '../../index/index/login');
    }
    public function person()
    {
        $person=new YlnUser();
        $cookie=Cookie::get('safecode');
        $person_data=YlnUser::getBySafecode($cookie);
        $view=new View();
        $view->assign('user',$person_data);
        return $view->fetch('person',[],['__PUBLIC__'=>'/public/static']);
    }
    public function edit()
    {
        $person=new YlnUser();
        $cookie=Cookie::get('safecode');
        $person_data=YlnUser::getBySafecode($cookie);
        if(input('post.'))
        {
            $post_data=input('post.');
            $update=$person->allowField(true)->save($post_data,['safecode' => $cookie]);
            if($update)
            {
                $this->success('操作成功','edit');
            }else{
                $this->error('什么都没有发生...');
            }
        }

        $view=new View();
        $view->assign('person',$person_data);
        return $view->fetch('edit',[],['__PUBLIC__'=>'/public/static']);
    }
    public  function user_data()
    {
        $Id=input('get.Id');
        $friend=new YlnUser();
        $friend_data=YlnUser::getById($Id)->toArray();
        $view=new View();
        $view->assign('friend',$friend_data);
        return $view->fetch('user_data',[],['__PUBLIC__'=>'/public/static']);
    }
    public  function blog()
    {
        $cookie=Cookie::get('safecode');
        $person=YlnUser::getBySafecode($cookie);
        $blog_data=YlnBlog::where('status',1)->order('time','desc')->paginate(3);
        $page=$blog_data->render();
        $view=new View();
        for($i=0;$i<count($blog_data);++$i)
        {
            $blog_data[$i]['comment']=YlnComment::where('blog_Id',$blog_data[$i]['Id'])->order('time','desc')->select()->toArray();
        }
        if(input('post.blog_Id'))
        {
             $post_data=input('post.');
             $post_data['name']=$person['name'];
             $post_data['qq']=$person['qq'];
             $post_data['info']=nl2br($post_data['info']);
             $comment=new YlnComment($post_data);
             if($comment->save())
             {
                 $this->success('发表成功哦','blog');
             }else{
                 $this->error('发表失败!');
             }
        }
        if(input('post.status')=='1')
        {
            $post_data=input('post.');
            $post_data['name']=$person['name'];
            $post_data['qq']=$person['qq'];
            $post_data['info']=nl2br($post_data['info']);
            $blog=new YlnBlog($post_data);
            if($blog->save())
            {
                $this->success('发表成功哦','blog');
            }else{
                $this->error('发表失败!');
            }
        }
        if(input('get.delete_Id'))
        {
            $get_data=input('get.');
            $comment_data=YlnComment::getById($get_data['delete_Id'])->toArray();
            if($comment_data['name']==$person['name'])
            {
                if(YlnComment::destroy($comment_data['Id'])){
                    $this->success('你的评论删除成功哦','blog');
                }else{
                    $this->error('不好意思，删除失败了');
                }
            }else{
                $this->error('你想删除的好像不是你的评论哦');
            }

        }
        if(input('get.delete_blog_Id'))
        {
            $get_data=input('get.');
            $blog_delete_data=YlnBlog::getById($get_data['delete_blog_Id'])->toArray();
            if($blog_delete_data['name']==$person['name'])
            {
                if(YlnBlog::destroy($blog_delete_data['Id']))
                {
                    if(YlnComment::where('blog_Id','=',$blog_delete_data['Id'])->delete())
                    {
                        $this->success('你的动态删除成功哦', 'blog');
                    }else{
                        $this->error("动态删除成功，但是评论没有删除干净哦");
                    }
                }else{
                    $this->error('不好意思，删除失败了');
                }
            }else{
                $this->error('你想删除的好像不是你的动态哦');
            }

        }

        $view->assign('blog',$blog_data);
        $view->assign('person',$person);
        $view->assign('page',$page);
        return $view->fetch('blog',[],['__PUBLIC__'=>'/public/static']);
    }
    public function chat()
    {
        global $ip_address;
        require_once 'application/ip.php';
        $cookie=Cookie::get('safecode');
        $person_data=YlnUser::getBySafecode($cookie);
        $chat_data=YlnChatData::order('time','asc')->select()->toArray();
        $time1=YlnChatData::order('time','desc')->limit(1)->select()->toArray();

        if(!$time1){
            $time['time']='1970-1-1';
        }else{
            $time=$time1[0];
        }
        $view=new View();
        $view->assign('ip',$ip_address);
        $view->assign('time',$time);
        $view->assign('chat',$chat_data);
        $view->assign('user',$person_data);
        return $view->fetch('chat',[],['__PUBLIC__'=>'/public/static']);
    }
    public function bind()
    {
        global $ip_address;
        require_once 'application/ip.php';
        $ip=$ip_address.':1238';
        Gateway::$registerAddress = $ip;

        $cookie=Cookie::get('safecode');
        $person_data=YlnUser::getBySafecode($cookie);
        $uid = $person_data['Id'];
        $client_id=input('post.client_id');
        Gateway::bindUid($client_id,$uid);
        $message=[
            'type'=>'login_in',
        ];
        Gateway::sendToAll(json_encode($message));
    }
    public function in_data()
    {
        $chat_data=input('post.');
        $chat=new YlnChatData($chat_data);
        print_r($chat_data);
        if($chat->save())
        {
            $callback['result']='success';
            print_r(json_encode($callback,true));
        }else{
            $callback['result']='error';
            print_r(json_encode($callback,true));
        }
    }
    public function online()
    {
        global $ip_address;
        require_once 'application/ip.php';
        $ip=$ip_address.':1238';
        Gateway::$registerAddress = $ip;
        $online_id=Gateway::getAllUidList();
        $array=array();
        foreach ($online_id as $uid){
            $user_list=YlnUser::getById($uid)->toArray();
            array_push($array,$user_list);
        }
        print_r(json_encode($array));
    }

}