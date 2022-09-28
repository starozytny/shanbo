import React, { Component } from 'react';

import axios                   from "axios";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input }               from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { Drop }                from "@dashboardComponents/Tools/Drop";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

const URL_INDEX              = "user_albums_index";
const URL_CREATE_ELEMENT     = "api_members_albums_create";
const URL_UPDATE_GROUP       = "api_members_albums_update";
const TXT_CREATE_BUTTON_FORM = "Enregistrer";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

export function AlbumFormulaire ({ type, element })
{
    let url = Routing.generate(URL_CREATE_ELEMENT);
    let msg = "Félicitations ! Vous avez ajouté un nouvel album !"

    if(type === "update"){
        url = Routing.generate(URL_UPDATE_GROUP, {'id': element.id});
        msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";
    }

    let form = <Form
        context={type}
        url={url}
        name={element ? element.name : ""}
        cover={element ? element.cover : ""}
        access={element ? element.access : ""}
        messageSuccess={msg}
    />

    return <FormLayout url={Routing.generate(URL_INDEX)} form={form} />
}

export class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            name: props.name,
            cover: props.cover,
            access: props.access,
            errors: [],
            success: false
        }

        this.inputCover = React.createRef();

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = (e) => {
        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        this.setState({[name]: value})
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { name } = this.state;

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "text", id: 'name',  value: name},
        ];

        let inputCover = this.inputCover.current;
        let cover = inputCover ? inputCover.drop.current.files : [];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            Formulaire.loader(true);
            let self = this;

            let formData = new FormData();
            if(cover[0]){
                formData.append('cover', cover[0].file);
            }

            formData.append("data", JSON.stringify(this.state));

            axios({ method: "POST", url: url, data: formData, headers: {'Content-Type': 'multipart/form-data'} })
                .then(function (response) {
                    self.setState({ success: messageSuccess, errors: [] });
                    setTimeout(() => {
                        location.href = response.data.url
                    }, 2000)
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error);
                    Formulaire.loader(false);
                })
            ;
        }
    }

    render () {
        const { context } = this.props;
        const { errors, success, name, cover } = this.state;

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line line-2">
                    <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange}>Nom de l'album</Input>
                    <div className="form-group" />
                </div>

                <div className="line line-2">
                    <Drop ref={this.inputCover} identifiant="cover" previewFile={cover} errors={errors} accept={"image/*"} maxFiles={1}
                          label="Téléverser une photo" labelError="Seules les images sont acceptées.">Image de couverture</Drop>
                    <div className="form-group" />
                </div>

                <div className="line">
                    <div className="form-button">
                        <Button isSubmit={true}>{context === "create" ? TXT_CREATE_BUTTON_FORM : TXT_UPDATE_BUTTON_FORM}</Button>
                    </div>
                </div>
            </form>
        </>
    }
}
