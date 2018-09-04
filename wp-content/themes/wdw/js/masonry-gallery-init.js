jQuery(function ($) {
    $('.gallery').each(initMasonry);

    function initMasonry() {
        var container = this;
        var grid = $(this);
        imagesLoaded(container, function () {
            new Masonry(container, {
                itemSelector: '.gallery-item',
                percentPosition: true
            });
        });
    }
});