$( document ).ready(function() {
    $( 'a.ReplyComment' ).click( insertName );
});

function insertName() {
    // element.preventDefault();

    // Get username (decodeURIComponent might be harmful).
    var slug = this.href.split( '/' );
    var username = decodeURIComponent( slug[ slug.length - 1 ] );

    // Append "@username".
    // Test if there is the markup used by the advanced editor present.
    var commentbox = $( '#Form_Comment iframe' ).contents().find( 'body.TextBox' );
    if ( commentbox != null && typeof( commentbox ) != 'undefined' ) {
        // Advanced editor inserts an iframe, so this must be used.
        commentbox.text( commentbox.text() + '@' + username + ' ' );
    } else {
        // This is the normal textbox for comments.
        commentbox = $( '#Form_Comment #Form_Body' );
        commentbox.val( commentbox.val() + '@' + username + ' ' );
    }

    $('html, body').animate({
        scrollTop: $( '.MessageForm.CommentForm' ).offset().top
    }, 800);

    commentbox.focus();

    return false;
}
