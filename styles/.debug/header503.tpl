<html>
<head>
	<base href="{SITE_ROOT_PATH}" />
	<title>503 Service Unavailable</title>
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
	<div class="alert-area">
		<h1>503 Service Unavailable</h1>
		<p>{EXCEPTION_MESSAGE}</p>
		<p>Try it again later.</p>
	</div>
</div>
</body>
</html>
