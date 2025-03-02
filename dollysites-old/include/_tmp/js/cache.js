$(function(){
	new MaxieSystems.TreeView('.cached_files');
	$('.b_dolly_clear_cache').click(function(){
		if(confirm('Clear cache?')) ms.jpost({}, false, 'clear_cache');
	});
});