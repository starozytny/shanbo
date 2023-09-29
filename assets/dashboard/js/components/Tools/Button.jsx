import React from "react";

export function ButtonIcon(props){
    const { icon, isSubmit=false, children, text, onClick, element="button", target="_self", tooltipWidth=null } = props;

    let divStyle = tooltipWidth ? { width: tooltipWidth + "px" } : null;

    if(element === "button"){
        return <button className="btn-icon" type={isSubmit ? "submit" : "button"} onClick={onClick}>
            <span className={`icon-${icon}`} />
            {text && <span>{text}</span>}
            {children && <span className="tooltip" style={divStyle}>{children}</span>}
        </button>
    }else{
        return <a className="btn-icon" target={target} href={onClick}>
            <span className={`icon-${icon}`} />
            {text && <span>{text}</span>}
            {children && <span className="tooltip" style={divStyle}>{children}</span>}
        </a>
    }
}

export function Button(props){
    const { icon, type="primary", isSubmit=false, outline=false, children, onClick, element="button", target="_self",
        isLoader=false, loaderWithText=false, iconPosition="before" } = props;

    let loaderClasse = isLoader ? " btn-loader-" + (loaderWithText ? "with-text" : "without-text") : ""

    if(element === "button"){
        return <button className={`btn${loaderClasse} btn-${outline ? "outline-" : ""}${type}`}
                       type={isSubmit ? "submit" : "button"} onClick={onClick}>
            <Content icon={icon ? icon : (isLoader ? 'chart-3' : '')} iconPosition={iconPosition} children={children} />
        </button>
    }else{
        return <a className={`btn${loaderClasse} btn-${outline ? "outline-" : ""}${type}`}
                  target={target} href={onClick}>
            <Content icon={icon ? icon : (isLoader ? 'chart-3' : '')} iconPosition={iconPosition} children={children} />
        </a>
    }
}

export function ButtonDropdown(props){
    const { items, children } = props;

    return <div className="btn-dropdown">
        <Button {...props}>{children}</Button>
        <div className="dropdown-items">
            {items.map((item, index) => {
                if(item) {
                    return <div className="item" key={index}>
                        {item.data}
                    </div>
                }
            })}
        </div>
    </div>
}

export function ButtonIconDropdown(props){
    const { items, children } = props;

    return <div className="btn-dropdown">
        <ButtonIcon {...props}>{children}</ButtonIcon>
        <div className="dropdown-items">
            {items.map((item, index) => {
                if(item){
                    return <div className="item" key={index}>
                        {item.data}
                    </div>
                }
            })}
        </div>
    </div>
}

function Content ({icon, iconPosition, children}) {
    return <>
        {(icon && iconPosition === "before") && <span className={`icon-${icon}`} />}
        {children && <span>{children}</span>}
        {(icon && iconPosition === "after") && <span className={`icon-${icon}`} />}
    </>
}
