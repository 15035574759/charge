<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:84:"D:\WWW\FlowShow\FlowProject\charge\public/../application/admin\view\index\index.html";i:1499409563;s:86:"D:\WWW\FlowShow\FlowProject\charge\public/../application/admin\view\public\header.html";i:1490862160;s:86:"D:\WWW\FlowShow\FlowProject\charge\public/../application/admin\view\public\footer.html";i:1489835668;}*/ ?>
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
<style type="text/css">
    .layui-nav-tree .layui-nav-child a:hover { color: #fff; background: #4E5465; }
    /*sidebar mini*/
    .layui-layout-admin .sidebar-mini { width: 70px; }
    .layui-layout-admin .sidebar-mini .layui-side-scroll { width: 100px; }
    .layui-layout-admin .sidebar-mini #sidebar { width: 70px; }
    .layui-layout-admin .sidebar-mini .layui-nav-tree { width: 70px; }
    .layui-layout-admin .sidebar-mini .layui-nav .layui-nav-item a { padding: 0 15px; }
    .layui-layout-admin .sidebar-mini .layui-nav-item a cite { display: none; }
    #sidebar { height: 40px; width: 200px; background: #4A5064; text-align: center; line-height: 40px !important; font-size: 18px; user-select: none; cursor: pointer; -webkit-user-select: none; -moz-user-select: none; }
</style>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header header header-admin">
        <div class="layui-main">
            <div class="fhuaui-logo">
                <a class="logo" href="#">
                    <img src="__img__/fhua/fhua-logo1.png" alt="FhuAdmin" />
                </a>
            </div>
           <!-- <ul class="layui-nav">
                <li class="layui-nav-item">
                    <a href="javascript:;" class="admin-header-user">
                        <img src="/public/static/images/0.jpg" />
                        <span>beginner</span>
                    </a>
                    <dl class="layui-nav-child">
                        <dd>
                            <a href="javascript:;"><i class="fa fa-user-circle" aria-hidden="true"></i> 个人信息</a>
                        </dd>
                        <dd>
                            <a href="javascript:;"><i class="fa fa-gear" aria-hidden="true"></i> 设置</a>
                        </dd>
                        <dd id="lock">
                            <a href="javascript:;">
                                <i class="fa fa-lock" aria-hidden="true" style="padding-right: 3px;padding-left: 1px;"></i> 锁屏 (Alt+L)
                            </a>
                        </dd>
                        <dd>
                            <a href="login.html"><i class="fa fa-sign-out" aria-hidden="true"></i> 注销</a>
                        </dd>
                    </dl>
                </li>
            </ul>-->
            <!--  <ul class="layui-nav" id="menu">
                  <li class="layui-nav-item">
                      <a href="javascript:;" data-fid="85">
                          <i class="fa fa-send-o"></i>
                          <cite>站点</cite>
                      </a>
                  </li>
                  <li class="layui-nav-item site-nav-layim">
                      <a href="javascript:;" data-fid="99">
                          <i class="fa fa-cube"></i>
                          <cite>应用</cite>
                      </a>
                  </li>
                  <li class="layui-nav-item site-nav-layim">
                      <a href="javascript:;" data-fid="4">
                          <i class="fa fa-server"></i>
                          <cite>界面</cite>
                      </a>
                  </li>
                  <li class="layui-nav-item site-nav-layim">
                      <a href="javascript:;" data-fid="5">
                          <i class="fa fa-cog"></i>
                          <cite>配置</cite>
                      </a>
                  </li>
              </ul>-->


            <div class="nav-user site-nav-layim">
                <a class="avatar" href="/user/">
                    <img src="http://q.qlogo.cn/qqapp/101235792/3DC0A1C44E35263F3C44E95D0BEBF7DD/100" />
                    <cite><?php echo $rolename; ?></cite>
                </a>

                <div class="nav">

                    <a class="do-admin" data-type="doLoginOut" data-href="<?php echo url('admin/login/loginOut'); ?>" data-rturl="<?php echo url('admin/login/index'); ?>"><i class="fa fa-dot-circle-o"></i>退出</a>

                </div>
            </div>
        </div>
    </div>
    <div id="sidebar-side" class="layui-side layui-bg-black">
        <div id="admin-navbar-side" class="layui-side-scroll" lay-filter="side">
        </div>
    </div>
    <div id="admin-body" class="layui-body" style="bottom: 0;">
        <div class="layui-tab layui-tab-card admin-nav-card" lay-filter="admin-tab">
            <ul class="layui-tab-title" id="admin-tab">
                <li class="layui-this">
                    <i class="layui-icon" style="top: 2px; font-size: 16px;">&#xe609;</i>
                    <cite>FlowAdmin后台管理</cite>
                </li>
            </ul>
            <div class="layui-tab-content" id="admin-tab-container" style="min-height: 150px; padding: 0px;">
                <div class="layui-tab-item layui-show">
                    <iframe name="mainframe" frameborder="0" src="<?php echo url('index/center'); ?>"></iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-footer footer admin-footer">
        <div class="layui-main">
            <p>COPYRIGHT &#169; 2017 Andy. FlowAdmin V1.0后台管理系统 技术支持：qinlh</p>
            <p>
                <a id="pay" href="javascript:;">捐赠作者</a>
                <a id="git" href="javascript:;">Git仓库</a>
                <a id="weibo" href="javascript:;">微博</a>
                <a id="weixin" href="javascript:;">微信公众号</a>
            </p>
        </div>
    </div>
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
<script src="__js__/_admin.js"></script>
<script src="__common__/common.js"></script>
<script>
// var str = ['1','2','3','4','5']
// var item = str[1];
// str[1] = str[2];
// str[2] = item;
// // var arr = str[1] = str[2];

// alert(str);
</script>