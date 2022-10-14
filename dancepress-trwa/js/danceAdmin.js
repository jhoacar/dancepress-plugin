var DanceSchool;
var $j = jQuery;

$j(document).ready(function () {
	DanceSchool = new cDanceSchool();
});

function cDanceSchool (params) {
	var self = this;
	this.initFormValidation();
	this.initEditableDateField();

	try {
		$j('.tablesortable').tablesorter({
			sortList: [[2, 0], [1, 0]],
			headers: {
				0: {
					sorter: false
				}
			}
		});
	} catch (e) {}	// this breaks everything else below on error

	// toggle-selectec
	$j('#ds-selectall').click(function () {
		var checkBoxes = $j('input[name=ids\\[\\]]');
		checkBoxes.prop('checked', !checkBoxes.prop('checked'));
	});

	// Setup form validation on the #register-form element
	$j('#addclass-form').validate({

		// Specify the validation rules
		rules: {
			class_name: 'required',
			category_id: 'required',
			startdate: 'required',
			enddate: 'required',
			starttime: 'required',
			ages: 'required',
			endtime: 'required'

		},

		// Specify the validation error messages
		messages: {
			class_name: 'Please enter Class Name',
			category_id: 'Please select class category',
			teacher_id: 'Please select Class Teacher',
			startdate: 'Please enter Start Date',
			enddate: 'Please enter End Date',
			starttime: 'Please enter Start Time',
			ages: 'Please enter age',
			endtime: 'Please enter End Time'

		},

		submitHandler: function (form) {
			form.submit();
		}
	});

	// Setup form validation on the #emailgroups-form element
	$j('#emailgroups-form').validate({

		// Specify the validation rules
		rules: {
			group_id: 'required'
		},

		// Specify the validation error messages
		messages: {
			group_id: 'Please select the group you wish to use'
		},

		submitHandler: function (form) {
			form.submit();
		}
	});

	// Setup form validation on the #register-form element
	$j('#editstudent-form').validate({

		// Specify the validation rules
		rules: {
			firstname: 'required',
			lastname: 'required',
			birthdate: {
				required: true,
				date: true
			}
		},

		// Specify the validation error messages
		messages: {
			firstname: 'Please enter first name',
			birthdate: 'Please enter a correctly formatted date of birth'
		},

		submitHandler: function (form) {
			form.submit();
		}
	});

	// Setup form validation on the #register-form element
	$j('#addstudentclasses-form').validate({

		// Specify the validation rules
		rules: {
			payment_choice: 'required'
		},

		// Specify the validation error messages
		messages: {
			payment_choice: 'Please select a payment action'
		},

		submitHandler: function (form) {
			form.submit();
		}
	});

	$j('#emailgroups-form').validate({

		// Specify the validation rules
		rules: {
			subject: 'required',
			target_group: 'required'
		},

		// Specify the validation error messages
		messages: {
			subject: 'Please provide a subject',
			target_group: 'Please select a target group for this message'
		},

		submitHandler: function (form) {
			form.submit();
		}
	});

	$j('#startdate').datetimepicker({
		timepicker: false,
		format: 'Y/m/d',
		formatDate: 'Y/m/d'

	});
	$j('#enddate').datetimepicker({
		timepicker: false,
		format: 'Y/m/d',
		formatDate: 'Y/m/d'

	});

	$j('#starttime').datetimepicker({
		datepicker: false,
		format: 'h:i A',
		formatTime: 'h:i A',
		step: 10
	});

	$j('.datetime').datetimepicker({
		format: 'Y-m-d h:i A'
	});

	$j('#dance-school-admin-parents-bulk-action-form').submit(function () {
		var bulk_action = $j('#bulk-action').val();
		var selected_items = $j('input[type="checkbox"][name="ids[]"]').serializeArray();

		if (!selected_items || !selected_items.length) {
			alert('Select one or more parents to deactivate.');
			return false;
		}

		if (bulk_action == 'deactivate-account') {
			var response = confirm('Deactivate ' + selected_items.length + ' parent(s), Are you sure?');
			if (!response) return false;
		}
	});

	$j('#dance-school-admin-students-bulk-action-form').submit(function () {
		var bulk_action = $j('#bulk-action').val();
		var selected_items = $j('input[type="checkbox"][name="ids[]"]').serializeArray();

		if (!selected_items || !selected_items.length) {
			alert('Select one or more students to deactivate.');
			return false;
		}

		if (bulk_action == 'deactivate-account') {
			var response = confirm('Deactivate ' + selected_items.length + ' student(s), Are you sure?');
			if (!response) return false;
		}
	});

	$j('#dance-school-admin-venues-bulk-action-form').submit(function () {
		var bulk_action = $j('#bulk-action').val();
		var selected_items = $j('input[type="checkbox"][name="ids[]"]').serializeArray();

		if (!selected_items || !selected_items.length) {
			if (bulk_action == 'deletevenues') {
				alert('Select one or more venues to delete.');
				return false;
			}
		}

		if (bulk_action == 'deletevenues') {
			var response = confirm('Delete ' + selected_items.length + ' venue(s), Are you sure?');
			if (!response) return false;
		}
	});

	$j('#dance-school-admin-events-bulk-action-form').submit(function () {
		var bulk_action = $j('#bulk-action').val();
		var selected_items = $j('input[type="checkbox"][name="ids[]"]').serializeArray();

		if (!selected_items || !selected_items.length) {
			if (bulk_action == 'deleteevents') {
				alert('Select one or more events to delete.');
				return false;
			}
		}

		if (bulk_action == 'deleteevents') {
			var response = confirm('Delete ' + selected_items.length + ' event(s), Are you sure?');
			if (!response) return false;
		}
	});

	$j('.add-event-image-button').click(function (e) {
		tb_show('Upload/Select Event Image', '/wp-admin/media-upload.php?referer=wp-settings&type=image&TB_iframe=true&width=640&height=158', false);
		window.send_to_editor = function (html) {
			var media_url = jQuery('img', html).attr('src');// is image - doesn't always work
			if (typeof (media_url) == 'undefined') {
				media_url = jQuery(html).attr('src'); // is image
			}
			if (typeof (media_url) == 'undefined') {
				media_url = jQuery(html).attr('href'); // is document
			}
			jQuery('.event-image').attr('src', media_url).show();
			jQuery('.event-image-url').val(media_url);
			tb_remove();
		};
		return false;
	});

	$j('.print-attendance').click(function () {
		window.print();
	});

	$j('.print-report').click(function () {
		var yourDOCTYPE = '<!DOCTYPE html...';// your doctype declaration
		var printPreview = window.open('about:blank', '_blank', 'width=900,height=900,scrollbars=1,toolbar=0,top=100,left=100,status=0');
		var printDocument = printPreview.document;
		printDocument.open();
		printDocument.write(yourDOCTYPE +
			'<!DOCTYPE html><html><title>Print Report</title>' +
			'<link type="text/css" href="/wp-content/plugins/dancepress-trwa/css/danceAdmin.css" rel="stylesheet">' +
			'<style type="text/css">' +
			'body {' +
			'    color: #444;' +
			'    font-family: "Open Sans",sans-serif;' +
			'    font-size: 13px;' +
			'    line-height: 1.4em;' +
			'	}' +
			'</style>' +
			'<body>' +
			document.getElementById('report-table-wrapper').innerHTML +
			'</body></html>');
		printPreview.print();
		printDocument.close();
	}
	);

	$j('#ds-enrollment-customage select').change(function () {
		var currentClassId = g_data_classId;
		var year = $j('#customage-year').val();
		var month = $j('#customage-month').val();
		var day = $j('#customage-day').val();
		window.location = '/wp-admin/admin.php?page=admin-editclass&id=' + currentClassId + '&enrollment=1&year=' + year + '&month=' + month + '&day=' + day;
	});

	$j('.delete-parent-el').click(function () {
		$j(this).parent().fadeOut(400, function () {
			$j(this).remove();
		});
	});

	$j('.medicalbool').change(function (e) {
		var value = e.target.value;
		if (value == 1) {
			$j('#medical').show();
		} else {
			$j('#medical').hide();
		}
	});

	function deleteInstallment (el) {
		var selector = 'tr.installment .delete-installment';

		$j(selector).click(function () {
			$j(this).parents('tr').remove();

			var installmentsDefined = $j('.installment').length;

			if (installmentsDefined == 11) {
				$j('.add-class-fee-installment').removeClass('disabled').removeAttr('disabled');
			}

			return false;
		});
	}

	function installmentDate () {
		var selector = 'input.installment-date';

		jQuery(selector).datetimepicker({
			timepicker: false,
			format: 'Y/m/d',
			formatDate: 'Y/m/d',

		});
	}

	$j('.add-class-fee-installment').click(function () {
		var installmentsDefined = $j('.installment').length;
		var installment = "<tr class='installment'><td data-rowindex='" + (installmentsDefined + 1) + "'>Installment #" + (installmentsDefined + 1) + "<input type='hidden' name='ds_installment_fees[name][]' value='Installment #" + (installmentsDefined + 1) + "'/></td> <td data-rowindex='" + (installmentsDefined + 1) + "'><input type='text' name='ds_installment_fees[date][]' value='' class='installment-date' required/></td> <td data-rowindex='" + (installmentsDefined + 1) + "'>Default fee: <input type='number' name='ds_installment_fees[amount][]' value='0' min='1' step='0.01' required/><hr>Alternative rates based on number of courses registered (optional):<br><input type='button' value='Add multi fee(s)' class='button-secondary button-sm add-multi-fee' /></td> <td data-rowindex='" + (installmentsDefined + 1) + "'><input type='button' class='button button-alert delete-installment' value='Delete'/></td></tr>";

		$j('tr.no-installments').remove();
		$j(this).parents('tr').before(installment);

		deleteInstallment();
		installmentDate();

		if (installmentsDefined >= 11) {
			$j(this).addClass('disabled').attr('disabled', 'disabled');
		}

		self.addMultiFeeClickAction();

		return false;
	});

	deleteInstallment();
	installmentDate();

	$j('.delete-billing-custom-payment').click(function () {
		var table = $j(this).parents('table');
		var id = $j(this).data('id');

		$j(table).append('<input type="hidden" name="deleted_billing_custom_payments[]" value="' + id + '" />');

		$j(this).parents('tr').first().remove();
		return false;
	});

	this.addMultiFeeClickAction();
}

