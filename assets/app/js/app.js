import '../css/app.scss';
import '@commonComponents/functions/toastrOptions';

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from 'react';
import { render } from 'react-dom';

import { ContactForm } from "./pages/components/Contact/ContactForm";
import { Menu }        from "@dashboardComponents/Layout/Menu";
import { Cookies, CookiesGlobalResponse } from "@appComponents/Tools/Cookies";

Routing.setRoutingData(routes);

const menu = document.getElementById("menu");
render(
    <Menu {...menu.dataset} />, menu
)

let cookies = document.getElementById("cookies");
render(
    <Cookies {...cookies.dataset}/>, cookies
)

let cookiesGlobalResponse = document.getElementById("cookies-global-response");
if(cookiesGlobalResponse){
    render(
        <CookiesGlobalResponse {...cookies.dataset}/>, cookiesGlobalResponse
    )
}

let el = document.getElementById("contact");
if(el){
    render(<ContactForm />, el)
}