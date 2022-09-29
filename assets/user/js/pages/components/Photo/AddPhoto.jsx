import React, { Component } from "react";

import Helper from "@commonComponents/functions/helper";

import { Input } from "@dashboardComponents/Tools/Fields";

export class AddPhoto extends Component {
    constructor(props) {
        super(props);

        this.state = {
            photos: [],
            errors: []
        }

        this.handleChangeFile = this.handleChangeFile.bind(this);
    }

    handleChangeFile = (e) => {
        const { photos } = this.state;

        let files = e.target.files;
        let self = this;

        let rank = 1;
        photos.forEach(p => {
            if(p.isTrash !== true){
                rank++;
            }
        })
        if(files){
            Array.prototype.forEach.call(files, (file) => {
                if(rank <= 100){
                    if(file.size > 30048576){
                        toastr.error("Le fichier est trop gros.")
                    }else{
                        if (/\.(jpe?g|png|gif)$/i.test(file.name)){
                            Helper.getBase64(file, self, rank);
                            rank++;
                        }
                    }
                }else{
                    toastr.error("Vous avez atteint la limite max de photos.")
                }
            })
        }
    }

    render () {
        const { errors, photos } = this.state;

        return <div className="modal-add-photos">

            <div className="input-add-photos">
                <div className="design-input-file">
                    <div className="icon">
                        <span className="icon-image"></span>
                    </div>
                    <div className="name">
                        Faites glisser et déposez jusqu'à <span className="txt-color1">100</span> images
                        ou <span className="txt-color1">parcourez</span> pour choisir un fichier.
                    </div>

                    <div className="files">
                        {photos.map((file, index) => {
                            return <div key={index}>
                                <img src={file.file} alt="photo base64"/>
                            </div>
                        })}
                    </div>
                </div>

                <Input type="file" identifiant="photos" isMultiple={true} valeur={photos}
                       acceptFiles={".jpg, .jpeg, .png"}
                       errors={errors} onChange={this.handleChangeFile}>
                    <span>Photos</span>
                </Input>
            </div>

        </div>
    }
}
