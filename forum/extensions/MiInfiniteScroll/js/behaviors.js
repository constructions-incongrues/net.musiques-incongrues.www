$(document).ready(function() {
    $('ol.PageList, .PageInfo p, div.ContentInfo.Bottom').hide();
    $('#Discussions').append($('<div style="color:red;" id="loader">CHARGEMENT DES DISCUSSIONS SUIVANTES, UN PEU DE PATIENCE :)</div>'));
    $('#loader').hide();
    $(window).scroll(function() {
        if ($(window).scrollTop() == $(document).height() - $(window).height()) {
            $('#loader').show();
            var numPage =  $('ol.PageList li:last a').attr('href').match(/discussions\/(\d+)/)[1];
            numPage++;
            $('<div />').load($('ol.PageList li:last a').attr('href') + ' #Discussions .Discussion', function() {
                $(this).appendTo('#Discussions');
                $('ol.PageList li:last a').attr('href', $('ol.PageList li:last a').attr('href').replace(/discussions\/\d+/, 'discussions/' + numPage));
                $('#loader').remove();
                $('#Discussions').append($('<div style="color:red;" id="loader">CHARGEMENT DES DISCUSSIONS SUIVANTES, UN PEU DE PATIENCE :)</div>'));
            });
        }
    });
});

