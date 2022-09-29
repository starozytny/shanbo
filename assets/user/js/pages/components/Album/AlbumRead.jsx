import React, { Component } from 'react';

import { ButtonIcon } from "@dashboardComponents/Tools/Button";

export class AlbumRead extends Component {
    render () {
        return <div className="read-album">
            <div className="toolbar">
                <div className="left">
                    <ButtonIcon icon="left-arrow">Retour</ButtonIcon>
                </div>
                <div className="right">
                    <div className="item">
                        <ButtonIcon icon="add">Ajouter des photos</ButtonIcon>
                    </div>
                </div>
            </div>
        </div>
    }
}
