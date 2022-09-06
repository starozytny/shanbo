import '../../css/pages/photo.scss';

import Masonry from "masonry-layout/dist/masonry.pkgd.min";
import imagesLoaded from "imagesloaded/imagesloaded.pkgd.min";


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


