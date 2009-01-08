<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<base href="{SITE_ROOT_PATH}" />
	<title>{EXCEPTION_MESSAGE}</title>
	<link rel="stylesheet" type="text/css" href="./styles/.debug/media/css/style.css">
	<link rel="stylesheet" type="text/css" href="./app/lib/js/nifty-corners/niftyCorners.css">
	<link rel="stylesheet" type="text/css" href="./app/lib/js/nifty-corners/niftyPrint.css" media="print">
	<script type="text/javascript" src="./app/lib/js/nifty-corners/nifty.js"></script>
	<script type="text/javascript" src="./app/lib/js/jquery.js"></script>
	<script type="text/javascript">
$(document).ready(function()
{
	if(!NiftyCheck()) return;
	Rounded("div.root","#EEE","#FFF",'small');
	//Rounded("div.log","#EEE","#FFF",'small');
	Rounded("small#status","#EEE","#DDD",'small');
	
	$('*[id~=heading]').click(function()
	{
		$('#area-'+this.id.substr(8)).slideToggle();
		if(!$(this).hasClass('active')) $(this).addClass('active');
		else $(this).removeClass('active');
	});
});
	</script>
</head>
<body>
<div class="root">
	<div class="alert-area">
		<h1>{EXCEPTION_MESSAGE}</h1>
		<p>Try it again later.</p>
	</div>
</div>
<div class="root" id="log">
	<div class="alert-area">
		<h2 id="heading-errors" class="active">Errors ({DEBUG_ERRORS_COUNT})</h2>
		<div class="area" id="area-errors">
<loop(DEBUG_ITEMS)>
		<h3 class="slider"><span id="heading-error-0"><var(DEBUG_ITEM_FILE)> (on line <var(DEBUG_ITEM_LINE)>)</span> <var(DEBUG_ITEM_MESSAGE)></h3>
		<div id="area-error-0" style="display:none;"><pre><var(DEBUG_ITEM_CODE)></pre></div>
</loop(DEBUG_ITEMS)>		</div>

		<h2 id="heading-enviorement">Enviorement</h2>
		<div class="area" id="area-enviorement" style="display:none;">
			<h3>Constants</h3>
			<table cellspacing="0" cellpadding="0"><tbody>
<loop(DEBUG_CONSTANTS)>
				<tr><td class="escms_debug_label"><tt><var(CONSTANT)></tt></td><td><tt><var(CONSTANT_VALUE)></tt></td></tr>
</loop(DEBUG_CONSTANTS)>			</tbody></table>
		</div>
		
	</div>
</div>
<p id="status"><small><strong>Debug mode</strong><br />HTTP/1.1 503 Service Unavailable<br /><br />Try it again later.<br />You do not need to contact the site administrator. This mode is evoked by the site developer.<br />The problems are being solved ;-)</small></p>
</body>
</html>
