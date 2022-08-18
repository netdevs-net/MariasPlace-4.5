jQuery(function ($) {
    'use strict';
    // here for each comment reply link of WordPress
    $('.comment-reply-link').addClass('btn btn-primary');

    // here for the submit button of the comment reply form
    $('#commentsubmit').addClass('btn btn-primary');

    // The WordPress Default Widgets
    // Now we'll add some classes for the WordPress default widgets - let's go

    // the search widget
    $('.widget_search input.search-field').addClass('form-control');
    $('.widget_search input.search-submit').addClass('btn btn-default');
    $('.variations_form .variations .value > select').addClass('form-control');
    $('.widget_rss ul').addClass('media-list');

    $('.widget_meta ul, .widget_recent_entries ul, .widget_archive ul, .widget_categories ul, .widget_nav_menu ul, .widget_pages ul, .widget_product_categories ul').addClass('nav flex-column');
    $('.widget_meta ul li, .widget_recent_entries ul li, .widget_archive ul li, .widget_categories ul li, .widget_nav_menu ul li, .widget_pages ul li, .widget_product_categories ul li').addClass('nav-item');
    $('.widget_meta ul li a, .widget_recent_entries ul li a, .widget_archive ul li a, .widget_categories ul li a, .widget_nav_menu ul li a, .widget_pages ul li a, .widget_product_categories ul li a').addClass('nav-link');

    $('.widget_recent_comments ul#recentcomments').css('list-style', 'none').css('padding-left', '0');
    $('.widget_recent_comments ul#recentcomments li').css('padding', '5px 15px');

    $('table#wp-calendar').addClass('table table-striped');

    // Adding Class to contact form 7 form
    $('.wpcf7-form-control').not(".wpcf7-submit, .wpcf7-acceptance, .wpcf7-file, .wpcf7-radio").addClass('form-control');
    $('.wpcf7-submit').addClass('btn btn-primary');

    // Adding Class to Woocommerce form
    $('.woocommerce-Input--text, .woocommerce-Input--email, .woocommerce-Input--password').addClass('form-control');
    $('.woocommerce-Button.button').addClass('btn btn-primary mt-2').removeClass('button');

    $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).parent().siblings().removeClass('open');
        $(this).parent().toggleClass('open');
    });

    // Fix woocommerce checkout layout
    $('#customer_details .col-1').addClass('col-12').removeClass('col-1');
    $('#customer_details .col-2').addClass('col-12').removeClass('col-2');
    $('.woocommerce-MyAccount-content .col-1').addClass('col-12').removeClass('col-1');
    $('.woocommerce-MyAccount-content .col-2').addClass('col-12').removeClass('col-2');

    // Add Option to add Fullwidth Section
    function fullWidthSection() {
        var screenWidth = $(window).width();
        if ($('.entry-content').length) {
            var leftoffset = $('.entry-content').offset().left;
        } else {
            var leftoffset = 0;
        }
        $('.full-bleed-section').css({
            'position': 'relative',
            'left': '-' + leftoffset + 'px',
            'box-sizing': 'border-box',
            'width': screenWidth,
        });
    }
    fullWidthSection();
    $(window).resize(function () {
        fullWidthSection();
    });

    // Allow smooth scroll
    $('.page-scroller').on('click', function (e) {
        e.preventDefault();
        var target = this.hash;
        var $target = $(target);
        $('html, body').animate({
            'scrollTop': $target.offset().top
        }, 1000, 'swing');
    });
