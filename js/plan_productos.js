/* 
 * 
 * 
 */

$(document).ready(function()
{     


});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES
function Actualiza()
{        
    var uri;
    uri='plan_productos/';
    uri+=($('#hideGantt').val()=='t')?'generar_gantt_planes':'generar_tabla_planes';
    
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
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Planificación.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){                                          
                       $('#Planes').html(data);
                       CancelarModal(); },                   
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

function editarActividad(id_plan)
{
    $.ajax({
       type:'POST',
       url:'plan_productos/editarActividad',
       data:{
             'id_plan':id_plan
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
                      camposFecha();
                },                   
       dataType:'html'});
    return false;
}

function actualizarActividad(id_plan)
{
  if(trim($('#Actividad').val())==='' || parseInt($('#Responsable').val())=== 0 ||
          parseInt($('#Cantidad').val())=== 0 || $('#FechaI').val()==='')
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }    
  $.ajax({
     type:'POST',
     url:'plan_productos/actualizar_actividad',
     data:{
           'id_plan':id_plan,
           'actividad':encodeURIComponent($('#Actividad').val()),
           'cantidad' : parseInt($('#Cantidad').val()),
           'id_responsable':$('#Responsable').val(),
           'fecha_ini':$('#fechaI').val(),
           'fecha_fin':$('#fechaF').val()
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Error Interno del Servidor.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){
              var Mensaje='Se han guardado los cambios correctamente.';
              var Botones={Cerrar: function(){                  
                   Actualiza();
                   CancelarModal();                   
                   $( this ).dialog( "close" )}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;      
}

function eliminarActividad(id_plan)
{
    var Botones={No: function(){$( this ).dialog( "close" )},
                 Sí: function(){ 
     $.ajax({
     type:'POST',
     url:'plan_productos/eliminar_actividad',
     data:{
           'id_plan':id_plan
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},
     error: function(){
                 var Mensaje='Ha Ocurrido un Error al Intentar Borrar la Actividad.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){                                          
                 var Mensaje='Se ha Borrado la actividad correctamente. ';
                 var Botones={Cerrar: function(){
                     Actualiza();
                     CancelarModal();                       
                     $( this ).dialog( "close" )}};
                 CajaDialogo('Borrado', Mensaje, Botones);
                   },                   
     dataType:'text'});
     
     $( this ).dialog( "close" )}
                  };                   
    var Mensaje='¿Está Seguro que desea Eliminar la Actividad?';
    CajaDialogo('Pregunta', Mensaje, Botones);
}

function agregarActividad(id_sp)
{
    $.ajax({
       type:'POST',
       url:'plan_productos/agregarActividad',
       data:{
             'id_sp':id_sp
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
                      camposFecha();
                },                   
       dataType:'html'});
    return false;
}

function camposFecha()
{
    $( "#fechaI" ).datepicker({
              showOn: "both",
              buttonImage: "imagenes/cal.gif",
              buttonText:"clic para Seleccionar",
              buttonImageOnly: true,
              showOtherMonths: true,
              selectOtherMonths: true,
              dateFormat:"dd/mm/yy",
              currentText:"Hoy",
              nextText:"Sig",
              defaultDate: "01/01/"+$('#year_poa').val(),
              minDate:_FechaMayor("01/01/"+$('#year_poa').val(),_DiaHoy()),
              maxDate: "31/12/"+$('#year_poa').val(),
              dayNames:[ "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado" ],
              dayNamesMin:[ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
              monthNames:["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
              onSelect: function( selectedDate ) {
                  $( "#fechaF" ).datepicker( "option", "minDate", selectedDate )}            
          });
     
    $( "#fechaF" ).datepicker({
              showOn: "both",
              buttonImage: "imagenes/cal.gif",
              buttonText:"clic para Seleccionar",
              buttonImageOnly: true,
              showOtherMonths: true,
              selectOtherMonths: true,
              dateFormat:"dd/mm/yy",
              nextText:"Sig",        
              defaultDate:$('#fechaI').val()+5,
              minDate:_FechaMayor($('#fechaI').val(),_DiaHoy()), 
              maxDate:"31/12/"+$('#year_poa').val(),
              dayNames:[ "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado" ],
              dayNamesMin:[ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
              monthNames:["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
              onSelect: function( selectedDate ) {
                  $( "#fechaI" ).datepicker( "option", "maxDate", selectedDate )}
          });
}

function guardarActividad(id_sp)
{
  if(trim($('#Actividad').val())==='' || parseInt($('#Responsable').val())===0 || 
          parseInt($('#Cantidad').val())=== 0 || 
          $('#fechaI').val()==='' || $('#fechaF').val()==='')
  {
    var Mensaje='Debe completar todos los campos para guardar la programación.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }    
  $.ajax({
     type:'POST',
     url:'plan_productos/guardar_actividad',
     data:{
           'id_subprod':id_sp,
           'descripcion':encodeURIComponent($('#Actividad').val()),
           'cantidad' : parseInt($('#Cantidad').val()),
           'id_responsable':$('#Responsable').val(),
           'fecha_ini':$('#fechaI').val(),
           'fecha_fin':$('#fechaF').val()
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Error Interno del Servidor.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){
              var Mensaje='Se ha guardado la programación correctamente.';
              var Botones={Cerrar: function(){                  
                   Actualiza();
                   CancelarModal();                   
                   $( this ).dialog( "close" )}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;      
}

function ToggleBotonGantt()
{
   if ($("#hideGantt").val()=='t')
   {
     $("#hideGantt").val('f');      
     $("#imgGantt").attr('src', 'imagenes/tabla.png');
     $("#imgGantt").attr('title', 'Visualizar Planificación en Diagrama Gantt');
     
   }
   else
   {
     $("#hideGantt").val('t');      
     $("#imgGantt").attr('src', 'imagenes/gantt16.png');
     $("#imgGantt").attr('title', 'Visualizar Planificación en Tabla');
   }
}