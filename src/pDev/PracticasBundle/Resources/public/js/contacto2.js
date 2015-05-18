// Validador de rut Supervisor
$("input[id$='contacto_rut']").focusout(function() 
{
    var $rut = $(this);
    var rut = $.Rut.quitarFormato($rut.val());
    $rut.closest('.form-group').find('.glyphicon').remove();
    $rut.closest('.form-group').find('.alerta').remove();
    
    if(rut != '')
    {
        // Validamos el rut
        if(!$.Rut.validar(rut)){
            $rut.after('<span class="alerta">El rut es inválido</span>');
            $rut.val('');
            $("input[id$='contacto_nombres']").val('');
            $("input[id$='contacto_apellidoPaterno']").val('');
            $("input[id$='contacto_apellidoMaterno']").val('');
            $("input[id$='contacto_email']").val('');
            return;
        }
    
        $.ajax({
            url: Routing.generate('practicas_contacto_rut', {'rut': rut}),
            type: "POST",
            beforeSend: function(){
                var div = '<span class="loader"></span>';
                $rut.before(div);
            },
            success: function(data){
                if(data.status == 200){
	                $("input[id$='contacto_nombres']").val(data.nombres);
	                $("input[id$='contacto_apellidoPaterno']").val(data.apellidoPaterno);
	                $("input[id$='contacto_apellidoMaterno']").val(data.apellidoMaterno);
	                $("input[id$='contacto_email']").val(data.email);
	                $('.loader').remove();
                }
                else{
                    $("input[id$='contacto_nombres']").val('');
	                $("input[id$='contacto_apellidoPaterno']").val('');
	                $("input[id$='contacto_apellidoMaterno']").val('');
	                $("input[id$='contacto_email']").val('');
	                $('.loader').remove();
                }
                $rut.after('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>');
            },
            error: function(jqXHR, textStatus, errorThrown){
                alert(textStatus);
                $('.loader').remove();
            }
        });
    }
});
