/**
 * Public-facing JavaScript for the Genex Alert Banner plugin.
 *
 * @since 1.0.0
 */
(function( $ ) {
	'use strict';

	$(function() {
		// Check if the banner should be at the bottom
		if ($('#genex-alert-banner').length) {
			// You can add logic here to determine if the banner should be at the bottom
			// For example, based on a setting or a specific page
			// For now, let's just add the class to demonstrate the functionality
			// $('#genex-alert-banner').addClass('bottom');
		}

		// Flag to track if AJAX has been called
		let ajaxCalled = false;

		// Function to fetch and display active alerts
		function fetchAndDisplayActiveAlerts() {
			// Show the loader
			showLoader();

			$.ajax({
				url: '/wp-admin/admin-ajax.php',
				type: 'GET',
				data: {
					action: 'get_active_banners'
				},
				success: function(response) {
					console.log(response)
					
					// Process and display the alerts
					displayActiveAlerts(response.data);
					ajaxCalled = true; // Set the flag after successful AJAX call
				},
				error: function(error) {
					console.error('Error fetching active alerts:', error);
				},
				complete: function() {
					// Hide the loader, regardless of success or error
					hideLoader();
				}
			});
		}


		// Function to display active alerts in the offcanvas panel
		function displayActiveAlerts(alerts) {
			// Create or get the offcanvas panel
			var offcanvas = $('#genex-alerts-offcanvas');
			if (offcanvas.length === 0) {
				offcanvas = $('<div id="genex-alerts-offcanvas" class="genex-offcanvas"><div class="genex-offcanvas-header"><h5 class="genex-offcanvas-title">Active Alerts</h5><button type="button" class="genex-offcanvas-close" aria-label="Close">×</button></div><div class="genex-offcanvas-body"></div></div>');
				$('body').append(offcanvas);

				// Add event listener for the close button
				offcanvas.find('.genex-offcanvas-close').on('click', function() {
					hideOffcanvas();
				});
			}
	
			// Clear previous alerts
			offcanvas.find('.genex-offcanvas-body').empty();
	
			// Filter and display active alerts
			var activeAlerts = [];
			if (Array.isArray(alerts)) {
				activeAlerts = alerts.filter(function(alert) {
					var now = new Date();
					var startDate = new Date(alert.start_datetime);
					var endDate = new Date(alert.end_datetime);
					return alert.status === 'active' && now >= startDate && now <= endDate;
				});
			} else {
				console.error('Invalid alerts data:', alerts);
			}
	
			if (activeAlerts.length > 0) {
				$.each(activeAlerts, function(index, alert) {
					var startDateFormatted = new Date(alert.start_datetime).toLocaleString();
					var endDateFormatted = new Date(alert.end_datetime).toLocaleString();
					var alertHtml = '<div class="genex-alert-item">';
					alertHtml += '<h5>' + alert.title + '</h5>';
					alertHtml += '<p>' + alert.content + '</p>';
					alertHtml += '<p class="genex-alert-banner-date">Start Date: ' + startDateFormatted + ' - End Date: ' + endDateFormatted + '</p>';
					alertHtml += '</div>';
					offcanvas.find('.genex-offcanvas-body').append(alertHtml);
				});
			} else {
				offcanvas.find('.genex-offcanvas-body').append('<p>No active alerts at the moment.</p>');
			}
	
			// Show the offcanvas panel
			showOffcanvas();
		}

		// Function to show the offcanvas
		function showOffcanvas() {
			$('#genex-alerts-offcanvas').addClass('show');
			$('#genex-alert-banner').hide(); // Hide the banner when offcanvas is shown
            $('#wpadminbar').hide(); // Hide the WordPress toolbar
		}

		// Function to hide the offcanvas
		function hideOffcanvas() {
			$('#genex-alerts-offcanvas').removeClass('show');
			$('#genex-alert-banner').show(); // Show the banner when offcanvas is hidden
            $('#wpadminbar').show(); // Show the WordPress toolbar
		}

		// Function to show the loader
		function showLoader() {
			// Check if the loader already exists
			if ($('#genex-loader').length === 0) {
				// Create the loader element
				var loader = $('<div id="genex-loader" class="genex-loader">Loading...</div>');
				$('body').append(loader);
			}
		}

		// Function to hide the loader
		function hideLoader() {
			$('#genex-loader').remove();
		}

		// Event listener for the "View all active messages" button
		$(document).on('click', '#genex-alert-banner a', function(e) {
			e.preventDefault();
			const offcanvas = $('#genex-alerts-offcanvas');
			if (ajaxCalled) {
				// If AJAX has been called, toggle the offcanvas
				offcanvas.hasClass('show') ? hideOffcanvas() : showOffcanvas();
			} else {
				// If AJAX hasn't been called, fetch and display alerts
				fetchAndDisplayActiveAlerts();
			}
		});



	});

})( jQuery );
