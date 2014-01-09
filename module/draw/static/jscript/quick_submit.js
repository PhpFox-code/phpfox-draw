function quickSubmit(id, sUrl)
{
	var sText = $('#js_quick_edit'+id).val();	
	$.ajaxCall('draw.quickSubmit', 'id='+id+'&sText='+sText+'&sUrl='+sUrl);
	
	
}

