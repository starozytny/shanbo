import React, { Component } from 'react';

import axios      from "axios";
import Routing    from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import Validateur from "@commonComponents/functions/validateur";
import Formulaire from "@dashboardComponents/functions/Formulaire";

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Aside } from "@dashboardComponents/Tools/Aside";
import { Input } from "@dashboardComponents/Tools/Fields";

const URL_UPDATE_ELEMENT = "";
const URL_CREATE_ELEMENT = "";

export class AlbumRead extends Component {
    constructor(props) {
        super();

        this.state = {
            groups: [],
            group: null
        }

        this.asideGroup = React.createRef();

        this.handleOpenGroup = this.handleOpenGroup.bind(this);
    }

    handleOpenGroup = (e) => { this.asideGroup.current.handleOpen(); }

    render () {
        const { groups, group } = this.state;

        return <div className="read-album">
            <div className="toolbar">
                <div className="left">
                    <ButtonIcon icon="left-arrow">Retour</ButtonIcon>
                </div>
                <div className="right">
                    <div className="item">
                        <ButtonIcon tooltipWidth={110} icon="add-square" onClick={this.handleOpenGroup}>Ajouter des groupes</ButtonIcon>
                        <ButtonIcon tooltipWidth={110} icon="add">Ajouter des photos</ButtonIcon>
                    </div>
                </div>
            </div>

            <div className="content">

            </div>

            <Aside ref={this.asideGroup} content={<GroupForm element={group} />} />
        </div>
    }
}

export class GroupForm extends Component {
    constructor(props) {
        super();

        this.state = {
            name: props.element ? props.element.name : "",
            errors: [],
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = (e) => { this.setState({ [e.currentTarget.name]: e.currentTarget.value }) }

    handleSubmit = (e) => {
        e.preventDefault();

        const { element } = this.props;
        const { name } = this.state;

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "text", id: 'name',  value: name},
        ];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            Formulaire.loader(true);
            let self = this;

            let url = element ? Routing.generate(URL_UPDATE_ELEMENT, {'id': element.id}) : Routing.generate(URL_CREATE_ELEMENT);
            let methode = element ? "PUT" : "POST";

            axios({ method: methode, url: url, data: this.state})
                .then(function (response) {
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error);
                    Formulaire.loader(false);
                })
            ;
        }
    }

    render () {
        const { errors, name } = this.state;

        return <form className="form" onSubmit={this.handleSubmit}>
            <div className="line">
                <Input identifiant="name" valeur={name} onChange={this.handleChange} errors={errors} >Nom du groupe</Input>
            </div>

            <div className="line line-buttons">
                <Button isSubmit={true}>Enregistrer</Button>
            </div>
        </form>
    }
}
