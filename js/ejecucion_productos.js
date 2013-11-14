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

function EditarActividad(id_sp,id_plan)
{   
  var form;
  form='<div class="EntraDatos">';
  form+='<table>';
  form+='<thead>';
  form+='<tr><th colspan="2">';            
  form+='Edición de la Planificación del Sub-Producto';  
  form+='</th></tr>';           
  form+='</thead>';            
  form+='<tbody>';
  form+='<tr>';
  form+='<td colspan="2">';
  form+='<label>Sub-Producto:</label>';
  form+='<input type="text" class="Campos" id="Correo" title="Nombre del Sub-Producto" readonly="readonly" ';
  form+='value="'+$('#codSP_'+id_sp).html()+' '+$('#nomSP_'+id_sp).html()+'"';
  form+='/>';
  form+='</td>';
  form+='</tr>'; 
  form+='<tr>';
  form+='<td colspan="2">';  
  form+='<label>Descripción de la Actividad:</label>';
  form+='<input type="text" class="Campos Editable" id="Actividad" title="Descripción de la Actividad" tabindex="1002" ';
  form+='value="'+$('#act_'+id_plan).html()+'"';
  form+='/>';
  form+='</td>';  
  form+='</tr>';
  form+='<tr>';
  form+='<td width="50%">';
  form+='<label>Responsable de la Actividad:</label>';
  form+='<select class="Campos Editable" id="Responsable" title="Seleccione el Responsable" tabindex="1003">';
  form+='</select>';
  form+='</td>';
  form+='</tr>'; 
  form+='<td>';
  form+='<label>Fecha de Inicio:</label>';
  form+='<input type="text" class="Fechas Editable" id="fechaI" title="Fecha Inicial" tabindex="1003" ';
  form+='value="'+$('#fechaIni_'+id_plan).val()+'" ';
  form+='readonly="readonly"/>';
  form+='</td>';
  form+='<td>';
  form+='<label>Fecha de Culminación:</label>';
  form+='<input type="text" class="Fechas Editable" id="fechaF" title="Fecha Final" tabindex="1004" ';
  form+='value="'+$('#fechaFin_'+id_plan).val()+'" ';
  form+='readonly="readonly" />';
  form+='</td>';
  form+='</tr>';
  form+='</tbody>';
  
  form+='<tfoot>';
  form+='<tr><td colspan="2">';
  form+='<div class="BotonIco" onclick="javascript:EliminarActividad('+id_plan+')" title="Eliminar Actividad">';
  form+='<img src="imagenes/plan_del.png"/>&nbsp;';   
  form+='Eliminar';
  form+= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';  
  form+='<div class="BotonIco" onclick="javascript:ActualizarActividad('+id_plan+')" title="Guardar Cambios">';
  form+='<img src="imagenes/guardar32.png"/>&nbsp;';   
  form+='Guardar';
  form+= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  form+='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cancelar">';
  form+='<img src="imagenes/cancel.png"/>&nbsp;';
  form+='Cancelar';
  form+= '</div>';
  form+='</td></tr>';
  form+='</tfoot>';
  form+='</table>';   
  form+='</div>';
  
  $('#VentanaModal').html(form);
  $('#VentanaModal').show();
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
            defaultDate:$('#fechaIni_'+id_plan).val(),
            minDate:_FechaMayor("01/01/"+$('#year_poa').val(),_DiaHoy()),
            maxDate:$('#fechaF').val(),
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
            defaultDate:$('#fechaFin_'+id_plan).val(),
            minDate:_FechaMayor($('#fechaI').val(),_DiaHoy()), 
            maxDate:"31/12/"+$('#year_poa').val(),
            dayNames:[ "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado" ],
            dayNamesMin:[ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
            monthNames:["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            onSelect: function( selectedDate ) {
                $( "#fechaI" ).datepicker( "option", "maxDate", selectedDate )}
        });
        
  
  url='plan_productos/listar_usuarios';
  $.post(url,
        {'id_estructura':$('#idEstructura_'+id_sp).val(),
         'id_responsable':$('#idResponsable_'+id_plan).val()
        }, 
        function(data){
          $('#Responsable').html(data);
        }      
  );  
}

function ActualizarActividad(id_plan)
{
  if(trim($('#Actividad').val())=='' || $('#Responsable').val()==0 || $('#FechaI').val()=='')
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

function EliminarActividad(id_plan)
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

function AgregarActividad(id_sp)
{  
  var form;
  form='<div class="EntraDatos">';
  form+='<table>';
  form+='<thead>';
  form+='<tr><th colspan="2">';            
  form+='Planificación del Sub-Producto';  
  form+='</th></tr>';           
  form+='</thead>';            
  form+='<tbody>';
  form+='<tr>';
  form+='<td colspan="2">';
  form+='<label>Sub-Producto:</label>';
  form+='<input type="text" class="Campos" id="Correo" title="Nombre del Sub-Producto" readonly="readonly" ';
  form+='value="'+$('#codSP_'+id_sp).html()+' '+$('#nomSP_'+id_sp).html()+'"';
  form+='/>';
  form+='</td>';
  form+='</tr>'; 
  form+='<tr>';
  form+='<td colspan="2">';  
  form+='<label>Descripción de la Actividad:</label>';
  form+='<input type="text" class="Campos Editable" id="Actividad" title="Descripción de la Actividad" tabindex="1002" ';
  form+='/>';
  form+='</td>';  
  form+='</tr>';
  form+='<tr>';
  form+='<td width="50%">';
  form+='<label>Responsable de la Actividad:</label>';
  form+='<select class="Campos Editable" id="Responsable" title="Seleccione el Responsable" tabindex="1003">';
  form+='</select>';
  form+='</td>';
  form+='</tr>'; 
  form+='<td>';
  form+='<label>Fecha de Inicio:</label>';
  form+='<input type="text" class="Fechas Editable" id="fechaI" title="Fecha Inicial" tabindex="1003" ';
  form+='value="" ';
  form+='readonly="readonly"/>';
  form+='</td>';
  form+='<td>';
  form+='<label>Fecha de Culminación:</label>';
  form+='<input type="text" class="Fechas Editable" id="fechaF" title="Fecha Final" tabindex="1004" ';
  form+='value="" ';
  form+='readonly="readonly" />';
  form+='</td>';
  form+='</tr>';
  form+='</tbody>';
  
  form+='<tfoot>';
  form+='<tr><td colspan="2">';
  form+='<div class="BotonIco" onclick="javascript:GuardarActividad('+id_sp+')" title="Guardar Actividad">';
  form+='<img src="imagenes/guardar32.png"/>&nbsp;';   
  form+='Guardar';
  form+= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  form+='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cancelar">';
  form+='<img src="imagenes/cancel.png"/>&nbsp;';
  form+='Cancelar';
  form+= '</div>';
  form+='</td></tr>';
  form+='</tfoot>';
  form+='</table>';   
  form+='</div>';
  
  $('#VentanaModal').html(form);
  $('#VentanaModal').show();
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
        
  
  url='plan_productos/listar_usuarios';
  $.post(url,
        {'id_estructura':$('#idEstructura_'+id_sp).val(),
         'id_responsable':0
        }, 
        function(data){
          $('#Responsable').html(data);
        }      
  );  
}

function GuardarActividad(id_sp)
{
  if(trim($('#Actividad').val())=='' || $('#Responsable').val()==0 || $('#FechaI').val()=='' || $('#FechaF').val()=='')
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