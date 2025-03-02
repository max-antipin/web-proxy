$(function(){
    $(document).on('click', '.text_changer .remove', function() {
        if ($('.text_changer .column').length > 1) {
            $(this).parent().parent().parent().remove();
        } else {

            $(this).parent().parent().parent().remove();
        }
    });
    // text change page - changing input to textarea
    // we use document ON because our DOM is changing in real time
    $(document).on('focus', '.text_changer .textbox', function() {
        $('.text_changer .column .input_wrap').show();
        $('.text_changer .column .textarea_wrap').hide();
        $(this).parent().parent().parent().find('.input_wrap').hide();
        $(this).parent().parent().parent().find('.textarea_wrap').show();
        $(this).parent().parent().parent().find('.textarea_wrap [name^=textarea_from]').focus();
    });
    // adding more text fields to changer
    $('.text_changer .fields').each(function() {
        var count = $('.text_changer .fields').length;
        if (count <= 1 ) {
            $('.text_changer .column#column_0').find('.input_wrap').hide();
            $('.text_changer .column#column_0').find('.textarea_wrap').show();
        }
    });
    $('#work_area .add_column').click(function(){
        var count = $('.text_changer .fields').length;
        count++;

        var item_t = ' <div class="fields item">\
						<div class="column" id="column_' + count + '">\
							<div class="input_wrap">\
							<div class="remove"></div>\
							<div class="left pd0">                                                            \
							<input type="text"                                                                \
						name="out['+ count +'][l_input]"                                               \
						class="textbox"                                                                       \
						placeholder="' + SET_TEXT + '"                                                           \
						>                                             \
							</div>                                                                            \
							<div class="right pd0">                                                           \
							<input type="text"                                                                \
						name="out['+ count +'][r_input]"                                               \
						class="textbox"                                                                       \
						placeholder="' + SET_TEXT + '"                                                           \		                                                                              \
							>                                                                                 \
							</div>                                                                            \
							</div>                                                                            \
							<div class="textarea_wrap hidden">                                                \
							<div class="remove"></div>                                                        \
							<div class="left pd0">                                                            \
							<textarea name="out['+ count +'][l_textarea]"                              \
						class="magic_textarea"                                                                \
						placeholder="' + SET_TEXT + '"></textarea>             \
						</div>                                                                                \
						<div class="right pd0">                                                               \
							<textarea name="out['+ count +'][r_textarea]"                              \
						placeholder="' + SET_TEXT + '"                                                           \
						class="magic_textarea"></textarea>                  \
						</div>                                                                                \
						<div class="change_wrap">                                                             \
							<div class="change_type">                                                         \
							<input type="checkbox"                                                            \
						class="super_checkbox"                                                                \
						name="out['+ count +'][change_type]"                                           \
						id="regular['+ count +']">                                                                                     \
						<label for="regular['+ count +']" class="label">' + PREG + '</label>                    \
						</div>                                                                                \
						</div>                                                                                \
						</div>                                                                                \
						</div>                                                                                \
						</div>';
        //$('.form_step_3 .form_wrap_2 .item .input_wrap').show();
        //$('.form_step_3 .form_wrap_2 .item .textarea_wrap').hide();
        $('#replaces').append(item_t);

        $('.input_wrap').show();
        $('.textarea_wrap').hide();

        $('.text_changer .column#column_' + count).find('.input_wrap').hide();
        $('.text_changer .column#column_' + count).find('.textarea_wrap').show();
    });

});