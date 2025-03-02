$(function(){
	var group_db = $('.form__group._gr_cache_db'), group_other = $('.form__group._gr_cache_other_domains');
	$('select[name="mssimpleconfig_cache_type"]').change(function(){
		if('none' === this.value)
		 {
			group_db.addClass('_hidden');
			group_other.addClass('_hidden');
		 }
		else
		 {
			group_other.removeClass('_hidden');
			group_db['mysql' === this.value ? 'removeClass' : 'addClass']('_hidden');
		 }
	}).change();
});