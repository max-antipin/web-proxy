$(function(){
	$('input[type="checkbox"][name="mssimpleconfig_translate"]').MSUIBindInputs({'inputs':'select[name="mssimpleconfig_translator"], select[name="mssimpleconfig_translate_source"], select[name="mssimpleconfig_translate_target"]', 'prop':{'disabled':'!checked'}});
});