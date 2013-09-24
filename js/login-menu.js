$(function() {
$('#login').click(function(event) {
    event.preventDefault();
    
    $( '#msg' ).html( '' );
    var display = $('#div_login').css('display');
    if (display == 'none') {
        $('#div_login').show(1000);
    } else {
        $('#div_login').hide(1000);
    }
});

$('#logout').click(function(event) {
    event.preventDefault();
    
    var url = $( '#logout' ).attr( "href" );

    var posting = $.post( '?user/logout', { json: 1}, "json");

    posting.done(function( data ) {
        var row = JSON.parse(data);
        $( '#msg' ).html( row.msg );
        if (row.success == 1) {
            $('#msg').removeClass('error').addClass('success');
            $('#msg').show(1000);
            $('#div_auth').hide(1000);
            $('#div_notauth').show(1000);
        } else {
            $('#msg').removeClass('success').addClass('error');
            $('#msg').show(1000);
        }
    });
});

$( "#lg_form" ).submit(function( event ) {
    event.preventDefault();

    var $form = $( '#lg_form' ),
    login = $form.find( "input[name='login']" ).val(),
    password = $form.find( "input[name='password']" ).val(),
    url = $form.attr( "action" );

    var posting = $.post( url, { login: login, password: password, json: 1}, "json");

    posting.done(function( data ) {
        var row = JSON.parse(data);
        $( '#msg' ).html( row.msg );
        $('#div_login').hide(1000);
        if (row.success == 1) {
            $('#msg').removeClass('error').addClass('success');
            $('#msg').show(1000);
            $('#div_notauth').hide(1000);
            $('#div_auth').show(1000);
        } else {
            $('#msg').removeClass('success').addClass('error');
            $('#msg').show(1000);
        }
    });
});
});