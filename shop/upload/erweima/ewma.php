<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>PHP生成标准二维码</title>
<style>
body{font-family:微软雅黑;background-color:#f3f3f3;}
#main{margin:0 auto;border:1px solid #ccc;background-color:#fff;width:750px;margin-top:100px;padding:18px;text-align:center;}
.title{font-size:40px;text-align:center;}
#sea_area_left{margin:0 auto;border:3px solid #E56A6A;padding-left:5px;width:550px;margin-top:15px;height:40px;}
.searchtext{float:left;border:0px;width:350px;padding:5px;font-size:14.5pt;font-family:"Microsoft YaHei","微软雅黑",Verdana !important;}
.searchbtn{height:40px;border:0px;width:189px;font-family:"Microsoft YaHei","微软雅黑",Verdana !important;color:white;padding:3px;background-color:#E56A6A;font-size:15pt;}
* html input#searchbutton{margin-bottom:-1px;height:40px;}
*+html input#searchbutton{margin-bottom:-1px;height:40px;}
</style>
</head>
<body>
<div id="main">
<span class="title">PHP生成标准二维码</span>
<hr>
<?php
if(!empty($_POST['keyword'])){
	//文件输出
    include('phpqrcode.php');
	// 二维码数据
    $data = $_POST['keyword'];
	// 生成的文件名
    $filename = 'ewm.png';
	// 纠错级别：L、M、Q、H
    $errorCorrectionLevel = 'L';
	// 点的大小：1到10
    $matrixPointSize = 4;
    QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
?>
<img src="ewm.png"><br>
成功创建二维码，预览图如上所示<br>有时可能缓存原因，本预览图可能未更新，请手动打开ewm.png文件查看<br>
<a href="ewm.php">重新生成</a>
<?php }else{ ?>
<div id="sea_area_left">
<form name="searchform" method="post" action="/ewm.php">
<input name="keyword" type="text" id="keyword" value="http://" class="searchtext"/>
<input type="submit" id="searchbutton" value="生成" class="searchbtn" />
</form>
</div><br>
请在上面的文本框中输入正确的网址，若网址格式不对，则生成的二维码无效。
<?php } ?>
</div>
<div style="display:none"><script language="javascript" type="text/javascript" src="/include/tongji.js"></script></div>
</body>
</html>