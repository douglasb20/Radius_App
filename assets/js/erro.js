

//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////// VARIAVEIS GLOBAIS ///////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////

let url_erro_log    = '';
let erro_def_url    = false
var erro_JSON       = [];
var config_erro     = [];
let aberto          = false;
let ambiente        = 'producao';


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////

iniciaUrl = () => {
    switch(document.location.hostname){
        case 'portal.cedet.com.br':
        case 'portal-intra.cedet.com.br':
        case 'parceiros.cedet.com.br':
        case 'api.cedet.com.br':
            url_erro_log = "https://log.cedet.com.br/portal-prod/";
        break;
        
        case 'portaldev.cedet.com.br':
            url_erro_log = "https://log.cedet.com.br/portal-prod/";
            ambiente     = 'teste';
        break;
        case 'cedetusa.com':
        default:
            url_erro_log = "http://cedetlocal.wslog.com/";
            ambiente     = 'teste';
        break;
    }
    
};

iniciaUrl();



// essa função trata o erro para saber quais dados serão enviados para o WSLog
trataErro = (xhr, settings) => {
    erro_JSON   = [];
    config_erro = [];
    
    if(!erro_def_url){

        let dt              = new Date();
        let msg             = "";
        let msg_deadlock    = "";
        let id_log          = "";
        let descricao       = "";
        var post_fields     = "";
        let status          = 200;
        let show_btns       = false;
        let envia_direto    = false;
    
        
        if(xhr.status == 500){
            if(xhr.responseJSON != undefined){
                msg = xhr.responseJSON.erroMensagem;
                if(xhr.responseJSON.codeResponse != undefined){
                    status = xhr.responseJSON.codeResponse;
                }
            }else{
                msg = xhr.statusText;
            }

            show_btns = true;
        }else{
            if(xhr.responseJSON != undefined){
                if(xhr.responseJSON.erro != undefined) {
                    if(xhr.responseJSON.erro){

                        if(xhr.responseJSON.erroMensagem != undefined){
                            msg = xhr.responseJSON.erroMensagem;
                        }else{
                            msg = xhr.responseJSON.mensagem;
                        }

                        if(xhr.responseJSON.id_log_user != undefined){
                            id_log = xhr.responseJSON.id_log_user;
                        }
                        if(xhr.responseJSON.codeResponse != undefined){
                            status = xhr.responseJSON.codeResponse;
                        }
    
                        if(xhr.responseJSON.codeResponse != 204){
                            show_btns = true;
                        }
                        
                        if(xhr.responseJSON.codeResponse == 401){
                            show_btns = false;
                        }

                        if(status != 401){
                            if(msg.toLowerCase().includes('deadlock ')){
                                show_btns       = false;
                                envia_direto    = true;
                                descricao       = 'Deadlock no banco';
                                msg_deadlock    = 'Houve um problema na requisição, aguarde um momento e tente novamente mais tarde.<br><br>Já foi enviado um log para o servidor.'
                            }
                        }
                    }
                    
                }else{
                    msg         = xhr.responseText;
                    status      = xhr.status;
                    show_btns   = true;
                }
            }
        }
    
        if(settings.data != undefined){
            
            if(settings.data instanceof FormData){
                
                post_fields = getFormData(settings.data);
                post_fields = $.param(post_fields);
                
            }else{
                post_fields = "";
                post_fields = settings.data;
            }
        }
        

        config_erro = {
            show_btns       : show_btns,
            code            : status,
            envia_direto    : false,
            erro_dl         : msg_deadlock
        }
        id_usuario_erro = document.getElementById("error_id_usuario").value;
        erro_JSON = {
            codigo_erro   : status,
            descricao     : "",
            msg           : msg,
            portal_origem : 'portal_usa',
            data          : dt.toLocaleString(),
            id_usuario    : id_usuario_erro == "" ? null : id_usuario_erro,
            id_log_user   : id_log,
            url           : document.location.href,
            path_req      : settings.url,
            post_fields   : post_fields
        }

        if(envia_direto){
            enviaErro(false, descricao);
        }
    }

}


// Função onde será enviado os dados para o WSLogPortal
enviaErro = (mostra_alerta = true, descricao = '') => {

    if(descricao == ''){
        erro_JSON.descricao = document.getElementById("error_descricao").value;
    }else{
        erro_JSON.descricao = descricao;
    }

    var token       = encryptAJAX(erro_JSON);
    var dt          = new Date();
    var data        = ('0' + dt.getDate()).slice(-2) + ('0' + (dt.getMonth()+1)).slice(-2) + dt.getFullYear().toString().substring(2);
    var secret_iv   = data+`549d0aad4f261463b179c94c2ea3c736` ;
    var iv          = CryptoJS.SHA256(secret_iv); 
    
    
    fetch(url_erro_log, 
        {
            method: "POST",
            credentials: 'same-origin',
            body:  $.param( {token : token } ) ,
            headers: {
                "X-Token": iv, 
                "origin-url": window.location.pathname,
                'Content-Type':'application/x-www-form-urlencoded',
                'Accept': 'application/json',
            }
        }
    )
    .then( resp => resp.json() )
    .then( resp => {
        resp    = decryptaAJAX(resp);
        
        if(ambiente != 'producao'){
            console.log(resp);
        }

        if(resp.erro){
            alerta(`Erro ao enviar Log para o webservice, avise ao TI Motivo: <b>${resp.mensagem}</b>`);
            console.log(resp)
        }else{
            
            try{
                document.getElementById("error_descricao").value = "";
            }catch(e){

            }
            

            if(mostra_alerta){
                alerta("Log de erro enviado com sucesso.",'success');
            }
        }
    });

}


// Funcao para encryptar dados do Ajax para enviar par ao WSLogPortal
encryptAJAX = dados => {

    let validation      = getValidation();
    dados.validation    = validation;

    dados               = JSON.stringify(dados);
    dados               = CryptoJS.AES.encrypt(dados, validation, {format: CryptoJSAesJson});
    return dados.toString();
}

decryptaAJAX = dados => {
    let decrypt = CryptoJS.AES.decrypt(JSON.stringify(dados), getValidation(), {format: CryptoJSAesJson}).toString(CryptoJS.enc.Utf8);
    let ret    = JSON.parse(decrypt);

    return ret;
}

getValidation = () => {
    var dt              = new Date();
    var data = ('0' + dt.getDate()).slice(-2) + ('0' + (dt.getMonth()+1)).slice(-2) + dt.getFullYear().toString().substring(2);
    var valid_key       = data+`c09275cbd16a911a00c3a077e36f379b` ;

    return CryptoJS.SHA256(valid_key).toString();
}
var CryptoJSAesJson = {
    stringify: cipherParams =>  {
        var j = {ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64)};
        if (cipherParams.iv) j.iv = cipherParams.iv.toString();
        if (cipherParams.salt) j.s = cipherParams.salt.toString();
        return JSON.stringify(j);
    },
    parse: jsonStr => {
        var j = JSON.parse(jsonStr);
        var cipherParams = CryptoJS.lib.CipherParams.create({ciphertext: CryptoJS.enc.Base64.parse(j.ct)});
        if (j.iv) cipherParams.iv = CryptoJS.enc.Hex.parse(j.iv)
        if (j.s) cipherParams.salt = CryptoJS.enc.Hex.parse(j.s)
        return cipherParams;
    }
}