cDanceSchool.prototype.addMultiFeeClickAction = function () {
	$j('.add-multi-fee').click(function () {
		// var installmentsDefined = $j('.installment').length;
		// var refnum = $j(this).parent().data('rowindex');
		// var multiFeesDefined = $j(".multi-fees").length ? $j(".multi-fees").length : 0;
		var tds = $j(this).parent().siblings();
		var inputs = $j(tds).children('input');

		var installName = encodeURIComponent(inputs[0].value);
		var multiFeeEl = "<div class='multi-fees'>Min. number of courses registered: <input type='number' name='dstrwa_multi[" + installName + "][num_required][]' min='0' step='1' value=''/></div>";
		var multiFeeCostEl = "<div class='multi-fees'>Installment Fee: <input type='number' min='0' step='0.01' name='dstrwa_multi[" + installName + "][fee][]' value=''/></div>";
		$j(this).parent().append(multiFeeEl, multiFeeCostEl);
		return false;
	});
};

cDanceSchool.prototype.checkRegistrationDuplicate = function (field, value) {
	var self = this;

	$j.ajax({ 'dataType': 'json', 'type': 'post', 'url': '/wp-admin/admin-ajax.php?action=checkregistrationduplicate', 'data': {'field': field, 'value': value} }).done(
		function (v) { self.onCheckRegistrationDuplicateCallback(field, v['hasduplicate']); }
	);
};

