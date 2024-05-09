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

const validFormPass = new JustValidate('#formUpdatePass', {
  // errorLabelCssClass: ['hidden'],
  errorFieldCssClass: ['invalid'],
  focusInvalidField: false,
});

validFormPass
  .addField('#change_operator_pass', [
    {
      rule: 'required',
      errorMessage: 'Campo não pode ficar em branco',
    },
    {
      rule: 'minLength',
      value: 4,
      errorMessage: 'Senha deve ter mais de 4 dígitos',
    },
  ])
  .addField('#confirm_change_pass', [
    {
      rule: 'required',
      errorMessage: 'Campo não pode ficar em branco',
    },
    {
      validator: (val) => $('#formUpdatePass #change_operator_pass').val() === val,
      errorMessage: 'Senhas não coincidem',
    },
  ])
  .onSuccess(function () {
    confirmaAcao(`Confirma a alteraração de senha?`, UpdatePasswordUser);
  });

let modalPassword = new bootstrap.Modal('#modalUpdatePassword', modalOption);
$('#chageUserPass').click(function () {
  $('#formUpdatePass input').not('input[type=hidden]').val('');
  validFormPass.refresh();
  modalPassword.show();
  ModalDraggable();
});

const UpdatePasswordUser = () => {
  let form = new FormData($('#formUpdatePass')[0]);

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
