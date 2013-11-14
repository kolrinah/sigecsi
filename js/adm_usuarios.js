/* 
 * 
 * 
 */

$(document).ready(function()
{     
    $('#usuarios').dataTable( {
	"sPaginationType": "full_numbers",
        "aaSorting": [[ 3, "asc" ],[ 2, "asc" ]]        
			} );
});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES
function AgregarUsuario(admin)
{  
  var form;
  form='<div class="EntraDatos">';
  form+='<table>';
  form+='<thead>';
  form+='<tr><th colspan="2">';            
  form+='Nuevo Usuario';  
  form+='</th></tr>';           
  form+='</thead>';            
  form+='<tbody>';
  form+='<tr>';
  form+='<td width="50%">';              
  form+='<label>Cédula de Identidad:</label>';
  form+='<input type="text" id="CI" class="Editable" tabindex="1000" title="Introduzca el Número de Cédula"/>';
  form+='<input type="button" onclick="javascript:BuscarUsuario()" tabindex="1001" title="Buscar" value="Buscar"/>';
  form+='</td>';
  form+='<td>';
  form+='<label>Correo Electrónico:</label>';
  form+='<input type="text" class="Campos" id="Correo" title="Correo Electrónico" readonly="readonly"/>';
  form+='</td>';
  form+='</tr>';
  form+='<tr>';
  form+='<td>';
  form+='<label>Nombre:</label>';
  form+='<input type="text" class="Campos" id="Nombre" title="Nombre" readonly="readonly"/>';
  form+='</td>';
  form+='<td>';
  form+='<label>Apellido:</label>';
  form+='<input type="text" class="Campos" id="Apellido" title="Apellido" readonly="readonly"/>';
  form+='</td>';
  form+='</tr>';  
  form+='<tr>';
  form+='<td colspan="2">';
  form+='<input type="hidden" id="id_unidad" />';  
  form+='<label>Unidad Administrativa:</label>';
  form+='<center><input type="text" class="Campos Editable" id="Unidad" title="Unidad Administrativa" tabindex="1002"/></center>';
  form+='</td>';  
  form+='</tr>';
  form+='<tr>';
  form+='<td>';
  form+='<label>Nivel de Usuario:</label>';
  form+='<select class="Campos Editable" id="Nivel" title="Seleccione el Nivel del Usuario" tabindex="1003">';
  form+='<option selected="selected" value="0">[Seleccione]</option>';
  form+='</select>';
  form+='</td>';
  form+='<td>';
    if (admin==1)
    {
      form+='<label>Rol de Usuario:</label>';
      form+='<div class="ToggleBoton" onclick="javascript:ToggleBotonAdmin()" title="Haga clic para cambiar">';
      form+='<img id="imgAdmin" src="imagenes/user16.png"/>';
      form+='</div>';
      form+='<span id="spanAdmin">&nbsp;Usuario Normal</span>';      
    }
  form+='<input type="hidden" id="hideAdmin" value="f" />';     
  form+='</td>';
  form+='</tr>';       
  form+='</tbody>';
  
  form+='<tfoot>';
  form+='<tr><td colspan="2">';
  form+='<div class="BotonIco" onclick="javascript:GuardarUsuario()" title="Guardar Usuario">';
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
  $('#CI').focus();
  
  selector_autocompletar();  
}

function GuardarUsuario()
{
  if($('#CI').val()=='' || $('#id_unidad').val()=='' || $('#Nivel').val()=='0' || $('#Correo').val()=='')
  {
    var Mensaje='Debe llenar todos los campos para guardar el Usuario.';  
    CajaDialogo("Alerta", Mensaje);
    return false;
  }
  $.ajax({
          type:'POST',
          url:'adm_usuarios/insertar_usuario',
          data:{
                'cedula':$('#CI').val(),
                'nombre':encodeURIComponent($('#Nombre').val()),
                'apellido':encodeURIComponent($('#Apellido').val()),
                'correo':$('#Correo').val(),
                'id_estructura':$('#id_unidad').val(),
                'id_nivel':$('#Nivel').val(),
                'administrador':$('#hideAdmin').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Guardar el Usuario.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){                  
                   var Mensaje='El Usuario se ha Guardado Correctamente.';
                   var Botones={Cerrar: function(){                       
                       CancelarModal();
                       window.location.reload( true );
                       $( this ).dialog( "close" )}};
                   CajaDialogo('Guardar', Mensaje, Botones);},
          dataType:'text'});         
  return false;
}

