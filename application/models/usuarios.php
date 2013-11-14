<?php
class Usuarios extends CI_Model
{
    function verificar_acceso($usuario,$clave)
    {
      // $this->db->select();
      // $this->db->from('a_usuarios');
        $this->db->where('a_usuarios.usuario', $usuario);
        $this->db->where('a_usuarios.clave', $clave);
        $this->db->where('a_usuarios.activo', 't');        
        $this->db->join('e_estructura', 'a_usuarios.id_estructura=e_estructura.id_estructura');     
        $query=$this->db->get('a_usuarios');
        if($query->num_rows()==1){return $query->row_array();}
        else {return false;}
     }
     
     function obten_estructura($id_estructura)
     {
        $this->db->where('id_estructura', $id_estructura);
        $this->db->where('activo', 't');
        $query=$this->db->get('e_estructura');
        if($query->num_rows()==1){return $query->row_array();}
        else {return false;}
     }
     
     function cambiar_clave($id_user, $actual, $nueva)
     {
       $datos=array(
                'clave'=> $nueva
                );        
       $this->db->where('id_usuario', $id_user);
       $this->db->where('clave', $actual);
       $this->db->update('a_usuarios', $datos);
       if ($this->db->affected_rows()>0){return true;}
       else {return false;}
     }
     
     function listar_usuarios($id_estructura)
     {
        $sql="select u.id_estructura, u.id_usuario, u.nombre, u.apellido, u.id_nivel, u.activo, u.correo, u.administrador, e.codigo, e.descripcion, e.id_tipo_estructura from a_usuarios u
              join
              (-- OBTENEMOS OFICINAS
                select id_estructura, codigo, descripcion, id_tipo_estructura, id_sup, servicio_interno, activo, fecha_tope from e_estructura
                where (id_sup=$id_estructura or id_estructura=$id_estructura) and servicio_interno=true and activo=true
                union
               -- OBTENEMOS DIRECCIONES DE LINEA
               select d.id_estructura, d.codigo, d.descripcion, d.id_tipo_estructura, d.id_sup, d.servicio_interno, d.activo, d.fecha_tope from e_estructura d
               join (select id_estructura, codigo, descripcion, id_tipo_estructura, id_sup, servicio_interno, activo, fecha_tope from e_estructura where id_sup=$id_estructura or id_estructura=$id_estructura ) o on o.id_estructura=d.id_sup
               where d.servicio_interno=true and d.activo=true
               union
               -- OBTENEMOS AREAS
               select a.id_estructura, a.codigo, a.descripcion, a.id_tipo_estructura, a.id_sup, a.servicio_interno, a.activo, a.fecha_tope from e_estructura a
               join (select d.id_estructura, d.codigo, d.descripcion, d.id_tipo_estructura, d.id_sup, d.servicio_interno, d.activo from e_estructura d
               join (select * from e_estructura where id_sup=$id_estructura or id_estructura=$id_estructura ) o on o.id_estructura=d.id_sup) m on m.id_estructura= a.id_sup
               where a.servicio_interno=true and a.activo=true
               order by 1) e using (id_estructura)
               order by 1, 3";
        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
     }
     
     function obtener_usuario($id_usuario)
     {        
        $seleccion='a_usuarios.id_usuario, a_usuarios.id_estructura, a_usuarios.id_nivel';
        $seleccion.=', a_usuarios.nombre, a_usuarios.apellido, a_usuarios.id_nivel';
        $seleccion.=', a_usuarios.correo, a_usuarios.administrador, a_usuarios.activo';
        $seleccion.=', a_usuarios.cedula, e_estructura.codigo, e_estructura.descripcion';
        $seleccion.=', e_estructura.id_tipo_estructura';
          
        $this->db->select($seleccion);
        $this->db->where('id_usuario', $id_usuario);
        $this->db->join('e_estructura', 'a_usuarios.id_estructura=e_estructura.id_estructura');
        $query=$this->db->get('a_usuarios');
        if($query->num_rows()==1){return $query->row_array();}
        else {return false;}
     }
     
     function obtener_jefe_inmediato($id_estructura)
     {
         $sql="select u.* from a_usuarios u
               join (-- OBETENEMOS LA ESTRUCTURA SUPERIOR Y SU NIVEL
                     select i.id_sup as id_estructura, s.id_tipo_estructura as id_nivel from e_estructura i
                     join e_estructura s on (i.id_sup=s.id_estructura)
                     where i.id_estructura=$id_estructura ) h using (id_estructura, id_nivel)
               where u.id_estructura!=$id_estructura and u.activo=true;";
         
         $query = $this->db->query($sql);
         return $query;
     }
     
     function obtener_jefe_unidad($id_estructura)
     {
         $sql="select * from a_usuarios u
               join e_estructura e using (id_estructura)
               where e.id_estructura=$id_estructura and u.activo=true and e.id_tipo_estructura=u.id_nivel;";
         
         $query = $this->db->query($sql);
         return $query;         
     }
}
?>