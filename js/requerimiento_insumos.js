/************************************************************************/
/* SISTEMA DE PLANIFICACIÓN, PRESUPUESTO Y CONTROL DE GESTIÓN           */
/* DEL PLAN OPERATIVO DEL SERVICIO INTERNO                              */
/* DESARROLLADO POR: ING.REIZA GARCÍA                                   */
/*                   ING.HÉCTOR MARTÍNEZ                                */
/* PARA EL MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES      */
/* JUNIO DE 2013                                                        */
/* TELEFONOS DE CONTACTO PARA SOPORTE: 0416-9052533 / 0212-5153033      */
/************************************************************************/

$(document).ready(function()
{     
   
});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES

function Actualiza()
{        
    var uri;
    uri='requerimiento_insumos/listar_insumos';
    
    $.ajax({
          type:'POST',
          url:uri,
          data:{
                'yearpoa':$('#year_poa').val()
               },
          beforeSend:function(){$("#cargandoModal").show();},
          complete: function(){
                      $("#cargandoModal").hide();},
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Data.';
                      CajaDialogo('Error', Mensaje);},
          success: function(data){                                          
                       $('#Planes').html(data);
                       CancelarModal(); },                   
          dataType:'html'});
    return false;
}

function RevisarFicha(id_requerimiento_insumo,editable)
{    
    $.ajax({
       type:'POST',
       url:'requerimiento_insumos/revisar_ficha',
       data:{
             'id_requerimiento_insumo':id_requerimiento_insumo,
             'editable':editable,
             'yearpoa':$('#year_poa').val()             
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                          
                     $('#Planes').html(data);
                     $('#Planes').show();
                     selectorInsumo();
                },                   
       dataType:'html'});
    return false;
}

function CambioTipo()
{
    $('.um').html('');
    $("#codart").val(0);
    $('#spg_cuenta').val(0);
    $('#codunimed').val(0);
    
    if ($('#TipoInsumo').val()==="0")
    {
       $('#Insumo').val('-- Seleccione Primero el tipo de Insumo --');   
       $('#Insumo').attr('disabled','disabled');       
    }
    else
    {
       $('#Insumo').val('-- Escriba el Nombre del Insumo --');   
       $('#Insumo').removeAttr('disabled');     
    }
}

