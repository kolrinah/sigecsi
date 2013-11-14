/* 
 * 
 * 
 */

$(document).ready(function()
{     


});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES

function actualiza()
{        
    var uri;
    uri='ejecucion_proyectos/listar_proyectos';
    
    $.ajax({
          type:'POST',
          url:uri,
          data:{
                'yearpoa':$('#year_poa').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar los proyectos.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){                                          
                       $('#Planes').html(data);
                       CancelarModal(); },                   
          dataType:'html'});
    return false;
}

function revisarEjecucion(id_proyecto)
{    
    $.ajax({
       type:'POST',
       url:'ejecucion_proyectos/revisar_ejecucion',
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
                     $('#Planes').html(data);
                     $('#Planes').show();
                },                   
       dataType:'html'});
    return false;
}

function revisarMetas(id_proyecto)
{
    $.ajax({
       type:'POST',
       url:'ejecucion_proyectos/revisarMetas',
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
                     $('#Planes').html(data);
                     $('#Planes').show();
                },                   
       dataType:'html'});
    return false;    
}

function registrarMeta(id_actividad)
{    
    $.ajax({
       type:'POST',
       url:'ejecucion_proyectos/registrarMeta',
       data:{'id_actividad': id_actividad },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                          
                     $('#VentanaModal').html(data);
                     $('#VentanaModal').show();
                     fechaPicker('fechaMeta');
                },                   
       dataType:'html'});
    return false;    
}

function guardarMeta(id_actividad, id_proyecto)
{
    if ($('#cantidadMeta').val()=='' || $('#fechaMeta').val()=='') 
    {
       var Mensaje='Error: Debe llenar los campos correctamente.';  
       CajaDialogo("Alerta", Mensaje);   
       return false;
    }
    
    $.ajax({
       type:'POST',
       url:'ejecucion_proyectos/guardarMeta',
       data:{'id_actividad':id_actividad,
             'cantidad_meta': $('#cantidadMeta').val(),
             'fecha_meta':encodeURIComponent($('#fechaMeta').val()),
             'observaciones':encodeURIComponent($('#observaciones').val()),
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Registrar la Meta.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                       
                  var Mensaje='Operación Exitosa.';  
                  var Botones={Cerrar: function(){                  
                         revisarMetas(id_proyecto);
                         CancelarModal();
                         $( this ).dialog( "close" );}};
                  CajaDialogo('Exito', Mensaje, Botones);
                },                   
       dataType:'json'});
    return false; 
}

function revisarAsociacion(id)
{
    $.ajax({
       type:'POST',
       url:'ejecucion_proyectos/revisar_asociacion',
       data:$('#'+id).serialize(),
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

function asociarActividad()
{
    if ($('#id_actividad').val()=='0') 
    {
       var Mensaje='Error: Selección Incorrecta.';  
       CajaDialogo("Alerta", Mensaje);   
       return false;
    }
    
    $.ajax({
       type:'POST',
       url:'ejecucion_proyectos/asociarActividad',
       data:$('#asociacion').serialize(),
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Asociar la Actividad.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                       
                  var Mensaje='Asociación Exitosa.';  
                  var Botones={Cerrar: function(){                  
                         revisarEjecucion(data.id_proyecto);
                         CancelarModal();
                         $( this ).dialog( "close" );}};
                  CajaDialogo('Exito', Mensaje, Botones);
                },                   
       dataType:'json'});
    return false; 
}

function fechaPicker(fechaMeta)
{
    $( "#"+fechaMeta ).datepicker({
                           showOn: "both",
                           buttonImage: "imagenes/cal.gif",
                           buttonText:"clic para Seleccionar",
                           buttonImageOnly: true,
                           showOtherMonths: true,
                           selectOtherMonths: true,
                           dateFormat:"dd/mm/yy",
                           currentText:"Hoy",
                           nextText:"Sig",
                           defaultDate: _DiaHoy(),
                           minDate: "01/01/"+$('#year_poa').val(),
                           maxDate: "31/12/"+$('#year_poa').val(),
                           dayNames:[ "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado" ],
                           dayNamesMin:[ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
                           monthNames:["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                                       
                       });
}