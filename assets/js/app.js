/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';
import $ from 'jquery';
import 'bootstrap';

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
    	let self = $(this); console.info(self);

        let elementTop = self.offset().top;
        let elementBottom = elementTop + self.outerHeight();

        let viewportTop = global.window.scrollTop();
        let viewportBottom = viewportTop + global.window.height();

        return elementBottom > viewportTop && elementTop < viewportBottom;
    };

    global.window.on("load scroll", () => {
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
});
