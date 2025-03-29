/**
 * Admin-specific JavaScript for the Genex Alert Banner plugin.
 *
 * @since 1.0.0
 */
(function( $ ) {
	'use strict';

	$(function() {
		// Initialize date and time pickers
		$('#start_datetime, #end_datetime').attr('type', 'datetime-local');
	});

})( jQuery );
