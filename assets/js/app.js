
// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';
import $ from 'jquery';
import 'bootstrap';
import './argon/plugins/chartjs.min'
/*global document, window*/

/* DOM elements with background images */
let backgrounds = document.querySelectorAll(".parallax-background");

$(() => {
    "use strict";
    
    /* global namespace */
    let global = {
    	"window": $(window),
        "document": $(document),
        "parallaxBackground": $(backgrounds)
    };

    /* check if the element is in viewport */
    $.fn.isInViewport = function() {
    	let self = $(this);

        let elementTop = self.offset().top;
        let elementBottom = elementTop + self.outerHeight();

        let viewportTop = global.window.scrollTop();
        let viewportBottom = viewportTop + global.window.height();

        return elementBottom > viewportTop && elementTop < viewportBottom;
    };

    global.window.on("scroll", () => {
        let scroll = global.document.scrollTop();
        let offset = -0.4;

        global.parallaxBackground.each(function() {
        	let self = $(this);
            let selfPosition = self.offset().top;

        	if (self.isInViewport()) {
                self.css({
                    "background-position": "50% " + (selfPosition * offset - scroll * offset) + "px"
                });
			}
        });
    });

    /*$('.modal-trigger').click(function () {
        $('#sign_modal .modal-content').html('<div class="loading-container"><div class="sb-loading sb-loading--blue"></div></div>');
        $('#sign_modal .modal-content').css('min-height', '340px');
        $('#sign_modal').modal();

        let url = $(this).attr('data-target');
        $.get(url, function (data) {
            $('.modal-content').html(data);
            $('#inscription_modal').modal();
        });
    });*/

    /* --- GESTION INPUT --- */
    $('.form-group').each(function() {
        if($(this).find('input').val() != '') {
            $(this).find('input').addClass('active');
        }

        $(this).find('input').blur(function(){
            if($(this).val() == '') {
                $(this).removeClass('active');
            } else {
                $(this).addClass('active');
            }
        });
    });

    /* --- GESTION CLICK MENU FOND SOMBRE --- */
    $('.navbar-toggler').click(function() {
        $('.responsive-dark-background').toggle();
        $(this).find('#nav-icon-menu').toggleClass('open');
        $('body').toggleClass('hidden');
    });

    /* --- GESTION CLICK BOUTON SCROLL TOP --- */
    $('#btn-scroll-top').click(function() {
        $('html, body').animate({scrollTop:0},500);
    });

    $(window).on("scroll", function() {
        var scrollHeight = $(document).height();
        var scrollPosition = $(window).height() + $(window).scrollTop();
        if ((scrollHeight - scrollPosition) / scrollHeight <= 0.5) {
            $('#btn-scroll-top').css('display', 'block');
        } else {
            $('#btn-scroll-top').css('display', 'none');
        }
    });
    var hashtag = window.location.hash;
    $("[href='" + hashtag + "']").click();
    $(".nav a").on('click', function(){
        window.location.hash = $(this).attr('href');
    });
    /* --- GESTION MODAL ERREURS --- */
    if($('.alert-modal').length) {
        $('.alert-modal').modal('show');
    }


});
