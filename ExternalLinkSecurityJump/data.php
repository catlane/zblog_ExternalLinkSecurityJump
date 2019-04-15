<?php
// +---------------------------------------------------------------------+
// | 猫巷 [ WE CAN DO IT JUST THINK ]                                     |
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://www.lovyou.top All rights reserved.  |
// +---------------------------------------------------------------------+
// | Author: yangyuhui <2218006427@qq.com>                               |
// +---------------------------------------------------------------------+
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
class data{
    protected $zbp;
    public function __construct()
    {
        global $zbp;
        $this->zbp = $zbp;
        $this->zbp->Load();
    }
    public function all(){
        if($_GET['page'] && $_GET['limit']){
            $page = $_GET['page'];
            $limit = $_GET['limit'];
            $data = $this->zbp->Config('ExternalLinkSecurityJump')->data;
            $start = ( $page - 1 ) * $limit; //数组下标,k
            $limitdata = array();

            if(!is_array($data)){
                $data = array();
            }
            foreach ($data as $k => $v){
                //等k大于等于的时候开始追加，追到最大长度
//                0
                $v[ 'url' ] = '*.' . $v[ 'url' ];
                if($k >= $start && $k <= ($start + $limit - 1)){
                    array_push($limitdata, $v);
                }

            }

            $arr['code'] = 0;
            $arr['msg'] = "";
            $arr["count"] = count($data);
            $arr["data"] = $limitdata;


            echo json_encode($arr,true);
        }
    }
    public function del(){
        if($_GET['del']){
            $id = $_GET['del'];
            $data = $this->zbp->Config('ExternalLinkSecurityJump')->data;
            $newData = array();
            foreach ($data as $key => $value) {
                if($value['id'] != $id){
                    array_push($newData, $value);
                }
            }
            $data = $newData;
            $this->zbp->Config('ExternalLinkSecurityJump')->data = $data;
            $this->zbp->SaveConfig('ExternalLinkSecurityJump');
            $this->zbp->SetHint('good', "删除成功");
            echo json_encode(count($data),true);
        }
    }


    public function add () {
        try {
            global $zbp;
            //仿sql创建主键自增
            $oldData = $zbp->Config('ExternalLinkSecurityJump')->data ? : [];

            end ( $oldData );
            $endKey = count($oldData) ? $oldData[key ( $oldData )]['id'] : 0 ;
            $endKey ++;
            $data['id'] = $endKey;
            $data['title'] = $_POST['title'];
            $data['url'] = $_POST['url'];
            //先去掉https://或者http://
            $data['url'] = str_replace ( 'http://' , '' , $data['url'] );
            $data['url'] = str_replace ( 'https://' , '' , $data['url'] );
            $pos = explode ( '.' , $data['url'] );
            end ( $pos );
            $endKey = key ( $pos );
            if ( isset( $pos[ $endKey - 1 ] ) && $pos[ $endKey - 1 ] ){
                $data['url'] = $pos[ $endKey - 1 ] . '.' . $pos[ $endKey ];
            }else{
                throw new \Exception ( '域名不规范！' );
            }
            $data['time'] = date ('Y-m-d H:i:s',time());
            //获取旧数据

            array_push($oldData, $data);
            $zbp->Config('ExternalLinkSecurityJump')->data = $oldData;
            $zbp->SaveConfig('ExternalLinkSecurityJump');
            echo json_encode (array('code' => 200, 'msg' => '添加成功'));
        } catch (\Exception $exception) {
            echo json_encode (array('code' => 0, 'msg' => $exception->getMessage ()));
        }
    }
    public function loglist(){
        global $zbp;
        if ( !$zbp->Config ( 'ExternalLinkSecurityJump' )->logActive) {
            $arr['code'] = 0;
            $arr['msg'] = "已关闭";
            $arr["count"] = 0;
            $arr["data"] = [];
            echo json_encode($arr,true);die;
        }
        if($_GET['page'] && $_GET['limit']){
            $page = $_GET['page'];
            $limit = $_GET['limit'];


            $start = ( $page - 1 ) * $limit + 1; //数组下标,k

//            public function Select($table, $select = null, $where = null, $order = null, $limit = null, $option = null)

            $sql = $zbp->db->sql->Select ( '%pre%external_link_security_jump_log','*','',['time'=>'desc'] ,"{$start},{$limit}");
            $data = $zbp->db->Query ( $sql );

            $total = $zbp->db->Query ( 'select count(*) as total from zbp_external_link_security_jump_log' );
            $total = is_array ($total) && $total['0'] ? $total[0]['total'] : 0;



            $arr['code'] = 0;
            $arr['msg'] = "";
            $arr["count"] = $total;
            $arr["data"] = $data;


            echo json_encode($arr,true);
        }
    }
}

$data = new data();
//这里根据请求获取数据
if(isset($_GET['list'])){
    $data->all();
}else if(isset($_GET['del'])){
    $data->del();
} else if(isset($_GET['add'])){
    $data->add();
} else if(isset($_GET['loglist'])){
    $data->loglist();
}else{
    echo json_encode(array('code' => 1, 'msg' => '非法请求'), true);
    die;
}

