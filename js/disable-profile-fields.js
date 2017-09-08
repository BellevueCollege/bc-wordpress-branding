// from https://www.role-editor.com/hide-disable-wordpress-user-profile-fields/
jQuery(document).ready(function ($) {
	var fields_to_disable = ['first_name', 'last_name', 'email'];
	for (i = 0; i < fields_to_disable.length; i++) {
		if ($('#' + fields_to_disable[i]).length) {
			$('#' + fields_to_disable[i]).attr("disabled", "disabled");
		}
	}
	$('#your-profile').before('<p>Many parts of your profile (Username, Name, Email, and Password) are set based on the information Bellevue College has on file, and can not be changed within WordPress.</p>');
});