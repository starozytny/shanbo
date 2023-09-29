import "../../css/pages/photos.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React   from "react";
import Modal   from "@commonComponents/functions/modal";
import Mansory from "@commonComponents/functions/mansory";
import { render } from "react-dom";
import { AddPhoto } from "@userPages/components/Photo/AddPhoto";

Modal.managaModal();
Mansory.loadMansory();

Routing.setRoutingData(routes);

let el = document.getElementById("drop-add-photos");
if(el){
    render(<AddPhoto type="create" />, el)
}
