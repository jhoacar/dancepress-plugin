var Dancer;
var $j = jQuery;

//var ajaxurl = "/wp-admin/admin-ajax.php";

$j(document).ready(function() {
	Dancer = new cDancePublic();
});

function cDancePublic(){
	var self = this;
	this.storedEls = new Array();
	this.el = new Array();

	$j('#ds_numberofdancers').on('change', function(e){
		num = $j(this).val();
		self.showStudentRegForm(e, num);
	});

	$j('#ds_reg_stage2').on('change', '.same_address_selector', function(e){
		num = $j(this).val();
		self.toggleStudentAddress(e, num);
	});
	$j('#ds_reg_stage1').on('change', '.same_address_selector', function(e){
		num = $j(this).val();
		self.toggleParentAddress(e, num);
	});
	$j('#dschequebutton').on('click', function(e){
		num = $j(this).val();
		self.handlePaymentChoice(e, num);
	});

	$j('#ds_reg_stage2').on('change', 'input[type="checkbox"]', function(e){
		num = $j(this).val();
		self.toggleExperience(e, num);
	});

	$j("#ds_reg_stage3").on('change', '.timeselect', function(e){
		self.toggleTimeClashes(e,this);
	});

	$j("#confirmLegal").on('click', function(e){
		self.toggleLegal(e);
	});

	$j('#ds_reg_stage2').on('change', '.medicalbool', function(e){

		var id = $j(e.target).attr('data-ref');

		var value = e.target.value;
		if(value == 1){
			$j('#medical-' + id).show();
			$j('#medical-' + id + ' input').addClass('req');
		}else{
			$j('#medical-' + id).hide();
			$j('#medical-' + id + ' input').val('');
			$j('#medical-' + id + ' input').removeClass('req');
		}
	});


	$j('[class$=_ifdifferent]').hide();
	$j('#ds_reg_stage2').on('change', '.same_address_selector_existing', function(e){


		var id = $j(e.target).attr('data-student');

		var value = e.target.value;

		if(value == 0){
			$j('.student_id_' + id + '_ifdifferent').show();
			$j('.student_id_' + id + '_ifdifferent input').addClass('req');
		}else{
			$j('.student_id_' + id + '_ifdifferent').hide();
			$j('.student_id_' + id + '_ifdifferent input').val('');
			$j('.student_id_' + id + '_ifdifferent input').removeClass('req');
		}
	});

	//After backbutton to stage2, reshow selected.
	$j(window).bind('pageshow', function(){
		//$j('.showreturning').attr('checked', false);
		$j('.showreturning').each(function(e){
			if (this.checked){
				var targetClass = $j(this).attr('data-show');
				$j('.' + targetClass).show();
				$j('.' + targetClass + ' input.toreq').addClass('req');
			}else{
				var targetClass = $j(this).attr('data-show');

				$j('.' + targetClass).hide();
				$j('.' + targetClass + ' input.toreq').removeClass('req');
			}
		})
	});

	$j('.showreturning').on('change', function(e){
		if (this.checked){
			var targetClass = $j(e.target).attr('data-show');
			$j('.' + targetClass).show();
			$j('.' + targetClass + ' input.toreq').addClass('req');
		}else{
			var targetClass = $j(e.target).attr('data-show');
			$j('.' + targetClass).hide();
			$j('.' + targetClass + ' input.toreq').removeClass('req');
		}
	});

	this.initFormValidation('');
}

cDancePublic.prototype.showStudentRegForm = function(e, num){

	table = $j('.ds_student_cloneable_tbody').html();

	$j('#ds_reg_stage2 .ds-dynamic-row').remove();

	for (i = 1; i <= num; i ++){

		this.el[i] = table.replace(/xxx/g, i);

		$j('#row_z').before(this.el[i]);
		this.storedEls[i] = $j('.student_' + i + '.ifdifferent').detach();
	}
	$j('#ds_reg_stage2 .ds-dynamic-row').show("blind", 200);

	//bind medical field toggle
	$j('.medicalbool').change( function(e){
		var id = $j(e.target).attr('data-ref');

		var value = e.target.value;
		if(value == 1){
			$j('#medical-' + id).show();
		}else{
			$j('#medical-' + id).hide();
			$j('#medical-' + id).val('');
		}
	});

	for (i = 1; i <= num; i ++){

        $j('.medicalbool').filter('[value=0]').prop('checked', true);

	}
}

cDancePublic.prototype.toggleStudentAddress = function(e, num){

	if (num == 0){
		student = $j(e.currentTarget).attr('data-student');
		$j(e.currentTarget).parent().parent().after(this.storedEls[student]).show("blind",200);
		$j('.student_' + student + '.ifdifferent').show('blind',200);
	}else{
		$j('.student_' + student + '.ifdifferent').remove();
	}
}

cDancePublic.prototype.toggleParentAddress = function(e, num){

	parent = $j(e.currentTarget).attr('data-parent');

	if (num == 0){
		table = $j('.ds_parent_cloneable_tbody').html();
		el = table.replace(/yyy/g, parent);
		$j(e.currentTarget).parent().parent().after(el).show("blind",200);
	}else{
		$j('.' + parent + '.removeable').remove();
	}
}

