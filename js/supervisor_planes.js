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
    uri='supervisor_planes/planificacion_unidades';    
    
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
             url:'supervisor_planes/actualizaSelector', 
            data:{'selector':selector},
        complete:function(){actualiza(unidad);} });    
    return true;
}

function actualizaGantt(gantt, unidad)
{   
    unidad = unidad || 0;    
    $.ajax({type:'POST',
             url:'supervisor_planes/actualizaGantt', 
            data:{'gantt':gantt},
        complete:function(){actualiza(unidad);} });    
    return true;
}

function revisarFicha(id_proyecto)
{    
    $.ajax({
       type:'POST',
       url:'supervisor_planes/revisar_ficha',
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

function actualizarFicha(id_proyecto)
{  
  $.ajax({
     type:'POST',
     url:'supervisor_planes/actualizar_ficha',
     data:{
           'id_proyecto':id_proyecto,           
           'cod_proy':encodeURIComponent($('#cod_proy').val()),
           'monto_aprobado':$('#monto_aprobado').val()           
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
                   actualiza(1);
                   CancelarModal();
                   $( this ).dialog( "close" );}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;      
}

function listarAcciones(id_proyecto)
{    
    $.ajax({
       type:'POST',
       url:'supervisor_planes/listar_acciones',
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
       url:'supervisor_planes/revisar_actividad',
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
       url:'supervisor_planes/listar_presupuesto',
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
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){                                          
                        $('#VentanaModal').html(data);
                        $('#VentanaModal').show();
                   },                   
          dataType:'html'});
    return false;
}

function estableceFechaTope(id_estructura, fecha_tope)
{
   $.ajax({
          type:'POST',
          url:'supervisor_planes/estableceFechaTope',
          data:{
                'id_estructura':id_estructura,
                'fecha_tope':encodeURIComponent(fecha_tope)
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cambiar la fecha.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){                                          
                        $('#VentanaModal').html(data);
                        $('#VentanaModal').show();
                        
         $( "#Fecha" ).datepicker({
            showOn: "both",
            buttonImage: "imagenes/cal.gif",
            buttonText:"clic para Seleccionar",
            buttonImageOnly: true,
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat:"dd/mm/yy",
            currentText:"Hoy",
            nextText:"Sig",
            defaultDate: fecha_tope,
            minDate:_DiaHoy(),
            maxDate:"31/12/"+$('#year_poa').val(),
            dayNames:[ "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado" ],
            dayNamesMin:[ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
            monthNames:["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]                      
        });
     
                   },                   
          dataType:'html'});      

    return false;
}

function GuardarFecha(id_estructura)
{    
    $.ajax({
       type:'POST',
       url:'supervisor_planes/guardar_fecha',
       data:{
             'id_estructura':id_estructura,
             'fecha_tope':$('#Fecha').val()
            },
       beforeSend:function(){$("#cargandoModal").show()},
       complete: function(){
                   $("#cargandoModal").hide()},               
       error: function(){
                   var Mensaje='Error Interno del Servidor.';
                   CajaDialogo('Error', Mensaje)},
       success: function(data){
                var Mensaje='Se ha establecido la nueva fecha de cierre correctamente.';
                var Botones={Cerrar: function(){                  
                     actualiza();
                     CancelarModal();                   
                     $( this ).dialog( "close" )}};
                CajaDialogo('Guardar', Mensaje, Botones);},
       dataType:'text'});   
    return false;      
}