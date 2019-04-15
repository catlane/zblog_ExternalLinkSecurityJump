<?php

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load ();

$action = 'root';
if ( ! $zbp->CheckRights ( $action ) ) {
    $zbp->ShowError ( 6 );
    die();
}
if ( ! $zbp->CheckPlugin ( 'ExternalLinkSecurityJump' ) ) {
    $zbp->ShowError ( 48 );
    die();
}

$blogtitle = '文章外链转内链安全跳转';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';


if ( $_POST && count ( $_POST ) > 0 ) {

    if ( function_exists ( 'CheckIsRefererValid' ) )
        CheckIsRefererValid ();

    foreach ( $_POST as $k => $v ) {
        $zbp->Config ( 'ExternalLinkSecurityJump' )->$k = $v;
    }
    //然后这里判断是否开启了，
    if ( $_POST[ 'logActive' ] ) {//创建库
        ExternalLinkSecurityJump_CreateTable ();
    }else{//删除库
        ExternalLinkSecurityJump_DeleteTable ();
    }
    $zbp->SaveConfig ( 'ExternalLinkSecurityJump' );
    $zbp->SetHint ( 'good' , "保存成功" );
    Redirect ( "./main.php" );
}
?>
	<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu">
            <a href="main.php"><span class="m-left m-now">插件配置</span></a>
            <a href="trusted_domain.php"><span class="m-left">可信域名列表</span></a>
            <a href="log.php"><span class="m-left">跳转日志</span></a>
            <a href="http://www.lovyou.top" target="_blank"><span class="m-right">帮助</span></a>
	  <a href="tencent://message/?uin=2218006427&Menu=yes& Service=300&sigT=42a1e5347953b64c5ff3980f8a6e644d4b31456cb0b6ac6b27663a3c4dd0f4aa14a543b1716f9d45"><span class="m-right">QQ</span></a>
  </div>
  <div id="divMain2">
        <form action="" method="post">
	        <?php if ( function_exists ( 'CheckIsRefererValid' ) ) {
                echo '<input type="hidden" name="csrfToken" value="' . $zbp->GetCSRFToken () . '">';
            } ?>
	        <table border="1" class="tableFull tableBorder tableBorder-thcenter" style="max-width: 1000px">
                <thead>
                <tr>
                    <th width="200px">配置名称</th>
                    <th>配置内容</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>启用文章站内安全跳转</td>
                    <td>
                        <input name="active" type="text" class="checkbox" style="display:none;" value="<?php echo $zbp->Config ( 'ExternalLinkSecurityJump' )->active; ?>" />
                    </td>
                </tr>
                <tr>
                    <td>屏蔽蜘蛛爬取</td>
                    <td>
                        <input name="nofollow" type="text" class="checkbox" style="display:none;" value="<?php echo $zbp->Config ( 'ExternalLinkSecurityJump' )->nofollow; ?>" />
                    </td>
                </tr>
                <tr>
                    <td>开启日志</td>
                    <td>
                        <input name="logActive" type="text" class="checkbox" style="display:none;" value="<?php echo $zbp->Config ( 'ExternalLinkSecurityJump' )->logActive; ?>" />
	                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;如果停用后将会删除日志数据表！
                    </td>
                </tr>
                <tr>
                    <td>自动跳转时长(秒为单位)</td>
                    <td>
                        <input type="number" class="text-config" name="auto_jump_time" value="<?php echo $zbp->Config ( 'ExternalLinkSecurityJump' )->auto_jump_time; ?>" />
	                    可信链接才会跳转，未验证链接则会有风险提示不会自动跳转！
                    </td>
                </tr>
                 <tr>
                    <td>选择主题样式</td>
                    <td>
                        <select name="theme" class="edit" id="themes">
                            <option value="1" <?php if ( $zbp->Config ( 'ExternalLinkSecurityJump' )->theme == 1 )
                                echo 'selected="selected"'; ?>>主题1</option>
                            <option value="2" <?php if ( $zbp->Config ( 'ExternalLinkSecurityJump' )->theme == 2 )
                                echo 'selected="selected"'; ?>>主题2</option>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="submit" value="保存配置" style="margin: 0; font-size: 1em;" />
        </form>
        <style>
            .readme {
	            max-width: 1000px;
	            padding: 10px;
	            margin-bottom: 10px;
	            background: #f9f9f9;
            }

            .readme h3 {
	            font-size: 16px;
	            font-weight: normal;
	            color: #000;
            }

            .readme ul li {
	            margin-bottom: 5px;
	            line-height: 30px;
            }

            .readme a {
	            color: #333 !important;
	            text-decoration: underline;
            }

            .readme code {
	            display: inline-block;
	            margin: 0 5px;
	            padding: 0 8px;
	            line-height: 25px;
	            font-size: 12px;
	            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
	            color: #1a1a1a;
	            border-radius: 4px;
	            background: #eee;
            }

            .readme code.copy {
	            cursor: pointer;
            }

            .readme-item {
	            -webkit-display: flex;
	            display: flex;
	            margin-bottom: 10px;
            }

            .readme-item .name {
	            display: block;
	            width: 100px;
	            height: 24px;
	            line-height: 24px;
            }

            .readme-item .preview {
	            display: block;
	            width: 300px;
            }

            .readme-item .options {
	            display: block;
	            width: 300px;
	            height: 24px;
            }

            .readme-item .code-pre {
	            display: none;
            }

            .readme-item .copy-btn {
	            display: inline-block;
	            width: 64px;
	            height: 24px;
	            margin: 0;
	            margin-left: 10px;
	            padding: 0;
	            line-height: 24px;
	            font-size: 13px;
	            color: #fff;
	            border: none;
	            border-radius: 2px;
	            background: #3a6ea5;
	            cursor: pointer;
            }

            .readme-item .copy-btn:active,
            .readme-item .copy-btn:focus {
	            outline: 0;
            }

            .readme-item .copy-btn:active {
	            opacity: .95;
            }
        </style>
        <div class="readme">
            <h3>插件配置说明</h3>
            <ul>
                <li>- 插件会自动提取文章中的外链并进行转换，不需要进行额外操作。</li>
                <li>- 插件不会修改任何ZBlog数据，这很好地保护了你的数据安全。任何情况下删除该插件均不会留下痕迹。</li>
                <li>- 插件可以将链接设置为防止蜘蛛爬取，有效提供SEO优化。</li>
                              <li>- 插件可以记录点击的链接地址、引用地址、点击时间、点击者IP等。</li>
                              <li>- 插件可以添加可信域名，可信域名将自动跳转，无验证域名则提示有风险，谨慎跳转。</li>
                              <li>- 插件目前提供了两款不同的主题，用户可以根据自己的喜好改变。</li>
            </ul>
        </div>
    </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime ();
?>