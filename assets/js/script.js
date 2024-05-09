const modalOption = {
  backdrop: "static",
  keyboard: true,
};

// ====================================================================

//  CORREÇÃO MODAL NESTED BACKDROP
$(document).on("click", ".modal", function () {
  // if more than 1 modal openned.
  if ($(".modal-backdrop").length > 1) {
    // move backdrop up (in z)
    var ZindexBackdrop =
      parseInt($(".modal-backdrop").eq(0).css("z-index")) + 20;
    $(".modal-backdrop").eq(1).css({ "z-index": ZindexBackdrop });

    // move modal up (in z)
    var ZindexModal = parseInt($(".modal").eq(0).css("z-index")) + 20;
    $(".modal").eq(2).css({ "z-index": ZindexModal });
  }
});

$.extend($.fn.datepicker.defaults, {
  format: "dd-mm-yyyy",
  language: "pt-BR",
  autoclose: true,
});
$.fn.select2.defaults.set("language", "pt-BR");
$.fn.select2.defaults.set("width", "100%");
$.fn.select2.defaults.set("theme", "bootstrap-5");
$.fn.select2.defaults.set("selectionCssClass", "select2--small");
$.fn.select2.defaults.set("dropdownCssClass", "select2--small");
// let datepicker = $.fn.datepicker.noConflict(); // return $.fn.datepicker to previously assigned value
// $.fn.bootstrapDP = datepicker;

$(() => {
  $("body").tooltip({
    selector: '[data-toggle="tooltip"]',
    trigger: "hover", // corrigindo bug de não fechar tooltip apos clicar no botao e abrir algum modal
    delay: { show: 200, hide: 0 },
  });

  renderizaTooltip();

  $(document).on("click", ".select2-selection", function () {
    setTimeout(() => {
      let el = document.querySelector(".select2-search__field");
      if (el != null) {
        el.focus();
      }
    }, 150);
  });

  $(".selectWithAll").on("select2:select", function ({ params: { data } }) {
    // Do something
    if (data.id === "-1") {
      $(this).select2("close").val("-1").change();
    }
  });

  $(".selectWithAll").on("change", function (e) {
    if ($(this).val() == "") {
      $(this).val("-1");
    }

    if ($(this).val().length > 1) {
      if ($(this).val().includes("-1")) {
        var select = $(this);

        // Remove o item com valor "2" da seleção
        var selectedValues = select.val(); // Obtém os valores selecionados
        var indexToRemove = selectedValues.indexOf("-1"); // Encontra o índice do valor a ser removido
        if (indexToRemove !== -1) {
          // Se o valor existe na seleção
          selectedValues.splice(indexToRemove, 1); // Remove o valor do array
        }
        select.val(selectedValues); // Atualiza a seleção do Select2
      }
    }
  });

  $(".dateNoEmpty").change(function () {
    if ($(this).val() === "") {
      $(this).val(moment().format("DD/MM/YYYY"));
    }
  });

  $(".modal").on("hidden.bs.modal", (event) => {
    $(".modal .ui-draggable").removeAttr("style");
  });
});

let loading = true;
const ModalDraggable = () =>
  $(".modal-dialog").draggable({ handle: ".modal-header" });

const StartLoading = () => {
  $("body").LoadingOverlay("show", {
    image: "",
    fontawesome: "fa-duotone fa-spinner-third fa-spin iconLoading",
  });
};

const EndLoading = () => $("body").LoadingOverlay("hide");
const NoLoading = () => (loading = false);
$.fn.serializeObject = function () {
  var o = {};
  var a = this.serializeArray();
  $.each(a, function () {
    if (o[this.name] !== undefined) {
      if (!o[this.name].push) {
        o[this.name] = [o[this.name]];
      }
      o[this.name].push(this.value || "");
    } else {
      o[this.name] = this.value || "";
    }
  });
  return o;
};

$(document).ajaxStart(function () {
  if (loading) {
    StartLoading();
  }
});
$(document).ajaxComplete(function (event, xhr, settings) {
  EndLoading();
  loading = true;
});

