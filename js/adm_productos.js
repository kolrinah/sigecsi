/* 
 * 
 * 
 */

$(document).ready(function()
{      
  //CARGAMOS LA FUNCION DE AUTOCOMPLETAR    
  selector('unidad');
  selector('codigo');
    
  var uniMsj='-- Escriba Aquí el Nombre de la Unidad que desea Explorar --';
  var codMsj='Código';
  
  if ($("#codigo").val()=='')
  {
    $("#codigo").val(codMsj);
  }
  
  if ($("#unidad").val()=='')
  {
    $("#unidad").val(uniMsj);
  }
  
  $("#unidad").focusin(function()
  {if ($(this).val()==uniMsj){$(this).val('');}}).focusout(function(){
      if ($(this).val()==''){$(this).val(uniMsj);}});

  $("#codigo").focusin(function()
  {if ($(this).val()==codMsj){$(this).val('');}}).focusout(function(){
      if ($(this).val()==''){$(this).val(codMsj);}});

});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES
function selector(destino)
{  
     $('#'+destino).autocomplete({
     minLength:1, //le indicamos que busque a partir de haber escrito dos o mas caracteres en el input
     delay:1,
     source: function(request, response)
             {
                var url="adm_productos/armar_lista";  //url donde buscará las oficinas
                $.post(url,{'frase':request.term}, response, 'json');
             },
     select: function( event, ui ) 
             {                  
                $("#unidad").val( ui.item.descripcion );
                $("#codigo").val( ui.item.codigo );
                $("#id_unidad").val(ui.item.id); 
                var htm='';
                switch (parseInt(ui.item.tipo))
                {                       
                  case 3:
                    htm='<div class="superior" title="Oficina General">'+ui.item.oficina+'</div>';
                    htm+='<div class="lineas_orga"></div>';
                    $("#superiores").html(htm);                    
                    break;
                  case 4:
                    htm='<div class="superior" title="Oficina General">'+ui.item.oficina+'</div>';
                    htm+='<div class="lineas_orga"></div>';
                    htm+='<div class="superior" title="Dirección de Línea">'+ui.item.direccion+'</div>';
                    htm+='<div class="lineas_orga"></div>';
                    $("#superiores").html(htm);                    
                    break;
                  default: 
                    $("#superiores").html( '' );                        
                    break;
                }
                listar_productos(ui.item.id);                
                return false;
             }
   }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )                
                .data( "item.autocomplete", item )
		.append( "<a>" + ((item.descripcion==undefined)?'Sin coincidencias':item.descripcion) + "<br/><span style='font-size:10px;'>" +((item.oficina==undefined)?'':item.oficina) + "</span></a>" )
		.appendTo( ul );
	  };
  }
  
  function listar_productos(id)
  {                 
    $.ajax({
      type:'POST',
      url:'adm_productos/listar_productos',
      data:{'id':id},
      beforeSend:function(){$("#cargandoModal").show()},
      complete: function(){$("#cargandoModal").hide()},               
      error: function(){
                  $("#productos").html('');
                  var Mensaje='Error Interno del Servidor.';
                  CajaDialogo('Error', Mensaje)},
      success: function(data){$("#productos").html(data);},
      dataType:'html'});
  }
  
  function listar_subproductos(id_prod)
  {                
       if ($("#subproductos_"+id_prod).html()!=0)
       {
           $("#subproductos_"+id_prod).html('');
           $("#BotonSubproductos_"+id_prod).removeClass("BotonPisado");
           return false;
       }  
       $("#BotonSubproductos_"+id_prod).toggleClass("BotonPisado");
      
       $.ajax({
         type:'POST',
         url:'adm_productos/listar_subproductos',
         data:{'id':id_prod},
         beforeSend:function(){$("#cargandoModal").show()},
         complete: function(){$("#cargandoModal").hide()},               
         error: function(){
                     var Mensaje='Error Interno del Servidor.';
                     CajaDialogo('Error', Mensaje)},
         success: function(data){$("#subproductos_"+id_prod).html(data);},                        
         dataType:'html'});
  }
  
  function listar_dependencias(id_subprod)
  {       
      if ($("#detalles_"+id_subprod).html()!=0)
       {
           $("#detalles_"+id_subprod).html('');           
           $("#BotonDependencias_"+id_subprod).removeClass("Nivel3Pisado");
           $("#BotonInsumos_"+id_subprod).removeClass("Nivel3Pisado");
           return false;
       }  
       $("#BotonDependencias_"+id_subprod).toggleClass("Nivel3Pisado");    
       $.ajax({
                type:'POST',
                url:'adm_productos/listar_dependencias',
                data:{'id':id_subprod},
                beforeSend:function(){$("#cargandoModal").show()},
                complete: function(){
                            $("#cargandoModal").hide()},               
                error: function(){
                            var Mensaje='Error Interno del Servidor.';
                            CajaDialogo('Error', Mensaje)},
                success: function(data){
                            $("#detalles_"+id_subprod).html(data);},                        
                dataType:'html'});       
  }
  
  function listar_insumos(id_subprod)
  {       
       if ($("#detalles_"+id_subprod).html()!=0)
       {
           $("#detalles_"+id_subprod).html('');          
           $("#BotonDependencias_"+id_subprod).removeClass("Nivel3Pisado");
           $("#BotonInsumos_"+id_subprod).removeClass("Nivel3Pisado");
           return false;
       }  
       $("#BotonInsumos_"+id_subprod).toggleClass("Nivel3Pisado");       
       $.ajax({
                type:'POST',
                url:'adm_productos/listar_insumo',
                data:{'id':id_subprod},
                beforeSend:function(){$("#cargandoModal").show()},
                complete: function(){
                            $("#cargandoModal").hide()},               
                error: function(){
                            var Mensaje='Error Interno del Servidor.';
                            CajaDialogo('Error', Mensaje)},
                success: function(data){
                            $("#detalles_"+id_subprod).html(data);},                        
                dataType:'html'});        
  }
  
