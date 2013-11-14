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
    uri='plan_proyectos/listar_proyectos';
    
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

function RevisarFicha(id_proyecto,editable)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/revisar_ficha',
       data:{
             'id_proyecto':id_proyecto,
             'editable':editable
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

function ActualizarFicha(id_proyecto)
{
  if(trim($('#obj_esp').val())=='' || trim($('#obj_gen').val())=='' ||
     trim($('#descripcion_breve').val())=='' ||
     trim($('#problema').val())=='' || trim($('#indicador_problema').val())=='' ||
     trim($('#indicador_obj_proy').val())=='' || trim($('#resultado').val())=='' ||
     trim($('#telefonos').val())=='') 
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }
  
  $.ajax({
     type:'POST',
     url:'plan_proyectos/actualizar_ficha',
     data:{
           'id_proyecto':id_proyecto,
           'id_responsable':$('#id_responsable').val(),           
           'obj_esp':encodeURIComponent($('#obj_esp').val()),
           'obj_gen':encodeURIComponent($('#obj_gen').val()),
           'descripcion_breve':encodeURIComponent($('#descripcion_breve').val()),
           'problema':encodeURIComponent($('#problema').val()),
           'indicador_problema':encodeURIComponent($('#indicador_problema').val()),
           'indicador_obj_proy':encodeURIComponent($('#indicador_obj_proy').val()),
           'resultado':encodeURIComponent($('#resultado').val()),
           'telefonos':encodeURIComponent($('#telefonos').val())
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

function CrearProyecto(id_estructura)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/crear_proyecto',
       data:{
             'id_estructura':id_estructura
            },
       beforeSend:function(){$("#cargandoModal").show()},
       complete: function(){
                   $("#cargandoModal").hide()},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje)},
       success: function(data){                                          
                     $('#Planes').html(data);
                     $('#Planes').show();
                },                   
       dataType:'html'});
    return false;
}

