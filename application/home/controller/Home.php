<?php
namespace app\home\controller;
use \think\Cookie;
use \think\View;
use \think\Model;
use \think\Request;
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
class YlnUrl extends Model
{
    protected $resultSetType = 'collection';

}
class Home extends \think\Controller
{
    function getDir($path)
    {
        //判断目录是否为空
        if(!file_exists($path)) {
            return [];
        }

        $files = scandir($path);
        $fileItem = [];
        foreach($files as $v) {
            $newPath = $v;
            if($newPath!='.'&& $newPath !='..')
            $fileItem[] = $newPath;

        }

        return $fileItem;
    }
    public function home()    //主页
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
    public function friend()     //交友中心
    {
        $friend_data=YlnUser::where('en_searched','1')->paginate(3);
        $page=$friend_data->render();
        $view=new View();
        $view->assign('friend',$friend_data);
        $view->assign('page',$page);
        return $view->fetch('friend',[],['__PUBLIC__'=>'/public/static']);
    }
    public function url()      //站点列表
    {
        $person=new YlnUser();
        $cookie=Cookie::get('safecode');
        $person_data=YlnUser::getBySafecode($cookie);
        $uid=$person_data['Id'];
        $url=new YlnUrl();
        $url_list=YlnUrl::where('uid',$uid)->select()->toArray();
        $view=new View();
        $view->assign('user',$person_data);
        $view->assign('url',$url_list);
        return $view->fetch('url',[],['__PUBLIC__'=>'/public/static']);

    }
    public function admin()
    {
        $url=input('post.url');
        $domain=$url;
        $cookie=Cookie::get('safecode');
        $person=YlnUser::getBySafecode($cookie)->toArray();
        $url_data=YlnUrl::getByDomain($url)->toArray();
        if($url_data['uid'] == $person['Id']){
            if(input('post.do') == '1'){
                $url1=$url.'/index.php/api/api/api?copy='.input('post.copy').'&op=update_data&identity_code='.$url_data['identity_code'].'&name='.input('post.name');
                $ch = curl_init();
                $timeout = 5;
                curl_setopt($ch, CURLOPT_URL, $url1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);   //参数设置
                $return1 = curl_exec($ch);  //返回数据
                curl_close($ch);
                $return_data1=json_decode($return1,true);
                if($return_data1['message'] == 'success'){
                    $this->success('操作成功','url');
                }else{
                    $this->error('操作失败');
                }
            }
            $url=$url.'/index.php/api/api/api?op=select&identity_code='.$url_data['identity_code'];
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);   //参数设置
            $return = curl_exec($ch);  //返回数据
            curl_close($ch);
            $return_data=json_decode($return,true);
            $view=new View();
            $view->assign('url',$return_data);
            $view->assign('domain',$domain);
            return $view->fetch('url_edit',[],['__PUBLIC__'=>'/public/static']);
        }else{
            $this->error('这不是你的域名');
        }
    }
    public function loginout()   //退出
    {
        $cookie=Cookie::get('safecode');
        if(!$cookie)
        {
            $this->error('不好意思哦，请先登陆一下吧','../../index/index/login');
        }
        $delete=Cookie::delete('safecode');
        $this->success('已退出账户，如有需要请重新登录哦', '../../index/index/login');
    }
    public function person()   //个人信息
    {
        $person=new YlnUser();
        $cookie=Cookie::get('safecode');
        $person_data=YlnUser::getBySafecode($cookie);
        $view=new View();
        $view->assign('user',$person_data);
        return $view->fetch('person',[],['__PUBLIC__'=>'/public/static']);
    }
    public function edit()    //个人信息修改
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
    public  function user_data()    //用户信息
    {
        $Id=input('get.Id');
        $friend=new YlnUser();
        $friend_data=YlnUser::getById($Id)->toArray();
        $view=new View();
        $view->assign('friend',$friend_data);
        return $view->fetch('user_data',[],['__PUBLIC__'=>'/public/static']);
    }
    public  function blog()    //动态程序
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
    public function chat()     //在线聊天
    {
        global $ip_address;
        require_once 'application/ip.php';
        $cookie=Cookie::get('safecode');
        $person_data=YlnUser::getBySafecode($cookie);
        $chat_data=YlnChatData::order('time','asc')->select()->toArray();
        $time1=YlnChatData::order('time','desc')->limit(1)->select()->toArray();

        if(!$time1){
            $time['time']='无';
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
    public function bind()     //绑定用户id与聊天id
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
    public function in_data()   //聊天信息录入
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
    public function online()   //在线用户列表
    {
        global $ip_address;
        require_once 'application/ip.php';
        $ip=$ip_address.':1238';
        Gateway::$registerAddress = $ip;
        $online_id=Gateway::getAllUidList();
        $array=array();
        foreach ($online_id as $uid){
            $user_list=YlnUser::getbyId($uid)->toArray();
            array_push($array,$user_list);
        }
        print_r(json_encode($array));
    }
    public function file(){

        $request = Request::instance();
        $domain = $request->domain();
        $person=new YlnUser();
        $cookie=Cookie::get('safecode');
        $person_data=YlnUser::getBySafecode($cookie);
        if(!$person_data['dir']){
            $this->success("您是老用户，正在为您跳转到空间申请页哦","apply");
        }
        $file_arr=$this->getDir('file/'.$person_data['dir']);
        $dir=$person_data['dir'];
        if(input('post.del')){
            $input=input('post.del');
            $del_file='file/'.$dir.'/'.$input;
            if(unlink($del_file)){
                $this->success("删除成功");
            }
        }
        $view=new View();
        $view->assign('dir',$dir);
        $view->assign('domain',$domain);
        $view->assign('file',$file_arr);
        return $view->fetch('file',[],['__PUBLIC__'=>'/public/static']);
    }
    public function apply(){
        $view=new View();
        $random=random(20,'all');
        $person=new YlnUser();
        $cookie=Cookie::get('safecode');
        if(!mkdir('file/'.$random,0777)){
            $this->error('出了点小问题');
            exit();
        }
        $person_up=YlnUser::where('safecode',$cookie)->update(['dir'=>$random]);
        if($person_up){
                $view->assign('msg','<div class="alert alert-success">
                            处理成功<br>
                            您可以愉快得使用此功能了
                        </div>');
        }else{
            $view->assign('msg','<div class="alert alert-danger">
                            处理失败
                        </div>');
        }
        return $view->fetch('apply',[],['__PUBLIC__'=>'/public/static']);
    }
    public function upload(){//上传文件
        //{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}
        //print_r('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
        $person=new YlnUser();
        $cookie=Cookie::get('safecode');
        $person_data=YlnUser::getBySafecode($cookie);
        $allowedExts = array("php");
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = end($temp);        // 获取文件后缀名
        if (($_FILES["file"]["size"] < 524288000)    // 小于 500 mb
            && !in_array($extension, $allowedExts))
        {
            if ($_FILES["file"]["error"] > 0)
            {
                echo '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}';
            }
            else
            {
                if (file_exists("file/".$person_data["dir"]."/". $_FILES["file"]["name"]))
                {
                    echo '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "文件存在"}, "id" : "id"}';
                }
                else
                {
                    // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
                    move_uploaded_file($_FILES["file"]["tmp_name"], "file/".$person_data["dir"]."/" . $_FILES["file"]["name"]);
                    echo'{"jsonrpc" : "2.0", "result" : null, "id" : "id"}';
                }

            }
        }
        else
        {
            echo '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "文件格式非法."}, "id" : "id"}';
        }

    }
}