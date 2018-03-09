<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>{eq name="code" value="1"}操作成功{else/}操作失败{/eq}</title>
<style type="text/css">
<!--
* {
	padding: 0;
	margin: 0;
	font-size: 12px
}
a:link, a:visited {
	text-decoration: none;
	color: #0068a6
}
a:hover, a:active {
	color: #ff6600;
	text-decoration: underline
}
.showMsg {
	border: 1px solid #1e64c8;
	zoom: 1;
	width: 450px;
	height: 172px;
	text-align: left;
	margin-right: auto;
	margin-left: auto;
	margin-top: 200px;
}
.showMsg h5 {
	background-image: url(__IMG__/msg.png);
	background-repeat: no-repeat;
	color: #fff;
	padding-left: 35px;
	height: 25px;
	line-height: 26px;
*line-height:28px;
	overflow: hidden;
	font-size: 14px;
	text-align: left
}
.showMsg .content {
	padding: 46px 12px 10px 45px;
	font-size: 14px;
	height: 64px;
	text-align: left
}
.showMsg .bottom {
	background: #e4ecf7;
	margin: 0 1px 1px 1px;
	line-height: 26px;
*line-height:30px;
	height: 26px;
	text-align: center
}
.showMsg .ok, .showMsg .guery {
	background: url(__IMG__/msg_bg.png) no-repeat 0px -560px;
}
.showMsg .guery {
	background-position: left -460px;
}
-->
</style>
</head>
<body>
<div class="showMsg" style="text-align:center">
  <h5>{eq name="code" value="1"}操作成功{else/}操作失败{/eq}</h5>
  <div class="content guery" style="display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline;max-width:330px">
    {$msg|strip_tags}
  </div>
  <div class="bottom"> 系统将在<span style="color:blue;font-weight:bold" id="wait">{$wait}</span>秒后自动跳转，如果不想等待，直接点击<a id="href" href="{$url}">这里</a> </div>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>