$(document).ready(function() {
    setTimeout( () => {
        $(".cpf").mask("000.000.000-00");
        $(".cnpj").mask("00.000.000/0000-00");
        $(".date").mask("00/00/0000");
        // $(".cellphone").mask();
        $(".cep").mask("00000-000");
        $(".porcentagem").mask("##0%", {reverse: true});
        $(".numero").on('keypress keyup', function(e) {
            if (/\D/g.test(this.value)){
                this.value = this.value.replace(/\D/g, '');
            }
        });
        $(".codigo-cupom").keyup(function() {
            $(this).val($(this).val().replace(/[^a-z0-9]/gi, ''));
        });
        let cpfCnpjMask     = val => val.replace(/\D/g, '').length >= 12 ? '00.000.000/0000-00' : '000.000.000-009';
        let cpfCnpjOptions  = { onKeyPress: (val, e, field, options) => { args = [val,e,field,options]; return field.mask(cpfCnpjMask.apply({}, args), options) }};
        let telefoneMask    = val => val.replace(/\D/g, '').length === 11 ? '(00) 0 0000-0000' : '(00) 0000-00009';
        let telefoneOptions = { onKeyPress: (val, e, field, options) => { args = [val, e, field, options]; return field.mask(telefoneMask.apply({}, args), options) } };
        $(".cellphone").mask(telefoneMask, telefoneOptions);
    },120);
});