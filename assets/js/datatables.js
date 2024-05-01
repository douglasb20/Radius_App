// Custom Button export excel correto
$.fn.dataTable.ext.buttons.excelNumber = {
    text: 'Excel',
    extend: 'excel',
    exportOptions: {
        format: {
            body: function(data, row, column, node) {

                var tmpData = data;
                if (typeof data == 'string') {

                    var regex = /value=\"((-|)\d+[\.|\,]*\d*)\"/;
                    var valor = data.match(regex);
                    if (valor) {
                        if (valor[1]) {
                            tmpData = valor[1];
                        }
                    }

                    var tmpData2 = tmpData.replace(/[,.]/g, '');

                    // Caso for um número , formata corretamente
                    if (!isNaN(tmpData2)) {

                        tmpData = tmpData.replace(/[,]/g, '.');
                        tmpData = tmpData.replace(/[.](?=.*[.])/g, "");

                    }
                }

                return tmpData;

            }
        }
    }
};

$.extend(true, $.fn.dataTable.defaults, {
    columnDefs: [
        { defaultContent: " ", targets: '_all', orderDataType: 'orderAll' },
        { responsivePriority: 1, targets: -1 },
    ],
    language: {
        decimal: ",",
        thousands: ".",
        lengthMenu: "_MENU_ registros por página",
        emptyTable: "Nenhum registro encontrado",
        zeroRecords: "Nenhum registro encontrado",
        info: "Página _PAGE_ de _PAGES_ (_TOTAL_ registros)",
        infoEmpty: "Nenhum registro encontrado.",
        infoFiltered: "(filtrado de _MAX_ registros)",
        sSearch: "Buscar",
        paginate: {
            previous: "<i class='fa-solid fa-chevron-left'></i>",
            next: "<i class='fa-solid fa-chevron-right'></i>",
            sFirst: "1",
			sLast: "_TOTAL_",
            info: '_INPUT_'
        },
        buttons: {
            pageLength: {
                '_': "Paginação (%d)",
                '-1': "Paginação (Todos)"
            }
        }
    },
    order: [[1, "asc"]],
    info: true,
    responsive: {
        details: {
            type: 'column',
            target: 'tr'
        }
    },
	pageLength: 20,
    dom: '<"header-table"Bfr>t<"footer-table"ip>',
    lengthMenu: [[20, 50, 100, 200], [20, 50, 100, 200]],
    buttons: ['pageLength',exportMenu('csv', 'pdf', 'excelNumber')],
    drawCallback: function(){ renderizaTooltip()}
});

// Função para adicionar item no dataTable sempre na primeira na linha

$.fn.dataTable.Api.register('row.addByPos()', function(data, index) {
    var currentPage = this.page();

    //insert the row
    this.row.add(data);

    //move added row to desired index
    var rowCount = this.data().length - 1,
        insertedRow = this.row(rowCount).data(),
        tempRow,
        table = this;

    for (var i = rowCount; i >= index; i--) {
        tempRow = table.row(i - 1).data();
        this.row(i).data(tempRow);
        this.row(i - 1).data(insertedRow);
    }

    //refresh the current page
    this.page(currentPage).draw(false);
});

$.fn.dataTable.Api.register('btnsAdicionais', function(data, index){


    new $.fn.dataTable.Buttons(this, {
        buttons: data
    });
    this.buttons(1, null).container().appendTo(
        $('.dt-buttons.btn-group .btn-group:eq(0)')
    );

})

// Função de ordenação genérica para todas as colunas
$.fn.dataTable.ext.order['orderAll'] = function(settings, col) {
    return this.api().column(col, { order: 'index' }).nodes().map(function(td, i) {

        var data = $(td).html();

        if ($(td).find('button').length) {

            var data = $('button', td).html();

            var dataSplit = data.split(',');

            if (dataSplit.length > 1) {
                data = dataSplit[0];
            }

        }

        if ($(td).find('input').length) {
            var data = $('input', td).val();
        }

        if (/<[a-z][\s\S]*>/i.test(data)) { // Não é botão e nem input, pego o texto dentro caso for html
            data = $(data).text();
        }

        if (typeof data == 'string') {

            var tmpData = data.replace(/[,.:%]|(R\$)/g, '');

            // Caso for um número , formata corretamente

            if (!isNaN(tmpData)) {

                data = data.replace(/[,%]|(R\$)/g, '.');
                data = data.replace(/[.](?=.*[.])/g, "");
                data = data * 1;

            } else {

                // Caso for uma data
                if (data.match(/^\d{2}[./\/]\d{2}[./\/]\d{4}$/)) {
                    data = data.split("/").reverse().join("");
                } else {
                    // Caso data com hora
                    if (data.match(/^\d{2}[./\/]\d{2}[./\/]\d{4} \d{2}:\d{2}:\d{2}$/)) {
                        var data = data.split(" ");
                        data[0] = data[0].split("/").reverse().join("");
                        data = data.join("");
                    }
                }
            }
        }

        return data;

    });
}

