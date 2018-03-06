$(document).ready(function() {

	$('p.employer').hide();
	$('p.contractor input').prop('required', true);

	$('.user-type-select').click(function() {
		var value = $(this).val();

		if(value == 'contractor') {
			$('p.contractor').show();
			$('p.employer').hide();

			$('p.contractor input').prop('required', true);
			$('p.employer input').prop('required', false);
		} else {
			$('p.contractor').hide();
			$('p.employer').show();

			$('p.contractor input').prop('required', false);
			$('p.employer input').prop('required', true);
		}

	});
});