cDanceSchool.prototype.onCheckRegistrationDuplicateCallback = function (field, status) {
	// console.log(field + ' ' +status);
	var cssDisplay = status ? 'inline' : 'none';
	var submitDisabled = status === true;
	switch (field) {
		case 'name':
			$j('#newclient-lastname-duplicate').css('display', cssDisplay);
			break;
		case 'email':
			$j('#newclient-email-duplicate').css('display', cssDisplay); $j('#db-submit').attr('disabled', submitDisabled);
			break;
		case 'additionalemail':
			$j('#newclient-additionalemail-duplicate').css('display', cssDisplay);
			break;
	}
};

cDanceSchool.prototype.initFormValidation = function (e) { // //
	var self = this;

	// admin-addclients duplication check..
	$j('#admin-addclients-form input').change(
		function (e) {
			switch (e.target.name) {
				case 'parent[lastname]':
					self.checkRegistrationDuplicate('name', $j('#newclient-firstname').val() + ' ' + $j('#newclient-lastname').val());
					break;
				case 'parent[email]':
					self.checkRegistrationDuplicate('email', $j('#newclient-email').val());
					break;
				case 'parent[email_additional]':
					self.checkRegistrationDuplicate('additionalemail', $j('#newclient-additionalemail').val());
					break;
			}
		}
	);

	// Setup form validation on the #register-form element
	$j('#db-submit').click(
		function (e) {
			var msg = '';
			$j('input').not('#db-submit').each(function () {
				$j(this).css('background-color', 'transparent');
			}
			);
			var required = $j('.registration .req');
			var phones = $j('.registration .phone');
			var emails = $j('.registration .email');

			$j.each(required, function (key, el) {
				if ($j(el).val() == '') {
					msg = 'Please complete all required fields';
					$j(el).css('background-color', '#FFC9C9');
				}
			});

			$j.each(phones, function (key, el) {
				var p = $j(el).val();
				if (p != '') {
					p = p.replace(/\D/g, '');
					if (p.length < 10 && p.length > 0) {
						msg = msg + '\nPlease provide a valid phone number';
						$j(el).css('background-color', '#FFC9C9');
						return false;
					}
				}
			});

			$j.each(emails, function (key, el) {
				var p = $j(el).val();
				if (p != '') {
					var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					if (!re.test(p)) {
						msg = msg + '\nPlease provide a valid email address';
						$j(el).css('background-color', '#FFC9C9');
						return false;
					}
				}
			});

			if (msg != '') {
				alert(msg);
			} else {
				$j('.registration').submit();
			}
		}
	);
};