function GuardarProyecto(id_estructura)
{
  if(trim($('#obj_esp').val())=='' || 
     trim($('#obj_gen').val())=='' || trim($('#descripcion_breve').val())=='' ||
     trim($('#problema').val())=='' || trim($('#indicador_problema').val())=='' ||
     trim($('#indicador_obj_proy').val())=='' || trim($('#resultado').val())=='' ||
     trim($('#telefonos').val())=='') 
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }
  $.ajax({
     type:'POST',
     url:'plan_proyectos/guardar_proyecto',
     data:{
           'id_estructura':id_estructura,
           'yearpoa':$('#year_poa').val(),
           'obj_esp':encodeURIComponent($('#obj_esp').val()),
           'obj_gen':encodeURIComponent($('#obj_gen').val()),
           'descripcion_breve':encodeURIComponent($('#descripcion_breve').val()),
           'problema':encodeURIComponent($('#problema').val()),
           'indicador_problema':encodeURIComponent($('#indicador_problema').val()),
           'indicador_obj_proy':encodeURIComponent($('#indicador_obj_proy').val()),
           'resultado':encodeURIComponent($('#resultado').val()),
           'id_responsable':$('#id_responsable').val(),
           'telefonos':encodeURIComponent($('#telefonos').val())
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
                   $( this ).dialog( "close" );}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;      
}

function EliminarProyecto(id_proyecto)
{
  var Botones={No: function(){$( this ).dialog( "close" );},
               Sí: function(){
   $.ajax({
   type:'POST',
   url:'plan_proyectos/eliminar_proyecto',
   data:{
         'id_proyecto':id_proyecto
        },
   beforeSend:function(){$("#cargandoModal").show();},
   complete: function(){
               $("#cargandoModal").hide();},               
   error: function(){
               var Mensaje='No se pudo Eliminar el Proyecto. Verifique que no posea Acciones específicas ni otras Dependencias';
               CajaDialogo('Error', Mensaje);},
   success: function(data){                         
            var Botones={Cerrar: function(){
                    Actualiza();
                    $( this ).dialog( "close" );}};
            var Mensaje='Proyecto Eliminado satisfactoriamente.';
            CajaDialogo('Borrado', Mensaje, Botones);},
   dataType:'text'});
   $( this ).dialog( "close" );}
              };
  var Mensaje='¿Está Seguro que desea Eliminar el Proyecto?';      
  CajaDialogo('Pregunta', Mensaje, Botones);
  return false;
}

function ListarAcciones(id_proyecto,editable)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/listar_acciones',
       data:{
             'id_proyecto':id_proyecto,
             'editable':editable
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

function EditarAccion(id_accion)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/editar_accion',
       data:{
             'id_accion':id_accion
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

function ActualizarAccion(id_accion, id_proyecto)
{
  if(trim($('#codigo_ae').val())=='' || trim($('#iov_ae').val())=='' || 
     trim($('#mv_ae').val())=='' || trim($('#supuestos_ae').val())==''||     
     trim($('#descripcion_ae').val())=='') 
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }
  
  $.ajax({
     type:'POST',
     url:'plan_proyectos/actualizar_accion',
     data:{
           'id_accion':id_accion,
           'descripcion_ae':encodeURIComponent($('#descripcion_ae').val()),
           'mv_ae':encodeURIComponent($('#mv_ae').val()),
           'iov_ae':encodeURIComponent($('#iov_ae').val()),
           'supuestos_ae':encodeURIComponent($('#supuestos_ae').val()),
           'codigo_ae':encodeURIComponent($('#codigo_ae').val())
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
                   ListarAcciones(id_proyecto,1);
                   CancelarModal();                   
                   $( this ).dialog( "close" );}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;      
}

function EliminarAccion(id_accion,id_proyecto)
{
  var Botones={No: function(){$( this ).dialog( "close" );},
               Sí: function(){
   $.ajax({
   type:'POST',
   url:'plan_proyectos/eliminar_accion',
   data:{
         'id_accion':id_accion
        },
   beforeSend:function(){$("#cargandoModal").show();},
   complete: function(){
               $("#cargandoModal").hide();},               
   error: function(){
               var Mensaje='No se pudo Eliminar la Acción Específica. Verifique que no posea Actividades u otras Dependencias';
               CajaDialogo('Error', Mensaje);},
   success: function(data){                         
            var Botones={Cerrar: function(){
                    ListarAcciones(id_proyecto,1);
                    $( this ).dialog( "close" );}};
            var Mensaje='Acción Específica Eliminada satisfactoriamente.';
            CajaDialogo('Borrado', Mensaje, Botones);},
   dataType:'text'});
   $( this ).dialog( "close" );}
              };
  var Mensaje='¿Está Seguro que desea Eliminar la Acción Específica?';      
  CajaDialogo('Pregunta', Mensaje, Botones);
  return false;
}

function CrearAccion(id_proyecto)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/crear_accion',
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

function GuardarAccion(id_proyecto)
{
  if(trim($('#codigo_ae').val())=='' || trim($('#iov_ae').val())=='' || 
     trim($('#mv_ae').val())=='' || trim($('#supuestos_ae').val())==''||     
     trim($('#descripcion_ae').val())=='') 
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }
  
  $.ajax({
     type:'POST',
     url:'plan_proyectos/guardar_accion',
     data:{
           'id_proyecto':id_proyecto,
           'descripcion_ae':encodeURIComponent($('#descripcion_ae').val()),
           'mv_ae':encodeURIComponent($('#mv_ae').val()),
           'iov_ae':encodeURIComponent($('#iov_ae').val()),
           'supuestos_ae':encodeURIComponent($('#supuestos_ae').val()),
           'codigo_ae':encodeURIComponent($('#codigo_ae').val())
          },
     beforeSend:function(){$("#cargandoModal").show();},
     complete: function(){
                 $("#cargandoModal").hide();},               
     error: function(){
                 var Mensaje='Error Interno del Servidor.';
                 CajaDialogo('Error', Mensaje);},
     success: function(data){
              var Mensaje='Se ha creado la Acción Específica correctamente.';
              var Botones={Cerrar: function(){                   
                   ListarAcciones(id_proyecto,1);
                   CancelarModal();                   
                   $( this ).dialog( "close" );}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;      
}

function ListarActividades(id_accion,editable)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/listar_actividades',
       data:{
             'id_accion':id_accion,
             'editable':editable
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

function planProyecto(id_proyecto,editable)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/plan_proyecto',
       data:{
             'id_proyecto':id_proyecto,
             'editable':editable
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

function actualizaGantt(gantt)
{   
    $.ajax({type:'POST',
             url:'enviar_poa/actualizaGantt', 
            data:{'gantt':gantt},
        complete:function(){planProyecto($("#idProyecto").val(),$("#editable").val());} });    
    return true;
}

function ProgramarActividad(id_accion)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/programar_actividad',
       data:{
             'id_accion':id_accion
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
                               $( "#fechaF" ).datepicker( "option", "minDate", selectedDate );}            
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
                                   $( "#fechaI" ).datepicker( "option", "maxDate", selectedDate );}
                           });                          
                      },                   
       dataType:'html'});    
      
    return false;
}

function GuardarActividad(id_accion, id_proyecto)
{
  if(trim($('#codigo_act').val())=='' || trim($('#descripcion_act').val())=='' || 
     trim($('#um_act').val())=='' || parseFloat($('#cantidad_act').val()) < 1 ||
     isNaN(parseFloat($('#cantidad_act').val())) ||
     trim($('#mv_act').val())=='' || trim($('#supuestos_act').val())==''||
     trim($('#fechaI').val())=='' || trim($('#fechaF').val())=='') 
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }
  
  $.ajax({
     type:'POST',
     url:'plan_proyectos/guardar_actividad',
     data:{
           'id_accion':id_accion,
           'codigo_act':encodeURIComponent($('#codigo_act').val()),
           'descripcion_act':encodeURIComponent($('#descripcion_act').val()),
           'um_act':encodeURIComponent($('#um_act').val()),
           'cantidad_act':$('#cantidad_act').val(),
           'mv_act':encodeURIComponent($('#mv_act').val()),
           'supuestos_act':encodeURIComponent($('#supuestos_act').val()),
           'id_responsable':$('#id_responsable').val(),
           'fecha_ini':$('#fechaI').val(),
           'fecha_fin':$('#fechaF').val()
          },
     beforeSend:function(){$("#cargandoModal").show();},
     complete: function(){
                 $("#cargandoModal").hide();},               
     error: function(){
                 var Mensaje='Error Interno del Servidor.';
                 CajaDialogo('Error', Mensaje);},
     success: function(data){
              var Mensaje='Se ha creado la Actividad correctamente.';
              var Botones={Cerrar: function(){                   
                   planProyecto(id_proyecto,1);
                   CancelarModal();                   
                   $( this ).dialog( "close" );}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;      
}

function EditarActividad(id_actividad)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/editar_actividad',
       data:{
             'id_actividad':id_actividad
            },
       beforeSend:function(){$("#cargandoModal").show()},
       complete: function(){
                   $("#cargandoModal").hide()},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje)},
       success: function(data){                                          
                     $('#Planes').html(data);
                     $('#Planes').show();
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
                },                   
       dataType:'html'});
    return false;
}

