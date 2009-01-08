<style type="text/css">
div.escms_debug_item { padding: 1px; background: #FFF; border-bottom: 1px solid #E9E8DB;}
div.escms_debug_item div
{
	padding: 1px;
	border: 4px solid #EEE;
}
div.escms_debug_item table
{
	width: 100%;
	padding: 1px;
	border: 1px solid #DDD;
	background: #FFF;
	color: #222;
	font-family: Arial;
	font-size: 12px;
}
div.escms_debug_item table td { padding: 0 4px; font-size: 11px; }
div.escms_debug_item table td.escms_debug_label { width: 80px; border-left: 4px solid #F88; }
div.escms_debug_item table a { color: #906; }
div.escms_debug_item table tt { font-size: 12px; }
</style><div class="escms_debug_item"><div>
<loop(DEBUG_ITEMS)>
	
		
		<table cellspacing="0" cellpadding="0"><tbody>
			<tr><td class="escms_debug_label">Message:</td><td><tt><var(DEBUG_ITEM_MESSAGE)></tt></td></tr>
			<tr><td class="escms_debug_label">File:</td><td><var(DEBUG_ITEM_FILE)></td></tr>
			<tr><td class="escms_debug_label">Line:</td><td><var(DEBUG_ITEM_LINE)></td></tr>
		</tbody></table>
		
	</loop(DEBUG_ITEMS)></div></div>