// nova regra de "link" no dataTable assim captura qual botao do maouse foi clicado
// document.body.addEventListener('keydown',   e =>  tecla = e.keyCode );
// document.body.addEventListener('keyup',     e =>  tecla = null );

// document.body.addEventListener('mousedown', e => {

//     mClick  = e
//     isTable = false;
//     if(e.target.nodeName.toLocaleLowerCase() !== 'body' || e.target.offsetParent != undefined  ){
//         if(e.target.offsetParent != null){
//             isTable = e.target.offsetParent.nodeName.toLocaleLowerCase() == 'table' ? true : false;
//         }
//     }

//     if((e.target.type == 'button' || isTable) && e.button == 1){
//         e.preventDefault();
//         e.stopPropagation();
//     }
// })

//document.body.addEventListener('mouseup', e => mClick = null);

function parserDataTable(json) {

    return json;
}

function parserDataTableServerside(json) {

    if (json.erro) {
        alerta(json.mensagem);
        return JSON.stringify( [] );
    } else {
        var json = jQuery.parseJSON( json );
            json.recordsTotal = json.dados.recordsTotal;
            json.recordsFiltered = json.dados.recordsFiltered;
            json.data = json.dados.dados;

        return JSON.stringify( json );
    }
}

function atualizaCampoDeDataTable(input_on_change, campo, tabela, draw = true) {
    var row = $('#' + tabela).DataTable().row($(input_on_change).parents('tr'));
    var data = row.data();
    var index = row.index();
    data[campo] = input_on_change.value;
    $('#' + tabela).dataTable().fnUpdate(data, index, undefined, false);

    if(draw){

        $('#' + tabela).DataTable().draw();

        $('.mascaraInteiro').maskMoney({
                allowNegative: false,
                precision: 0,
                thousands: '',
                decimal: ',',
                allowEmpty: false,
                allowZero: true,
                affixesStay: false
        });
        $('.mascaraMoedaFloat').maskMoney({
                prefix: 'R$ ',
                allowNegative: false,
                thousands: '',
                precision: 2,
                decimal: '.',
                allowEmpty: false,
                allowZero: true,
                affixesStay: false
        });

    }
}

function renderFormataNumero(data, type, row) {
   return floatToMoney(parseFloat(data));
}

function renderFormataMoeda(data, type, row) {
   return floatToMoney(parseFloat(data));
}

function renderFormataData(data, type, row) {
	return dateToScreen(data);
}

function renderFormataDataHora(data, type, row) {
	return dateToScreenComHora(data);
}

function renderNumTel(data, type, row){
    if(data.length == 11){
        return `(${data.slice(0,2)}) ${data[2]} ${data.slice(3,7)}-${data.slice(7,11)}`;
    }else{
        return `(${data.slice(0,2)}) ${data.slice(2,6)}-${data.slice(6,10)}`;
    }
}

function renderCpfCnpj(data, type, row){
    if(data.length == 11){
        return `${data.slice(0,3)}.${data.slice(3,6)}.${data.slice(6,9)}-${data.slice(9,11)}`;
    }else{
        return `${data.slice(0,2)}.${data.slice(2,5)}.${data.slice(5,8)}/${data.slice(8,12)}-${data.slice(12,14)}`;
    }
}

function geraMenuBotao(botoes){

	var html = '';
	var cont = 0;
	var dropdown = false;

	$.each(botoes, function() {
		if(cont == 2){
			//inicia o dropdown
			dropdown = true;
			html +=  `<div class="btn-group dropstart">
			        <button type="button" class="btn btn-group-action dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
			        	<i class="fa-solid fa-ellipsis"></i>
			        </button>
        			  <ul class="dropdown-menu dropdown-menu-start">`;
		}

		if(cont<2){
			html += geraBotao(this);
		}
		else{
			html += geraLinhaNoDropdown(this);
		}
		cont++;
	});

	if(dropdown){
		html +=  `</ul></div>`;
	}

   return html;
}

 function geraLinhaNoDropdown(botao){
	 var icone = '';
	 if(botao.icone){
		 icone = 'with-icon';
	 }
 		return `<li><button type="button" class="dropdown-item `+icone+`" onmouseup="`+botao.onclick+`" data-toggle="tooltip"><i class="fa-solid `+botao.icone+` position-absolute start-0 ps-2 ms-1 mt-1"></i>`+botao.texto+`</button></li>`;
 }

 function geraBotao(botao){
 		return `<button type="button" class="btn btn-`+botao.classe+` m-1"  data-toggle="tooltip" title="`+botao.title+`" onmouseup="`+botao.onclick+`" >
			        	<i class="fa-solid `+botao.icone+`"></i>
			     </button>`;
 }

function exportMenu(...components){

	let exportOptions = {
		extend: 'collection',
		text: 'Exportar',
		className: 'with-icon icon-fa-download',
		buttons: components
	};

	return exportOptions;
}
