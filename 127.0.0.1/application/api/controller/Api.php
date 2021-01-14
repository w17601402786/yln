<?php
namespace app\api\controller;

use \think\Controller;
use think\Model;

class YlnIndex extends Model
{
    protected $resultSetType='collection';
}
class Api
{
    public function api()
    {
        global $identity_code;
        require_once 'application/identity.php';
        $op=input('get.op');
        $get_identity_code=input('get.identity_code');
        if($get_identity_code == $identity_code) {
            switch ($op){
                case 'update':
                break;
                case 'select':
                    $web_data=YlnIndex::get(1)->toArray();
                    if($web_data){
                        print_r(json_encode($web_data));
                    }else{
                        $return['message']='error';
                        print_r(json_encode($return));
                    }
                break;
                case 'update_data':

                    if(input('get.name' && input('get.copy'))){
                        $data['name']=input('get.name');
                        $data['copy']=input('get.copy');
                        $web=new YlnIndex();
                        $update=$web->allowField(true)->save($data,['Id'=>'1']);
                        if($update){
                            $return['message']='success';
                            print_r(json_encode($return));
                        }else{
                            $return['message']='error';
                            print_r(json_encode($return));
                        }
                    }else{
                        $return['message']='error';
                        print_r(json_encode($return));
                    }
                break;
            }
        }else{
            $return['message']='error';
            print_r(json_encode($return));
        }
    }
}