function ActualizarActividad(id_actividad, id_proyecto)
{    
  if(trim($('#codigo_act').val())=='' || trim($('#descripcion_act').val())=='' || 
     trim($('#um_act').val())=='' || parseFloat($('#cantidad_act').val())< 1 ||
     isNaN(parseFloat($('#cantidad_act').val())) ||
     trim($('#mv_act').val())=='' || trim($('#supuestos_act').val())==''||
     trim($('#Responsable').val())=='' || trim($('#fechaI').val())=='' ||
     trim($('#fechaF').val())=='') 
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }

  if(parseFloat($('#codigo_act').val())<1.0 || parseFloat($('#codigo_act').val())>99.99 || isNaN($('#codigo_act').val()))
  {
    var Mensaje='Debe introducir un código válido';  
    CajaDialogo("Alerta", Mensaje);
    return false;
  }
    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/actualizar_actividad',
       data:{
             'id_actividad':id_actividad,
             'id_fuente':$('#id_fuente').val(),
             'codigo_act':encodeURIComponent($('#codigo_act').val()),
             'descripcion_act':encodeURIComponent($('#descripcion_act').val()),
             'um_act':encodeURIComponent($('#um_act').val()),
             'cantidad_act':$('#cantidad_act').val(),
             'mv_act':encodeURIComponent($('#mv_act').val()),
             'supuestos_act':encodeURIComponent($('#supuestos_act').val()),
             'id_responsable':$('#Responsable').val(),
             'fecha_ini':$('#fechaI').val(),
             'fecha_fin':$('#fechaF').val()
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},
       error: function(){
                   var Mensaje='Ha ocurrido un error al Actualizar la Actividad.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){
                var Mensaje='Se han guardado los cambios correctamente.';
                var Botones={Cerrar: function(){
                     planProyecto(id_proyecto,1);                     
                     CancelarModal();                       
                     $( this ).dialog( "close" );}};
                CajaDialogo('Guardar', Mensaje, Botones);},
       dataType:'text'});    
}  