cDancePublic.prototype.initFormValidation = function(e){
	var self = this;
	//admin-addclients duplication check..
	$j('#ds_reg_stage1 input').change(
		function(e){
			switch(e.target.name){
				case 'parent[email]': 			self.checkRegistrationDuplicate('email', 			$j('#newclient-email').val() ); 										break;
				case 'parent[email_additional]':self.checkRegistrationDuplicate('additionalemail', 	$j('#newclient-additionalemail').val() ); 									break;
			}
		}
	);

	// Setup form validation on the #register-form element
    	$j("#db-submit").click(
    		function(e){
    			msg = '';
    			$j('#db-submit').each(function(){ //prevent double-click
					$j(this).prop('disabled', true);
    			});
			//Remove all hidden fields as not required before submitting.
			$j('#ds_reg_stage2 tr').each(function(){
				if ($j(this).css('display') == 'none'){
					$j(this).remove();
				}
			});

    			required = $j('.registration .req');
    			phones = $j('.registration .phone');
    			emails = $j('.registration .email');
    			msg = '';
    			$j.each(required, function(key, el){
    				if ($j(el).val() == ''){
    					msg = "Please complete all required fields";
    					$j(el).css("background-color", '#FFC9C9');
    				}
    			});

    			$j.each(phones, function(key, el){

    				p = $j(el).val();
    				if (p != ""){
	    				p = p.replace(/\D/g,'');
	    				if (p.length < 10 && p.length > 0){
	    					msg = msg + "\nPlease provide a valid phone number";
	    					$j(el).css("background-color", '#FFC9C9');
	    					return false;
	    				}
	    			}
    			});

    			$j.each(emails, function(key, el){

    				p = $j(el).val();
    				if (p != ""){
	    				re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    				if (!re.test(p)){
	    					msg = msg + "\nPlease provide a valid email address";
	    					$j(el).css("background-color", '#FFC9C9');
	    					return false;
	    				}
	    			}
    			});

    			if (msg != ""){
					$j('#db-submit').each(function(){
						$j(this).prop('disabled', false); //re-enabled submit
	    			});
    				alert (msg);
					return false;
    			}else{
    				$j(".registration").submit();
    			}
    		}
    	);
}

cDancePublic.prototype.handlePaymentChoice = function(e,num){
	if (num == 0){
		$j('#dspaybycheque').show('blind', 600);
		$j('#dssubmit').hide('blind',200);
		$j('html, body').animate({
			scrollTop: $j("#dspaybycheque").offset().top
          }, 2000);
	}else{
		$j('#dspaybycheque').hide('blind', 600);
		$j('#dssubmit').show('blind',200);
	}

}

cDancePublic.prototype.toggleExperience = function(e,num){
	$j("span[data-id='"+e.currentTarget.id+"']").fadeToggle();
}

cDancePublic.prototype.toggleTimeClashes = function(e, src){

	startattr = $j(src).attr('data-starttime');
	endattr = $j(src).attr('data-endtime');
	starttime = parseFloat(startattr.substr(startattr.indexOf('-')+1));
	endtime = parseFloat(endattr.substr(endattr.indexOf('-')+1));
	column = $j(src).attr('name');
	column = column.substr(column.indexOf('_')+1, 1);

	$j('[data-endtime]').each(function(k,v){
		compstart = $j(v).attr('data-starttime');
		compend = $j(v).attr('data-endtime');
		compstart = parseFloat(compstart.substr(compstart.indexOf('-')+1));
		compend = parseFloat(compend.substr(compend.indexOf('-')+1));
		targetcol = $j(v).attr('name');
		targetcol = targetcol.substr(targetcol.indexOf('_')+1, 1);

		if ($j(v).val() == $j(src).val()){
			if (((compstart <= starttime && compend <= starttime) || (compstart >= endtime && compend >= endtime))){
				//nothing to see
			}else if ($j(v).attr('name') != $j(src).attr('name') && targetcol == column){
				if ($j(v).prop('disabled') == false && $j(src).prop('checked') == true){
					$j(v).prop('disabled', true);
				}else{
					$j(v).prop('disabled', false);
				}
			}
		}
	});
}

cDancePublic.prototype.toggleLegal = function(e){
	if($j("#confirmLegal").is(':checked'))
		$j("#buttonarea").show('slow');  // checked
	else{
		$j("#buttonarea").hide('slow');  // unchecked
	}
}

cDancePublic.prototype.checkRegistrationDuplicate = function(field, value){
	var self = this;

	$j.ajax({'dataType':'json', 'type':'post', 'url':'/registration/?ajax&page=checkregistrationduplicate', 'data':{'field':field, 'value':value} }).done(
		function(v){
			if (v.error) {
				alert(v.error.message);
				return;
			}

			self.onCheckRegistrationDuplicateCallback(field, v['hasduplicate']);
		}
	);
}

cDancePublic.prototype.onCheckRegistrationDuplicateCallback = function(field, status){

	var cssDisplay = status ? 'inline' : 'none';
	var submitDisabled = status ? true : false;
	switch(field){
		case 'email': 			$j('#newclient-email-duplicate').css('display',cssDisplay); $j('#db-submit').attr('disabled',submitDisabled); break;
		case 'additionalemail': $j('#newclient-additionalemail-duplicate').css('display',cssDisplay); break;
	}

	//any duplicate errors shown?
	var shownDuplicates = $j('#reg_stage1 .duplicate:visible');

	//toggle submit
	if(shownDuplicates.length){
		$j('#db-submit').css('display', 'none');
	}else{
		$j('#db-submit').css('display', 'inline');
	}
}
