if ($("#sidebar").length > 0) {
    route = window.location.pathname === "" ? "/" : window.location.pathname;
    
    $(".nav-link").addClass("collapsed");
    let menu_route = $(`#sidebar [href="${route}"]`);

    if (menu_route.parents("ul").hasClass("nav-content")) {
        menu_route.parents("ul").addClass("show");
        menu_route.addClass("active");
        menu_route.parents(".nav-item").find(".nav-link").removeClass("collapsed")
    } else {
        menu_route.removeClass("collapsed");
    }

}

// $("#sidebar .nav-item a").not('[data-bs-toggle="collapse"]').click(() => StartLoading())

window.onload = () => EndLoading();
window.addEventListener('beforeunload', () => StartLoading());

let modalPassword = new bootstrap.Modal("#modalUpdatePassword", modalOption); 
$("#chageUserPass").click(function () {
    $("#formUpdatePass input").not("input[type=hidden]").val("")
    modalPassword.show();
    ModalDraggable();
})

$("#btnSalvarPassword").click(function () {
    const password        = $("#formUpdatePass #change_user_pass");
    const confirmPassword = $("#formUpdatePass #confirm_change_pass");

    if(password.val() !== confirmPassword.val()){
        alerta("Senhas não coincidem.", "Erro validação", "error");
        return;
    }

    const frm = required_elements($("#formUpdatePass .required"));

    if(!frm.valid){
        alerta("Campos obrigatórios não preenchidos", "Erro validação", "error")
    }else{
        confirmaAcao(`Confirma alterar senha ?`, UpdatePasswordUser, [])
    }
})

const UpdatePasswordUser = () => {
    let form = new FormData( $("#formUpdatePass")[0] );
    
    $.ajax({url: $("#url_req").val(),type:"POST",dataType:"json", data: form, processData: false, contentType: false})
    .done(resp => {
        alerta("Senha atualizado com sucesso.", "Sucesso", "success");
        CloseModalPassword()
    })
}

const CloseModalPassword = () => {
    modalPassword.hide()
}