function AgregaProducto(id_estructura)
{    
    $.ajax({
       type:'POST',
       url:'adm_productos/agregar_producto',
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
                    $('#VentanaModal').html(data);
                    $('#VentanaModal').show();           
                    $('#nombreNvo').focus();
                },                   
       dataType:'html'});
    return false;
}   

  function GuardarProducto()
  {
     if($('#nombreNvo').val()=='' || $('#defNvo').val()=='')
      {
        var Mensaje='Debe llenar todos los campos para poder guardar el producto.';  
        CajaDialogo("Alerta", Mensaje);
        return false;
      }           
       $.ajax({
               type:'POST',
               url:'adm_productos/insertar_producto',
               data:{
                     'id_estructura':$('#id_unidad').val(),
                     'cod_nvo':encodeURIComponent($('#codNvo').val()),
                     'nombre_nvo':encodeURIComponent($('#nombreNvo').val()),
                     'det_nvo':encodeURIComponent($('#detNvo').val()),
                     'def_nvo':encodeURIComponent($('#defNvo').val())
                    },
               beforeSend:function(){$("#cargandoModal").show()},
               complete: function(){
                           $("#cargandoModal").hide()},               
               error: function(){
                           var Mensaje='Ha Ocurrido un Error al Intentar Guardar el Producto.';
                           CajaDialogo('Error', Mensaje)},
               success: function(data){
                        var Mensaje='Se Ha Guardado el Producto Correctamente.';
                        var Botones={Cerrar: function(){
                                listar_productos($('#id_unidad').val());
                                $( this ).dialog( "close" );
                                CancelarModal();
                                }};
                        CajaDialogo('Guardar', Mensaje, Botones);},
               dataType:'text'});         
       return false;
  }
  
  function EditarProducto(id_prod)
  {    
    var fila;
    fila='<div class="EntraDatos">';
    fila+='<table>';
    fila+='<thead>';
    fila+='<tr><th>';
    fila+='Editar Información del Producto Administrativo';   
    fila+='</th></tr>';           
    fila+='</thead>';            
    fila+='<tbody>';
    fila+='<tr><td>';              
    fila+='<label>Código del Producto:</label>';
    fila+='<input type="text" class="Nro Editable" id="codNvo" tabindex="1000" title="Código del Producto" value="';
    fila+=$("#codigoP"+id_prod).html()+'" maxlength="9"';
    //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
    fila+=' onkeypress="return onlyDigits(event, this.value,false,false,false,\',\',\'.\',0);"';
    fila+=' onkeyup="return onlyDigits(event, this.value,false,false,false,\',\',\'.\',0);"';
    fila+=' onblur="return onlyDigits(event, this.value,false,false,false,\',\',\'.\',0);"';

    fila+='" />';   
    fila+='</td></tr>';
    fila+='<tr><td>';              
    fila+='<label>Nombre del Producto:</label>';
    fila+='<textarea id="nombreNvo" class="Nom Editable" rows="1" tabindex="1001" title="Nombre del Producto">';
    fila+=$("#nombreP"+id_prod).html();     
    fila+='</textarea>';
    fila+='</td></tr>';
    fila+='<tr><td>';
    fila+='<label>Definición del Producto:</label>';
    fila+='<textarea id="defNvo" class="Nom Editable" rows="4" tabindex="1002" title="Definición del Producto">';
    fila+=$("#definicionP"+id_prod).html();     
    fila+='</textarea>';
    fila+='</td></tr>';
    fila+='</tbody>';
    fila+='<tfoot>';
    fila+='<tr><td>';
    fila+='<div class="BotonIco" onclick="javascript:ActualizarProducto('+id_prod+')" title="Guardar Cambios">';
    fila+='<img src="imagenes/guardar32.png"/>&nbsp;';   
    fila+='Guardar';
    fila+='</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    fila+='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cancelar">';
    fila+='<img src="imagenes/cancel.png"/>&nbsp;';
    fila+='Cancelar';
    fila+= '</div>';
    fila+='</td></tr>';
    fila+='</tfoot>';
    fila+='</table>';   
    fila+='</div>';
    $('#VentanaModal').html(fila);
    $('#VentanaModal').show();           
    $('#nombreNvo').focus();    
  }
    
  function ActualizarProducto(id_prod)
  {
    if($('#codNvo').val()=='' || $('#nombreNvo').val()=='' || $('#defNvo').val()=='')
    {
      var Mensaje='Debe llenar todos los campos para poder guardar el producto.';  
      CajaDialogo("Alerta", Mensaje);
      return false;
    }
    
    if(parseFloat($('#codNvo').val())<1.0 || parseFloat($('#codNvo').val())>999.99 || isNaN(parseFloat($('#codNvo').val())))
    {
      var Mensaje='Debe introducir un código válido';  
      CajaDialogo("Alerta", Mensaje);
      return false;
    }
    
    $.ajax({
       type:'POST',
       url:'adm_productos/actualizar_producto',
       data:{
             'id_prod':id_prod,
             'cod':encodeURIComponent($('#codNvo').val()),
             'nom':encodeURIComponent($('#nombreNvo').val()),
             'def':encodeURIComponent($('#defNvo').val())
            },
       beforeSend:function(){$("#cargandoModal").show()},
       complete: function(){
                   $("#cargandoModal").hide()},               
       error: function(){
                   var Mensaje='Ha ocurrido un error al Actualizar el Producto.';
                   CajaDialogo('Error', Mensaje)},
       success: function(data){
                var Mensaje='Se han guardado los cambios correctamente.';
                var Botones={Cerrar: function(){
                    listar_productos($('#id_unidad').val());
                     CancelarModal();                       
                     $( this ).dialog( "close" )}};
                CajaDialogo('Guardar', Mensaje, Botones);},
       dataType:'text'});    
  }  
  
  function EliminarProducto(id_prod)
  {
      var Botones={No: function(){$( this ).dialog( "close" )},
              Sí: function(){
       $.ajax({
       type:'POST',
       url:'adm_productos/eliminar_producto',
       data:{
             'id_prod':id_prod
            },
       beforeSend:function(){$("#cargandoModal").show()},
       complete: function(){
                   $("#cargandoModal").hide()},               
       error: function(){
                   var Mensaje='No se pudo Eliminar el Producto. Verifique que no posea Subproductos ni otras Dependencias';
                   CajaDialogo('Error', Mensaje)},
       success: function(data){                         
                var Botones={Cerrar: function(){
                        listar_productos($('#id_unidad').val());
                        $( this ).dialog( "close" )}};
                var Mensaje='Producto Eliminado satisfactoriamente.';
                CajaDialogo('Borrado', Mensaje, Botones);},
       dataType:'text'});
       $( this ).dialog( "close" )}
                  };
      var Mensaje='¿Está Seguro que desea Eliminar el Producto?';      
      CajaDialogo('Pregunta', Mensaje, Botones);
  }

