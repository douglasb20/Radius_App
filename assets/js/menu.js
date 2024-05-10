if ($('#sidebar').length > 0) {
  route = window.location.pathname === '' ? '/' : window.location.pathname;

  $('.nav-link').addClass('collapsed');
  let menu_route = $(`#sidebar [href="${route}"]`);

  if (menu_route.parents('ul').hasClass('nav-content')) {
    menu_route.parents('ul').addClass('show');
    menu_route.addClass('active');
    menu_route.parents('.nav-item').find('.nav-link').removeClass('collapsed');
  } else {
    menu_route.removeClass('collapsed');
  }
}

window.onload = () => EndLoading();
window.addEventListener('beforeunload', () => StartLoading());

$("#formUpdatePass #change_operator_pass, #formUpdatePass #confirm_change_pass").keypress(function (e) {
  if (e.keyCode === 13) {
    $("#btnSalvarPassword").click();
  }
});

let countPassCheck = 0;
$('#formUpdatePass #change_operator_pass').on('keyup keypress keydown', function (e) {
  $('.checkPass').removeClass('accept');
  $('.checkPass span').removeClass('line-through');
  countPassCheck = 0;

  const pass = $(this).val();

  let regexMin = /[a-z]/;
  if (regexMin.test(pass)) {
    $('.checkPass.ctmMin span').addClass('line-through');
    $('.checkPass.ctmMin').addClass('accept');
    countPassCheck++;
  }

  let regexMai = /[A-Z]/;
  if (regexMai.test(pass)) {
    $('.checkPass.ctmMai span').addClass('line-through');
    $('.checkPass.ctmMai').addClass('accept');
    countPassCheck++;
  }

  let regexNumber = /[\d]/;
  if (regexNumber.test(pass)) {
    $('.checkPass.ctmNumber span').addClass('line-through');
    $('.checkPass.ctmNumber').addClass('accept');
    countPassCheck++;
  }

  let regexEsp = /[!@#$%^&*.?]/;
  if (regexEsp.test(pass)) {
    $('.checkPass.ctmEsp span').addClass('line-through');
    $('.checkPass.ctmEsp').addClass('accept');
    countPassCheck++;
  }

  if (pass.toString().length >= 8) {
    $('.checkPass.ctmQtd span').addClass('line-through');
    $('.checkPass.ctmQtd').addClass('accept');
    countPassCheck++;
  }

  if (pass.toString() !== '') {
    if (pass.toString() === $('#confirm_password').val()) {
      $('.checkPass.ctmSame span').addClass('line-through');
      $('.checkPass.ctmSame').addClass('accept');
      countPassCheck++;
    }
  }
});

$('#formUpdatePass #confirm_change_pass').on('keyup keydown', function (e) {
  if($(".checkPass.ctmSame").hasClass("accept")){
    countPassCheck--;
  }

  $(".checkPass.ctmSame").removeClass("accept");
  $(".checkPass.ctmSame span").removeClass("line-through");

  const confPass = ($(this).val())
  if(confPass.toString() !== ""){
    if (confPass.toString() === $("#formUpdatePass #change_operator_pass").val()) {
      $(".checkPass.ctmSame span").addClass("line-through");
      $(".checkPass.ctmSame").addClass("accept");
      countPassCheck++;
    }
  }
});

let modalPassword = new bootstrap.Modal('#modalUpdatePassword', modalOption);
$('#chageUserPass').click(function () {
  $('#formUpdatePass input').not('input[type=hidden]').val('');
  modalPassword.show();
  ModalDraggable();
});

$('#btnSalvarPassword').click(function () {
  confirmaAcao(`Confirma a alteraração de senha?`, UpdatePasswordUser);
});



const UpdatePasswordUser = () => {
  let form = new FormData($('#formUpdatePass')[0]);

  if (countPassCheck < 6) {
    // alerta('Senhas não coincidem.', 'Erro validação', 'error');
    return;
  }

  $.ajax({
    url: $('#url_req').val(),
    type: 'PUT',
    dataType: 'json',
    data: form,
    processData: false,
    contentType: false,
  }).done((resp) => {
    alerta('Senha atualizado com sucesso.', 'Sucesso', 'success');
    CloseModalPassword();
  });
};

const CloseModalPassword = () => {
  modalPassword.hide();
};
