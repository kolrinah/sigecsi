/************************************************************************/
/* SISTEMA DE PLANIFICACIÓN, PRESUPUESTO Y CONTROL DE GESTIÓN           */
/* DEL PLAN OPERATIVO DEL SERVICIO INTERNO                              */
/* DESARROLLADO POR: ING.REIZA GARCÍA                                   */
/*                   ING.HÉCTOR MARTÍNEZ                                */
/* PARA EL MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES      */
/* ABRIL DE 2013                                                        */
/* TELEFONOS DE CONTACTO PARA SOPORTE: 0416-9052533 / 0212-5153033      */
/************************************************************************/

$(document).ready(function()
{     
   
});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES

function Actualiza()
{        
    var uri;
    uri='requerimiento_personal/listar_personal';
    
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

function RevisarFicha(id_requerimiento_personal,editable)
{    
    $.ajax({
       type:'POST',
       url:'requerimiento_personal/revisar_ficha',
       data:{
             'id_requerimiento_personal':id_requerimiento_personal,
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
                     //CambioFuente();
                    // $('#TipoPersonal').change(function(){CambioTipo($('#TipoPersonal').val());});
                },                   
       dataType:'html'});
    return false;
}

function CambioTipo(id_tipo_personal)
{
    $.ajax({
       type:'POST',
       url:'requerimiento_personal/cambiar_tipo_personal',
       data:{
             'id_tipo_personal':id_tipo_personal,
             'fuente':$("#FuentePresupuestaria").val()
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                   
                     $('#Personal').html(data);
                     if ($('#TipoPersonal').val()==="0") $('#Personal').html('<option value="0">---</option>');
                },                   
       dataType:'html'});    
           
    return false;    
}

function CambioFuente()
{
    $.ajax({
       type:'POST',
       url:'requerimiento_personal/cambiar_fuente_presupuestaria',
       data:{
             'fuente':$("#FuentePresupuestaria").val()
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                   
                     $('#TipoPersonal').html(data);
                     CambioTipo();
                },                   
       dataType:'html'});    
           
    return false;    
}

function ActualizarFicha(id_requerimiento_personal)
{
  if($('#Personal').val()==="0" ||
     $('#TipoPersonal').val()==="0" ||     
        !verificarNro($('#Femenino').val()) || !verificarNro($('#Masculino').val()) ||
        ((parseInt($('#Femenino').val())+parseInt($('#Masculino').val()))===0) )
  {
    var Mensaje='Debe completar todos los campos correctamente.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }
  
  $.ajax({
     type:'POST',
     url:'requerimiento_personal/actualizar_ficha',
     data:{
           'id_requerimiento_personal':id_requerimiento_personal,
           'accion_centralizada':$('#FuentePresupuestaria').val(),
           'id_personal':$('#Personal').val(),
           'femenino':$('#Femenino').val(),
           'masculino':$('#Masculino').val()
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

function RequerirPersonal(id_estructura)
{   
    $.ajax({
       type:'POST',
       url:'requerimiento_personal/requerir_personal',
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
                     CambioFuente();
                },                   
       dataType:'html'});
    return false;
}

function GuardarFicha(id_estructura)
{
  if($('#Personal').val()==="0" ||
     $('#TipoPersonal').val()==="0" ||     
        !verificarNro($('#Femenino').val()) || !verificarNro($('#Masculino').val()) ||
        ((parseInt($('#Femenino').val())+parseInt($('#Masculino').val()))===0) )
  {
    var Mensaje='Debe completar todos los campos correctamente.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }
  
  $.ajax({
     type:'POST',
     url:'requerimiento_personal/guardar_ficha',
     data:{
           'id_estructura':id_estructura,
           'yearpoa':$('#year_poa').val(),
           'accion_centralizada':$('#FuentePresupuestaria').val(),
           'id_personal':$('#Personal').val(),
           'femenino':$('#Femenino').val(),
           'masculino':$('#Masculino').val()
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

function EliminarRequerimiento(id_requerimiento_personal)
{
  var Botones={No: function(){$( this ).dialog( "close" );
                              Actualiza(); },
               Sí: function(){
   $.ajax({
   type:'POST',
   url:'requerimiento_personal/eliminar_requerimiento',
   data:{
         'id_requerimiento_personal':id_requerimiento_personal
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