<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:85:"D:\WWW\FlowShow\FlowProject\charge\public/../application/admin\view\ad\add_focus.html";i:1501828285;s:86:"D:\WWW\FlowShow\FlowProject\charge\public/../application/admin\view\public\header.html";i:1490862160;s:86:"D:\WWW\FlowShow\FlowProject\charge\public/../application/admin\view\public\footer.html";i:1489835668;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Language" content="zh-cn" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="author" content="Fhua" />
    <meta name="Copyright" content="BLIT" />
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, initial-scale=1.0,initial-scale=1.0,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title>FlowAdmin后台管理</title>
    <link rel="icon" href="__img__/titlg.png" type="image/x-icon"/>
    <link href="__lay__/css/layui.css" rel="stylesheet" />
    <link href="__css__/style.css" rel="stylesheet" />
    <link href="__css__/search.css" rel="stylesheet" />
    <link href="__font__/font-awesome.css" rel="stylesheet" />

    <script src="__lay__/layui.js"></script>
    <script src="__js__/jquery-1.7.2.min.js"></script>
    <script src="__js__/zySearch.js"></script>
    
</head>
<body>
<style>
/*#default_img{margin-left:20px;}*/
    .site-demo-upload,
    .site-demo-upload img{width: 300px; height: 200px;}
    .site-demo-upload{position: relative; background: #e2e2e2;}
    .site-demo-upload .site-demo-upbar{position: absolute; top: 50%; left: 50%; margin: -18px 0 0 -56px;}
    .site-demo-upload .layui-upload-button{background-color: rgba(0,0,0,.2); color: rgba(255,255,255,1);}
    .upload-img{
        margin-left: 108px;
        margin-top: 10px;
    }
    .upload-img img{
        margin-top: -30px;
    }

    .site-demo-upload-smail,
    .site-demo-upload-smail img{width: 150px; height: 150px;}
    .site-demo-upload-smail{position: relative; background: #e2e2e2;}
    .site-demo-upload-smail .site-demo-upbar{position: absolute; top: 50%; left: 50%; margin: -18px 0 0 -56px;}
    .site-demo-upload-smail .layui-upload-button{background-color: rgba(0,0,0,.2); color: rgba(255,255,255,1);}
    .upload-img-smail{
        margin-left: 108px;
        margin-top: 10px;
    }
    .upload-img-smail img{
        margin-top: -30px;
    }
</style>

<div class="main-wrap">
    <blockquote class="layui-elem-quote fhui-admin-main_hd">
        <h2>添加焦点图</h2>
    </blockquote>
    <form class="layui-form" action="add_article">
        <div class="layui-form-item">
            <label class="layui-form-label">标题</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="title" placeholder="请输入焦点图标题"  lay-verify="title">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">链接地址</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="link_url" placeholder="请输入广告链接地址"  lay-verify="link_url">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <div style="float: left;">
                <label class="layui-form-label">焦点图片</label>
                <div class="site-demo-upload upload-img">
                    <img id="default_img" src="__img__/tong.jpg">
                    <div class="site-demo-upbar">
                            <input type="file" name="file" class="layui-upload-file" id="upload_ad_image">
                            <input type="hidden" id="images" name="img" value="http://www.flows.com/Flow/public/statisc/img/tong.jpg">
                    </div>
                </div>
            </div>

        </div>

        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">广告有效期</label>
                <div class="layui-input-inline" style="width: 30%;">
                    <input type="text" name="start_date" placeholder="开始时间" autocomplete="off" class="layui-input" id="start_time">
                </div>
                <div class="layui-form-mid">-</div>
                <div class="layui-input-inline" style="width: 30%;">
                    <input type="text" name="end_date" placeholder="结束时间" autocomplete="off" class="layui-input" id="end_time">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="order_by" placeholder="排序"  lay-verify="number">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-inline">
                <input type="checkbox" checked="" name="status" lay-skin="switch" lay-filter="switchTest" title="开关"><div class="layui-unselect layui-form-switch layui-form-onswitch"><i></i></div>
            </div>
        </div>


        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="add-role" data-href="<?php echo url('add_focus'); ?>">立即提交</button>
                <a class="layui-btn layui-btn-small do-action" data-type="doGoBack" data-href=""><i class="fa fa-mail-reply"></i>返回上一页</a>
            </div>
        </div>
    </form>

</div>


<div class="shang_box" style="display: none;">
    <div class="shang_tit">
        <p>感谢您的支持，我会继续努力的!</p>
    </div>
    <div class="shang_payimg">
        <img src="__img__/fhua/alipayimg.png" alt="扫码支持" title="扫一扫" />
    </div>
    <div class="pay_explain">扫码打赏，你说多少就多少</div>
    <div class="shang_info">
        <p>打开<span id="shang_pay_txt">支付宝</span>扫一扫，即可进行扫码打赏哦</p>
    </div>
</div>
<script src="__js__/global.js"></script>

</body>
</html>
<script>
    layui.use(['form','common','laydate','upload'], function(){
        var $ = layui.jquery
                ,common=layui.common
                ,laydate = layui.laydate
                ,form = layui.form();

        var start = {
            min: laydate.now()
            ,max: '2099-06-16 23:59:59'
            ,istoday: false
            ,choose: function(datas){
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };

        var end = {
            min: laydate.now()
            ,max: '2099-06-16 23:59:59'
            ,istoday: false
            ,choose: function(datas){
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };

        document.getElementById('start_time').onclick = function(){
            start.elem = this;
            laydate(start);
        }
        document.getElementById('end_time').onclick = function(){
            end.elem = this
            laydate(end);
        }
        //自定义验证规则
        form.verify({
            title:function(value){
                if(value == ""){
                    return '等级名称不能为空';
                }
            }

        });
        //上传广告图片
        layui.upload({
            url: "<?php echo url('Upload/uploadAdImage'); ?>" //上传接口
            ,before: function(input){
                //返回的参数item，即为当前的input DOM对象
                console.log('图片上传中');
            }
            ,title:'上传封面图'
            ,elem: '#upload_ad_image' //指定原始元素，默认直接查找class="layui-upload-file"
            ,method: 'post' //上传接口的http类型
            ,ext: 'jpg|png|gif'
            ,type:'images'
            ,success: function(data){ //上传成功后的回调
                //console.log(res)
                if(data.status == 1){
                    $("#images").val('/upload/testfile/' +data.image_name);
                    $("#default_img").attr('src', '/upload/testfile/' + data.image_name).show();
                }else{
                    alert(data.error_info);
                }

            }
        });
        
        //监听提交
        form.on('submit(add-role)', function(data){
            var sub=true;
            var url=$(this).data('href');
            if(url){
                if(sub){
                    $.ajax({
                        url: url,
                        type: 'post',
                        dataType: 'json',
                        data: data.field,
                        success: function (data) {
                            if (data.code == 1) {
                                // location.href = rturl;
                                common.layerAlertS(data.msg, '提示');
                                window.location.href="<?php echo url('ad/focus_map'); ?>";
                            }
                            else {
                                common.layerAlertE(data.msg, '提示');
                            }
                        },
                        beforeSend: function () {
                            //    // 一般是禁用按钮等防止用户重复提交
                            $(data.elem).attr("disabled", "true").text("提交中...");
                        },
                        //complete: function () {
                        //    $(sbbtn).removeAttr("disabled");
                        //},
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            common.layerAlertE(textStatus, '提示');
                        }
                    });
                }
            }else{
                common.layerAlertE('链接错误！', '提示');
            }

            return false;
        });
    });
</script>
