$(function() {
    /*
     * Thumbnails
     */
    $('a[data-toggle=tab][aria-controls=images]').on('shown.bs.tab', function(e) {
        thumbnails();
    });

});


var thumbnails = function() {
    var thumbnailWidth = $('.thumbnail').width();
    $('.thumbnail').css('height', (thumbnailWidth + 10) + 'px');
    $('.thumb span').width(thumbnailWidth);
    $('.thumb span').height(thumbnailWidth);

    $('.thumbnail img').each(function() {
        var imgWidth = $(this).width();
        var imgHeight = $(this).height();

        if (imgWidth > thumbnailWidth || imgHeight > thumbnailWidth) {
            if (imgWidth > imgHeight) {
                var displayHeight = imgHeight * (thumbnailWidth / imgWidth);
                var displayWidth = thumbnailWidth;
            }
            else {
                var displayWidth = imgWidth * (thumbnailWidth / imgHeight);
                var displayHeight = thumbnailWidth;
            }
            $(this).css({'width': displayWidth + 'px', 'height': displayHeight + 'px'});
        }
    });
};
