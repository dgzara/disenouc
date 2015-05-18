// Validador de rut Supervisor
$("input[id$='supervisor_rut']").focusout(function() 
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
            $("input[id$='supervisor_nombres']").val('');
            $("input[id$='supervisor_apellidoPaterno']").val('');
            $("input[id$='supervisor_apellidoMaterno']").val('');
            $("input[id$='supervisor_email']").val('');
            return;
        }
        
        $.ajax({
            url: Routing.generate('practicas_supervisor_rut', {'rut': rut}),
            type: "POST",
            beforeSend: function(){
                var div = '<span class="loader"></span>';
                $rut.before(div);
            },
            success: function(data){
                if(data.status == 200){
	                $("input[id$='supervisor_nombres']").val(data.nombres);
	                $("input[id$='supervisor_apellidoPaterno']").val(data.apellidoPaterno);
	                $("input[id$='supervisor_apellidoMaterno']").val(data.apellidoMaterno);
	                $("input[id$='supervisor_cargo']").val(data.cargo);
	                $("input[id$='supervisor_email']").val(data.email);
	                $("input[id$='supervisor_profesion']").val(data.profesion);
	                $('.loader').remove();
                }
                else{
                    $("input[id$='supervisor_nombres']").val('');
	                $("input[id$='supervisor_apellidoPaterno']").val('');
	                $("input[id$='supervisor_apellidoMaterno']").val('');
	                $("input[id$='supervisor_cargo']").val('');
	                $("input[id$='supervisor_email']").val('');
	                $("input[id$='supervisor_profesion']").val('');
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