function BuscarUsuario()
{
  if (!(verificarCI($('#CI').val()) || verificarEmail($('#CI').val())))
  {    
    var Mensaje='Debe Introducir Datos Válidos';  
    CajaDialogo("Alerta", Mensaje);
    $('#CI').val('');
    return false; 
  }  
  
  $.ajax({
          type:'POST',
          url:'adm_usuarios/buscar_usuario',
          data:{
                'patron':trim($('#CI').val())
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Error Interno del Servidor.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){
                      if (data==1)
                      {
                        var Mensaje='Usuario Ya Existe.';
                        var Botones={Cerrar: function(){
                            $( this ).dialog( "close" )}};
                        CajaDialogo('Error', Mensaje, Botones);        
                      }
                      else
                      {
                         BuscarLDAP();     
                      }
                                },
          dataType:'text'});          
  return false;  
}

function BuscarLDAP()
{
   $.ajax({
          type:'POST',
          url:'adm_usuarios/buscarLDAP',
          data:{
                'patron':trim($('#CI').val()) 
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Error Interno del Servidor.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){                      
                      if (data.count!=0)
                      {                        
                        $('#CI').val(trim(data.cedula));
                        $('#CI').attr('readonly','readonly');
                        $('#Correo').val(trim(data.mail));
                        $('#Nombre').val(trim(data.nombre));
                        $('#Apellido').val(trim(data.apellido));
                      }
                      else
                      {
                        var Mensaje='Usuario No Encontrado en LDAP.';
                        var Botones={Cerrar: function(){
                            $( this ).dialog( "close" )}};
                        CajaDialogo('Error', Mensaje, Botones);        
                      }
                                  },
          dataType:'json'});          
  return false;   
}

function EditarUsuario(id_usuario)
{
   $.ajax({
      type:'POST',
      url:'adm_usuarios/editar_usuario',
      data:{'id_usuario':id_usuario},
      beforeSend:function(){$("#cargandoModal").show()},
      complete: function(){
                  $("#cargandoModal").hide()},               
      error: function(){
                  var Mensaje='Error Interno del Servidor.';
                  CajaDialogo('Error', Mensaje)},
      success: function(data){                  
                  $('#VentanaModal').html(data);
                  $('#VentanaModal').show();
                  
                  selector_autocompletar();                                     
                             },
      dataType:'html'});
   return false; 
}

