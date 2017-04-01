<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"D:\WWW\charge\public/../application/admin\view\index\index.html";i:1489112302;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="__PUBLIC__/javascript/"/>
<script type="text/javascript" src="js/jquery-3.0.0.js"></script>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
	<center>
		<h1>111</h1>
		<form action="" method="post">
			<table border="1" cellspacing="0" cellpadding="0">
				<tr>
					<td>用户名</td>
					<td><input type="text" name=""></td>
				</tr>
				<tr>
					<td>密码</td>
					<td><input type="text" name=""></td>
				</tr>
				<tr align="center">
					<td colspan="2">
						<input type="button" id="submit" value="提交">
					</td>
				</tr>
			</table>
		</form>
	</center>
</body>
</html>
<script>
$(function(){
	$("#submit").click(function(){
		var han = 'list';
		var url = "http://h5.qlh520.top/media/public/index.php/port/Userreg/listdata";
		$.post(url,{han:han},function(msg){
			alert(msg);
		},"json")
	})
})
</script>