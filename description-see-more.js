(function($) {
    $('.listing-tab').one('mylisting:single:tab-switched', function() {
        var blocks = $('body.single-listing .block-field-job_description > .content-block');
        var collapsed_height = 200; // in px;

        blocks.each( function() {
            var block = $(this);
            if ( block.outerHeight() <= collapsed_height ) {
                return;
            }

            block.find('.pf-body').css( { height: collapsed_height + 'px', overflow: 'hidden' } );
            block.append('<a href="#" class="toggle-more">See More</a>').click( function(e) {
                e.preventDefault();
                if ( block.hasClass('toggled') ) {
                    block.removeClass('toggled');
                    block.find('.pf-body').css( 'height', collapsed_height + 'px' );
                    block.find('.toggle-more').text('See More');
                } else {
                    block.addClass('toggled');
                    block.find('.pf-body').css( 'height', 'auto' );
                    block.find('.toggle-more').text('See Less');
                }
            } );
        } );
    });
})(jQuery);
