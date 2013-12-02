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
    $.ajax({
          type:'POST',
          url:'ejecucion_productos/generar_tabla_ejecucion',
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

function revisarEjecucion(idSp)
{
    $.ajax({
          type:'POST',
          url:'ejecucion_productos/revisarEjecucion',
          data:{
                'id_subproducto' : idSp,
                       'yearpoa' : $('#year_poa').val()
               },
          beforeSend:function(){$("#cargandoModal").show();},
          complete: function(){
                      $("#cargandoModal").hide();},
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                      CajaDialogo('Error', Mensaje);},
          success: function(data){
                        $('#Planes').html(data);
                   },
          dataType:'html'});
     return true;
}

function revisarRegistro(idEjecucion)
{
    $.ajax({
          type:'POST',
          url:'ejecucion_productos/revisarRegistro',
          data:{
                'idEjecucion' : idEjecucion,
                    'yearpoa' : $('#year_poa').val()
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
                      campoFecha('fechaEjecucion');
                   },
          dataType:'html'});
     return true;
}

function actualizarRegistro(idEjecucion)
{
  if( parseInt($('#cantidadEjecutada').val())===0 || parseInt($('#idUsuario').val())=== 0 ||
       trim($('#cantidadEjecutada').val())==='' ||   
          trim($('#descripcion').val())=== '' || $('#fechaEjecucion').val()==='')
  {
    var Mensaje='Debe completar todos los campos correctamente para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }    
  
  $.ajax({
     type:'POST',
     url:'ejecucion_productos/actualizarRegistro',
     data:{
           'idEjecucion': idEjecucion,
           'idUsuario':$('#idUsuario').val(),
           'descripcion':encodeURIComponent($('#descripcion').val()),
           'cantidadEjecutada' : parseInt($('#cantidadEjecutada').val()),           
           'fechaEjecucion':$('#fechaEjecucion').val()           
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
                   revisarEjecucion($("#id_subproducto").val());
                   CancelarModal();                   
                   $( this ).dialog( "close" )}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return true;      
}

function registrarEjecucion(idSp)
{
    $.ajax({
          type:'POST',
          url:'ejecucion_productos/registrarEjecucion',
          data:{
                'idSp' : idSp
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
                      campoFecha('fechaEjecucion');
                   },
          dataType:'html'});
     return true;
}

function guardarRegistro(idSp)
{
  if( parseInt($('#cantidadEjecutada').val())===0 || parseInt($('#idUsuario').val())=== 0 ||
       trim($('#cantidadEjecutada').val())==='' ||   
          trim($('#descripcion').val())=== '' || $('#fechaEjecucion').val()==='')
  {
    var Mensaje='Debe completar todos los campos correctamente para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }    
  
  $.ajax({
     type:'POST',
     url:'ejecucion_productos/guardarRegistro',
     data:{
           'idSp': idSp,
           'idUsuario':$('#idUsuario').val(),
           'descripcion':encodeURIComponent($('#descripcion').val()),
           'cantidadEjecutada' : parseInt($('#cantidadEjecutada').val()),           
           'fechaEjecucion':$('#fechaEjecucion').val()           
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Error Interno del Servidor.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){
              var Mensaje='Se ha guardado el registro correctamente.';
              var Botones={Cerrar: function(){                  
                   revisarEjecucion(idSp);
                   CancelarModal();                   
                   $( this ).dialog( "close" )}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return true;      
}

function eliminarRegistro(idEjecucion)
{
    var Botones={No: function(){$( this ).dialog( "close" )},
                 Sí: function(){ 
     $.ajax({
     type:'POST',
     url:'ejecucion_productos/eliminarRegistro',
     data:{
           'idEjecucion':idEjecucion
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},
     error: function(){
                 var Mensaje='Ha Ocurrido un Error al Intentar Borrar el registro.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){                                          
                 var Mensaje='Se ha Borrado el registro correctamente. ';
                 var Botones={Cerrar: function(){
                     revisarEjecucion($("#id_subproducto").val());
                     CancelarModal();                       
                     $( this ).dialog( "close" )}};
                 CajaDialogo('Borrado', Mensaje, Botones);
                   },                   
     dataType:'text'});
     
     $( this ).dialog( "close" )}
                  };                   
    var Mensaje='¿Está Seguro que desea Eliminar el registro?';
    CajaDialogo('Pregunta', Mensaje, Botones);
}

function campoFecha(id)
{
    $( "#"+id ).datepicker({
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
              minDate:"01/01/"+$('#year_poa').val(),
              maxDate: _DiaHoy(),
              dayNames:[ "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado" ],
              dayNamesMin:[ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
              monthNames:["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
              onSelect: function( selectedDate ) {
                  $( "#fechaF" ).datepicker( "option", "minDate", selectedDate )}            
          });
}