function EliminarActividad(id_actividad,id_proyecto)
{
  var Botones={No: function(){$( this ).dialog( "close" );},
               Sí: function(){
   $.ajax({
   type:'POST',
   url:'plan_proyectos/eliminar_actividad',
   data:{
         'id_actividad':id_actividad
        },
   beforeSend:function(){$("#cargandoModal").show();},
   complete: function(){
               $("#cargandoModal").hide();},
   error: function(){
               var Mensaje='No se pudo Eliminar la Actividad. Verifique que no posea Presupuesto ni Dependencias';
               CajaDialogo('Error', Mensaje);},
   success: function(data){                         
            var Botones={Cerrar: function(){
                    planProyecto(id_proyecto,1); 
                    $( this ).dialog( "close" );}};
            var Mensaje='Actividad Eliminada satisfactoriamente.';
            CajaDialogo('Borrado', Mensaje, Botones);},
   dataType:'text'});
   $( this ).dialog( "close" );}
              };
  var Mensaje='¿Está Seguro que desea Eliminar la Actividad?';      
  CajaDialogo('Pregunta', Mensaje, Botones);
  return false;
}

function listarPresupuesto(id_actividad,editable)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/listar_presupuesto',
       data:{
             'id_actividad':id_actividad,
             'editable':editable
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

function agregarPresupuesto(id_actividad)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/agregar_presupuesto',
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
                     $('#presupuesto').html(data);
                     $('#presupuesto').show();
                     selector('partida');
                },                   
       dataType:'html'});
    return false;
}

function actualizaMensaje(id_actividad, montoActividadOrig)
{   
    var montoActividadOrig= montoActividadOrig || 0;
    var cantidad = parseFloat(($('#cantidad').val()).replace(/\./g,"").replace(",","."));
    var costo = parseFloat(($('#costo_unitario').val()).replace(/\./g,"").replace(",","."));
    $.ajax({
       type:'POST',
       url:'plan_proyectos/actualizaMensaje',
       data:{
             'id_actividad':id_actividad,
           'montoActividad':((cantidad * costo) - montoActividadOrig)
            },
     /*  beforeSend:function(){$("#cargandoModal").show();},
      complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},*/
       success: function(data){                                          
                     $('#msjPresup').html(data);                     
                },                   
       dataType:'html'});
    return false;    
}