//Image Block 
    $('.image-block').each(function () {
        var ImageHeading = $(this).find('.wpb_singleimage_heading').text();
        $(this).find('figure .vc_single_image-wrapper').append('<span class="image-block-title">' + ImageHeading + '</span>');
    });
    //Search Form In Header
    $(document).ready(function () {
        // Configure/customize these variables.
        var showChar = 110;  // How many characters are shown by default
        var ellipsestext = "...";
        var moretext = "More (+)";
        var lesstext = "Less (-)";


        $('.more').each(function () {
            var content = $(this).text();
//            var content = $(this).text().split(" ");

            if (content.length > showChar) {

                var c = content.substr(0, showChar);
                var h = content.substr(showChar, content.length - showChar);

                var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink d-inline text-navyblue">' + moretext + '</a></span>';

                $(this).html(html);
            }


        });

        $(".morelink").click(function () {
            if ($(this).hasClass("less")) {
                $(this).removeClass("less");
                $(this).html(moretext);
            } else {
                $(this).addClass("less");
                $(this).html(lesstext);
            }
            $(this).parent().prev().toggle();
            $(this).prev().toggle();
            return false;
        });

        $('#search-item,#mobile-search-item').click(function (e) {
            $("#searchformWrap").removeClass('d-none');
            $("#searchformWrap").addClass('d-flex');
            $("#searchformWrap #s").focus();
        });
        $('#searchformWrap').click(function (e) {
            if (!$(e.target).is("#s")) {
                $(this).addClass('d-none');
                $(this).removeClass('d-flex');
            }
        });
        $('.image_box a').click(function () {
            $('.image_box').removeClass('active');
            $(this).closest('.image_box').addClass('active');
        });

    });
    $('form.loginform_all_page').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: myAjax.ajaxurl,
            data: {
                'action': 'ajax_login', //calls wp_ajax_nopriv_ajaxlogin
                'username': $(this).find('.user_login').val(),
                'password': $(this).find('.user_pass').val(),
                'security': $(this).find('[name="security"]').val(),
                'remember': $(this).find('.rememberme').val(),
                'redirect_to': $(this).find('.redirect_to').val()
            },
            success: function (data) {
                console.log(data.redirect_to);
                if (data.redirect_to == undefined) {
                    $('form.loginform_all_page .loginform_status p.status').addClass('error');
                } else
                {
                    $('form.loginform_all_page .loginform_status p.status').removeClass('error');
                }
                $('form.loginform_all_page .loginform_status p.status').text(data.message);
                if (data.loggedin == true) {
                    document.location.href = data.redirect_to;
                }
            }
        });
    });
    $('form.loginform_single_page').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: myAjax.ajaxurl,
            data: {
                'action': 'ajax_login', //calls wp_ajax_nopriv_ajaxlogin
                'username': $(this).find('.user_login').val(),
                'password': $(this).find('.user_pass').val(),
                'security': $(this).find('[name="security_single"]').val(),
                'remember': $(this).find('.rememberme').val(),
                'redirect_to': $(this).find('.redirect_to').val()
            },
            success: function (data) {
                console.log(data.redirect_to);
                if (data.redirect_to == undefined) {
                    $('form.loginform_single_page .loginform_status p.status').addClass('error');
                } else
                {
                    $('form.loginform_single_page .loginform_status p.status').removeClass('error');
                }
                $('form.loginform_single_page .loginform_status p.status').text(data.message);
                if (data.loggedin == true) {
                    document.location.href = data.redirect_to;
                }
            }
        });
    });
    $('#Login a[data-target="#Registeration"]').on('click', function () {
        $("#Login").modal("toggle");
        // setTimeout(function(){
        //     $('body').addClass('modal-open');
        // }, 200)
        // var __this = $(this).data('target');
        // if (__this == '#Login') {
        //     $("#Registeration").modal("hide");
        // } else
        // {
        //     $("#Login").modal("hide");
        //     $('body').addClass();
        // }
    });
    $('#Registeration a[data-target="#Login"]').on('click', function () {
        $("#Registeration").modal("toggle");
    });
    $("#masthead #close-btn").click(function () {
        jQuery('#mobile-navigation').removeClass('show');
    });
//PDF IFRAME STYLE
    $(window).load(function () {
        $('.single-post .post-pdf-iframe').each(function () {
            var width = parseInt($(this).width());
            var height = parseInt(width * 1.4);
            $(this).find('iframe').css({"width": width, "height": height});
        });
    });
    $(window).resize(function () {
        $('.single-post .post-pdf-iframe').each(function () {
            var width = parseInt($(this).width());
            var height = parseInt(width * 1.4);
            $(this).find('iframe').css({"width": width, "height": height});
        });
    });
    $(document).ready(function () {
        $('.single-post .post-pdf-iframe').each(function () {
            var width = parseInt($(this).width());
            var height = parseInt(width * 1.4);
            $(this).find('iframe').css({"width": width, "height": height});
        });
    });
//STICKY HEADER ON MOBILE
    $(window).scroll(function () {
        var width = $(window).width();
        if (width <= 767) {
            var sticky = $('#masthead'),
                    scroll = $(window).scrollTop();
            if (scroll >= 100) {
                sticky.addClass('sticky-header');
            } else {
                sticky.removeClass('sticky-header');
            }
        }
    });
//    BACK TO TOP
    $(document).ready(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 700) {
                $('.back-top').addClass('open');
            } else {
                $('.back-top').removeClass('open');
            }
        });
        $('.back-top').click(function () {
            if ($("html, body").height() < 5000) {
                $("html, body").animate({scrollTop: 0}, 1000);
            } else if (($("html, body").height() > 5000 && $("html, body").height() < 10000)) {
                $("html, body").animate({scrollTop: 0}, 2000);
            } else if (($("html, body").height() > 10000 && $("html, body").height() < 20000)) {
                $("html, body").animate({scrollTop: 0}, 3000);
            } else {
                $("html, body").animate({scrollTop: 0}, 3500);
            }
            return false;
        });
    });

});
