<?php
/**
 * Created by PhpStorm.
 * User: 猫巷
 * Email:catlane@foxmail.com
 * Date: 2019/1/11
 * Time: 2:09 PM
 */
/**
 * 数据库信息列表
 */
$external_link_security_jump_database = array (
    /**
     * 社交账户绑定表
     */
    'external_link_security_jump_log' => array (
        'name' => '%pre%external_link_security_jump_log' ,
        'info' => array (
            'ID' => array ( 'id' , 'integer' , '' , 0 ) ,
            'Url' => array ( 'url' , 'string' , 255 , '' ) ,
            'ArticleUrl' => array ( 'article_url' , 'string' , 255 , '' ) ,
            'Ip' => array ( 'ip' , 'string' , 255 , '' ) ,
            'Address' => array ( 'address' , 'string' , 255 , '' ) ,
            'UA' => array ( 'ua' , 'string' , 255 , '' ) ,
            'time' => array ( 'time' , 'string' , 255 , '' ) ,
        ) ,
    ) ,
);

foreach ( $external_link_security_jump_database as $k => $v ) {
    $table[ $k ] = $v[ 'name' ];
    $datainfo[ $k ] = $v[ 'info' ];
}
/**
 * 检查是否有创建数据库
 */
function ExternalLinkSecurityJump_CreateTable () {
    global $zbp , $external_link_security_jump_database;
    foreach ( $external_link_security_jump_database as $k => $v ) {
        if ( ! $zbp->db->ExistTable ( $v[ 'name' ] ) ) {
            $s = $zbp->db->sql->CreateTable ( $v[ 'name' ] , $v[ 'info' ] );
            $zbp->db->QueryMulit ( $s );
        }
    }
}
function ExternalLinkSecurityJump_DeleteTable () {
    global $zbp ;
    $zbp->db->DelTable ( '%pre%external_link_security_jump_log' );
}
