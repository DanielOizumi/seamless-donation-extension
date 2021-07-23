/**
 * JS Seameless Donation Extension for frontend user
 */
(function($) {
	//add donation menu
	$('#greennature-main-navigation').append('<a class="greennature-donate-button" href="donate"><span class="greennature-button-overlay"></span><span class="greennature-button-donate-text">Join the Challenge</span></a>');
	$('#menu-main-menu').append('<li><a class="greennature-donate-button" href="donate"><span class="greennature-button-overlay"></span><span class="greennature-button-donate-text">Join the Challenge</span></a></li>');
	//change price button color when selected
	$('.donate-ammount').on('change', 'input', function() {
		$('.donate-ammount-checked').removeClass('donate-ammount-checked');
		$(this).closest('.donate-ammount').addClass('donate-ammount-checked');
	});

	//change donation frequency button color when selected
	$('.donate-frequency').on('change', 'input', function() {
		$('.donate-frequency-checked').removeClass('donate-frequency-checked');
		$(this).closest('.donate-frequency').addClass('donate-frequency-checked');
	});

	//limit input length
	$("#_dgx_donate_message input").attr('maxlength', '100');
	$("#_dgx_donate_team input").attr('maxlength', '100');
	$("#_dgx_donate_honoree_name input").attr('maxlength', '100');
	$("#_dgx_donate_donor_first_name input").attr('maxlength', '500');
	$("#_dgx_donate_donor_last_name input").attr('maxlength', '500');
	$("#_dgx_donate_donor_email input").attr('maxlength', '500');
	$("#_dgx_donate_donor_phone input").attr('maxlength', '20');

	//sets input mask
	$('#_dgx_donate_user_amount input').mask('#.##0 trees', {reverse: true});
	$('#_dgx_donate_donor_email input').mask("A", {
		translation: {
			"A": { pattern: /[\w@\-.+]/, recursive: true }
		}
	});
	$('.phone_us').mask('(000) 000-0000');

	//removes "Send acknowledgement via postal mail" option
	$('#_dgx_donate_honor_by_post_mail').remove();

	//some adjustment on the validation and addition of custom data saving (personal message and team).
	$('#seamless-donations-form').removeAttr('onsubmit');
	$('#dgx-donate-pay-enabled').on('click', 'input', function(e) {
		e.preventDefault();

		$('#_dgx_donate_user_amount input').unmask();

		if($('#other_radio_button label').hasClass('donate-ammount-checked')) {
			var amount = $('#_dgx_donate_user_amount input').val();
			if(amount=''){
				jQuery('#seamless-donations-form').find('#_dgx_donate_user_amount').addClass('seamless-donations-invalid-input');
				jQuery('#seamless-donations-form').find('#_dgx_donate_user_amount-error-message').text('Please use only numbers.').show('fast');
			}
		}

		if(SeamlessDonationsFormsEngineValidator()==true){
			burkina_save_custom_data($);
		}
	});

	workImage($);

	// Modal
	$loading_modal = '<div id="image_loading_modal" class="burkina-modal"><div id="image_loading_container" class="burkina-modal-content" style="max-width:280px"><div id="image_loading"></div></div></div>';
	$('body').prepend($loading_modal);

})( jQuery );

function burkina_save_custom_data($){
	var session_id = $("#session_id_element input").val();
	var amount = $('input[name=_dgx_donate_amount]:checked', '#seamless-donations-form').val();
	if(amount == 'OTHER') {
		amount = $("#_dgx_donate_user_amount input").val();
	}
	var msg = $("#_dgx_donate_message input").val();
	var team = $("#_dgx_donate_team input").val();
	var tribute_type = $('input[name=_dgx_donate_tribute_gift_radio]:checked').val();
	var tribute = $("#_dgx_donate_honoree_name input").val();
	var fname = $("#_dgx_donate_donor_first_name input").val();
	var lname = $("#_dgx_donate_donor_last_name input").val();
	var email = $("#_dgx_donate_donor_email input").val();
	var phone = $("#_dgx_donate_donor_phone input").val();
	var anonymous = 0;
	if ($('#_dgx_donate_anonymous input').is(":checked")){
		anonymous = 1;
	}
	var image = $('#_dgx_donate_image')[0].files[0];

	var x = $('#Jcrop_x').val();
	var y = $('#Jcrop_y').val();
	var w = $('#Jcrop_w').val();
	var h = $('#Jcrop_h').val();

	var ow = $('#image_prev').css('width');
	var oh = $('#image_prev').css('height');
	ow = ow.substring(0, ow.length - 2);
	oh = oh.substring(0, oh.length - 2);

	var fd = new FormData();
    fd.append('action', 'burkina_vert_save_custom_data');
    fd.append('nonce', burkina_ajax_obj.nonce);
    fd.append('_dgx_donate_session_id', session_id);
    fd.append('_dgx_donate_amount', amount);
    fd.append('_dgx_donate_message', msg);
    fd.append('_dgx_donate_team', team);
    fd.append('_dgx_donate_tribute_type', tribute_type);
    fd.append('_dgx_donate_honoree_name', tribute);
    fd.append('_dgx_donate_donor_first_name', fname);
    fd.append('_dgx_donate_donor_last_name', lname);
    fd.append('_dgx_donate_donor_email', email);
    fd.append('_dgx_donate_donor_phone', phone);
    fd.append('_dgx_donate_anonymous', anonymous);
    fd.append('_dgx_donate_image', image);
    fd.append('Jcrop_x', x);
    fd.append('Jcrop_y', y);
    fd.append('Jcrop_w', w);
    fd.append('Jcrop_h', h);
    fd.append('Jcrop_ow', ow);
    fd.append('Jcrop_oh', oh);

	// This does the ajax request
	$.ajax({
		type: 'POST',
		url: burkina_ajax_obj.ajaxurl,
		//async: false,
		processData: false,
        contentType: false,
        cache: false,
		data: fd,
		beforeSend: function(){
        	$('#image_loading_modal').css('left','0');
		},
		success:function(data) {
			if(data>0){
				$('#seamless-donations-form').submit();
			}
		},
		error: function(errorThrown) {
			console.log(errorThrown);
			$('#dgx-donate-pay-enabled-error-message').html("Sorry, an error occurred. Please try again later.");
			$('#dgx-donate-pay-enabled-error-message').show();
        	$('#image_loading_modal').css('left','-10000px');
		}
	});
	return false;
}

