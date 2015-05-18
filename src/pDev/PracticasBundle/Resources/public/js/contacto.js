// Validador de rut Contacto
$("input[id$='contactotype_rut']").focusout(function()  {
    var $rut = $(this);
    var rut = $.Rut.quitarFormato($rut.val());
    $rut.closest('.controls').find('.glyphicon').remove();
    $rut.closest('.controls').find('.alerta').remove();
    
    if(rut != '')
    {
        $.ajax({
            url: Routing.generate('practicas_contacto_rut', {'rut': rut}),
            type: "POST",
            beforeSend: function(){
                var div = '<span class="loader"></span>';
                $rut.before(div);
            },
            success: function(data){
                if(data.status == 200){
	                alert('El contacto '+data.nombre+' '+data.apellidoPaterno+' ya existe bajo el email '+data.email);
	                $("input[id$='contactotype_nombres']").val('').prop('disabled', false);
	                $("input[id$='contactotype_rut']").val('').prop('disabled', false);
	                $("input[id$='contactotype_apellidoPaterno']").val('').prop('disabled', false);
	                $("input[id$='contactotype_apellidoMaterno']").val('').prop('disabled', false);
	                $("input[id$='contactotype_email']").val('').prop('disabled', false);
                }
                else{
                    // Validamos el rut
                    if($.Rut.validar(rut)){
                        $rut.after('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>');
                    }
                    else{
	                    $rut.after('<span class="alerta">El rut es inválido</span>');
	                    $("input[id$='contactotype_rut']").val('').prop('disabled', false);
                    }
                }
                $('.loader').remove();
            },
            error: function(jqXHR, textStatus, errorThrown){
                alert(textStatus);
                $('.loader').remove();
            }
        });
    }
});

// Validador de correo
$("input[id$='contactotype_email']").focusout(function() {
    var $email = $(this);
    var email = $email.val();
    $email.closest('.controls').find('.glyphicon').remove();
    $email.closest('.controls').find('.alerta').remove();
    
    if(email != '')
    {
        $.ajax({
            url: Routing.generate('practicas_contacto_email', {'email': email}),
            type: "POST",
            beforeSend: function(){
                var div = '<span class="loader"></span>';
                $email.before(div);
            },
            success: function(data){
                if(data.status == 200){
	                alert('El contacto '+data.nombres+' '+data.apellidoPaterno+' ya existe bajo el email '+data.email);
	                $("input[id$='contactotype_nombres']").val('').prop('disabled', false);
	                $("input[id$='contactotype_rut']").val('').prop('disabled', false);
	                $("input[id$='contactotype_apellidoPaterno']").val('').prop('disabled', false);
	                $("input[id$='contactotype_apellidoMaterno']").val('').prop('disabled', false);
	                $("input[id$='contactotype_email']").val('').prop('disabled', false);
                }
                else{
                    if(isValidEmailAddress(email)){
                        $email.after('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>');
                    }
                    else{
                        $email.after('<span class="alerta">Ingrese un email válido</span>');
                        $("input[id$='contactotype_email']").val('').prop('disabled', false);
                    }
                }
                $('.loader').remove();
            },
            error: function(jqXHR, textStatus, errorThrown){
                alert(textStatus);
                $('.loader').remove();
            }
        });
    }
});
