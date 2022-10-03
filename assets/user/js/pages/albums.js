import "../../css/pages/albums.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import Modal from "@commonComponents/functions/modal";
import { render } from "react-dom";
import { AlbumFormulaire } from "@userPages/components/Album/AlbumForm";
import { AlbumRead } from "@userPages/components/Album/AlbumRead";

Modal.managaModal();

Routing.setRoutingData(routes);

let el = document.getElementById("album-create");
if(el){
    render(<AlbumFormulaire type="create" />, el)
}

el = document.getElementById("album-update");
if(el){
    render(<AlbumFormulaire type="update" element={JSON.parse(el.dataset.element)} />, el)
}

el = document.getElementById("album-read");
if(el){
    render(<AlbumRead {...el.dataset} />, el)
}
