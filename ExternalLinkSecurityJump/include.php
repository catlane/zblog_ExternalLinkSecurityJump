<?php



include $zbp->path . 'zb_users/plugin/ExternalLinkSecurityJump/database/index.php';
#注册插件

RegisterPlugin ( "ExternalLinkSecurityJump" , "ActivePlugin_ExternalLinkSecurityJump" );

function ActivePlugin_ExternalLinkSecurityJump () {
    global $zbp;
    if ( $zbp->Config ( 'ExternalLinkSecurityJump' )->active == 1 ) {//开启的状态，才会用到
        Add_Filter_Plugin ( 'Filter_Plugin_ViewPost_Template' , 'ExternalLinkSecurityJump_Content' );//处理文章页模板接口
        Add_Filter_Plugin ( 'Filter_Plugin_Index_Begin' , 'ExternalLinkSecurityJump_Main' );
    }
}

/**
 * 如果是这个url，那么就跳转到这里
 */
function ExternalLinkSecurityJump_Main () {
    global $zbp;
    if ( strpos ( $zbp->currenturl , 'external_link_security_jump.html' ) !== false ) {
        ExternalLinkSecurityJump_View ();
    }
    if ( strpos ( $zbp->currenturl , 'external_link_security_jump_add_log.html' ) !== false ) {
        ExternalLinkSecurityJump_Add_Log ();
    }
}


/**
 * 添加日志
 */
function ExternalLinkSecurityJump_Add_Log () {
    if ( count ( $_POST ) ) {
        global $zbp;
        $article_url = isset( $_POST[ 'article_url' ] ) ? $_POST[ 'article_url' ] : '';
        $url = isset( $_POST[ 'url' ] ) ? $_POST[ 'url' ] : '';
        $ip = $_SERVER[ "REMOTE_ADDR" ];
        if ( ! isset( $_SESSION[ 'ip' ] ) || $_SESSION[ 'ip' ] != $ip ) {//不一样,如果没有，就存进去
            $_SESSION[ 'ip' ] = $ip;
            $address = ExternalLinkSecurityJump_City ( $ip );
            $_SESSION[ 'address' ] = $address;
        } else {//一样，直接取出来，避免查询多次导致无数据
            $address = $_SESSION[ 'address' ];
        }


        $ua = ExternalLinkSecurityJump_Browser ();

        $db = $zbp->db->sql;
        $sql = $db->Insert ( 'zbp_external_link_security_jump_log' , array (
            'article_url' =>$article_url ,
            'url' => urldecode (str_replace ('/external_link_security_jump.html?jump_url=','',$url)) ,
            'ip' => $ip ,
            'address' => $address ,
            'ua' => $ua ,
            'time' => date ( 'Y-m-d H:i:s' , time () )
        ) );
        $zbp->db->QueryMulit ( $sql );
    }
    echo json_encode ( [ 'code' => 200 ] );
    die;
}

/**
 * 获取访问ip地址
 * @param $ip
 * @return mixed
 */
function ExternalLinkSecurityJump_City ( $ip ) {

    $data = file_get_contents ( 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip );

    $data = json_decode ( $data , $assoc = true );
    return $data['data'][ 'country' ] . $data['data'][ 'region' ] . $data['data'][ 'city' ] . $data['data'][ 'county' ];

}


/**
 * 获取访问地区
 * @return string
 */
function ExternalLinkSecurityJump_Browser () {

    $agent = $_SERVER[ "HTTP_USER_AGENT" ];

    if ( strpos ( $agent , 'MSIE' ) !== false || strpos ( $agent , 'rv:11.0' ) ) //ie11判断

        return "Ie";

    else if ( strpos ( $agent , 'Firefox' ) !== false )

        return "Firefox";

    else if ( strpos ( $agent , 'Chrome' ) !== false )

        return "Chrome";

    else if ( strpos ( $agent , 'Opera' ) !== false )

        return 'Opera';

    else if ( ( strpos ( $agent , 'Chrome' ) == false ) && strpos ( $agent , 'Safari' ) !== false )

        return 'Safari';

    else

        return 'Unknown';

}

/**
 *自动替换文章内外链
 */
function ExternalLinkSecurityJump_Content ( &$template ) {

    global $zbp;
    $article = $template->GetTags ( 'article' );
    $content = $article->Content;


    $reg1 = '/<a(.*?) href=\"(.*?)\"(.*?)>(.*?)<\/a>/i';//匹配所有A标签

    //1.全部2,前边的内容3.链接，4是后边的内容,5是链接内容


    //获取当前域名
    $host = $_SERVER[ 'SERVER_NAME' ];
    preg_match_all ( $reg1 , $content , $arr );




    $nofollow = $zbp->Config ( 'ExternalLinkSecurityJump' )->nofollow ? 'rel="nofollow"' : '';
    foreach ( $arr[ 2 ] as $k => $v ) {//循环链接
        if ( strpos ( $v , 'script' ) !== false ) {//说明是js
            continue;
        }
        $beforeOther = preg_replace ( '/target=".*?"/' , ' ' , $arr[ 1 ][ $k ] );//将里面的跳转方式去删除掉
        $afterOther = preg_replace ( '/target=".*?"/' , ' ' , $arr[ 3 ][ $k ] );//将里面的跳转方式去删除掉


        $other = $beforeOther . ' ' . $afterOther;
        if ( strpos ( $v , $host ) === false ) {//说明是外链
            $url = '<a class="external_link_security_jump" href="/external_link_security_jump.html?jump_url=' . urlencode ( $v ) . '" target="_blank" ' . $nofollow . $other . '>' . $arr[ 4 ][ $k ] . '</a>';
            $content = str_replace ( $arr[ 0 ][ $k ] , $url , $content );
        }
    }
    $content .= <<<eof
<script>
    article_url = window.location.href;
    $('.external_link_security_jump').click(function() {
        var url = $(this).attr('href');
        console.log(url);
         $.ajax ( {
            type: "post" ,
            url: "external_link_security_jump_add_log.html" ,
            data: {
                article_url : article_url,
                url : url
            } ,
            dataType: "json" ,
            success: function ( res ) {
                // return false;
            } 
        } )
        
    })
   
</script>
eof;

    $article->Content = $content;
    $template->SetTags ( 'article' , $article );
}


/**
 * 根据不用的theme，显示不同的主题
 */
function ExternalLinkSecurityJump_View () {
    global $zbp;
    include $zbp->path . 'zb_users/plugin/ExternalLinkSecurityJump/theme/theme_' . $zbp->Config ( 'ExternalLinkSecurityJump' )->theme . '.php';
    die;
}

function InstallPlugin_ExternalLinkSecurityJump () {
    global $zbp;
    $zbp->Config ( 'ExternalLinkSecurityJump' )->auto_jump_time = 5;
    $zbp->Config ( 'ExternalLinkSecurityJump' )->active = 0;
    $zbp->Config ( 'ExternalLinkSecurityJump' )->nofollow = 0;
    $zbp->Config ( 'ExternalLinkSecurityJump' )->theme = 2;
    $zbp->Config ( 'ExternalLinkSecurityJump' )->logActive = 0;
    $zbp->SaveConfig ( 'ExternalLinkSecurityJump' );
}

function UninstallPlugin_ExternalLinkSecurityJump () {
    ExternalLinkSecurityJump_DeleteTable ();//删除库
}


