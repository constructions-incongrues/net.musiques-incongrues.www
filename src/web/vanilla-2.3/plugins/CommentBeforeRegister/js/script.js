jQuery(document).ready(function($) {
	
	$('a.Stash').off('click').on('click', function(e) {
		var comment = $('#Form_Comment textarea').val(),
        	placeholder = $('#Form_Comment textarea').attr('placeholder');
    
	    // Stash a comment:
	    if (comment != '' && comment != placeholder) {
	    	var vanilla_identifier = gdn.definition('vanilla_identifier', false);
	    	
	    	if (vanilla_identifier) {
	    		// Embedded comment:
	    		var stash_name = 'CommentForForeignID_' + vanilla_identifier;
	    	} else {
	    		// Non-embedded comment:
	    		var stash_name = 'CommentForDiscussionID_' + gdn.definition('DiscussionID'); 
	    	}
	        var href = $(this).attr('href');
	        e.preventDefault();
	        
	        stash(stash_name, comment);
	    }
	});

});