<?php
namespace app\api\controller;

use \think\Model;
class YlnUser extends Model
{
    protected $resultSetType='collection';
    protected $createTime = false;
    protected $updateTime = false;
}
class YlnUrl extends Model
{
    protected $resultSetType='collection';
    protected $createTime = false;
    protected $updateTime = false;
}
class Api extends \think\Controller
{
    public function api()
    {
        $get_data=input('get.');
        if(!$get_data['url'] or !$get_data['code']){
            $message=[
                'message'=>'error'
            ];
            exit(json_encode($message));
        }
        $user=YlnUser::getbyCode($get_data['code'])->toArray();
        $uid=$user['Id'];
        $domain=$get_data['url'];
        $url=YlnUrl::getByDomain($get_data['url']);   //从数据库查找有无此域名存在
        if($url){
            $message=[
                'message'=>'error,存在此域名'
            ];
            exit(json_encode($message));
        }
        $identity_code=random(32,'string',0);
        $web_data=new YlnUrl;
        $web_data->data([
            'uid'=>$uid,
            'domain'=>$domain,
            'identity_code'=>$identity_code
        ]);
        if($web_data->save()){
            $message=[
                'message'=>'success',
                'identity_code'=>$identity_code
            ];
            print_r(json_encode($message));
        }
    }
}
