<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>PHP���ɱ�׼��ά��</title>
<style>
body{font-family:΢���ź�;background-color:#f3f3f3;}
#main{margin:0 auto;border:1px solid #ccc;background-color:#fff;width:750px;margin-top:100px;padding:18px;text-align:center;}
.title{font-size:40px;text-align:center;}
#sea_area_left{margin:0 auto;border:3px solid #E56A6A;padding-left:5px;width:550px;margin-top:15px;height:40px;}
.searchtext{float:left;border:0px;width:350px;padding:5px;font-size:14.5pt;font-family:"Microsoft YaHei","΢���ź�",Verdana !important;}
.searchbtn{height:40px;border:0px;width:189px;font-family:"Microsoft YaHei","΢���ź�",Verdana !important;color:white;padding:3px;background-color:#E56A6A;font-size:15pt;}
* html input#searchbutton{margin-bottom:-1px;height:40px;}
*+html input#searchbutton{margin-bottom:-1px;height:40px;}
</style>
</head>
<body>
<div id="main">
<span class="title">PHP���ɱ�׼��ά��</span>
<hr>
<?php
if(!empty($_POST['keyword'])){
	//�ļ����
    include('phpqrcode.php');
	// ��ά������
    $data = $_POST['keyword'];
	// ���ɵ��ļ���
    $filename = 'ewm.png';
	// ������L��M��Q��H
    $errorCorrectionLevel = 'L';
	// ��Ĵ�С��1��10
    $matrixPointSize = 4;
    QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
?>
<img src="ewm.png"><br>
�ɹ�������ά�룬Ԥ��ͼ������ʾ<br>��ʱ���ܻ���ԭ�򣬱�Ԥ��ͼ����δ���£����ֶ���ewm.png�ļ��鿴<br>
<a href="ewm.php">��������</a>
<?php }else{ ?>
<div id="sea_area_left">
<form name="searchform" method="post" action="/ewm.php">
<input name="keyword" type="text" id="keyword" value="http://" class="searchtext"/>
<input type="submit" id="searchbutton" value="����" class="searchbtn" />
</form>
</div><br>
����������ı�����������ȷ����ַ������ַ��ʽ���ԣ������ɵĶ�ά����Ч��
<?php } ?>
</div>
<div style="display:none"><script language="javascript" type="text/javascript" src="/include/tongji.js"></script></div>
</body>
</html>