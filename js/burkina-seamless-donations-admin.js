/**
 * JS Seameless Donation Extension for backend user
 */
(function($) {
	//Fix footer
    $('#wpfooter').hide();

    //PayPal Gateway Only
    $('.cmb2-id-dgx-donate-payment-processor-choice').hide();

	//sets input mask
	$('#_dgx_donate_user_amount input').mask('#.##0 arbres', {reverse: true});
	$('#_dgx_donate_donor_email input').mask("A", {
		translation: {
			"A": { pattern: /[\w@\-.+]/, recursive: true }
		}
	});

	//change donation frequency button color when selected
	$('#_dgx_donate_tribute_gift').on('click', 'input', function() {
		$('#_dgx_donate_memorial_gift').toggle();
		$('#_dgx_donate_honoree_name').toggle();
		$('#_dgx_donate_honoree_email_name').toggle();
		$('#_dgx_donate_honoree_email').toggle();
	});

	//some adjustment on the validation and addition of custom data saving (personal message and team).
	$('#admin-seamless-donations-form').removeAttr('onsubmit');
	$('#dgx-donate-pay-enabled').on('click', 'input', function(e) {
		e.preventDefault();

		$('#_dgx_donate_user_amount input').unmask();

		if($('#other_radio_button label').hasClass('donate-ammount-checked')) {
			var amount = $('#_dgx_donate_user_amount input').val();
			if(amount=''){
				jQuery('#admin-seamless-donations-form').find('#_dgx_donate_user_amount').addClass('seamless-donations-invalid-input');
				jQuery('#admin-seamless-donations-form').find('#_dgx_donate_user_amount-error-message').text('Please use only numbers.').show('fast');
			}
		}

		if(SeamlessDonationsFormsEngineValidator()==true){
			burkina_save_custom_data($);
		}
	});

	workImage($);
})( jQuery );

function burkina_save_custom_data($){
	var amount = $("#_dgx_donate_user_amount input").val();
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
    fd.append('action', 'burkina_vert_save_custom_offline_data');
    fd.append('nonce', burkina_ajax_obj.nonce);
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
        		$('#image_loading_modal').css('left','-10000px');
				alert('Success! Offline donation was added.');
			}
		},
		error: function(errorThrown) {
			console.log(errorThrown);
			$('#dgx-donate-pay-enabled-error-message').html("Désolé, une erreur s'est produite. Veuillez réessayer plus tard.");
			$('#dgx-donate-pay-enabled-error-message').show();
        	$('#image_loading_modal').css('left','-10000px');
		}
	});
	return false;
}

