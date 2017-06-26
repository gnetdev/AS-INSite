/*
 * Sync the User
 */
AmritaSinghSyncUser = function() {
	
    var request = jQuery.ajax({
        type: 'get', 
        url: './amrita/customer/sync'
    });
    
}

jQuery(document).ready(function() {
	AmritaSinghSyncUser();
});