<html>
<head>
	<base href="{SITE_ROOT_PATH}" />
	<title>404 Not Found</title>
	<link rel="stylesheet" type="text/css" href="./styles/.debug/media/css/style.css">
	<link rel="stylesheet" type="text/css" href="./app/lib/js/nifty-corners/niftyCorners.css">
	<link rel="stylesheet" type="text/css" href="./app/lib/js/nifty-corners/niftyPrint.css" media="print">
	<script type="text/javascript" src="./app/lib/js/nifty-corners/nifty.js"></script>
	<script type="text/javascript">
window.onload=function()
{
	if(!NiftyCheck()) return;
	Rounded("div.root","#EEE","#FFF",'small');
}
	</script>
</head>
<body>
<div class="root">
	<div class="error-area">
		<h1>404 Not Found</h1>
		<p>The page <strong>{PAGE_URI}</strong> was not found on this website!</p>
		<p>You can try to go <a href="javascript:history.go(-1);">back</a> or to the website <a href="{SITE_ROOT_PATH}">homepage</a>.</p>
	</div>
</div>
</body>
</html>
