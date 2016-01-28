$(function() {
    $('#navigation .content').append('<select/>');
    var list = $('#navigation ul li a');
    
    $('<option />', {
        'selected': 'selected',
        'value': '',
        'text': 'Go to...'
    }).appendTo('#navigation select');
    
    list.each(function() {
        var el = $(this);
        $('<option />', {
            'value': el.attr('href'),
            'text': el.text()
        }).appendTo('#navigation select');
    });
    
    $('#navigation').on('change', 'select', function() {
        window.location = $(event.target).find('option:selected').val();
    });
    
    $('#header_search input[name=q]').width($('#header_search').parent('div').width()-73);

    if (parseInt($('.container').css('width').substr(0,3)) < 768) {
        $('.facets .content, .query .content').hide();
        $('.facets h4').each(function() {
            $(this).nextAll().hide();
            if ($(this).children('span').eq(0).hasClass('ui-icon-triangle-1-s')) {
                $(this).children('span').eq(0).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
            }
        });
        
        $('#term').css('width',parseInt($('.container').css('width').substr(0,3))-73 + 'px');
        
        if ($('#search').length) {
            $('#header_search').hide();
        }
    }
    
    if ($('.logo img').css('height') === '100px') {
        $('.main-menu').insertAfter('#banner-content').css({'padding-top': 0, 'margin-top': '-25px'});
        $('#userinfo').hide();
    }
    else {
        $('.main-menu').insertAfter('.subtitle').css({'padding-top': '18px', 'margin-top': 0});
        $('#userinfo').show();
    }

    $(window).resize(function(){	
        $('#header_search input[name=q]').width($('#header_search').parent('div').width()-73);
        if (parseInt($('.container').css('width').substr(0,3)) < 769) {
            if ($('.facets h3 span:eq(0)').hasClass('ui-icon-triangle-1-e')){
                $('.facets .content').hide();
                $('.facets h4').each(function() {
                    $(this).nextAll().hide();
                    if ($(this).children('span').eq(0).hasClass('ui-icon-triangle-1-s')) {
                        $(this).children('span').eq(0).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
                    }
                });
            }
            if ($('.query h3 span:eq(0)').hasClass('ui-icon-triangle-1-e')){
                $('.query .content').hide();
                $('.query h4').each(function() {
                    $(this).nextAll().hide();
                    if ($(this).children('span').eq(0).hasClass('ui-icon-triangle-1-s')) {
                        $(this).children('span').eq(0).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
                    }
                });
            }
            
            $('#term').css('width',parseInt($('.container').css('width').substr(0,3))-73 + 'px');
        
            if ($('#search').length) {
                $('#header_search').hide();
            }
        }
        else {
            if ($('.facets h3 span:eq(0)').hasClass('ui-icon-triangle-1-s')) {
                $('.facets h3 span:eq(0)').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
            }
            if ($('.query h3 span:eq(0)').hasClass('ui-icon-triangle-1-s')) {
                $('.query h3 span:eq(0)').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
            }
            $('.facets .content, .query .content').show();
            $('.facets h4').each(function() {
                $(this).nextAll().show();
                if ($(this).children('span').eq(0).hasClass('ui-icon-triangle-1-e')) {
                    $(this).children('span').eq(0).removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
                }
            });
            
            $('#term').css('width', '18.75rem');
            
            $('#header_search').show();

            /*$('.name-entry .name').each(function() {
                $(this).parent().append($(this));
            });*/
            
            //$('.fam, .symbols').show();
            
            //$('.name').css('width', ($('.name-entry').width()-$('.fam').width()-$('.symbols').width()-2)+'px');
        }
        
    });
    
});