jQuery( document ).ready( function() {

	/* === Checkbox Multiple Control === */

	jQuery( '.customize-control-checkbox-multiple' ).on(
		'change',
		function() {

			console.log("multiple-checkbox clicked");
			checkbox_values = jQuery( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
				function() {
					return this.value;
				}
			).get().join( ',' );

			jQuery( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );
			console.log(checkbox_values);
		}
	);

} ); // jQuery( document ).ready