function workImage($){
	//support file upload
	$('#seamless-donations-form').attr( 'enctype', 'multipart/form-data' );

	//add file input for photo
	var input_file = '<div id="_dgx_donate_image_div"><input type="file" id="_dgx_donate_image" name="_dgx_donate_image" accept=".gif,.jpg,.jpeg,.png" class="inputfile"><label for="_dgx_donate_image">Choose a profile picture</label><div id="image_prev_modal" class="burkina-modal"><div id="image_prev_container" class="burkina-modal-content" style="max-width:280px"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" id="image_prev"><div class="image_prev_button"><span class="stunning-item-button greennature-button large">Ok</span></div></div></div></div>';
	$(input_file).insertBefore('#_dgx_donate_donor_first_name');
	$('#seamless-donations-form').append('<input type="hidden" id="Jcrop_x" name="Jcrop_x" value="">');
	$('#seamless-donations-form').append('<input type="hidden" id="Jcrop_y" name="Jcrop_y" value="">');
	$('#seamless-donations-form').append('<input type="hidden" id="Jcrop_w" name="Jcrop_w" value="">');
	$('#seamless-donations-form').append('<input type="hidden" id="Jcrop_h" name="Jcrop_h" value="">');

	//set preview and crop
	$('#_dgx_donate_image').on('change', function(){
		var file_name = $(this).val();
		if(file_name.length > 0){
			$('#image_prev_modal').css('left','0');
			var filename = $('#_dgx_donate_image').val().split('\\').pop();
			$('.inputfile + label').html(filename);
			$('.inputfile').addClass('loaded');
			addJcrop(this);
		}else{
			$('.inputfile + label').html('Choose a profile picture');
			$('.inputfile').removeClass('loaded');
		}
	});
	var addJcrop = function(input) {
		// this will add the src in image_prev as uploaded
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#image_prev').attr('src', e.target.result);
				$('#image_prev').one('load',function() {
					var iw = $('#image_prev_container').outerWidth();
					var ih = $('#image_prev_container').outerHeight();
					if(iw>ih){
						$('#image_prev').css('max-height','280px');
					}else{
						$('#image_prev').css('max-width','280px');
					}
					var wh = $(window).height() / 2;
					var ih = $('#image_prev_container').outerHeight() / 2;
					var tp = wh - ih;
					$('#image_prev_container').css('top', tp);
			    });
				$('#image_prev').Jcrop({
					boxWidth: 280,   //Maximum width you want for your bigger images
					boxHeight: 280,  //Maximum Height for your bigger images
					setSelect: [0, 0, 200],
					minSize: [80, 80],
					maxSize: [280, 280],
					aspectRatio: 1/1,
					onSelect: setCoordinates,
					onChange: setCoordinates,
					keySupport: false
				});
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
	var setCoordinates = function(c){
		$('#Jcrop_x').val(c.x);
		$('#Jcrop_y').val(c.y);
		$('#Jcrop_w').val(c.w);
		$('#Jcrop_h').val(c.h);
	};

	$('.image_prev_button').on('click', function(){
		$('#image_prev_modal').css('left','-10000px');
	});

	//reset crop
	$('#_dgx_donate_image').on('click', function() {
		$(this).val('');
		$('#image_prev').removeAttr( 'src' );
		$('#image_prev').removeAttr( 'style' );
		if ($('#image_prev').data('Jcrop')) {
			// if image is already set, destroy jcrop or its created dom element here
			$('#image_prev').data('Jcrop').destroy();
		}
	});
}