function AgregarSubproducto(id_producto)
{    
    $.ajax({
       type:'POST',
       url:'adm_productos/agregar_subproducto',
       data:{
             'id_producto':id_producto,
             'cod_prod':encodeURIComponent($("#codigoP"+id_producto).html())
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
                    $('#nombreNvo').focus();
                },                   
       dataType:'html'});
    return false;
} 
 
  function ToggleBotonDet()
  {
    if ($("#hideDet").val()=='t')
    {
      $("#hideDet").val('f');      
      $("#imgDet").attr('src', 'imagenes/indeterminado.png');
      $("#spanDet").html('&nbsp;Sub-Producto Indeterminado');
    }
    else
    {
      $("#hideDet").val('t');      
      $("#imgDet").attr('src', 'imagenes/determinado.png');
      $("#spanDet").html('&nbsp;Sub-Producto Determinado');
    }
  }
  
  function ToggleBotonTra()
  {
    if ($("#hideTra").val()=='t')
    {
      $("#hideTra").val('f');      
      $("#imgTra").attr('src', 'imagenes/notramite.png');
      $("#spanTra").html('&nbsp;No es Trámite Administrativo a Terceros');
    }
    else
    {
      $("#hideTra").val('t');      
      $("#imgTra").attr('src', 'imagenes/tramite.png');
      $("#spanTra").html('&nbsp;Trámite Administrativo a Terceros');
    }
  }
  
  function ToggleBotonActivo()
  {
    if ($("#hideActivo").val()=='t')
    {
      $("#hideActivo").val('f');      
      $("#imgActivo").attr('src', 'imagenes/cancel16.png');
      $("#spanActivo").html('&nbsp;Sub-Producto Inactivo');
    }
    else
    {
      $("#hideActivo").val('t');      
      $("#imgActivo").attr('src', 'imagenes/activo16.png');
      $("#spanActivo").html('&nbsp;Sub-Producto Activo');
    }
  }
  
  function ToggleBotonExtra()
  {
    if ($("#hideExtra").val()=='t')
    {
      $("#hideExtra").val('f');      
      $("#imgExtra").attr('src', 'imagenes/lego.png');
      $("#spanExtra").html('&nbsp;Sub-Producto Ordinario');
    }
    else
    {
      $("#hideExtra").val('t');      
      $("#imgExtra").attr('src', 'imagenes/medalla.png');
      $("#spanExtra").html('&nbsp;Sub-Producto Extraordinario');
    }
  }
  
  function GuardarSubproduto(id_prod)
  {     
      if($('#nombreNvoS').val()=='' || $('#defNvoS').val()=='' || $('#UMNvoS').val()=='')
      {
        var Mensaje='Debe llenar todos los campos para guardar el Sub-Producto.';  
        CajaDialogo("Alerta", Mensaje);
        return false;
      }           
      $.ajax({
               type:'POST',
               url:'adm_productos/insertar_subproducto',
               data:{
                     'id_prod':id_prod,
                     'cod_nvo':encodeURIComponent($('#codNvoS').val()),
                     'nombre_nvo':encodeURIComponent($('#nombreNvoS').val()),
                     'def_nvo':encodeURIComponent($('#defNvoS').val()),
                     'um_nvo':encodeURIComponent($('#UMNvoS').val()),
                     'det_nvo':$('#hideDet').val(),
                     'tra_nvo':$('#hideTra').val(),
                     'extra_nvo':$('#hideExtra').val()
                    },
               beforeSend:function(){$("#cargandoModal").show()},
               complete: function(){
                           $("#cargandoModal").hide()},               
               error: function(){
                           var Mensaje='Ha Ocurrido un Error al Intentar Guardar el Sub-Producto.';
                           CajaDialogo('Error', Mensaje)},
               success: function(data){
                        var Mensaje='El Sub-Producto se ha Guardado Correctamente.';
                        var Botones={Cerrar: function(){
                            var url="adm_productos/listar_subproductos";
                            $.post(url,{'id':id_prod}, function(data){
                            $("#subproductos_"+id_prod).html(data);
                                                       },'html');  
                            $("#BotonSubproductos_"+id_prod).html('<img src="imagenes/hayalgo.png"/>&nbsp;&nbsp;Sub-Productos');
                            CancelarModal();
                            $( this ).dialog( "close" )}};
                        CajaDialogo('Guardar', Mensaje, Botones);},
               dataType:'text'});         
       return false;
  }
  
  function EliminarSubproducto(id_subproducto, id_prod)
  {
      var Botones={No: function(){$( this ).dialog( "close" )},
              Sí: function(){
       $.ajax({
       type:'POST',
       url:'adm_productos/eliminar_subproducto',
       data:{
             'id_subproducto':id_subproducto
            },
       beforeSend:function(){$("#cargandoModal").show()},
       complete: function(){
                   $("#cargandoModal").hide()},               
       error: function(){
                   var Mensaje='Ha Ocurrido un Error al Intentar Eliminar el Sub-Producto. Verifique que no posea Alguna Dependencia';
                   CajaDialogo('Error', Mensaje)},
       success: function(data){                         
                var Botones={Cerrar: function(){
                    var url="adm_productos/listar_subproductos";
                    $.post(url,{'id':id_prod}, function(data){                    
                    $("#subproductos_"+id_prod).html(data);
                    if(data.length<640)
                    {
                      $("#BotonSubproductos_"+id_prod).html('&nbsp;&nbsp;Sub-Productos');
                    }
                    },'html'); 
                    $( this ).dialog( "close" )}};
                var Mensaje='Sub-Producto Eliminado correctamente.';
                CajaDialogo('Exito', Mensaje, Botones);},
       dataType:'text'});
       $( this ).dialog( "close" )}
                  };
      var Mensaje='¿Está Seguro que desea Eliminar el Sub-Producto?';      
      CajaDialogo('Pregunta', Mensaje, Botones);
  }
  
  function EditarSubproducto(id_subproducto)
  {
       var url="adm_productos/editar_subproducto";
       $.post(url,{'id_subproducto':id_subproducto}, function(data){
            $('#VentanaModal').html(data);
            $('#VentanaModal').show();           
            $('#nombreNvo').focus();
            },'text');
  }
  
  function ActualizarSubproducto(id_subprod, id_prod)
  {    
    if($('#codSubProd').val()=='' || $('#nombreNvoS').val()=='' || $('#defNvoS').val()=='' || $('#UMNvoS').val()=='')
    {
      var Mensaje='Debe llenar todos los campos para poder guardar el Sub-Producto.';  
      CajaDialogo("Alerta", Mensaje);
      return false;
    }

    if(parseFloat($('#codSubProd').val())<1.0 || parseFloat($('#codSubProd').val())>99.99 || isNaN($('#codSubProd').val()))
    {
      var Mensaje='Debe introducir un código válido';  
      CajaDialogo("Alerta", Mensaje);
      return false;
    }
    
    $.ajax({
       type:'POST',
       url:'adm_productos/actualizar_subproducto',
       data:{
             'id_subprod':id_subprod,
             'pcod':encodeURIComponent($('#codProd').val()),
             'scod':encodeURIComponent($('#codSubProd').val()),
             'nom':encodeURIComponent($('#nombreNvoS').val()),
             'def':encodeURIComponent($('#defNvoS').val()),
             'um':encodeURIComponent($('#UMNvoS').val()),
             'det':$('#hideDet').val(),
             'tra':$('#hideTra').val(),
             'extra':$('#hideExtra').val(),
             'activo':$('#hideActivo').val()
            },
       beforeSend:function(){$("#cargandoModal").show()},
       complete: function(){
                   $("#cargandoModal").hide()},               
       error: function(){
                   var Mensaje='Ha ocurrido un error al Actualizar el Sub-Producto.';
                   CajaDialogo('Error', Mensaje)},
       success: function(data){
                var Mensaje='Se han guardado los cambios correctamente.';
                var Botones={Cerrar: function(){
                     var url="adm_productos/listar_subproductos";
                     $.post(url,{'id':id_prod}, function(data){
                     $("#subproductos_"+id_prod).html(data);
                                                       },'html');                      
                     CancelarModal();                       
                     $( this ).dialog( "close" )}};
                CajaDialogo('Guardar', Mensaje, Botones);},
       dataType:'text'});    
  }  
  
  function AgregarDependencia(id_subproducto)
  {          
    data='<table width="100%">';
    data+='<thead>';
    data+='<tr><th>';            
    data+='Vinculación de Dependencias con el Sub-Producto';         
    data+='</th></tr>';           
    data+='</thead>';            
    data+='<tbody>';
    data+='<tr><td>';
    data+='<input type="hidden" id="id_unidad_depen_'+id_subproducto+'"/>';
    data+='<input type="text" class="NvaDependencia" id="unidad_depen_'+id_subproducto+'" title="Indique la unidad a la que pertenece el Sub-Producto del cual Depende"/><br/>';
    data+='</td></tr>';
    data+='<tr><td>'; 
    data+='</td></tr>'; 
    data+='</tbody>';      
    data+='</table>';
    data+='<div id="listado_subproductos_'+id_subproducto+'">';
    data+='</div>';
    
    $('table#TablaDependencias_'+id_subproducto+' > tfoot').remove();
    $('#detalles_'+id_subproducto).append(data);
    
    $("#unidad_depen_"+id_subproducto).autocomplete({
        minLength:1,
        delay:1,
        source: function(request, response)
                {
                  var url="adm_productos/armar_lista";  //url donde buscará las oficinas
                  $.post(url,{'frase':request.term}, response, 'json');
                },
        select: function( event, ui ) 
                {                    
                  var unidad='';
                  switch (parseInt(ui.item.tipo))
                  {                       
                   case 1:
                     unidad=ui.item.codigo+' - '+ui.item.descripcion;
                     break;
                   case 2:
                     unidad=ui.item.codigo+' - '+ui.item.descripcion;
                     break;
                   default: 
                     unidad=ui.item.codigo+' - '+ui.item.descripcion+' / '+ui.item.oficina;              
                     break;
                  }
                  $("#unidad_depen_"+id_subproducto).val( unidad );                  
                  $("#id_unidad_depen_"+id_subproducto).val(ui.item.id);
                  $.ajax({
                         type:'POST',
                         url:'adm_productos/listar_subproductos_unidad',
                         data:{
                               'id_unidad':ui.item.id,
                               'id_subprod':id_subproducto
                              },
                         beforeSend:function(){$("#cargandoModal").show()},
                         complete: function(){$("#cargandoModal").hide()},   
                         error: function(){
                                     $("#listado_subproductos_"+id_subproducto).html('');
                                     $('#unidad_depen_'+id_subproducto).val(uniMsj);
                                     var Mensaje='Ha ocurrido un error al listar los Sub-Productos.';
                                     CajaDialogo('Error', Mensaje)},
                         success: function(data){
                                   $("#listado_subproductos_"+id_subproducto).html(data);},                         
                         dataType:'html'});                                     
                  return false;
             }
      }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )                
                .data( "item.autocomplete", item )
		.append( "<a>" + ((item.descripcion==undefined)?'Sin coincidencias':item.descripcion) + "<br/><span style='font-size:10px;'>" +((item.oficina==undefined)?'':item.oficina) + "</span></a>" )
		.appendTo( ul );
	  };
    
    uniMsj='-- Escriba Aquí el Nombre de la Unidad donde Pertenece el Sub-Producto del cual Depende --';    
    if ($('#unidad_depen_'+id_subproducto).val()=='')
    {
        $('#unidad_depen_'+id_subproducto).val(uniMsj);        
    }  
    $('#unidad_depen_'+id_subproducto).focusin(function()
        {if ($(this).val()==uniMsj){$(this).val('');}}).focusout(function(){
         if ($(this).val()==''){$("#listado_subproductos_"+id_subproducto).html('');$(this).val(uniMsj);}});
  }
  
  function CrearDependencia(id_subprod,id_subprod_depen)
  {    
    $.ajax({
            type:'POST',
            url:'adm_productos/crear_dependencia',
            data:{
                  'id_subprod':id_subprod,
                  'id_subprod_depen':id_subprod_depen
                 },
            beforeSend:function(){$("#cargandoModal").show()},
            complete: function(){
                        $("#cargandoModal").hide()},               
            error: function(){
                        var Mensaje='Ha Ocurrido un Error al Intentar Crear la Dependencia.';
                        CajaDialogo('Error', Mensaje)},
            success: function(data){
                        var Botones={Cerrar: function(){
                                $('#Imagen_'+id_subprod+'_'+id_subprod_depen).attr('src','imagenes/agregado.png');
                                $('#Imagen_'+id_subprod+'_'+id_subprod_depen).attr('title','Producto Agregado');
                                $('#Imagen_'+id_subprod+'_'+id_subprod_depen).removeAttr('onclick');
                                $("#BotonDependencias_"+id_subprod).html('<img src="imagenes/hayalgo.png"/>&nbsp;&nbsp;Dependencias');
                                $( this ).dialog( "close" )}};
                        var Mensaje='La Dependencia se ha creado correctamente.';
                        CajaDialogo('DepCreada', Mensaje, Botones);},
            dataType:'text'});
  }
  
  function EliminarDependencia(id_dependencia,id_subprod)
  { 
      var Botones={No: function(){$( this ).dialog( "close" )},
                   Sí: function(){
            $.ajax({
            type:'POST',
            url:'adm_productos/eliminar_dependencia',
            data:{
                  'id_dependencia':id_dependencia
                 },
            beforeSend:function(){$("#cargandoModal").show()},
            complete: function(){
                        $("#cargandoModal").hide()},               
            error: function(){
                        var Mensaje='Ha Ocurrido un Error al Intentar Eliminar la Dependencia.';
                        CajaDialogo('Error', Mensaje)},
            success: function(data){                         
                     var Botones={Cerrar: function(){
                         var url="adm_productos/listar_dependencias";
                         $.post(url,{'id':id_subprod}, function(data){
                         $("#detalles_"+id_subprod).html(data);
                         if(data.length<625)
                         {
                            $("#BotonDependencias_"+id_subprod).html('&nbsp;&nbsp;Dependencias');
                         }                            
                         },'html');
                         $( this ).dialog( "close" )}};
                     var Mensaje='La Dependencia se ha Eliminado correctamente.';
                     CajaDialogo('DepBorrada', Mensaje, Botones);},
            dataType:'text'});
            $( this ).dialog( "close" )}
                  };
      var Mensaje='¿Está Seguro que desea Eliminar la Dependencia?';      
      CajaDialogo('Pregunta', Mensaje, Botones);
  }