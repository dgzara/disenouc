// Validador de rut Organizacion
$("input[id$='_rut']").focusout(function() 
{
    var $rut = $(this);
    var rut = $.Rut.quitarFormato($rut.val());
    
    if(rut != '')
    {
        $.ajax({
            url: Routing.generate('practicas_organizacion_rut', {'rut': rut}),
            type: "POST",
            beforeSend: function(){
                var div = '<span class="loader"></span>';
                $rut.before(div);
            },
            success: function(data){
                if(data.status == 200)
                {
                    alert('El RUT ingresado ya existe para la organización '+data.nombre);
	                $("input[id$='_rut']").val('').prop('disabled', false);
	                $("input[id$='_nombre']").val('').prop('disabled', false);
	                $("input[id$='_rubro']").val('').prop('disabled', false);
	                $("input[id$='_descripcion']").val('').prop('disabled', false);
	                $("input[id$='_pais']").val('').prop('disabled', false);
	                $("input[id$='_web']").val('').prop('disabled', false);
	                $("input[id$='_personasTotal']").val('').prop('disabled', false);
	                $("input[id$='_antiguedad']").val('').prop('disabled', false);
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
