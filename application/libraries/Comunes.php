<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script'); 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *  SISTEMA DE GESTION Y CONTROL DEL SERVICIO INTERNO                *
 *  DESARROLLADO POR: ING.REIZA GARCÍA                               *
 *                    ING.HÉCTOR MARTÍNEZ                            *
 *  PARA:  MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES   *
 *  FECHA: AGOSTO DE 2013                                             *
 *  FRAMEWORK PHP UTILIZADO: CodeIgniter Version 2.1.3               *
 *                           http://ellislab.com/codeigniter         *
 *  TELEFONOS PARA SOPORTE: 0416-9052533 / 0212-5153033              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class Comunes 
{
    // METODO PARA ANALIZAR PRESUPUESTO DE PROYECTOS
    public function analisis_proyecto($aprobado, $planificado, $estatus)
    {      
      $diferencia = $planificado - $aprobado;
      switch (round($diferencia,2))
      {
        case 0: $r['img'] = 'activo16.png';
                $r['msj'] = "Planificación presupuestaria correcta";
                $r['ok']  = true;
                break;
        case $diferencia < 0 : $r['img'] = 'advertencia.png';
                               $r['msj'] = 'Faltan Bs.'.number_format(abs($diferencia), 2,',','.').
                                           ' por planificar';
                               $r['ok']  = false;
                               break;
        case $planificado:     $r['img'] = 'activo16.png';
                               $r['msj'] = "Planificación presupuestaria correcta";
                               $r['ok']  = true;
                               break;            
        case $diferencia > 0 : $r['img'] = 'advertencia.png';
                               $r['msj'] = 'La planificación excede en Bs.'.
                                            number_format($diferencia, 2,',','.').
                                           ' al presupuesto aprobado';
                               $r['ok']  = false;
                               break;
      }      
      if ($estatus == 0)
      {
          $r['img'] = 'cancel16.png';
          $r['msj'] = 'Planificación presupuestaria incompleta';
          $r['ok']  = false;
      }      
      return $r;
    }

    // CONSTRUYE OPCIONES DE CAJAS SELECT A PARTIR DE UNA MATRIZ
    public function construye_opciones($opciones, $seleccionada=0)  
    {    
      $combo='';
      foreach ($opciones as $value => $text)
      {
        if ($value == $seleccionada)
        {
          $combo.='<option value="'.$value.'" selected="selected">'.$text.'</option>';
        }
        else
        {
          $combo.='<option value="'.$value.'">'.$text.'</option>';
        }
      }
      return $combo;
    }     
    
    // METODO PARA CALCULAR DIFERENCIA ENTRE FECHAS
    public function diferenciaEntreFechas($fecha_principal, $fecha_secundaria, $obtener = 'SEGUNDOS', $redondear = false)
    {
        $f0 = strtotime($fecha_principal);
        $f1 = strtotime($fecha_secundaria);
        if ($f0 < $f1) { $tmp = $f1; $f1 = $f0; $f0 = $tmp; }
        $resultado = ($f0 - $f1);
        switch ($obtener) 
        {
            default: break;
            case "MINUTOS"   :   $resultado = $resultado / 60;   break;
            case "HORAS"     :   $resultado = $resultado / 60 / 60;   break;
            case "DIAS"      :   $resultado = $resultado / 60 / 60 / 24;   break;
            case "SEMANAS"   :   $resultado = $resultado / 60 / 60 / 24 / 7;   break;
            case "MESES"     :   $resultado = $resultado / 60 / 60 / 24 / 30;   break;
        }
        if($redondear) $resultado = round($resultado);
        return $resultado;
    }
    
    // ESTE METODO OBTIENE LAS CABECERAS DE LAS TABLAS DE GANTT
    public function cabecetabla()
    {    
      $tabla='<thead><tr><th width="30px">Nº</th>';
      $tabla.='<th colspan="2">Acción Específica / Actividad</th>';
      $tabla.='<th width="40px" title="Enero">E</th>';
      $tabla.='<th width="40px" title="Febrero">F</th>';
      $tabla.='<th width="40px" title="Marzo">M</th>';
      $tabla.='<th width="40px" title="Abril">A</th>';
      $tabla.='<th width="40px" title="Mayo">M</th>';
      $tabla.='<th width="40px" title="Junio">J</th>';
      $tabla.='<th width="40px" title="Julio">J</th>';
      $tabla.='<th width="40px" title="Agosto">A</th>';
      $tabla.='<th width="40px" title="Septiembre">S</th>';
      $tabla.='<th width="40px" title="Octubre">O</th>';
      $tabla.='<th width="40px" title="Noviembre">N</th>';
      $tabla.='<th width="41px" title="Diciembre">D</th>';
      $tabla.='<th width="40px"></th>';     
      $tabla.='</tr></thead>';
      $tabla.='<tfoot><tr>';
      $tabla.='<td >Nº</td>';
      $tabla.='<td colspan="2">Acción Específica / Actividad</td>';
      $tabla.='<td title="Enero">E</td>';
      $tabla.='<td title="Febrero">F</td>';
      $tabla.='<td title="Marzo">M</td>';
      $tabla.='<td title="Abril">A</td>';
      $tabla.='<td title="Mayo">M</td>';
      $tabla.='<td title="Junio">J</td>';
      $tabla.='<td title="Julio">J</td>';
      $tabla.='<td title="Agosto">A</td>';
      $tabla.='<td title="Septiembre">S</td>';
      $tabla.='<td title="Octubre">O</td>';
      $tabla.='<td title="Noviembre">N</td>';
      $tabla.='<td title="Diciembre">D</td>';    
      $tabla.='<td></td>';    
      $tabla.='</tr></tfoot>';
      return $tabla;
    }
    
    // ESTE METODO OBTIENE LAS CABECERAS DE LAS TABLAS TOTALIZADORAS
    public function cabecetablatotal()
    {    
      $tabla='<thead><tr><th width="30px">Nº</th>';
      $tabla.='<th colspan="2"> Actividad</th>';
      $tabla.='<th width="40px" title="Enero">E</th>';
      $tabla.='<th width="40px" title="Febrero">F</th>';
      $tabla.='<th width="40px" title="Marzo">M</th>';
      $tabla.='<th width="40px" title="Abril">A</th>';
      $tabla.='<th width="40px" title="Mayo">M</th>';
      $tabla.='<th width="40px" title="Junio">J</th>';
      $tabla.='<th width="40px" title="Julio">J</th>';
      $tabla.='<th width="40px" title="Agosto">A</th>';
      $tabla.='<th width="40px" title="Septiembre">S</th>';
      $tabla.='<th width="40px" title="Octubre">O</th>';
      $tabla.='<th width="40px" title="Noviembre">N</th>';
      $tabla.='<th width="41px" title="Diciembre">D</th>';
      $tabla.='<th width="40px">TOTAL</th>';     
      $tabla.='</tr></thead>';
      $tabla.='<tfoot><tr>';
      $tabla.='<td >Nº</td>';
      $tabla.='<td colspan="2"> Actividad</td>';
      $tabla.='<td title="Enero">E</td>';
      $tabla.='<td title="Febrero">F</td>';
      $tabla.='<td title="Marzo">M</td>';
      $tabla.='<td title="Abril">A</td>';
      $tabla.='<td title="Mayo">M</td>';
      $tabla.='<td title="Junio">J</td>';
      $tabla.='<td title="Julio">J</td>';
      $tabla.='<td title="Agosto">A</td>';
      $tabla.='<td title="Septiembre">S</td>';
      $tabla.='<td title="Octubre">O</td>';
      $tabla.='<td title="Noviembre">N</td>';
      $tabla.='<td title="Diciembre">D</td>';    
      $tabla.='<td>TOTAL</td>';    
      $tabla.='</tr></tfoot>';
      return $tabla;
    }      
    
    // ESTA FUNCION OBTIENE EL DIV DE LA BARRA DEL GANTT
    public function mini_gantt($fecha_ini, $fecha_fin, $px_mes)  // $px_mes='Ancho de Pixeles por Mes'
    {
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      // OBTENEMOS LA CANTIDAD DE DIAS DE DURACION DE LA ACTIVIDAD EN DIAS
      $duracion=$this->diferenciaEntreFechas($fecha_ini, $fecha_fin, 'DIAS', true);
  
      // PROCESAMOS FECHA INICIAL
      $fechai=strtotime($fecha_ini); // Convertimos la fecha a UNIX
      $fechai=getdate($fechai); // Convertimos la fecha a matriz asociativa
      // PROCESAMOS FECHA FINAL
      $fechaf=strtotime($fecha_fin); // Convertimos la fecha a UNIX
      $fechaf=getdate($fechaf); // Convertimos la fecha a matriz asociativa
      
      $dia_i=$fechai['yday']; // Obtenemos el día del año para la fecha inicial
      $dia_f=$fechaf['yday']; // Obtenemos el día del año para la fecha final   
         
      // LLEVAMOS LAS FECHAS A LA ESCALA DEL LIENZO DE TRABAJO        
      $bloque=intval(round($dia_i*$px_mes*$fechai['mon']/($this->dias_mes($fechai['mon']))));
      $duracion=($duracion>365)?365:
              intval(round($dia_f*$px_mes*$fechaf['mon']/($this->dias_mes($fechaf['mon']))));
      $duracion=$duracion-$bloque+1;
      
      $duracion=($duracion<1)?1:$duracion;
      $bloque=($bloque<2)?0:$bloque;
      $alto='15px';
         
      $html='<div style="float:left; width:'.$bloque.'px; height:'.$alto.'; display:inline">&nbsp';
      $html.='</div>';    
      $html.='<div class="Gantt" style="float:left; width:'.$duracion.'px; height:'.$alto.'; display:inline">&nbsp';
      $html.='</div>';
      $html.='<div class="clear"></div>';    
      
      return $html;
    }    
    
    // ESTA FUNCION DEVUELVE LA CANTIDAD DE DIAS TRANSCURRIDOS 
    // DESDE EL COMIENZO DEL AÑO AL ULTIMO DÍA DEL MES CONSULTADO
    public function dias_mes($mes)
    {
      $dias=0; 
      switch ($mes) 
      {       
        case 1:
               $dias=31;
               break;
        case 2:
               $dias=59;
               break;
        case 3:
               $dias=90;
               break;
        case 4:
               $dias=120;
               break;
        case 5:
               $dias=151;
               break;
        case 6:
               $dias=181;
               break;
        case 7:
               $dias=212;
               break;         
        case 8:
               $dias=243;
               break;         
        case 9:
               $dias=273;
               break;         
        case 10:
               $dias=304;
               break;         
        case 11:
               $dias=334;
               break;   
         default:
               $dias=365;
               break;
      }
      return $dias;
    }    
}

?>
