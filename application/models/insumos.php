<?php
class Insumos extends CI_Model
{
// LISTAR REQUERIMIENTO DE INSUMOS A PARTIR DEL CODIGO DE ESTRUCTURA Y AÑO DEL POA
   function listar_requerimiento_insumos($estructuras,$yearpoa) 
   {
        $sql = "select * from
                (
                  select * from ri_requerimiento_insumos                                    
                  where yearpoa=$yearpoa
                  order by id_estructura, partida_generica    
                ) p
                join e_estructura using (id_estructura)
                join ri_tipo_insumos g using (partida_generica)
                $estructuras
                order by id_estructura, partida_generica, denart;";               

        $query = $this->db->query($sql);
        return $query;       
   }

   // OBTENER FICHA DE REQUERIMIENTO DE INSUMO A PARTIR DE SU id    
     function obtener_ri($id_requerimiento_insumo)
     {                 
         $this->db->join('e_estructura','e_estructura.id_estructura=ri_requerimiento_insumos.id_estructura');
         $this->db->join('ri_tipo_insumos','ri_tipo_insumos.partida_generica=ri_requerimiento_insumos.partida_generica');
         $query = $this->db->get_where('ri_requerimiento_insumos', array('id_requerimiento_insumo' => $id_requerimiento_insumo));         

        if($query->num_rows()>0)
        {
           return $query->row();
        }
        else {return false;}
     }
     
     // CONSULTA PARA OBTENER LA LISTA DE INSUMOS
  /*   function listar_articulos($frase, $tipo_insumo)
     {  
       $sql="select * from vista_sigesp
             where (translate(upper(denart),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             like translate(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU')
             or translate(upper(codart),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             like translate(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') )
             and SUBSTRING(spg_cuenta,1,3) = '$tipo_insumo'
             order by denart asc";

        $query =$this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();          
        }
        else {return array('No hubo coincidencias');}
     } */
     
     function listar_articulos($frase, $tipo_insumo)
     { 
       $DB2 = $this->load->database('SIGESP', TRUE);  
       $sql="select * from articulos_servicios
             where (translate(upper(denart),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             like translate(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU')
             or translate(upper(codart),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             like translate(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') )
             and SUBSTRING(spg_cuenta,1,3) = '$tipo_insumo'
             order by denart asc";

        $query = $DB2->query($sql);
        if($query->num_rows()>0)
        {
           $org = $query->result_array();
           // CONVERSION A MAYUSCULAS           
           $nvo=array();
           foreach ($org as $a)
           {
             $nvo[]=array(
                  'codart' => trim($a['codart']),
                  'denart' => trim(mb_convert_case(utf8_encode($a['denart']),MB_CASE_UPPER,'UTF-8')),
                  'feccreart' => trim($a['feccreart']),
                  'spg_cuenta' => trim($a['spg_cuenta']),
                  'codtipart' => trim($a['codtipart']),
                  'dentipart' => trim(mb_convert_case(utf8_encode($a['dentipart']),MB_CASE_UPPER,'UTF-8')),
                  'codunimed' => trim($a['codunimed']),
                  'denunimed' => trim(mb_convert_case(utf8_encode($a['denunimed']),MB_CASE_UPPER,'UTF-8')),
                  'unidad' => trim(mb_convert_case(utf8_encode($a['unidad']),MB_CASE_UPPER,'UTF-8')),
                  'costo' => trim($a['costo'])
              );                           
           }           
           return $nvo;
        }
        else {return array('No hubo coincidencias');}
     }    

     
     // CONSULTA PARA OBTENER EL CONSOLIDADO DE INSUMOS POR AÑO     
     function consolidar_requerimiento_insumos($yearpoa)
     {
         $sql="select partida_generica, p.tipo_insumo, spg_cuenta,codart, denart, 
                      sum(requerido) as requerido, sum(existencia) as existencia, 
                      denunimed from ri_requerimiento_insumos
               join ri_tipo_insumos p using (partida_generica)
               where yearpoa=$yearpoa
               group by partida_generica, p.tipo_insumo, spg_cuenta,codart, denunimed, denart
               order by partida_generica, p.tipo_insumo, spg_cuenta,codart, denunimed, denart; ";
            
         $query = $this->db->query($sql);
         return $query; 
     }
}
?>