function ActualizarUsuario(id_usuario)
{
  if($('#CI').val()=='' || $('#id_unidad').val()=='' || $('#Nivel').val()=='0' || $('#Correo').val()=='')
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }    
  $.ajax({
     type:'POST',
     url:'adm_usuarios/actualizar_usuario',
     data:{
           'id_usuario':id_usuario,
           'id_estructura':$('#id_unidad').val(),
           'id_nivel':$('#Nivel').val(),
           'administrador':$('#hideAdmin').val(),
           'activo':$('#hideActivo').val()
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Ha ocurrido un error al Actualizar el Usuario.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){
              var Mensaje='Se han guardado los cambios correctamente.';
              var Botones={Cerrar: function(){                  
                   CancelarModal();                    
                   window.location.reload( true );
                   $( this ).dialog( "close" )}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;  
}  

function ResetearClave(id_usuario)
{
  if($('#CI').val()=='' || $('#id_unidad').val()=='' || $('#Nivel').val()=='0' || $('#Correo').val()=='')
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }    
  $.ajax({
     type:'POST',
     url:'adm_usuarios/resetear_clave',
     data:{
           'id_usuario':id_usuario
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Ha ocurrido un error al Actualizar el Usuario.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){
              var Mensaje='Contraseña reiniciada correctamente.<br/>La nueva contraseña es "123"';
              var Botones={Cerrar: function(){
                  
                   CancelarModal();                    
                   
                   $( this ).dialog( "close" )}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;  
}

function ToggleBotonAdmin()
{
   if ($("#hideAdmin").val()=='t')
   {
     $("#hideAdmin").val('f');      
     $("#imgAdmin").attr('src', 'imagenes/user16.png');
     $("#spanAdmin").html('&nbsp;Usuario Normal');
   }
   else
   {
     $("#hideAdmin").val('t');      
     $("#imgAdmin").attr('src', 'imagenes/admin16.png');
     $("#spanAdmin").html('&nbsp;Administrador');
   }
}

function ToggleBotonActivo()
{
   if ($("#hideActivo").val()=='t')
   {
     $("#hideActivo").val('f');      
     $("#imgActivo").attr('src', 'imagenes/cancel16.png');
     $("#spanActivo").html('&nbsp;Usuario Inactivo');
   }
   else
   {
     $("#hideActivo").val('t');      
     $("#imgActivo").attr('src', 'imagenes/activo16.png');
     $("#spanActivo").html('&nbsp;Usuario Activo');
   }
}

function selector_autocompletar()
{
    $("#Unidad").autocomplete({
        minLength:1,
        delay:3,
        source: function(request, response)
                {
                  var url="adm_usuarios/listar_unidades";  //url donde buscará las oficinas
                  $.post(url,{'frase':request.term}, response, 'json');
                },
        select: function( event, ui ) 
                {                    
                  var unidad='';
                  var combo='<option selected="selected" value="0">[Seleccione]</option>';
                  switch (parseInt(ui.item.tipo))
                  {                       
                   case 1: // Ministro
                     unidad=ui.item.codigo+' - '+ui.item.descripcion;                     
                     combo+='<option value="6">Funcionario</option>';
                     combo+='<option value="5">Responsable del POA</option>';
                     combo+='<option value="1">Ministro / Canciller</option>';
                     break;
                   case 2: // Director General / Jefe de Misión / Vice-Ministro
                     unidad=ui.item.codigo+' - '+ui.item.descripcion;
                     combo+='<option value="6">Funcionario</option>';
                     combo+='<option value="5">Responsable del POA</option>';
                     combo+='<option value="2">Director General / Vice-Ministro</option>';
                     break;
                   case 3: // Director de Línea
                     unidad=ui.item.codigo+' - '+ui.item.descripcion+' / '+ui.item.oficina;
                     combo+='<option value="6">Funcionario</option>';
                     combo+='<option value="5">Responsable del POA</option>';
                     combo+='<option value="3">Director de Línea</option>';
                     break;
                   case 4: // Director de Línea
                     unidad=ui.item.codigo+' - '+ui.item.descripcion+' / '+ui.item.oficina;
                     combo+='<option value="6">Funcionario</option>';
                     combo+='<option value="5">Responsable del POA</option>';
                     combo+='<option value="4">Coordinador de Área</option>';
                     break; 
                   default: 
                     unidad=ui.item.codigo+' - '+ui.item.descripcion+' / '+ui.item.oficina;
                     combo+='<option value="6">Funcionario</option>';
                     combo+='<option value="5">Responsable del POA</option>';
                     break;
                  }
                  $("#Unidad").val( unidad );                  
                  $("#id_unidad").val(ui.item.id);   
                  $("#Nivel").html(combo);   
                  return false;
                }
      }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )                
                .data( "item.autocomplete", item )
		.append( "<a>" + ((item.descripcion==undefined)?'Sin coincidencias':item.descripcion) + "<br/><span style='font-size:10px;'>" +((item.oficina==undefined)?'':item.oficina) + "</span></a>" )
		.appendTo( ul );
	  };
   uniMsj='-- Escriba aquí el nombre de la Unidad donde pertenece el Usuario --';
   if ($('#Unidad').val()=='')
   {
       $('#Unidad').val(uniMsj);        
   }  
   $('#Unidad').focusin(function()
       {if ($(this).val()==uniMsj){$(this).val('');}}).focusout(function(){
        if ($(this).val()=='')
        {
            $(this).val(uniMsj);
            $("#id_unidad").val('');   
            $("#Nivel").html('<option selected="selected" value="0">[Seleccione]</option>');
        }});     
}