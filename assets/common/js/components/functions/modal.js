function managaModal () {
    let btnsModal = document.querySelectorAll('.modal-btn');
    btnsModal.forEach(btn => {
        btn.addEventListener('click', (e) => {
            let modal = document.querySelector(btn.dataset.modal);
            if(modal){
                if(modal.classList.contains('active')){
                    modal.classList.remove('active');
                }else{
                    modal.classList.add('active');
                }
            }
        })
    })
}

module.exports = {
    managaModal
}
