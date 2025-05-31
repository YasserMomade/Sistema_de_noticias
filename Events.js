let downUp = false;
const size_ajust = document.getElementById('topicos');
const down_icon = document.getElementById('down_icon');

function categoryEvt(){
    // downUp = !downUp;
    if(!downUp){
        size_ajust.style.height = 'auto';
        size_ajust.style.transition = '2s';
        down_icon.style.transform = 'rotate(180deg)';
        size_ajust.style.boxShadow = '0px 0px 7px 0px #00000031';
    }else{
        size_ajust.style.height= '49px';
        size_ajust.style.boxShadow = '0px 0px 0px 0px #00000031';
        down_icon.style.transform = 'rotate(0deg)';
    }
}

function toogleClose(){
    size_ajust.style.transition = '1s';
    size_ajust.style.height = '49px';
    size_ajust.style.boxShadow = '0px 0px 0px 0px #00000031';
}

const publisherFrame = document.getElementById('publisher-login');
function toogle_cadastro(){
    publisherFrame.style.display = 'flex';
}

function toogle_closeBox(){
    publisherFrame.style.display = 'none';
}

// CADASTRO UTILIZADOR
const hideShow = false;
const userFrame = document.getElementById('cadastro-utilizador');
function tgl_cadastro(){
    userFrame.style.display = 'flex';
}

function toogle_closedBox(){
    userFrame.style.display = 'none';
}