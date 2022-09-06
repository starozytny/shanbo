import '../../css/pages/photo.scss';

import Masonry from "masonry-layout/dist/masonry.pkgd.min";

let elem = document.querySelector('.grid');
let msnry = new Masonry( elem, {
    itemSelector: '.grid-item',
    columnWidth: '.grid-sizer',
    percentPosition: true,
    gutter: 24,
});