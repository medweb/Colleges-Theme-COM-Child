jQuery( document ).ready(function($) {

	// Turn twitter feed items into a masonry grid.
    $('.grid').masonry({
        itemSelector: '.grid-item',
        columnWidth: 220,
        horizontalOrder: true,
        gutter: 50
    });

});