cDanceSchool.prototype.initEditableDateField = function () {
	var self = this;

	$j('.receiptwrap').click(function () {
		self.updateTextFields(this);
		$j('.editable-date-input').hide();
		$j('.editable-date-txt').show();
	}).find('.editable-date-td, .editable-date-td *').on('click', function (e) {
		e.stopPropagation();
	});

	$j('.editable-date-input').change(function () {
		self.updateTextFields(this);
	});
	$j('.editable-date-input').blur(function () {
		self.updateTextFields(this);
	});

	$j('.editable-date-txt').on('click', function (e) {
		self.toggleDateFields(this);
		e.stopPropagation();
	});
};

cDanceSchool.prototype.toggleDateFields = function (el) {
	$j(el).next('input').show().focus();
	$j(el).hide();
};

cDanceSchool.prototype.updateTextFields = function (el) {
	var dateInputs = $j('.editable-date-input');

	for (var i = 0; i < dateInputs.length; i++) {
		var elVal = $j(dateInputs[i]).val();
		$j(dateInputs[i]).prev('span').html(elVal);
	}

	if ($j(el).attr('class') !== 'receiptwrap') {
		$j(el).hide();
		$j(el).prev('span').show();
	}
};

cDanceSchool.prototype.__ = function (text) {
	if (typeof translations === 'undefined') {
		return text;
	}

	return text;
};

cDanceSchool.prototype.formatDate = function (date) {
	if (typeof date === 'undefined') {
		date = new Date();
	}

	return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
};
