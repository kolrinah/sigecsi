/************************************************************************/
/* SISTEMA DE PLANIFICACIÓN, PRESUPUESTO Y CONTROL DE GESTIÓN           */
/* DEL PLAN OPERATIVO DEL SERVICIO INTERNO                              */
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

function actualiza()
{        
    var uri;
    uri='reportes_consolidados/consolidar';    
    $.ajax({
          type:'POST',
          url:uri,
          data:{
                'yearpoa':$('#yearpoa').val()
               },
          beforeSend:function(){$("#cargandoModal").show();},
          complete: function(){
                      $("#cargandoModal").hide();},
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Data.';
                      CajaDialogo('Error', Mensaje);},
          success: function(data){                        
                       $('#Planes').html(data);
                       CancelarModal(); },                   
          dataType:'html'});
    return false;
}

function actualizaSelector(selector,unidad)
{
    unidad = unidad || 0;    
    $.ajax({type:'POST',
             url:'supervisor_planes/actualizaSelector', 
            data:{'selector':selector},
        complete:function(){actualiza();} });    
    return true;
}

function presupuestoProyecto(idProyecto)
{
    if (idProyecto===0)
    {
       var Mensaje='Proyecto con Programación Presupuestaria Incompleta.';
       CajaDialogo('Alerta', Mensaje);
       return false;
    }
    
    var uri;
    uri='reportes_consolidados/presupuestoProyecto';    
    $.ajax({
          type:'POST',
          url:uri,
          data:{
                'id_proyecto':idProyecto
               },
          beforeSend:function(){$("#cargandoModal").show();},
          complete: function(){
                      $("#cargandoModal").hide();},
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Data.';
                      CajaDialogo('Error', Mensaje);},
          success: function(data){                        
                       $('#Planes').html(data);
                       CancelarModal(); },                   
          dataType:'html'});
    return false;
}

function metasActividadesProyecto(idProyecto)
{
    if (idProyecto===0)
    {
       var Mensaje='Proyecto con Programación Incompleta.';
       CajaDialogo('Alerta', Mensaje);
       return false;
    }
    
    var uri;
    uri='reportes_consolidados/metasActividadesProyecto';    
    $.ajax({
          type:'POST',
          url:uri,
          data:{
                'id_proyecto':idProyecto
               },
          beforeSend:function(){$("#cargandoModal").show();},
          complete: function(){
                      $("#cargandoModal").hide();},
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Data.';
                      CajaDialogo('Error', Mensaje);},
          success: function(data){                        
                       $('#Planes').html(data);
                       CancelarModal(); },                   
          dataType:'html'});
    return false;
}

function menuReportePlan(id_proyecto)
{
    $.ajax({
          type:'POST',
          url:'reportes_consolidados/menuReportePlan',
          data:{
                'id_proyecto' : id_proyecto
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
     return true;
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