$.ajaxSetup({
  dataType: "json",
  error: function (data) {
    $("body").LoadingOverlay("hide");
    if (data.responseJSON) {
      alerta(data.responseJSON.mensagem, "Erro de requisição", "error");
    } else {
      alerta(
        "Ocorreu um erro inesperado, tente denovo em alguns minutos.",
        "Erro de requisição",
        "error"
      );
    }
  },
});

const caixaAlerta = Swal.mixin({
  showCloseButton: true,
  showConfirmButton: false,
  allowOutsideClick: false,
});

function confirmaAcao(
  texto,
  callback,
  dados = [],
  titulo = "Confirmação",
  btn_confirma = "Sim",
  btn_cancela = "Não"
) {
  if (typeof dados == "undefined") {
    dados = [];
  }
  caixaAlerta.fire({
    title: titulo,
    html: texto,
    customClass: {
      footer: "arrumaFooterAlert",
    },
    icon: "question",
    footer:
      `<button type="button" class="btn btn-outline-danger px-4 " onclick="Swal.close()"  >` +
      btn_cancela +
      `</button>
                    <button id='confirmaAcaoSim' type="button" class="btn btn-primary btn-orange with-icon icon-fa-check px-4 ">` +
      btn_confirma +
      `</button>`,
  });

  $("[id=confirmaAcaoSim]:last").unbind();
  $("[id=confirmaAcaoSim]:last").click(function () {
    callback(dados);
    Swal.close();
  });
}

const UcWords = (str) => {
  return (str + "")
    .toLowerCase()
    .replace(/^([a-z])|\s+([a-z])/g, ($1) => $1.toUpperCase());
};

function alertaRedireciona(
  texto,
  redirecionamento = false,
  icon = "info",
  titulo = "Aviso"
) {
  caixaAlerta.fire({
    title: titulo,
    html: texto,
    icon: icon,
    willClose: () => {
      if (redirecionamento == -2) {
        location.reload();
      } else if (redirecionamento) {
        window.location.assign(redirecionamento);
      } else {
        history.back();
      }
    },
    footer: `<button type="button" class="btn btn-secondary rounded-pill px-4 pl-1" onclick="Swal.close()"  >Fechar</button> `,
  });
}

function alerta(texto = "", titulo = null, tipo = "info") {
  setTimeout(() => {
    if (titulo == null || titulo == "") {
      titulo = "Aviso";
    }

    caixaAlerta.fire({
      title: titulo,
      html: texto,
      footer: `<button type="button" class="btn btn-primary px-4 pl-1" onclick="Swal.close()">Fechar</button> `,
      icon: tipo,
    });
  }, 100);
}

function alertaErro(texto = "", titulo = null) {
  setTimeout(() => {
    let tipo = "error";
    if (titulo == null || titulo == "") {
      titulo = "Erro";
    }

    caixaAlerta.fire({
      title: titulo,
      html: texto,
      footer: `<button type="button" class="btn btn-primary px-4 pl-1" onclick="Swal.close()">Fechar</button> `,
      icon: tipo,
    });
  }, 100);
}

getFormData = (form) => {
  ret = [];

  for (var value of form.entries()) {
    ret[value[0]] = value[1];
  }

  ret = { ...ret };

  return ret;
};

function formataImagemSelect2(state) {
  var textoSelect2 = "";

  textoSelect2 += '<div style="display:flex;">';

  textoSelect2 +=
    '<div class="col-xs-1"><img src="' +
    state.caminho_imagem +
    '" style="height:70px;width:50px;"/></div>';
  textoSelect2 +=
    '<div class="col-xs-11" style="padding-left:40px;font-size:14px;">' +
    state.text +
    "</div>";

  textoSelect2 += "</div>";

  return $(textoSelect2);
}

function popula_dados(form, json, newIndexCode) {
  $.each(json, function (index, value) {
    if (typeof newIndexCode != "undefined") {
      index = newIndexCode.replace("index", index);
    }
    if (!$("input[name='" + index + "']", form).is(":file")) {
      if (
        $("input[name='" + index + "']", form).is(":checkbox") ||
        $("input[name='" + index + "']", form).is(":radio")
      ) {
        $("input[name='" + index + "'][value='" + value + "']", form).prop(
          "checked",
          true
        );
      } else {
        $("input[name='" + index + "']", form).val(value);
        $("select[name='" + index + "']", form).val(value);
        $("textarea[name='" + index + "']", form).val(value);
        $("img[name='" + index + "']", form).attr("src", value);
      }
    }
  });
}

