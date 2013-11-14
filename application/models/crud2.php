<?php
class Crud2 extends CI_Model
{   
   function listar_tablas()
   {
       $DB2 = $this->load->database('SIGESP', TRUE); 
       $tables = $DB2->list_tables();
       return $tables;
   }
   
   function listar_campos($tabla)
   {
       $DB2 = $this->load->database('SIGESP', TRUE); 
       $campos = $DB2->field_data($tabla);
       return $campos;
   }
   
   // EJECUTA COMANDOS SQL
     function ventana_sql($sql)
     { 
        $DB2 = $this->load->database('SIGESP', TRUE); 
        $query = $DB2->query($sql);
        
        if (is_bool($query))return $query;
                
        return $query->result_array();        
     }
}
?>