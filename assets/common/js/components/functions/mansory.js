const Masonry = require("masonry-layout/dist/masonry.pkgd.min");
const imagesLoaded = require("imagesloaded/imagesloaded.pkgd.min");

function loadMansory () {
    let elem = document.querySelector('.grid');
    let msnry;

    if (window.matchMedia("(min-width: 1280px)").matches) {
        msnry = new Masonry( elem, {
            itemSelector: '.grid-item',
            columnWidth: '.grid-sizer',
            percentPosition: true,
            gutter: 24,
        });
    } else {
        msnry = new Masonry( elem, {
            itemSelector: '.grid-item',
            columnWidth: '.grid-sizer',
            percentPosition: true,
            gutter: 12,
        });
    }

    imagesLoaded( elem ).on( 'progress', function() {
        // layout Masonry after each image loads
        msnry.layout();
    });
}

module.exports = {
    loadMansory
}
