



// ====================================================================


// let datepicker = $.fn.datepicker.noConflict(); // return $.fn.datepicker to previously assigned value
// $.fn.bootstrapDP = datepicker;

let loading = true;
const ModalDraggable = () => $(".modal-dialog").draggable({ handle: ".modal-header" });

const StartLoading   = () => $('body').LoadingOverlay("show",{image:"", fontawesome: "fa-duotone fa-spinner-third fa-spin iconLoading"});
const EndLoading     = () => $('body').LoadingOverlay("hide");
const NoLoading      = () => loading = false;

$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

$(document).ajaxStart(function () {
    if (loading) {
        StartLoading();
    }
});
$(document).ajaxComplete(function(event, xhr, settings) {

    EndLoading();
    loading = true;
});

$.ajaxSetup({
    dataType: 'json',
    error: function (data) {
        $('body').LoadingOverlay("hide");
        if (data.responseJSON) {
            alerta(data.responseJSON.mensagem, "Erro de requisição", "error");
        } else {
            alerta("Ocorreu um erro inesperado, tente denovo em alguns minutos.", "Erro de requisição", "error");
        }
        
    }
})

const caixaAlerta = Swal.mixin(
    {
        showCloseButton: true,
        showConfirmButton: false,
        allowOutsideClick: false,
    }
)

function confirmaAcao(texto, callback, dados, titulo = 'Confirmação', btn_confirma = 'Sim', btn_cancela = 'Não') {
    if (typeof(dados) == 'undefined') {
        dados = []
    }
    caixaAlerta.fire(
        {
            title:titulo,
            html: texto,
            customClass: {
                footer: "arrumaFooterAlert"
            },
            icon: 'question',
            footer: `<button type="button" class="btn btn-outline-danger px-4 " onclick="Swal.close()"  >`+btn_cancela+`</button>
                    <button id='confirmaAcaoSim' type="button" class="btn btn-primary btn-orange with-icon icon-fa-check px-4 ">`+btn_confirma+`</button>`
        }
    )
    
    $('[id=confirmaAcaoSim]:last').unbind();
    $('[id=confirmaAcaoSim]:last').click(function() {
        callback(dados);
        Swal.close();
    });
}

function alertaRedireciona(texto, redirecionamento =  false, icon = 'info', titulo = 'Aviso') {
    caixaAlerta.fire(
        {
            title: titulo,
            html: texto,
            icon: icon,
            willClose: () =>  {if(redirecionamento == -2){
                                    location.reload()
                                } else if(redirecionamento){
                                    window.location.assign(redirecionamento)
                                }else{
                                    history.back()
                                }},
            footer: `<button type="button" class="btn btn-secondary rounded-pill px-4 pl-1" onclick="Swal.close()"  >Fechar</button> `
        }
    )

}

function alerta(texto = '', titulo = null, tipo = 'info') {
    setTimeout( () => {

        if(titulo == null || titulo == ''){
            titulo = 'Aviso'
        }

        caixaAlerta.fire(
            {
                title:titulo,
                html:texto ,
                footer: `<button type="button" class="btn btn-primary px-4 pl-1" onclick="Swal.close()">Fechar</button> `,
                icon: tipo,
            }
        )
    },100)
}

getFormData = form => {

    ret = [];

    for (var value of form.entries()) {
        ret[value[0]] = value[1];

    }

    ret = {...ret};

    return ret;
}

function required_elements(elements) {

    $(".tmp_alert").remove();

    result = [];

    result['valid'] = true;

    result['elements'] = [];

    $.each(elements, function(index, value) {

        if (!$(this).prop("disabled")) {

            if ($(this).val() == '' || $(this).val() == null) {
                $(this).addClass("invalid");

                result['valid'] = false;
                result['elements'].push($(this));

                //Caso for select 2 , jogo a classe obrigatório no select 2 , assim vai mostrar o campo vermelho
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).next().children().children().addClass('invalid');
                }

                // CKeditor
                if ($(this).next().hasClass('cke')) {
                    $(this).next().addClass('invalid');
                }
            } else {

                //Caso for select 2 ,removo invalid
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).next().children().children().removeClass('invalid');
                } else {
                    $(this).removeClass("invalid");
                }

                // CKeditor

                if ($(this).next().hasClass('cke')) {
                    $(this).next().removeClass('invalid');
                }
            }
        }
    });

    return result;
}