function workImage($){
	//set preview and crop
	$('#_dgx_donate_image').on('change', function() {
		var file_name = $(this).val();
		if(file_name.length > 0){
			$('#image_prev_modal').css('left','0');
			var filename = $('#_dgx_donate_image').val().split('\\').pop();
			$('.inputfile + label').html(filename);
			$('.inputfile').addClass('loaded');
			addJcrop(this);
		}else{
			$('.inputfile + label').html('Choisissez une photo de profil');
			$('.inputfile').removeClass('loaded');
		}
	});
	var addJcrop = function(input) {
		// this will add the src in image_prev as uploaded
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
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

	$('.image_prev_button').on('click', function() {
		$('#image_prev_modal').css('left','-10000px');
	});

	//reset crop
	$('#_dgx_donate_image').on('click', function() {
		$(this).val('');
		$('#image_prev').removeAttr('src');
		$('#image_prev').removeAttr('style');
		if ($('#image_prev').data('Jcrop')) {
			// if image is already set, destroy jcrop or its created dom element here
			$('#image_prev').data('Jcrop').destroy();
		}
	});
}

/**
 * @return {boolean}
 */
function SeamlessDonationsFormsEngineValidator() {
    var formOkay = true;
    var formItemOkay = false;

    // first hide the error message data
    jQuery('.seamless-donations-forms-error-message').hide('fast').text();
    jQuery('.seamless-donations-error-message-field').hide('fast');

    // the approach is to find visible elements with 'validation' and check them
    jQuery('#admin-seamless-donations-form input:visible').each(function (index) {
    	formItemOkay = true;
        var validations = jQuery(this).attr('data-validate');
        if (validations !== undefined) {
            // the element has a validation request
            // the validation request can be one or more validation names, separated by commas
            var validationArray = validations.split(",");
            var valDex;
            for (valDex = 0; valDex < validationArray.length; ++valDex) {
                var validationTest = validationArray[valDex];
                switch (validationTest) {
                    case 'required':
                        if (!SeamlessDonationsValidateRequired(this)) {
                            formItemOkay = false;
    						console.log("-- Burkina Validator required: " + formOkay);
                        }
                        break;
                    case 'currency':
                        if (!SeamlessDonationsValidateCurrency(this)) {
                            formItemOkay = false;
    						console.log("-- Burkina Validator currency: " + formOkay);
                        }
                        break;
                    case 'email':
                        if (!SeamlessDonationsValidateEmail(this)) {
                            formItemOkay = false;
    						console.log("-- Burkina Validator email: " + formOkay);
                        }
                        break;
                }
            }
        }
    });

    formOkay = formItemOkay;

    if (!formOkay) {
        jQuery('.seamless-donations-forms-error-message').text('Please correct your input.').show('fast');
        jQuery('body').scrollTop(0);
        console.log("-- Burkina Validator: form not okay");
        return false; // returning false blocks the page loader from executing on submit
    } else {
        // the form is okay, so go on to the form submit function, another JavaScript
        console.log("-- Burkina Validator: form passed validation");
        return true;
    }
}

function SeamlessDonationsValidateRequired(validationObject) {
    var elementID = jQuery(validationObject).attr('id');
    var elementType = jQuery(validationObject).attr('type');
    var elementName = jQuery(validationObject).attr('name');
    var elementValue = jQuery(validationObject).val();
    var divSelector = "input[name=" + elementName + "]";
    var errorSelector = "div[id=" + elementName + "-error-message]";
    var valid = true;
    // currently only handles text, radio and checkbox input
    if (elementType == 'text') {
        valid = SeamlessDonationsTrim(elementValue) != '';
    } else if (elementType == 'radio' || elementType == 'checkbox') {
        valid = jQuery(validationObject).prop("checked");
    }

    if (valid) {
        jQuery('#admin-seamless-donations-form').find(divSelector).removeClass('seamless-donations-invalid-input');
        return true;
    } else {
        jQuery('#admin-seamless-donations-form').find(divSelector).addClass('seamless-donations-invalid-input');
        jQuery('#admin-seamless-donations-form').find(errorSelector).text('This is a required field.').show('fast');
        return false;
    }
}

function SeamlessDonationsValidateEmail(validationObject) {
    var elementID = jQuery(validationObject).attr('id');
    var elementType = jQuery(validationObject).attr('type');
    var elementName = jQuery(validationObject).attr('name');
    var elementValue = jQuery(validationObject).val();

    if (elementType == 'text' && elementValue != '') {
        var divSelector = "input[name=" + elementName + "]";
        var errorSelector = "div[id=" + elementName + "-error-message]";

        var lastAtPos = elementValue.lastIndexOf('@');
        var lastDotPos = elementValue.lastIndexOf('.');
        var isEmail = (lastAtPos < lastDotPos && lastAtPos > 0
        && elementValue.indexOf('@@') == -1 && lastDotPos > 2 && (elementValue.length - lastDotPos) > 2);
        if (!isEmail) {
            jQuery('#admin-seamless-donations-form').find(divSelector).addClass('seamless-donations-invalid-input');
            jQuery('#admin-seamless-donations-form').find(errorSelector).text('Please enter a valid email address.').show('fast');
            return false;
        }
        jQuery('#admin-seamless-donations-form').find(divSelector).removeClass('seamless-donations-invalid-input');
    }
    return true;
}

function SeamlessDonationsValidateCurrency(validationObject) {
    var elementID = jQuery(validationObject).attr('id');
    var elementType = jQuery(validationObject).attr('type');
    var elementName = jQuery(validationObject).attr('name');
    var elementValue = jQuery(validationObject).val();

    if (elementType == 'text') {
        var divSelector = "input[name=" + elementName + "]";
        var errorSelector = "div[id=" + elementName + "-error-message]";

        // Check if amount is empty
        if (!elementValue) {
            jQuery('#admin-seamless-donations-form').find(divSelector).addClass('seamless-donations-invalid-input');
            jQuery('#admin-seamless-donations-form').find(errorSelector).text('This is a required field.').show('fast');
            return false;
        }

        // Check for anything other than numbers and decimal points
        var matchTest = elementValue.match(/[^0123456789.]/g);
        if (matchTest != null) {
            jQuery('#admin-seamless-donations-form').find(divSelector).addClass('seamless-donations-invalid-input');
            jQuery('#admin-seamless-donations-form').find(errorSelector).text('Please use only numbers.').show('fast');
            return false;
        }

        // Count the number of decimal points
        var pointCount = DgxDonateCountNeedles(".", elementValue);

        // If more than one decimal point, fail right away
        if (pointCount > 1) {
            jQuery('#admin-seamless-donations-form').find(divSelector).addClass('seamless-donations-invalid-input');
            jQuery('#admin-seamless-donations-form').find(errorSelector).text('Please use only numbers.').show('fast');
            return false;
        }

        // A leading zero is not allowed
        if (elementValue.substr(0, 1) == "0") {
            jQuery('#admin-seamless-donations-form').find(divSelector).addClass('seamless-donations-invalid-input');
            jQuery('#admin-seamless-donations-form').find(errorSelector).text('A leading zero is not allowed.').show('fast');
            return false;
        }

        // A leading decimal point is not allowed (minimum donation is 1.00)
        if (elementValue.substr(0, 1) == ".") {
            jQuery('#admin-seamless-donations-form').find(divSelector).addClass('seamless-donations-invalid-input');
            jQuery('#admin-seamless-donations-form').find(errorSelector).text('Minimum value is 1.00.').show('fast');
            return false;
        }

        // If we have a decimal point and there is anything other than two digits after it, fail
        if (pointCount == 1) {
            var pointIndex = elementValue.indexOf(".");
            if (pointIndex + 2 != (elementValue.length - 1)) {
                jQuery('#admin-seamless-donations-form').find(divSelector).addClass('seamless-donations-invalid-input');
                jQuery('#admin-seamless-donations-form').find(errorSelector).text('Please use only numbers.').show('fast');
                return false;
            }
        }
        jQuery('#admin-seamless-donations-form').find(divSelector).removeClass('seamless-donations-invalid-input');
    }
    return true;
}

function SeamlessDonationsTrim(s) {
    if (s == undefined) {
        s = "";
    }

    s = s.replace(/(^\s*)|(\s*$)/gi, "");
    s = s.replace(/[ ]{2,}/gi, " ");
    s = s.replace(/\n /, "\n");
    return s;
}

function DgxDonateCountNeedles(a, b){
    var c = b.split(a) - 1;
    return c.length;
}