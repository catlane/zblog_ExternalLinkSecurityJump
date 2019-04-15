<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('ExternalLinkSecurityJump')) {$zbp->ShowError(48);die();}

$blogtitle='可信域名';
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
	<style>
		.layui-layer-btn0,.layui-layer-btn0:hover{
			color: #fff !important;
		}
	</style>
	<link rel="stylesheet" href="/zb_users/plugin/ExternalLinkSecurityJump/layui/css/layui.css" media="all">
	<div id="divMain">
        <div class="divHeader"><?php echo $blogtitle ;?></div>
        <div class="SubMenu">
            <a href="main.php"><span class="m-left">插件配置</span></a>
            <a href="trusted_domain.php"><span class="m-left m-now">可信域名列表</span></a>
            <a href="log.php"><span class="m-left">跳转日志</span></a>
            <a href="http://www.lovyou.top" target="_blank"><span class="m-right">帮助</span></a>
            <a href="tencent://message/?uin=2218006427&Menu=yes& Service=300&sigT=42a1e5347953b64c5ff3980f8a6e644d4b31456cb0b6ac6b27663a3c4dd0f4aa14a543b1716f9d45"><span class="m-right">QQ</span></a>
        </div>
        <div id="divMain2">
	        添加的域名会自动改为泛解析域名，例：输入 <b>http://www.baidu.com</b>，会自动适配为 <b>baidu.com</b>!
            <!--代码-->
            <table class="layui-hide" id="trusted_domain" lay-filter="trusted_domain_filter"></table>

	        <script type="text/html" id="toolbarDemo">
			  <div class="layui-btn-container">
			    <button class="layui-btn layui-btn-sm" lay-event="add">添加</button>
			  </div>
			</script>
            <script type="text/html" id="barDemo">
                <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="detail">查看</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del" style="color: #fff !important;">删除</a>
            </script>

            <script src="/zb_users/plugin/ExternalLinkSecurityJump/layui/layui.js"></script>
            <script src="/zb_users/plugin/ExternalLinkSecurityJump/js/js.js"></script>
        </div>
    </div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>