function ActualizarFicha(id_requerimiento_insumo)
{
  if($('#Insumo').val()==="" ||
     $('#TipoInsumo').val()==="0" ||     
     $('#codart').val()==="0" ||
     trim($('#codart').val())==="" ||
     $('#codunimed').val()==="0" ||     
     trim($('#spg_cuenta').val())==="" ||
        !verificarNro($('#Existencia').val()) || !verificarNro($('#Requerido').val()) ||
        ((parseInt($('#Existencia').val())+parseInt($('#Requerido').val()))===0) )
  {
    var Mensaje='Debe completar todos los campos correctamente.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }

  $.ajax({
     type:'POST',
     url:'requerimiento_insumos/actualizar_ficha',
     data:{
          'id_requerimiento_insumo':id_requerimiento_insumo,         
          'codart':encodeURIComponent(trim($('#codart').val())),
          'partida_generica':encodeURIComponent(trim($('#TipoInsumo').val())),
          'codunimed':encodeURIComponent(trim($('#codunimed').val())),
          'spg_cuenta':encodeURIComponent(trim($('#spg_cuenta').val())),
          'denunimed':encodeURIComponent(trim($('.um').html())),
          'denart':encodeURIComponent(trim($('#Insumo').val())),
          'existencia':$('#Existencia').val(),
          'requerido':$('#Requerido').val(),
          'observaciones':encodeURIComponent(trim($('#Observaciones').val()))
          },
     beforeSend:function(){$("#cargandoModal").show();},
     complete: function(){
                 $("#cargandoModal").hide();},               
     error: function(){
                 var Mensaje='Error Interno del Servidor.';
                 CajaDialogo('Error', Mensaje);},
     success: function(data){
              var Mensaje='Se han guardado los cambios correctamente.';
              var Botones={Cerrar: function(){                  
                   Actualiza();
                   CancelarModal();                   
                   $( this ).dialog( "close" );}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;      
}

function RequerirInsumo(id_estructura)
{   
    $.ajax({
       type:'POST',
       url:'requerimiento_insumos/requerir_insumo',
       data:{
             'id_estructura':id_estructura,
             'yearpoa':$('#year_poa').val()
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                          
                     $('#Planes').html(data);
                     $('#Planes').show();
                     selectorInsumo();
                },                   
       dataType:'html'});
    return false;
}

function selectorInsumo()
{  
    // AUTOCOMPLETAR PLACEHOLDER
    var Msj='-- Escriba el Nombre del Insumo --';  
    if (trim($("#Insumo").val())=='') $("#Insumo").val(Msj);
         
    $("#Insumo").focusin(function()
                {
                  $(this).val('');  
                  $('.um').html('');
                  $("#codart").val(0);
                  $('#spg_cuenta').val(0);
                  $('#codunimed').val(0);                                       
                }
         ).focusout(function(){
        if (trim($(this).val())==''  || $("#codart").val()=='0')
        {$(this).val(Msj);
         $("#codart").val(0)}});
 
     $('#Insumo').autocomplete({
     minLength:1, //le indicamos que busque a partir de haber escrito dos o mas caracteres en el input
     delay:1,
     position:{my: "left top", at: "left bottom", collision: "none"},
     source: function(request, response)
             {
                var url="requerimiento_insumos/listar_articulos";  //url donde buscará los Insumos 
                $.post(url,{'frase':request.term,'tipo_insumo':$("#TipoInsumo").val()}, response, 'json');
             },
     select: function( event, ui ) 
             {                  
                $("#Insumo").val( ui.item.denart );                
                $("#codart").val(ui.item.codart);
                $('#spg_cuenta').val(ui.item.spg_cuenta);
                $('#codunimed').val(ui.item.codunimed);
                $(".um").html(trim(ui.item.denunimed));
                $("#Existencia").focus();
                return false;
             }
   }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )                
                .data( "item.autocomplete", item )
		.append( "<a>" + ((item.denart==undefined)?'Sin coincidencias':item.denart) +
                         " - (" + ((item.denunimed==undefined)?'Sin coincidencias':item.denunimed) +
                         ") </a>")
		.appendTo( ul );
	  };
}

function GuardarFicha(id_estructura)
{
  if($('#Insumo').val()==="" ||
     $('#TipoInsumo').val()==="0" ||     
     $('#codart').val()==="0" ||
     trim($('#codart').val())==="" ||
     $('#codunimed').val()==="0" ||     
     trim($('#spg_cuenta').val())==="" ||
        !verificarNro($('#Existencia').val()) || !verificarNro($('#Requerido').val()) ||
        ((parseInt($('#Existencia').val())+parseInt($('#Requerido').val()))===0) )
  {
    var Mensaje='Debe completar todos los campos correctamente.';  
    $('#Insumo').val('-- Escriba el Nombre del Insumo --');
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }
  
  $.ajax({
     type:'POST',
     url:'requerimiento_insumos/guardar_ficha',
     data:{
           'id_estructura':id_estructura,
           'yearpoa':$('#year_poa').val(),  
           'codart':encodeURIComponent(trim($('#codart').val())),
           'partida_generica':encodeURIComponent(trim($('#TipoInsumo').val())),
           'codunimed':encodeURIComponent(trim($('#codunimed').val())),
           'spg_cuenta':encodeURIComponent(trim($('#spg_cuenta').val())),
           'denunimed':encodeURIComponent(trim($('.um').html())),
           'denart':encodeURIComponent(trim($('#Insumo').val())),
           'existencia':$('#Existencia').val(),
           'requerido':$('#Requerido').val(),
           'observaciones':encodeURIComponent(trim($('#Observaciones').val()))
          },
     beforeSend:function(){$("#cargandoModal").show();},
     complete: function(){
                 $("#cargandoModal").hide();},               
     error: function(){
                 var Mensaje='Error Interno del Servidor.';
                 CajaDialogo('Error', Mensaje);},
     success: function(data){
              var Mensaje='Se ha Registrado el Requerimiento correctamente.';
              var Botones={Cerrar: function(){                  
                   Actualiza();                 
                   $( this ).dialog( "close" );}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;      
}

function EliminarRequerimiento(id_requerimiento_insumo)
{
  var Botones={No: function(){$( this ).dialog( "close" );
                              Actualiza(); },
               Sí: function(){
   $.ajax({
   type:'POST',
   url:'requerimiento_insumos/eliminar_requerimiento',
   data:{
         'id_requerimiento_insumo':id_requerimiento_insumo
        },
   beforeSend:function(){$("#cargandoModal").show();},
   complete: function(){
               $("#cargandoModal").hide();},
   error: function(){
               var Mensaje='No se pudo Eliminar el Requerimiento.';
               CajaDialogo('Error', Mensaje);},
   success: function(data){                         
            var Botones={Cerrar: function(){
                    Actualiza();
                    $( this ).dialog( "close" );}};
            var Mensaje='Requerimiento Eliminado satisfactoriamente.';
            CajaDialogo('Borrado', Mensaje, Botones);},
   dataType:'text'});
   $( this ).dialog( "close" );
   Actualiza();}
              };
  var Mensaje='¿Está Seguro que desea Eliminar el Requerimiento?';
  CajaDialogo('Pregunta', Mensaje, Botones);
  return false;
}