function guardarPresupuesto(id_actividad)
{
  actualizaMensaje(id_actividad);
  if(trim($('#id_partida').val())==='0' || trim($('#descripcion_gasto').val())==='' || 
       !verificarNro($('#cantidad').val()) || trim($('#um').val())==='' ||
       !verificarNro($('#costo_unitario').val())     )
  {
    var Mensaje='Debe completar todos los campos para poder guardar.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }
  
  if ($('#permisoGuardar').val()==='0')
  {
    var Mensaje='El prespuesto planificado no puede exceder el monto aprobado.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }      
  
  $.ajax({
     type:'POST',
     url:'plan_proyectos/guardar_presupuesto',
     data:{
           'id_actividad':id_actividad,
           'id_partida':$('#id_partida').val(),
           'descripcion_gasto':encodeURIComponent($('#descripcion_gasto').val()),
           'um':encodeURIComponent($('#um').val()),
           'cantidad':encodeURIComponent($('#cantidad').val()),
           'costo_unitario':encodeURIComponent($('#costo_unitario').val())   
          },
     beforeSend:function(){$("#cargandoModal").show();},
     complete: function(){
                 $("#cargandoModal").hide();},               
     error: function(){
                 var Mensaje='Error Interno del Servidor.';
                 CajaDialogo('Error', Mensaje);},
     success: function(data){
              var Mensaje='Se ha registrado el presupuesto correctamente.';
              var Botones={Cerrar: function(){                   
                   listarPresupuesto(id_actividad,1);
                   CancelarModal();                   
                   $( this ).dialog( "close" );}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;          
}

function editarPresupuesto(id_presupuesto, id_actividad)
{    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/editar_presupuesto',
       data:{
             'id_presupuesto':id_presupuesto,
             'id_actividad': id_actividad
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Información.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){                                          
                     $('#presupuesto').html(data);
                     $('#presupuesto').show();
                     selector('partida');
                },                   
       dataType:'html'});
    return false;
}

function actualizarPresupuesto(id_presupuesto, id_actividad)
{       
    if(trim($('#id_partida').val())==='0' || trim($('#descripcion_gasto').val())==='' || 
       !verificarNro($('#cantidad').val()) || trim($('#um').val())==='' ||
       !verificarNro($('#costo_unitario').val())     )
    {
      var Mensaje='Debe completar todos los campos para poder guardar.';  
      CajaDialogo("Alerta", Mensaje);    
      return false;
    }
    if ($('#permisoGuardar').val()==='0')
    {
      var Mensaje='El prespuesto planificado no puede exceder el monto aprobado.';  
      CajaDialogo("Alerta", Mensaje);    
      return false;
    }   
    
    $.ajax({
       type:'POST',
       url:'plan_proyectos/actualizar_presupuesto',
       data:{
             'id_presupuesto':id_presupuesto,
             'id_partida':$('#id_partida').val(),
             'descripcion_gasto':encodeURIComponent($('#descripcion_gasto').val()),
             'um':encodeURIComponent($('#um').val()),
             'cantidad':encodeURIComponent($('#cantidad').val()),
             'costo_unitario':encodeURIComponent($('#costo_unitario').val())   
            },
       beforeSend:function(){$("#cargandoModal").show();},
       complete: function(){
                   $("#cargandoModal").hide();},
       error: function(){
                   var Mensaje='Ha ocurrido un error al Actualizar el Presupuesto.';
                   CajaDialogo('Error', Mensaje);},
       success: function(data){
                var Mensaje='Se han guardado los cambios correctamente.';
                var Botones={Cerrar: function(){                   
                     listarPresupuesto(id_actividad,1);
                     CancelarModal();                       
                     $( this ).dialog( "close" );}};
                CajaDialogo('Guardar', Mensaje, Botones);},
       dataType:'html'});    
}  

function eliminarPresupuesto(id_presupuesto, id_actividad)
{
  var Botones={No: function(){$( this ).dialog( "close" );},
               Sí: function(){
   $.ajax({
   type:'POST',
   url:'plan_proyectos/eliminar_presupuesto',
   data:{
         'id_presupuesto':id_presupuesto
        },
   beforeSend:function(){$("#cargandoModal").show();},
   complete: function(){
               $("#cargandoModal").hide();},
   error: function(){
               var Mensaje='No se pudo Eliminar el Presupuesto.';
               CajaDialogo('Error', Mensaje);},
   success: function(data){                         
            var Botones={Cerrar: function(){
                    listarPresupuesto(id_actividad,1); 
                    $( this ).dialog( "close" );}};
            var Mensaje='Presupuesto Eliminado satisfactoriamente.';
            CajaDialogo('Borrado', Mensaje, Botones);},
   dataType:'text'});
   $( this ).dialog( "close" );}
              };
  var Mensaje='¿Está Seguro que desea Eliminar el Presupuesto?';      
  CajaDialogo('Pregunta', Mensaje, Botones);
  return false;
}

function selector(destino)
{  
     $('#'+destino).autocomplete({
     minLength:1, //le indicamos que busque a partir de haber escrito dos o mas caracteres en el input
     delay:1,
     source: function(request, response)
             {
                var url="plan_proyectos/listado_partidas";  //url donde buscará las partidas
                $.post(url,{'frase':encodeURIComponent(request.term)}, response, 'json');
             },
     select: function( event, ui ) 
             {                  
                $("#id_partida").val( ui.item.id_partida );
                $("#partida").val( ui.item.id_partida+' '+ ui.item.denominacion ); 
                return false;
             }
   }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )                
                .data( "item.autocomplete", item )
		.append( "<a>" + ((item.id_partida==undefined)?'Sin coincidencias':item.id_partida) + " " +((item.denominacion==undefined)?'':item.denominacion) + "</a>" )
		.appendTo( ul );
	  };
 }