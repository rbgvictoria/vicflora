$(function() {
    $('a[data-toggle=tab][aria-controls=specimen-images]').on('shown.bs.tab', function(e) {
        var button = $('#showMoreImages');
        if (!button.data('offset')) {
            getImages(button);
        }
    });
    
    $('#specimenImageModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var src = base_url + '/flora/specimen_image_viewer/' + button.data('ala-image-uuid');
      var modal = $(this);
      modal.find('iframe').attr('src', src).attr('title', button.attr('title'));
      modal.find('.modal-title').text(button.data('caption'));
    }).on('shown.bs.modal', function() {
        var modal = $(this);
        var contentHeight = modal.find('.modal-content').height();
        var headerHeight = modal.find('.modal-header').height();
        modal.find('.modal-body').height(contentHeight - headerHeight - 60);
        
    });
    
    $('#showMoreImages').on('click', function() {
        var button = $(this);
        getImages(button);
    });
});

var getImages = function(button) {
    var taxonId = button.data('taxon-id');
    var limit = button.data('limit');
    var offset = button.data('offset');
    var url = base_url + '/ws/specimen_image_thumbnails/' + taxonId;
    var data = {
        "limit": limit,
        "offset": offset
    };

    var ajax = $.ajax({
        url: url,
        data: data
    });
    ajax.done(showMoreThumbnails);
};

var showMoreThumbnails = function(response) {
    var images = response.data;
    var thumbnailRow = $('#tab-specimen-images .thumbnail-row:eq(0)');
    $.each(images, function(index, img) {
       createThumbnail(img, thumbnailRow);
    });
    
    var showMoreButton = $('#showMoreImages');
    var newOffset = response.meta.offset + response.meta.limit;
    showMoreButton.data('offset', newOffset);
    if (newOffset >= response.meta.totalCount) {
        showMoreButton.hide();
    }
};

var createThumbnail = function(img, thumbnailRow)
{
    var figure = $('<figure/>', {
        class: 'col-xs-6 col-sm-4 col-md-3 col-lg-2'
    }).appendTo(thumbnailRow);
    
    var button = $('<button/>', {
        type: 'button',
        class: "thumbnail thumb",
        "data-toggle": "modal",
        "data-target": "#specimenImageModal",
        "data-ala-image-uuid": img.alaImageUuid,
        "data-alt": img.title,
        "data-caption": img.caption
    }).appendTo(figure);
    
    var span = $('<span/>').appendTo(button);
    
    var spinner = $('<span/>', {
        class: "loading",
        html: '<i class="fa fa-spinner fa-spin"></i>',
        style: "font-size:300%;color:#BB9D3D;"
    }).appendTo(span);
    
    var thumbnailWidth = Math.ceil($('#tab-specimen-images .thumbnail:eq(0)').width());
    var image = $('<img/>', {
        src: "https://data.rbg.vic.gov.au/cip/preview/thumbnail/public/" + img.id + '?maxsize=' + thumbnailWidth,
        class: "img-responsive"
    }).appendTo(span);
    
    thumbnailImageWidthHeight(figure, thumbnailWidth);
    image.on('load', function() {
        spinner.remove();
    });
    
};

var specimenImageThumbnails = function() {
    var thumbnailWidth = $('#tab-specimen-images .thumbnail:eq(0)').width();
    
    $('#tab-specimen-images .thumbnail').each(function() {
        var thumb = $(this);
        thumbnailImageWidthHeight(thumb, thumbnailWidth);
    });
};

var thumbnailImageWidthHeight = function(thumb, thumbnailWidth) {
    thumb.height(thumbnailWidth + 27);
    thumb.find('span').width(thumbnailWidth);
    thumb.find('span').height(thumbnailWidth);

    var thumbImg = thumb.find('img').eq(0);
    
    var imgWidth = thumbImg.width();
    var imgHeight = thumbImg.height();

    if (imgWidth > thumbnailWidth || imgHeight > thumbnailWidth) {
        if (imgWidth > imgHeight) {
            var displayHeight = imgHeight * (thumbnailWidth / imgWidth);
            var displayWidth = thumbnailWidth;
        }
        else {
            var displayWidth = imgWidth * (thumbnailWidth / imgHeight);
            var displayHeight = thumbnailWidth;
        }
        thumbImg.css({'width': displayWidth + 'px', 'height': displayHeight + 'px'});
    }
};

