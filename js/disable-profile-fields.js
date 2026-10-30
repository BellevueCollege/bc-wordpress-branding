// from https://www.role-editor.com/hide-disable-wordpress-user-profile-fields/
document.addEventListener( 'DOMContentLoaded', function () {
	var fieldsToDisable = [ 'first_name', 'last_name', 'email' ];

	for ( var index = 0; index < fieldsToDisable.length; index++ ) {
		var field = document.getElementById( fieldsToDisable[ index ] );
		if ( field ) {
			field.disabled = true;
		}
	}

	var profileForm = document.getElementById( 'your-profile' );
	if ( ! profileForm || ! profileForm.parentNode ) {
		return;
	}

	var notice = document.createElement( 'p' );
	notice.textContent =
		'Many parts of your profile (Username, Name, Email, and Password) are set based on the information Bellevue College has on file, and can not be changed within WordPress.';

	profileForm.parentNode.insertBefore( notice, profileForm );
} );
