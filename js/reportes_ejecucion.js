/************************************************************************/
/* SISTEMA DE PLANIFICACIÓN, PRESUPUESTO Y CONTROL DE GESTIÓN           */
/* DEL PLAN OPERATIVO DEL SERVICIO INTERNO                              */
/* DESARROLLADO POR: ING.REIZA GARCÍA                                   */
/*                   ING.HÉCTOR MARTÍNEZ                                */
/* PARA EL MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES      */
/* OCTUBRE DE 2013                                                        */
/* TELEFONOS DE CONTACTO PARA SOPORTE: 0416-9052533 / 0212-5153033      */
/************************************************************************/

$(document).ready(function()
{     
   
});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES

function actualiza()
{        
    var uri;
    uri='reportes_ejecucion/listar_proyectos';
    
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
       url:'reportes_ejecucion/revisar_ejecucion',
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

function exportarPDF()
{
   var yearpoa = $('#yearpoa').val();
  // var specs='toolbar=yes, titlebar=yes, status=yes, menubar=no, location=yes, fullscreen=yes, scrollbars=yes'; 
   window.open($('#base_url').val()+'reportes_consolidados/exportarPDF/'+yearpoa,'','',false);
   //window.open(URL,name,specs,replace);
}

function exportarXLS()
{     
   $('#datos_a_enviar').val($('#Planes').html());
   $('#formExportar').submit();     
}