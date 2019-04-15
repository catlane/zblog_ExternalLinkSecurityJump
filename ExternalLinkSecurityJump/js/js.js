layui.config({
    version: '1522709297490' //为了更新 js 缓存，可忽略
});

layui.use(['laydate', 'laypage', 'layer', 'table', 'carousel', 'upload', 'element','form'], function(){

    layer = layui.layer //弹层
        ,table = layui.table //表格
        ,element = layui.element
        ,form = layui.form; //元素操作

    /**
     * 这是可信域名列表
     */
    //执行一个 table 实例
    table.render({
        elem: '#trusted_domain'
        // ,height: 332
        ,height: 486
        ,url: 'data.php?list=' //数据接口
        ,page: true //开启分页
        ,toolbar: '#toolbarDemo'
        ,defaultToolbar: ['filter', 'print', 'exports']
        ,title: '可信域名列表'
        ,cols: [[ //表头
            {field: 'id', title: 'ID', sort: true}
            ,{field: 'time', title: '添加时间', sort: true}
            ,{field: 'title', title: '标题'}
            ,{field: 'url', title: '链接地址'}
            ,{fixed: 'right', width: 150, align:'center', toolbar: '#barDemo'}
        ]]
    });

    //监听工具条
    table.on('tool(trusted_domain_filter)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data //获得当前行数据
            ,layEvent = obj.event; //获得 lay-event 对应的值
        if(layEvent === 'detail'){
            layer.open({
                type: 1
                ,title: data.title //不显示标题栏
                ,closeBtn: false
                ,area: '300px;'
                ,shade: 0.8
                ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                ,btn: '关闭'
                ,btnAlign: 'c'
                ,moveType: 0 //拖拽模式，0或者1
                ,content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">' + data.url + '</div>'

            });
        } else if(layEvent === 'del'){
            layer.confirm('真的删除行么', function(index){
                //执行删除
                $.get("data.php?del=" + data.id, function(result){
                    $(".layui-laypage-count").html("共 " + result +  " 条");
                    obj.del(); //删除对应行（tr）的DOM结构
                    layer.close(index);
                });
                //向服务端发送删除指令
            });
        } else if(layEvent === 'edit'){
            layer.msg('编辑操作');
        }
    });


    //监听事件
    //监听头工具栏事件
    table.on('toolbar(trusted_domain_filter)', function(obj){
        var checkStatus = table.checkStatus(obj.config.id)
            ,data = checkStatus.data; //获取选中的数据
        switch(obj.event){
            case 'add':
                layer.open({
                    type: 1,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['420px', '240px'], //宽高
                    content: '<div id="add_trusted_domain">\n' +
                        '\t<form class="layui-form" action="" style="width: 380px;height: 186px;padding-top: 20px;">\n' +
                        '  <div class="layui-form-item">\n' +
                        '    <label class="layui-form-label">名称</label>\n' +
                        '    <div class="layui-input-block">\n' +
                        '      <input type="text" name="title" required  lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input">\n' +
                        '    </div>\n' +
                        '  </div>\n' +
                        '\t\t<div class="layui-form-item">\n' +
                        '    <label class="layui-form-label">链接</label>\n' +
                        '    <div class="layui-input-block">\n' +
                        '      <input type="url" name="url" required  value="" lay-verify="required" placeholder="请输入一级域名，则会自动解析为泛域名" autocomplete="off" class="layui-input">\n' +
                        '    </div>\n' +
                        '  </div>\n' +
                        '\n' +
                        '  <div class="layui-form-item">\n' +
                        '    <div class="layui-input-block">\n' +
                        '      <button class="layui-btn" lay-submit lay-filter="add_trusted_domain_btn">立即提交</button>\n' +
                        '    </div>\n' +
                        '  </div>\n' +
                        '</form>\n' +
                        '</div>'
                });
                break;
        };
    });


    form.on('submit(add_trusted_domain_btn)', function(data){
        $.ajax ( {
            type: "post" ,
            url: "data.php?add=" ,
            data: data.field ,
            dataType: "json" ,
            success: function ( res ) {
                if ( res.code == 200 ) {
                    layer.msg ( "添加成功" ,{time:1000},function () {
                        layer.closeAll();
                        window.location.reload ();
                    })
                } else {
                    layer.msg ( res.msg )
                }
            } ,
            error: function ( e ) {
                layer.msg ( "网络异常，Netword Code:" + e.status )
            }
        } )
        // console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    });



    /**
     * 这是日志列表
     */
    //执行一个 table 实例
    table.render({
        elem: '#log'
        ,height: 486
        ,url: 'data.php?loglist=' //数据接口
        ,page: true //开启分页
        ,toolbar: 'true'
        ,defaultToolbar: ['filter', 'print', 'exports']
        ,title: '跳转日志'
        ,cols: [[ //表头
            {field: 'id', title: 'ID', sort: true}
            ,{field: 'url', title: '链接地址'}
            ,{field: 'article_url', title: '引用地址'}
            ,{field: 'address', title: '引用地址'}
            ,{field: 'ip', title: 'ip地址'}
            ,{field: 'ua', title: 'UA'}
            ,{field: 'time', title: '访问时间', sort: true}
        ]]
    });


});