function clear_form_elements(ele) {
  $(ele)
    .find(":input")
    .each(function () {
      switch (this.type) {
        case "password":
        case "select-multiple":
        case "select-one":
        case "text":
        case "hidden":
        case "textarea":
          $(this).val("");
          break;
        case "checkbox":
        case "radio":
          //  this.checked = false;
          $(this).removeAttr("checked");
          break;
        case "file":
          $(this).val(null);
          break;
      }
    });
}

function remove_required(ele) {
  $(ele).each(function () {
    switch (this.type) {
      case "password":
      case "select-multiple":
      case "select-one":
      case "text":
      case "hidden":
      case "textarea":
      case "checkbox":
      case "radio":
      case "email":
        $(this).removeClass("invalid");

        if ($(this).hasClass("select2-hidden-accessible")) {
          $(this).next().children().children().removeClass("invalid");
        }
        break;
    }
  });

  $(".tmp_alert_valida").remove();
}

function required_elements(elements) {
  $(".tmp_alert").remove();

  result = [];

  result["valid"] = true;

  result["elements"] = [];

  $.each(elements, function (index, value) {
    if (!$(this).prop("disabled")) {
      if ($(this).val() == "" || $(this).val() == null) {
        $(this).addClass("invalid");

        result["valid"] = false;
        result["elements"].push($(this));

        //Caso for select 2 , jogo a classe obrigatório no select 2 , assim vai mostrar o campo vermelho
        if ($(this).hasClass("select2-hidden-accessible")) {
          $(this).next().children().children().addClass("invalid");
        }

        // CKeditor
        if ($(this).next().hasClass("cke")) {
          $(this).next().addClass("invalid");
        }
      } else {
        //Caso for select 2 ,removo invalid
        if ($(this).hasClass("select2-hidden-accessible")) {
          $(this).next().children().children().removeClass("invalid");
        } else {
          $(this).removeClass("invalid");
        }

        // CKeditor

        if ($(this).next().hasClass("cke")) {
          $(this).next().removeClass("invalid");
        }
      }
    }
  });

  return result;
}

function stringToDate(data) {
  var data = data.split("/");

  return new Date(data[2], data[1] - 1, data[0]);
}

function dateToScreen(data) {
  var data = data.split("-");
  return data[2] + "/" + data[1] + "/" + data[0];
}

function dateToScreenComHora(data) {
  if (data == null || data == "") return "";
  var tmp = data.split(" ");
  var data = tmp[0].split("-");
  return data[2] + "/" + data[1] + "/" + data[0] + " " + tmp[1];
}

function moneyToFloat(money) {
  money = money.replace(/\./g, "");
  money = money.replace(",", ".");
  money = parseFloat(money);
  return money;
}

function floatToMoney(money) {
  return money.toLocaleString("en-US", { style: "currency", currency: "USD" });
}

renderizaTooltip = () => {
  return $(`[title]`)
    .not(
      ".select2-selection__choice, .select2-selection__rendered, .select2-selection__choice__remove"
    )
    .attr("data-toggle", "tooltip");
};

const secToTime = (sec) => {
  var hours = Math.floor(sec / 3600);
  var minutes = Math.floor((sec - hours * 3600) / 60);
  var seconds = sec - hours * 3600 - minutes * 60;

  hours = `00${hours}`.slice(-2);
  minutes = `00${minutes}`.slice(-2);
  seconds = `00${seconds}`.slice(-2);

  return hours + ":" + minutes + ":" + seconds;
};

const tempoParaSegundos = (tempo) => {
  var partes = tempo.split(":");
  var horas = parseInt(partes[0]);
  var minutos = parseInt(partes[1]);
  var segundos = parseInt(partes[2]);

  var totalSegundos = horas * 3600 + minutos * 60 + segundos;
  return totalSegundos;
};

const ErrorLabels = (e) => {
  $(".error_label").addClass("hidden");
  for (let er in e) {
    if (!e[er].isValid && e[er].isValid !== undefined) {
      let error_label = $(e[er].elem).parents(".input-float").find("small");
      error_label.removeClass("hidden").text(e[er].errorMessage);
    }
  }
};
