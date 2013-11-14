/************************************************************************/
/* SISTEMA DE GESTION Y CONTROL DEL SERVICIO INTERNO                    */
/* DESARROLLADO POR: ING.REIZA GARCÍA                                   */
/*                   ING.HÉCTOR MARTÍNEZ                                */
/* PARA EL MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES      */
/* JULIO DE 2013                                                        */
/* TELEFONOS DE CONTACTO PARA SOPORTE: 0416-9052533 / 0212-5153033      */
/************************************************************************/
$(document).ready(function()
{     

});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES
function actualiza(unidad)
{       
    unidad = unidad || 0;
    var uri;
    uri='enviar_poa/planificacion_unidades';    
    
    $.ajax({
          type:'POST',
          url:uri,
          data:{
                'yearpoa':$('#year_poa').val(),
                'id_estructura':$('#id_unidad').val(),
                'verUnidad':unidad
               },
          beforeSend:function(){$("#cargandoModal").show();},
          complete: function(){
                      $("#cargandoModal").hide();},
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Planificación.';
                      CajaDialogo('Error', Mensaje);},
          success: function(data){                                          
                       $('#Planes').html(data);
                       CancelarModal();},        
          dataType:'html'});
    return false;
}

function cambiaUnidad(id_estructura, unidad)
{    
    unidad = unidad || 0;    
    $('#id_unidad').val(id_estructura);
    actualiza(unidad);
    return true;
}

function actualizaSelector(selector,unidad)
{
    unidad = unidad || 0;    
    $.ajax({type:'POST',
             url:'enviar_poa/actualizaSelector', 
            data:{'selector':selector},
        complete:function(){actualiza(unidad);} });    
    return true;
}

function actualizaGantt(gantt, unidad)
{   
    unidad = unidad || 0;    
    $.ajax({type:'POST',
             url:'enviar_poa/actualizaGantt', 
            data:{'gantt':gantt},
        complete:function(){actualiza(unidad);} });    
    return true;
}

function revisarFicha(id_proyecto)
{    
    $.ajax({
       type:'POST',
       url:'enviar_poa/revisar_ficha',
       data:{
             'id_proyecto':id_proyecto
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                          
                     $('#proyecto').html(data);
                     $('#proyecto').show();                     
                     $('#cod_proy').focus();
                },                   
       dataType:'html'});
    return false;
}

function listarAcciones(id_proyecto)
{    
    $.ajax({
       type:'POST',
       url:'enviar_poa/listar_acciones',
       data:{
             'id_proyecto':id_proyecto
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                          
                     $('#proyecto').html(data);
                     $('#proyecto').show();
                },                   
       dataType:'html'});
    return false;
}

function planProyecto(id_proyecto)
{    
    $.ajax({
       type:'POST',
       url:'enviar_poa/plan_proyecto',
       data:{
             'id_proyecto':id_proyecto
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                          
                     $('#proyecto').html(data);
                     $('#proyecto').show();
                },                   
       dataType:'html'});
    return false;
}

function actualizaGanttProyecto(gantt)
{           
    $.ajax({type:'POST',
             url:'enviar_poa/actualizaGantt', 
            data:{'gantt':gantt},
        complete:function(){planProyecto($("#idProyecto").val());} });    
    return true;
}

function revisarActividad(id_actividad)
{    
    $.ajax({
       type:'POST',
       url:'enviar_poa/revisar_actividad',
       data:{
             'id_actividad':id_actividad
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                          
                     $('#proyecto').html(data);
                     $('#proyecto').show();
                      },                   
       dataType:'html'});
    return false;
}

function listarPresupuesto(id_actividad)
{    
    $.ajax({
       type:'POST',
       url:'enviar_poa/listar_presupuesto',
       data:{
             'id_actividad':id_actividad
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                          
                     $('#proyecto').html(data);
                     $('#proyecto').show();
                },                   
       dataType:'html'});
    return false;
}

function VerInfo(id_sp)
{
   $.ajax({
          type:'POST',
          url:'plan_productos/ver_subproducto',
          data:{
                'id_subproducto':id_sp
               },
          beforeSend:function(){$("#cargandoModal").show();},
          complete: function(){
                      $("#cargandoModal").hide();},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                      CajaDialogo('Error', Mensaje);},
          success: function(data){                                          
                        $('#VentanaModal').html(data);
                        $('#VentanaModal').show();
                   },                   
          dataType:'html'});
    return false;
}

function enviarPOA(id_estructura)
{
   $.ajax({
          type:'POST',
          url:'enviar_poa/enviarPOA',
          data:{
                'id_estructura':id_estructura
               },
          beforeSend:function(){$("#cargandoModal").show();},
          complete: function(){
                      $("#cargandoModal").hide();},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al procesar la Información.';
                      CajaDialogo('Error', Mensaje);},
          success: function(data){                                          
                       $('#Planes').html(data);
                       CancelarModal();      
                        },                   
          dataType:'html'});
    return false;    
}

function confirmarEnvio(id_estructura)
{
     var Botones={No: function(){$( this ).dialog( "close" );},
                  Sí: function(){
                  $.ajax({
                  type:'POST',
                  url:'enviar_poa/confirmarEnvio',
                  data:{'id_estructura': id_estructura},
                  beforeSend:function(){$("#cargandoModal").show();},
                  complete: function(){
                              $("#cargandoModal").hide();},
                  error: function(){
                              var Mensaje='Ha Ocurrido un Error al procesar la Información.';
                              CajaDialogo('Error', Mensaje);},
                  success: function(data){
                           var Botones={Cerrar: function(){
                                   actualiza();
                                   $( this ).dialog( "close" );}};
                           var Mensaje='Planificación enviada satisfactoriamente.';
                           CajaDialogo('Exito', Mensaje, Botones);},
                  dataType:'text'});
                  $( this ).dialog( "close" ); } };
          
     var Mensaje='¿Está Seguro que desea enviar su Planificación Operativa?';
     CajaDialogo('Pregunta', Mensaje, Botones);
     return false;
}

function rechazarPOA(id_estructura)
{
     var Botones={No: function(){$( this ).dialog( "close" );},
                  Sí: function(){
                  $.ajax({
                  type:'POST',
                  url:'enviar_poa/rechazarEnvio',
                  data:{'id_estructura': id_estructura,
                        'yearpoa':$("#year_poa").val() },
                  beforeSend:function(){$("#cargandoModal").show();},
                  complete: function(){
                              $("#cargandoModal").hide();},
                  error: function(){
                              var Mensaje='Ha Ocurrido un Error al procesar la Información.';
                              CajaDialogo('Error', Mensaje);},
                  success: function(data){
                           var Botones={Cerrar: function(){
                                   actualiza(1);
                                   $( this ).dialog( "close" );}};
                           var Mensaje='La Planificación ha sido Rechazada Satisfactoriamente.';
                           CajaDialogo('Exito', Mensaje, Botones);},
                  dataType:'text'});
                  $( this ).dialog( "close" ); } };
          
     var Mensaje='¿Está Seguro que desea Rechazar la Planificación Operativa?';
     CajaDialogo('Pregunta', Mensaje, Botones);
     return false;    
}