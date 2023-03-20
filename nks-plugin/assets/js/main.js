jQuery(function() {

	/*
	 * This script serves two different versions of the page:
	 * 
	 * 1) setup version: user selects process settings there and presses "Start" button. Not much to do here for javascript.
	 * 2) running version: user can see progreess bar which is periodically updated, and can stop the process. The bulk of JS is used there.
	 */

	if ( document.getElementById('bael-in-progress') ) { // Runnung the process: comments are currently being processed. Page displays "Stop process" form. 
		
		// Once the page is loaded, make sure that the process is running.
		tellServerToStartWorking();
		
		// Setup AJAX pings every 3 seconds.
		var pingingInterval = setInterval( pingForProcessStatus, 3000 );	

		const percentageNote = jQuery('#bael-percentage-note');
		const percentageBar = jQuery('#bael-percentage-bar');
		
		/**
		 * Ping server to check process status 
		 */
		function tellServerToStartWorking() {
			
			const request_url = ajaxUrl + '?action=trigger_comment_deletion'; // ajaxUrl value is defined by Wordpress function wp_localize_script()
			
			jQuery.ajax({
				url: request_url,
				type: 'POST',
				success: function(response){
					if ( response.success ) {
						percentageNote.html("Work in progress...");
					}
				},
				error: function(_this, status, error){
					
					percentageNote.html("Work in progress....");
					
					var errorText = status;

					if(error) {
							errorText += " -> " + error;
					}
					
					console.log(errorText);
					
				}
			});
		}
		
		/**
		 * Ping server to check process status 
		 */
		function pingForProcessStatus() {
			
			const request_url = ajaxUrl + '?action=ping_comment_deletion'; // ajaxUrl value is defined by Wordpress function wp_localize_script()
			
			jQuery.ajax({
				url: request_url,
				type: 'POST',
				success: function(response){
					
					if ( response.success ) {
						
						if ( response.data['status'] === 'running' ) {
							const threads_total = response.data['threads_total'];
							const threads_processed  = response.data['threads_processed'];

							if ( threads_total > 0 ) {

								const percentage =  Math.round( ( threads_processed / threads_total ) * 100 ); 

								const percentageText = 'Processed <strong>' + threads_processed +  '</strong> out of <strong>' + threads_total + '</strong> threads ( <strong>' + percentage  + '%</strong> completed )';

								percentageNote.html(percentageText);
								percentageBar.val(percentage);
								percentageBar.html(percentage);
							}
						}
						else {
							
							if ( response.data['status'] === 'finished' ) {
								
								if ( response.data['log_file'] ) {
									var newNoteText = 'All done. You can see log file <a target="_blank" href="' + response.data['log_file'] + '">here</a>';
								}
								else {
									newNoteText = 'All done but the log file is missing. ';
								}
								
								percentageNote.html(newNoteText);
								percentageBar.val(100);
								percentageBar.html(100);
								
							}
							else {
								
								var newNoteText = 'Current status: <strong>' + response.data['status'] + '</strong>. ';
								
								if ( response.data['log_file'] ) {
									newNoteText += ' You can see log file <a target="_blank" href="' + response.data['log_file'] + '">here</a>';
								}
								else {
									newNoteText = ' Log file is missing. ';
								}
								
								percentageNote.html(newNoteText);
							}
							
							document.querySelector('#bael-in-progress p.submit').style.display = 'none';
							clearInterval(pingingInterval);
						}
					}
				},
				error: function(_this, status, error){
						var errorText = status;
						if(error) {
								errorText += " -> " + error;
						}
						percentageNote.html(errorText);

						console.log(errorText);
				}
			});
		}
		
		
	
	}
	else { // process is not started yet. Page displays "Start process" form
		
		
		jQuery('#bael-max-age').change(function(){
			var value		= jQuery('#bael-max-age').val();

			var months	= value % 12;
			var years		= (value - months ) / 12;

			var comment	= '<strong>' + years + '</strong> ' + ( years === 1 ? 'year' : 'years');

			if ( months > 0 ) {
				comment += ', <strong>' + months + '</strong> ' + ( months === 1 ? 'month' : 'months');
			}

			jQuery('.bael-max-age-comment').html(comment);
		});
	}
});


			
	 