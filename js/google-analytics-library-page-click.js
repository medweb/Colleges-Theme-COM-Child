/**
 * Created by stephen on 1/27/17.
 */
/**
 * Function that tracks a click on an outbound link in Analytics.
 * This function takes a valid URL string as an argument, and uses that URL string
 * as the event label. Setting the transport method to 'beacon' lets the hit be sent
 * using 'navigator.sendBeacon' in browser that support it.
 */
var trackOutboundLink = function(url) {
    ga('clientCOM.send', 'event', 'outbound', 'click', url, {
        'transport': 'beacon',
        //'hitCallback': function(){document.location = url;}
    });
    ga('clientCOMLibrary.send', 'event', 'outbound', 'click', url, {
        'transport': 'beacon',
        //'hitCallback': function(){document.location = url;}
    });
};

/**
 * This function catches all a links that aren't anchor links
 * and tracks them with google analytics.
 */
jQuery(document).ready(function($) {
    // get each 'a' link that isn't an anchor (#)
    $("a:not([href^='#'])").each(function() {
        var href = $(this).attr("href");
        var target = $(this).attr("target");
        var text = $(this).text();

        // when clicked, hook in and track
        $(this).click(function(event) {
            //event.preventDefault();
            if (href.indexOf(window.locationlhost) == -1) {
                trackOutboundLink(href); // run google tracking
                console.log(href);
            }
        });
    })
});
