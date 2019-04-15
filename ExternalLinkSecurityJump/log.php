<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('ExternalLinkSecurityJump')) {$zbp->ShowError(48);die();}

$blogtitle='跳转日志';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';


if($_POST && count($_POST) > 0){

    if ( function_exists ( 'CheckIsRefererValid' ) )
        CheckIsRefererValid ();

    foreach ( $_POST as $k => $v ) {
        $zbp->Config('ExternalLinkSecurityJump')->$k = $v;
    }
    $zbp->SaveConfig('ExternalLinkSecurityJump');
    $zbp->SetHint('good', "保存成功");
    Redirect("./main.php");
}
?>
    <link rel="stylesheet" href="/zb_users/plugin/ExternalLinkSecurityJump/layui/css/layui.css" media="all">
    <div id="divMain">
        <div class="divHeader"><?php echo $blogtitle ;?></div>
        <div class="SubMenu">
            <a href="main.php"><span class="m-left">插件配置</span></a>
            <a href="trusted_domain.php"><span class="m-left">可信域名列表</span></a>
            <a href="log.php"><span class="m-left  m-now">跳转日志</span></a>
            <a href="http://www.lovyou.top" target="_blank"><span class="m-right">帮助</span></a>
	        <a href="tencent://message/?uin=2218006427&Menu=yes& Service=300&sigT=42a1e5347953b64c5ff3980f8a6e644d4b31456cb0b6ac6b27663a3c4dd0f4aa14a543b1716f9d45"><span class="m-right">QQ</span></a>
        </div>
        <div id="divMain2">
            <!--代码-->
            <table class="layui-hide" id="log" lay-filter="log_filter"></table>



            <script src="/zb_users/plugin/ExternalLinkSecurityJump/layui/layui.js"></script>
            <script src="/zb_users/plugin/ExternalLinkSecurityJump/js/js.js"></script>
        </div>
    </div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>