<?php
namespace app\install\controller;

use think\Request;
use \think\View;
use \think\Db;
use \think\controller;
class Install
{
    public function install(){
               if(input('get.p')=='2'){
            if(file_exists('install.lock')){
                exit('安装完成！如需重新安装，请删除网站目录下的install.lock!');
            }

            if(input('post.'))
            {
                $data=input('post.');
                //Db::connect('mysql://'.$data['host_user'].':'.$data['host_password'].'@'.$data['host_address'].':'.$data['host_port'].'/'.$data['host_name'].'#utf8');
                $db=mysqli_connect($data['host_address'].':'.$data['host_port'],$data['host_user'],$data['host_password'],$data['host_name']);
                if($db)
                {
                    $domain=Request::instance()->domain();
                    $url = 'http://www.e-memories.cn/index.php/api/api/api?url='.$domain.'&code='.$data['code'];
                    $ch = curl_init();
                    $timeout = 5;
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                    $upload = curl_exec($ch);
                    curl_close($ch);
                    if($upload){
                        $return=json_decode($upload,true);
                        $data4='<?php
                $identity_code="'.$return["identity_code"].'";
                ';
                        @file_put_contents('application/identity.php',$data4);
                    }

                    $data2 = "<?php
                //忆流年小站-数据库文件
                //请在这里改数据库配置
return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '{$data['host_address']}',
    // 数据库名
    'database'        => '{$data['host_name']}',
    // 用户名
    'username'        => '{$data['host_user']}',
    // 密码
    'password'        => '{$data['host_password']}',
    // 端口
    'hostport'        => '{$data['host_port']}',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => '',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 自动读取主库数据
    'read_master'     => false,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'collection',
    // 自动写入时间戳字段
    'auto_timestamp'  => 'datetime',
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
];";
                    @file_put_contents('application/database.php', $data2);
                    $data3='<?php
                $ip="'.$_SERVER['SERVER_ADDR'].'";
                ';
                    @file_put_contents('application/ip.php', $data3);
                    $sqls=file_get_contents('install.sql');
                    $sqls=explode(';',$sqls);
                    $success = 0;
                    $error=0;
                    foreach ($sqls as $sql){
                        $str=trim($sql);
                        if(!empty($str)){
                            if(mysqli_query($db,$sql)){
                                $success++;
                            }else{
                                $error++;
                            }
                        }
                    }
                    @file_put_contents('install.lock','');
                    $view=new View();
                    $view->assign('success',$success);
                    $view->assign('error',$error);
                    return $view->fetch('install2',[],['__PUBLIC__'=>'../../../public/static']);
                }else{
                    exit('数据库连接失败');
                }
            }

        }else{
            $view=new View();
            return $view->fetch('install',[],['__PUBLIC__'=>'../../../public/static']);
        }

    }
}