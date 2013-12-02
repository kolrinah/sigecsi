<?php
class Proyectos extends CI_Model
{
// LISTAR PROYECTOS A PARTIR DEL CODIGO DE ESTRUCTURA Y AÑO DEL POA
   function listar_proyectos($estructuras, $yearpoa) 
   {
        $sql = "select * from (select * from p_proyectos 
                where yearpoa=$yearpoa) p
                join e_estructura using (id_estructura)
                left join (-- COSTO TOTAL PROYECTO
                           select id_proyecto, sum(cantidad*costo_unitario) as total from p_proyectos
                           join p_acciones using (id_proyecto)
                           join p_actividades using (id_accion)
                           join p_presupuesto_actividad using (id_actividad)
                           group by id_proyecto) m using (id_proyecto)
                left join (-- MONTO EJECUTADO
			   SELECT id_proyecto, sum(compromiso) as ejecutado
			   FROM p_ejecucion_presupuestaria   
			   JOIN p_actividades using(id_actividad)
		           JOIN p_acciones using(id_accion)             
			   JOIN p_proyectos using(id_proyecto)
			   GROUP BY id_proyecto) e using(id_proyecto)
                left join (-- METAS FISICAS
                           select id_proyecto, 
                                  sum(cantidad_act) as meta_planificada,
                                  sum(cantidad_meta) as meta_alcanzada from p_proyectos
                           join p_acciones using (id_proyecto)
                           join p_actividades using (id_accion)
                           left join p_ejecucion_fisica using (id_actividad)
                           group by id_proyecto) g using(id_proyecto) 
                left join (-- ESTATUS DE PROYECTO
			   select  id_proyecto,
			           bit_and(case when (coalesce(cantidad,0)*coalesce(costo_unitario,0))>0 then 1
				           else 0 
					   end) as estatus
			   from p_proyectos
			   left join p_acciones using (id_proyecto)
			   left join p_actividades using (id_accion)
			   left join p_presupuesto_actividad using (id_actividad)
			   where yearpoa=$yearpoa
			   group by 1 ) s using (id_proyecto)
		$estructuras
                order by id_estructura, id_proyecto";        

        $query = $this->db->query($sql);
        return $query;
   }

   // OBTENER FICHA DE UN PROYECTO A PARTIR DE SU id    
     function obtener_proyecto($id_proyecto)
     {  
       /*$this->db->join('a_usuarios','a_usuarios.id_usuario=p_proyectos.id_responsable');
         $this->db->join('a_niveles','a_niveles.id_nivel=a_usuarios.id_nivel');
         $this->db->join('e_estructura','e_estructura.id_estructura=p_proyectos.id_estructura');
         $query = $this->db->get_where('p_proyectos', array('id_proyecto' => $id_proyecto)); */
        $sql="SELECT p.*, u.*, n.*, e.*, 
              CASE WHEN e.id_tipo_estructura=4 THEN ss.descripcion
	           WHEN e.id_tipo_estructura=3 THEN s.descripcion	    
                   ELSE ''
              END as superior
              FROM p_proyectos p
              JOIN a_usuarios u on id_responsable=id_usuario
              JOIN a_niveles n using (id_nivel)
              JOIN e_estructura e on (p.id_estructura=e.id_estructura)
              JOIN e_estructura s on (e.id_sup=s.id_estructura)
              JOIN e_estructura ss on (s.id_sup=ss.id_estructura)
              WHERE id_proyecto=$id_proyecto";

        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->row();
        }
        else {return false;}
     }
     
     // OBTENER PROYECTO A PARTIR DE UNA ACTIVIDAD DADA
     function getProyectoByIdActividad($id_actividad)
     {
        $sql = "-- PRESUPUESTO TOTAL POR PROYECTO
                select id_proyecto, monto_aprobado, sum(cantidad*costo_unitario) as monto_planificado 
                from p_proyectos
                join p_acciones using (id_proyecto)
                join p_actividades using (id_accion)
                left join p_presupuesto_actividad using (id_actividad)
                join (-- OBTENEMOS PROYECTO POR id_actividad ASOCIADA
                      select min(id_proyecto) as id_proyecto  
                      from p_proyectos
                      join p_acciones using (id_proyecto)
                      join p_actividades using (id_accion)
                      left join p_presupuesto_actividad using (id_actividad)
                      where id_actividad = $id_actividad) p using (id_proyecto)
                group by id_proyecto, monto_aprobado";        

        $query = $this->db->query($sql);
        return $query;         
     }
     
    // LISTAR ACCIONES ESPECIFICAS DE UN PROYECTO A PARTIR DE SU id
     function listar_acciones($id_proyecto)
     {           
        $sql="select * from p_acciones 
              join p_proyectos using(id_proyecto) 
              left join (-- COSTO TOTAL ACCION
			 select id_accion, sum(cantidad*costo_unitario) as total from p_presupuesto_actividad
			 join p_actividades using (id_actividad)
			 join p_acciones using (id_accion)
		         group by id_accion
              ) p using (id_accion)
              where id_proyecto=$id_proyecto
              order by cast(codigo_ae as integer) ";
        $query = $this->db->query($sql);
        
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
     }
     
   // OBTENER ACCION ESPECIFICA DE UN PROYECTO A PARTIR DE SU id    
     function obtener_accion($id_accion)
     {  
        $this->db->join('a_usuarios','a_usuarios.id_usuario=p_proyectos.id_responsable');
        $this->db->join('e_estructura','e_estructura.id_estructura=p_proyectos.id_estructura');
        $this->db->join('p_acciones','p_acciones.id_proyecto=p_proyectos.id_proyecto');         
        $query = $this->db->get_where('p_proyectos', array('id_accion' => $id_accion));         

        if($query->num_rows()>0)
        {
           return $query->row();
        }
        else {return false;}
     }
     
     // OBTENER ACTIVIDAD A PARTIR DE SU id    
     function obtener_actividad($id_actividad)
     {  
        $sql="select * from p_actividades
              left join p_ejecucion_fisica using(id_actividad)
              join p_fuentes using(id_fuente)
              join p_acciones using(id_accion)
              join p_proyectos using(id_proyecto) 
              join e_estructura using(id_estructura)
              join a_usuarios on p_actividades.id_responsable=a_usuarios.id_usuario
              where id_actividad=$id_actividad";
        $query = $this->db->query($sql);
        
        if($query->num_rows()>0)
        {
           return $query->row();
        }
        else {return false;}
     }

    // LISTAR ACTIVIDADES DE UNA ACCION ESPECIFICA DE UN PROYECTO
     function listar_actividades($id_accion)
     {           
        $sql="select * from p_actividades
              join p_acciones using(id_accion)
              join p_proyectos using(id_proyecto) 
              left join (-- COSTO TOTAL ACTIVIDAD
			select id_actividad, sum(cantidad*costo_unitario) as total from p_presupuesto_actividad
			group by id_actividad
			) p using (id_actividad)
              where id_accion=$id_accion
              order by cast(codigo_act as integer) ";
        $query = $this->db->query($sql);
        
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
     }     
     
     // LISTAR PLANIFICACION DE ACTIVIDADES DE UN PROYECTO EN GANTT
     function gantt_proyecto($id_proyecto)
     {           
        $sql="select * from p_acciones
	     join p_proyectos using(id_proyecto)
	     join e_estructura using(id_estructura)
	     left join p_actividades using(id_accion)
             left join p_ejecucion_fisica using(id_actividad)
             join p_fuentes using(id_fuente)
	     left join (-- COSTO TOTAL ACTIVIDAD
			select id_actividad, sum(cantidad*costo_unitario) as total from p_presupuesto_actividad
			group by id_actividad
			) p using (id_actividad)
	     left join a_usuarios on p_actividades.id_responsable=a_usuarios.id_usuario
	     where p_proyectos.id_proyecto=$id_proyecto             
             order by cast(p_acciones.codigo_ae as integer), cast(codigo_act as integer)";
        $query = $this->db->query($sql);
        
        if($query->num_rows()>0)
        {
           return $query->result();
        }
        else {return false;}
     } 
     
     // LISTAR PLANIFICACION DE ACTIVIDADES DE UN PROYECTO EN TABLA TOTALIZADORA
     function tabla_proyecto($id_proyecto)
     {           
        $sql="-- LISTAR PLANIFICACION DE ACTIVIDADES DE UN PROYECTO EN TABLA        
              select *, case when date_part('month',fecha_fin)='01' then cantidad_act else 0 end as ene,
              	        case when date_part('month',fecha_fin)='02' then cantidad_act else 0 end as feb,
              	        case when date_part('month',fecha_fin)='03' then cantidad_act else 0 end as mar,
              	        case when date_part('month',fecha_fin)='04' then cantidad_act else 0 end as abr,
              	        case when date_part('month',fecha_fin)='05' then cantidad_act else 0 end as may,
              	        case when date_part('month',fecha_fin)='06' then cantidad_act else 0 end as jun,
              	        case when date_part('month',fecha_fin)='07' then cantidad_act else 0 end as jul,
              	        case when date_part('month',fecha_fin)='08' then cantidad_act else 0 end as ago,
              	        case when date_part('month',fecha_fin)='09' then cantidad_act else 0 end as sep,
              	        case when date_part('month',fecha_fin)='10' then cantidad_act else 0 end as oct,
              	        case when date_part('month',fecha_fin)='11' then cantidad_act else 0 end as nov,
              	        case when date_part('month',fecha_fin)='12' then cantidad_act else 0 end as dic
              from p_acciones
              	     join p_proyectos using(id_proyecto)
              	     join p_actividades using(id_accion)	     
              	     where p_proyectos.id_proyecto=$id_proyecto        
                           order by cast(p_acciones.codigo_ae as integer), cast(codigo_act as integer)";
        $query = $this->db->query($sql);
        
        if($query->num_rows()>0)
        {
           return $query->result();
        }
        else {return false;}
     }      

     // LISTAR PRESUPUESTO DE UNA ACTIVIDAD DE UN PROYECTO
     function listar_presupuesto($id_actividad)
     {
         $sql="select * from p_presupuesto_actividad
              join z_partidas_presupuestarias using (id_partida)
              where id_actividad=$id_actividad
              order by id_partida";
         $query = $this->db->query($sql);
         if($query->num_rows()>0)
         {
           return $query->result();
         }
         else {return false;}
     }
     
     // LISTAR LAS PARTIDAS PRESUPUESTARIAS PARA AUTOCOMPLETADO SEGUN FRASE
     function listado_partidas($frase)
     {
       $sql="SELECT id_partida, denominacion
             FROM z_partidas_presupuestarias
             WHERE (TRANSLATE(upper(denominacion),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             LIKE TRANSLATE(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU')
             OR id_partida LIKE '$frase%' )
             AND id_partida NOT LIKE '%.00.00'    
             ORDER BY id_partida ASC;";

        $query = $this->db->query($sql);
        return $query->result_array();
     }
     
     // OBTENER PRESUPUESTO A PARTIR DE SU id    
     function obtener_presupuesto($id_presupuesto)
     {  
        $sql="select * from p_presupuesto_actividad
              join z_partidas_presupuestarias using(id_partida)
              where id_presupuesto=$id_presupuesto";
        
        $query = $this->db->query($sql);
        
        if($query->num_rows()>0)
        {
           return $query->row();
        }
        else {return false;}
     }
 
     // PRESUPUESTO DE PROYECTO POR PARTIDAS
     function obtener_presupuesto_proyecto_x_partidas($id_proyecto)
     {
         $sql="SELECT id_proyecto, id_partida, denominacion,
                      sum(monto) as monto
               FROM (-- PARTIDA SUB-ESPECIFICA
                     SELECT id_proyecto, id_partida, denominacion, 
                            sum(cantidad*costo_unitario) as monto 
                     FROM p_proyectos
                     JOIN p_acciones using (id_proyecto)
                     JOIN p_actividades using (id_accion)
                     JOIN p_presupuesto_actividad using (id_actividad)
                     JOIN z_partidas_presupuestarias using (id_partida)
                     WHERE id_partida != '4.00.00.00.00'
                     AND id_proyecto=$id_proyecto
                     GROUP BY id_proyecto, id_partida, denominacion                     
                     UNION
                     -- PARTIDA ESPECIFICA
                     SELECT id_proyecto, b.id_partida as id_partida, b.denominacion,
                            sum(cantidad*costo_unitario) as monto 
                     FROM p_proyectos
                     JOIN p_acciones using (id_proyecto)
                     JOIN p_actividades using (id_accion)
                     JOIN p_presupuesto_actividad using (id_actividad)
                     JOIN z_partidas_presupuestarias a using (id_partida)
                     JOIN z_partidas_presupuestarias b on a.id_sup = b.id_partida
                     WHERE b.id_partida != '4.00.00.00.00'
                     AND id_proyecto=$id_proyecto
                     GROUP BY id_proyecto, b.id_partida, b.denominacion
                     UNION
                     -- PARTIDA GENERICA
                     SELECT id_proyecto, c.id_partida as id_partida, c.denominacion, 
                            sum(cantidad*costo_unitario) as monto 
                     FROM p_proyectos
                     JOIN p_acciones using (id_proyecto)
                     JOIN p_actividades using (id_accion)
                     JOIN p_presupuesto_actividad using (id_actividad)
                     JOIN z_partidas_presupuestarias a using (id_partida)
                     JOIN z_partidas_presupuestarias b on a.id_sup = b.id_partida
                     JOIN z_partidas_presupuestarias c on b.id_sup = c.id_partida
                     WHERE c.id_partida != '4.00.00.00.00'
                     AND id_proyecto=$id_proyecto
                     GROUP BY id_proyecto, c.id_partida, c.denominacion
                     UNION
                     -- PARTIDA 
                     SELECT id_proyecto, d.id_partida as id_partida, d.denominacion, 
                            sum(cantidad*costo_unitario) as monto 
                     FROM p_proyectos
                     JOIN p_acciones using (id_proyecto)
                     JOIN p_actividades using (id_accion)
                     JOIN p_presupuesto_actividad using (id_actividad)
                     JOIN z_partidas_presupuestarias a using (id_partida)
                     JOIN z_partidas_presupuestarias b on a.id_sup = b.id_partida
                     JOIN z_partidas_presupuestarias c on b.id_sup = c.id_partida
                     JOIN z_partidas_presupuestarias d on c.id_sup = d.id_partida
                     WHERE d.id_partida != '4.00.00.00.00'
                     AND id_proyecto=$id_proyecto
                     GROUP BY id_proyecto, d.id_partida, d.denominacion                     
                     UNION
                     --TOTALES
                     SELECT id_proyecto, 'TOTAL' as id_partida, e.denominacion, 
                            sum(cantidad*costo_unitario) as monto FROM p_proyectos
                     JOIN p_acciones using (id_proyecto)
                     JOIN p_actividades using (id_accion)
                     JOIN p_presupuesto_actividad using (id_actividad)
                     JOIN z_partidas_presupuestarias a using (id_partida)
                     JOIN z_partidas_presupuestarias b on a.id_sup = b.id_partida
                     JOIN z_partidas_presupuestarias c on b.id_sup = c.id_partida
                     JOIN z_partidas_presupuestarias d on c.id_sup = d.id_partida
                     JOIN z_partidas_presupuestarias e on d.id_sup = e.id_partida
                     WHERE id_proyecto=$id_proyecto
                     GROUP BY 1, 2, 3) q
               GROUP BY 1, 2, 3
               ORDER BY 1";         
         
         $query = $this->db->query($sql);
         return $query;
     }
     
     // PRESUPUESTO DE AÑO DE POA POR PARTIDAS
     function obtener_presupuesto_yearpoa_x_partidas($yearpoa)
     {
         $sql="SELECT yearpoa, id_partida, denominacion,
                      sum(monto) as monto
               FROM (-- PARTIDA SUB-ESPECIFICA
                     SELECT yearpoa, id_partida, denominacion, 
                            sum(cantidad*costo_unitario) as monto 
                     FROM p_proyectos
                     JOIN p_acciones using (id_proyecto)
                     JOIN p_actividades using (id_accion)
                     JOIN p_presupuesto_actividad using (id_actividad)
                     JOIN z_partidas_presupuestarias using (id_partida)
                     WHERE id_partida != '4.00.00.00.00'
                     AND yearpoa=$yearpoa
                     GROUP BY yearpoa, id_partida, denominacion                     
                     UNION
                     -- PARTIDA ESPECIFICA
                     SELECT yearpoa, b.id_partida as id_partida, b.denominacion,
                            sum(cantidad*costo_unitario) as monto 
                     FROM p_proyectos
                     JOIN p_acciones using (id_proyecto)
                     JOIN p_actividades using (id_accion)
                     JOIN p_presupuesto_actividad using (id_actividad)
                     JOIN z_partidas_presupuestarias a using (id_partida)
                     JOIN z_partidas_presupuestarias b on a.id_sup = b.id_partida
                     WHERE b.id_partida != '4.00.00.00.00'
                     AND yearpoa=$yearpoa
                     GROUP BY yearpoa, b.id_partida, b.denominacion
                     UNION
                     -- PARTIDA GENERICA
                     SELECT yearpoa, c.id_partida as id_partida, c.denominacion, 
                            sum(cantidad*costo_unitario) as monto 
                     FROM p_proyectos
                     JOIN p_acciones using (id_proyecto)
                     JOIN p_actividades using (id_accion)
                     JOIN p_presupuesto_actividad using (id_actividad)
                     JOIN z_partidas_presupuestarias a using (id_partida)
                     JOIN z_partidas_presupuestarias b on a.id_sup = b.id_partida
                     JOIN z_partidas_presupuestarias c on b.id_sup = c.id_partida
                     WHERE c.id_partida != '4.00.00.00.00'
                     AND yearpoa=$yearpoa
                     GROUP BY yearpoa, c.id_partida, c.denominacion
                     UNION
                     -- PARTIDA 
                     SELECT yearpoa, d.id_partida as id_partida, d.denominacion, 
                            sum(cantidad*costo_unitario) as monto 
                     FROM p_proyectos
                     JOIN p_acciones using (id_proyecto)
                     JOIN p_actividades using (id_accion)
                     JOIN p_presupuesto_actividad using (id_actividad)
                     JOIN z_partidas_presupuestarias a using (id_partida)
                     JOIN z_partidas_presupuestarias b on a.id_sup = b.id_partida
                     JOIN z_partidas_presupuestarias c on b.id_sup = c.id_partida
                     JOIN z_partidas_presupuestarias d on c.id_sup = d.id_partida
                     WHERE d.id_partida != '4.00.00.00.00'
                     AND yearpoa=$yearpoa
                     GROUP BY yearpoa, d.id_partida, d.denominacion                     
                     UNION
                     --TOTALES
                     SELECT yearpoa, 'TOTAL' as id_partida, e.denominacion, 
                            sum(cantidad*costo_unitario) as monto FROM p_proyectos
                     JOIN p_acciones using (id_proyecto)
                     JOIN p_actividades using (id_accion)
                     JOIN p_presupuesto_actividad using (id_actividad)
                     JOIN z_partidas_presupuestarias a using (id_partida)
                     JOIN z_partidas_presupuestarias b on a.id_sup = b.id_partida
                     JOIN z_partidas_presupuestarias c on b.id_sup = c.id_partida
                     JOIN z_partidas_presupuestarias d on c.id_sup = d.id_partida
                     JOIN z_partidas_presupuestarias e on d.id_sup = e.id_partida
                     WHERE yearpoa=$yearpoa
                     GROUP BY 1, 2, 3) q
               GROUP BY 1, 2, 3
               ORDER BY 1";          
         
         $query = $this->db->query($sql);
         return $query;
     }
     
     // REPORTE DE PLANIFICACION DE METAS FISICAS Y METAS FINANCIERAS DE UN PROYECTO DADO
     function obtener_metas_actividades_proyecto($id_proyecto)
     {
         $sql="SELECT cod_act, actividad, um, 
                      CASE WHEN date_part('month',fecha)=1 THEN meta 
                      ELSE 0 END as ene1,
                      CASE WHEN date_part('month',fecha)=2 THEN meta
                      ELSE 0 END as feb1, 
                      CASE WHEN date_part('month',fecha)=3 THEN meta
                      ELSE 0 END as mar1,
                      CASE WHEN date_part('month',fecha)=4 THEN meta
                      ELSE 0 END as abr1,
                      CASE WHEN date_part('month',fecha)=5 THEN meta
                      ELSE 0 END as may1,	
                      CASE WHEN date_part('month',fecha)=6 THEN meta
                      ELSE 0 END as jun1,
                      CASE WHEN date_part('month',fecha)=7 THEN meta
                      ELSE 0 END as jul1,	
                      CASE WHEN date_part('month',fecha)=8 THEN meta
                      ELSE 0 END as ago1,	
                      CASE WHEN date_part('month',fecha)=9 THEN meta
                      ELSE 0 END as sep1,	
                      CASE WHEN date_part('month',fecha)=10 THEN meta
                      ELSE 0 END as oct1,	
                      CASE WHEN date_part('month',fecha)=11 THEN meta
                      ELSE 0 END as nov1,	
                      CASE WHEN date_part('month',fecha)=12 THEN meta 
                      ELSE 0 END as dic1,
                      CASE WHEN date_part('month',fecha)=1 THEN monto 
                      ELSE 0 END as ene,
                      CASE WHEN date_part('month',fecha)=2 THEN monto 
                      ELSE 0 END as feb, 
                      CASE WHEN date_part('month',fecha)=3 THEN monto 
                      ELSE 0 END as mar,
                      CASE WHEN date_part('month',fecha)=4 THEN monto 
                      ELSE 0 END as abr,	
                      CASE WHEN date_part('month',fecha)=5 THEN monto 
                      ELSE 0 END as may,	
                      CASE WHEN date_part('month',fecha)=6 THEN monto 
                      ELSE 0 END as jun,
                      CASE WHEN date_part('month',fecha)=7 THEN monto 
                      ELSE 0 END as jul,	
                      CASE WHEN date_part('month',fecha)=8 THEN monto 
                      ELSE 0 END as ago,	
                      CASE WHEN date_part('month',fecha)=9 THEN monto 
                      ELSE 0 END as sep,	
                      CASE WHEN date_part('month',fecha)=10 THEN monto 
                      ELSE 0 END as oct,	
                      CASE WHEN date_part('month',fecha)=11 THEN monto 
                      ELSE 0 END as nov,	
                      CASE WHEN date_part('month',fecha)=12 THEN monto 
                      ELSE 0 END as dic,	
                      monto as total, meta as total1
               FROM (
                     SELECT (acc.codigo_ae||'.'||act.codigo_act) as cod_act, 
                             act.descripcion_act as actividad,
                             act.cantidad_act as meta,
                             act.um_act as um,
                             act.fecha_fin as fecha,
                             sum(pre.cantidad*pre.costo_unitario) as monto
                     FROM p_proyectos p
                     JOIN p_acciones acc using (id_proyecto)
                     JOIN p_actividades act using (id_accion)
                     JOIN p_presupuesto_actividad pre using (id_actividad)
                     WHERE p.id_proyecto=$id_proyecto
                     GROUP BY 1,2,3,4,5) q

               UNION
               SELECT 'TOTAL' as cod_act, '' as actividad, '' as um, 
                      sum(ene1) as ene1, sum(feb1) as feb1, sum(mar1) as mar1, 
                      sum(abr1) as abr1, sum(may1) as may1, sum(jun1) as jun1,
                      sum(jul1) as jul1, sum(ago1) as ago1, sum(sep1) as sep1, 
                      sum(oct1) as oct1, sum(nov1) as nov1, sum(dic1) as dic1,
                      sum(ene) as ene, sum(feb) as feb, sum(mar) as mar,
                      sum(abr) as abr, sum(may) as may, sum(jun) as jun,
                      sum(jul) as jul, sum(ago) as ago, sum(sep) as sep, 
                      sum(oct) as oct, sum(nov) as nove, sum(dic) as dic,
                      sum(total) as total, sum(total1) as total1
               FROM(
                      SELECT cod_act, actividad, um, 
                             CASE WHEN date_part('month',fecha)=1 THEN meta 
                             ELSE 0 END as ene1,
                             CASE WHEN date_part('month',fecha)=2 THEN meta
                             ELSE 0 END as feb1, 
                             CASE WHEN date_part('month',fecha)=3 THEN meta
                             ELSE 0 END as mar1,
                             CASE WHEN date_part('month',fecha)=4 THEN meta
                             ELSE 0 END as abr1,	
                             CASE WHEN date_part('month',fecha)=5 THEN meta
                             ELSE 0 END as may1,	
                             CASE WHEN date_part('month',fecha)=6 THEN meta
                             ELSE 0 END as jun1,
                             CASE WHEN date_part('month',fecha)=7 THEN meta
                             ELSE 0 END as jul1,	
                             CASE WHEN date_part('month',fecha)=8 THEN meta
                             ELSE 0 END as ago1,	
                             CASE WHEN date_part('month',fecha)=9 THEN meta
                             ELSE 0 END as sep1,	
                             CASE WHEN date_part('month',fecha)=10 THEN meta
                             ELSE 0 END as oct1,	
                             CASE WHEN date_part('month',fecha)=11 THEN meta
                             ELSE 0 END as nov1,	
                             CASE WHEN date_part('month',fecha)=12 THEN meta 
                             ELSE 0 END as dic1,
                             CASE WHEN date_part('month',fecha)=1 THEN monto 
                             ELSE 0 END as ene,
                             CASE WHEN date_part('month',fecha)=2 THEN monto 
                             ELSE 0 END as feb, 
                             CASE WHEN date_part('month',fecha)=3 THEN monto 
                             ELSE 0 END as mar,
                             CASE WHEN date_part('month',fecha)=4 THEN monto 
                             ELSE 0 END as abr,	
                             CASE WHEN date_part('month',fecha)=5 THEN monto 
                             ELSE 0 END as may,	
                             CASE WHEN date_part('month',fecha)=6 THEN monto 
                             ELSE 0 END as jun,
                             CASE WHEN date_part('month',fecha)=7 THEN monto 
                             ELSE 0 END as jul,	
                             CASE WHEN date_part('month',fecha)=8 THEN monto 
                             ELSE 0 END as ago,	
                             CASE WHEN date_part('month',fecha)=9 THEN monto 
                             ELSE 0 END as sep,	
                             CASE WHEN date_part('month',fecha)=10 THEN monto 
                             ELSE 0 END as oct,	
                             CASE WHEN date_part('month',fecha)=11 THEN monto 
                             ELSE 0 END as nov,	
                             CASE WHEN date_part('month',fecha)=12 THEN monto 
                             ELSE 0 END as dic,	
                             monto as total, meta as total1
                      FROM (
                             SELECT (acc.codigo_ae||'.'||act.codigo_act) as cod_act, 
                                     act.descripcion_act as actividad,
                                     act.cantidad_act as meta,
                                     act.um_act as um,
                                     act.fecha_fin as fecha,
                                     sum(pre.cantidad*pre.costo_unitario) as monto
                             FROM p_proyectos p
                             JOIN p_acciones acc using (id_proyecto)
                             JOIN p_actividades act using (id_accion)
                             JOIN p_presupuesto_actividad pre using (id_actividad)
                             WHERE p.id_proyecto=$id_proyecto
                             GROUP BY 1,2,3,4,5) q) t
                      GROUP BY 1,2,3
                      ORDER BY 1";
         $query = $this->db->query($sql);
         return $query;
     }
     
     // CONEXION A SIGESP PARA OBTENER EJECUCION DEL PROYECTO
     function  getEjecucionSigesp($proyecto, $ae, $uel)
     {  
        // REVISAMOS LOS REGISTROS EXISTENTES EN p_ejecucion_presupuestaria 
        $sql = "SELECT e.*, (codigo_ae ||'.'||codigo_act) as codact  
                FROM p_ejecucion_presupuestaria e
                JOIN p_actividades using(id_actividad)
                JOIN p_acciones using(id_accion)
                WHERE codestpro1 = '$proyecto'
                AND codestpro2 = '$ae'
                ORDER BY fecha, documento, spg_cuenta";
        $query = $this->db->query($sql); 
        
        $filas = $query->num_rows();
        $ejecucion = $query->result();
         
        $donde = "";
        if ($filas > 0)             
        {         
          foreach ($ejecucion as $e)
          {
             $donde.=" AND NOT (documento = '".$e->documento.
                     "' AND spg_cuenta = '".$e->spg_cuenta."') ";
          }
        } 
        
        // BUSCAMOS LOS REGISTROS NUEVOS DE SIGESP
        $DB2 = $this->load->database('SIGESP', TRUE);  
        $sql = "SELECT * FROM mayor_analitico
                WHERE estcla = 'P'
                AND operacion = 'CS'
                AND codestpro1 = '$proyecto'
                AND codestpro2 = '$ae'
                -- AND codestpro3 = '$uel'
                $donde    
                ORDER BY fecha, documento, spg_cuenta";

        $query = $DB2->query($sql);
        
        // UNIMOS LOS RESULTADOS
        foreach ($query->result() as $s)
        {
            $ejecucion[$filas]=$s;
            $filas++;
        }
        
        return $ejecucion;
     }      
     
     // Obtiene el monto total ejecutado de un proyecto dado desde SIGESP
     function getMontoEjecutadoSigesp($proyecto, $ae, $uel)
     {         
        $DB2 = $this->load->database('SIGESP', TRUE);  
        $sql = "SELECT codestpro1, codestpro2, sum(compromiso) as ejecutado
                FROM mayor_analitico
                WHERE estcla = 'P'
                AND operacion = 'CS'
                AND codestpro1 = '$proyecto'
                AND codestpro2 = '$ae'
                -- AND codestpro3 = '$uel'
                GROUP BY codestpro1, codestpro2";

        $query = $DB2->query($sql);
        
        if($query->num_rows()>0)
        {
           $r = $query->row();
           return $r->ejecutado;
        }
        else {return 0;}
     }

     // CONEXION A SIMULACRO DE SIGESP PARA OBTENER EJECUCION DEL PROYECTO
     function  getEjecucionSigesp1($proyecto, $ae, $uel)
     {  
        // REVISAMOS LOS REGISTROS EXISTENTES EN p_ejecucion_presupuestaria 
        $sql = "SELECT e.*, (codigo_ae ||'.'||codigo_act) as codact  
                FROM p_ejecucion_presupuestaria e
                JOIN p_actividades using(id_actividad)
                JOIN p_acciones using(id_accion)
                WHERE codestpro1 = '$proyecto'
                AND codestpro2 = '$ae'
                ORDER BY fecha, documento, spg_cuenta";
        $query = $this->db->query($sql); 
        
        $filas = $query->num_rows();
        $ejecucion = $query->result();
         
        $donde = "";
        if ($filas > 0)             
        {         
          foreach ($ejecucion as $e)
          {
             $donde.=" AND NOT (documento = '".$e->documento.
                     "' AND spg_cuenta = '".$e->spg_cuenta."') ";
          }
        } 
        
        // BUSCAMOS LOS REGISTROS NUEVOS DE SIGESP
        $sql = "SELECT * FROM mayor_analitico
                WHERE estcla = 'P'
                AND operacion = 'CS'
                AND codestpro1 = '$proyecto'
                AND codestpro2 = '$ae'
                -- AND codestpro3 = '$uel'
                $donde    
                ORDER BY fecha, documento, spg_cuenta";

        $query = $this->db->query($sql);
        
        // UNIMOS LOS RESULTADOS
        foreach ($query->result() as $s)
        {
            $ejecucion[$filas]=$s;
            $filas++;
        }
        
        return $ejecucion;
     }
     
     // EJECUCION PRESUPUESTARIA Y METAS FISICAS MES A MES
     
     function getEjecucionProyecto($id_proyecto)
     {
         $sql="SELECT codigo_ae||'.' ||codigo_act as cod_act, descripcion_act, um_act,
                      e.*
               FROM p_actividades
               JOIN p_acciones using (id_accion)
               JOIN p_proyectos using (id_proyecto)
               LEFT JOIN (
                          SELECT id_actividad, 
                          SUM(CASE WHEN date_part('month',fecha)=1 THEN compromiso
                              ELSE 0 END) as ene,
                          SUM(CASE WHEN date_part('month',fecha)=2 THEN compromiso
                              ELSE 0 END) as feb,
                          SUM(CASE WHEN date_part('month',fecha)=3 THEN compromiso
                              ELSE 0 END) as mar,
                          SUM(CASE WHEN date_part('month',fecha)=4 THEN compromiso
                              ELSE 0 END) as abr,
                          SUM(CASE WHEN date_part('month',fecha)=5 THEN compromiso
                              ELSE 0 END) as may,
                          SUM(CASE WHEN date_part('month',fecha)=6 THEN compromiso
                              ELSE 0 END) as jun,
                          SUM(CASE WHEN date_part('month',fecha)=7 THEN compromiso
                              ELSE 0 END) as jul,
                          SUM(CASE WHEN date_part('month',fecha)=8 THEN compromiso
                              ELSE 0 END) as ago,
                          SUM(CASE WHEN date_part('month',fecha)=9 THEN compromiso
                              ELSE 0 END) as sep,
                          SUM(CASE WHEN date_part('month',fecha)=10 THEN compromiso
                              ELSE 0 END) as oct, 
                          SUM(CASE WHEN date_part('month',fecha)=11 THEN compromiso
                              ELSE 0 END) as nov,
                          SUM(CASE WHEN date_part('month',fecha)=12 THEN compromiso
                              ELSE 0 END) as dic,
                          SUM(compromiso) as total,
                          SUM(CASE WHEN date_part('month',fecha_meta)=1 THEN cantidad_meta
                              ELSE 0 END) as ene1,
                          SUM(CASE WHEN date_part('month',fecha_meta)=2 THEN cantidad_meta
                              ELSE 0 END) as feb1,
                          SUM(CASE WHEN date_part('month',fecha_meta)=3 THEN cantidad_meta
                              ELSE 0 END) as mar1,
                          SUM(CASE WHEN date_part('month',fecha_meta)=4 THEN cantidad_meta
                              ELSE 0 END) as abr1,
                          SUM(CASE WHEN date_part('month',fecha_meta)=5 THEN cantidad_meta
                              ELSE 0 END) as may1,
                          SUM(CASE WHEN date_part('month',fecha_meta)=6 THEN cantidad_meta
                              ELSE 0 END) as jun1,
                          SUM(CASE WHEN date_part('month',fecha_meta)=7 THEN cantidad_meta
                              ELSE 0 END) as jul1,
                          SUM(CASE WHEN date_part('month',fecha_meta)=8 THEN cantidad_meta
                              ELSE 0 END) as ago1,
                          SUM(CASE WHEN date_part('month',fecha_meta)=9 THEN cantidad_meta
                              ELSE 0 END) as sep1,
                          SUM(CASE WHEN date_part('month',fecha_meta)=10 THEN cantidad_meta
                              ELSE 0 END) as oct1, 
                          SUM(CASE WHEN date_part('month',fecha_meta)=11 THEN cantidad_meta
                              ELSE 0 END) as nov1,
                          SUM(CASE WHEN date_part('month',fecha_meta)=12 THEN cantidad_meta
                              ELSE 0 END) as dic1,
                          SUM(cantidad_meta) as total1
                          FROM p_ejecucion_presupuestaria
                          LEFT JOIN p_ejecucion_fisica using(id_actividad)
                          GROUP BY 1) e using (id_actividad)
                          WHERE id_proyecto = $id_proyecto
                    UNION
                    SELECT 'TOTAL' as cod_act, '' as descripcion_act, '' as um_act, 0 as id_actividad,
                    SUM(CASE WHEN date_part('month',fecha)=1 THEN compromiso
                        ELSE 0 END) as ene,
                    SUM(CASE WHEN date_part('month',fecha)=2 THEN compromiso
                        ELSE 0 END) as feb,
                    SUM(CASE WHEN date_part('month',fecha)=3 THEN compromiso
                        ELSE 0 END) as mar,
                    SUM(CASE WHEN date_part('month',fecha)=4 THEN compromiso
                        ELSE 0 END) as abr,
                    SUM(CASE WHEN date_part('month',fecha)=5 THEN compromiso
                        ELSE 0 END) as may,
                    SUM(CASE WHEN date_part('month',fecha)=6 THEN compromiso
                        ELSE 0 END) as jun,
                    SUM(CASE WHEN date_part('month',fecha)=7 THEN compromiso
                        ELSE 0 END) as jul,
                    SUM(CASE WHEN date_part('month',fecha)=8 THEN compromiso
                        ELSE 0 END) as ago,
                    SUM(CASE WHEN date_part('month',fecha)=9 THEN compromiso
                        ELSE 0 END) as sep,
                    SUM(CASE WHEN date_part('month',fecha)=10 THEN compromiso
                        ELSE 0 END) as oct, 
                    SUM(CASE WHEN date_part('month',fecha)=11 THEN compromiso
                        ELSE 0 END) as nov,
                    SUM(CASE WHEN date_part('month',fecha)=12 THEN compromiso
                        ELSE 0 END) as dic,
                    SUM(compromiso) as total,
                    SUM(CASE WHEN date_part('month',fecha_meta)=1 THEN cantidad_meta
                        ELSE 0 END) as ene1,
                    SUM(CASE WHEN date_part('month',fecha_meta)=2 THEN cantidad_meta
                        ELSE 0 END) as feb1,
                    SUM(CASE WHEN date_part('month',fecha_meta)=3 THEN cantidad_meta
                        ELSE 0 END) as mar1,
                    SUM(CASE WHEN date_part('month',fecha_meta)=4 THEN cantidad_meta
                        ELSE 0 END) as abr1,
                    SUM(CASE WHEN date_part('month',fecha_meta)=5 THEN cantidad_meta
                        ELSE 0 END) as may1,
                    SUM(CASE WHEN date_part('month',fecha_meta)=6 THEN cantidad_meta
                        ELSE 0 END) as jun1,
                    SUM(CASE WHEN date_part('month',fecha_meta)=7 THEN cantidad_meta
                        ELSE 0 END) as jul1,
                    SUM(CASE WHEN date_part('month',fecha_meta)=8 THEN cantidad_meta
                        ELSE 0 END) as ago1,
                    SUM(CASE WHEN date_part('month',fecha_meta)=9 THEN cantidad_meta
                        ELSE 0 END) as sep1,
                    SUM(CASE WHEN date_part('month',fecha_meta)=10 THEN cantidad_meta
                        ELSE 0 END) as oct1, 
                    SUM(CASE WHEN date_part('month',fecha_meta)=11 THEN cantidad_meta
                        ELSE 0 END) as nov1,
                    SUM(CASE WHEN date_part('month',fecha_meta)=12 THEN cantidad_meta
                        ELSE 0 END) as dic1,
                    SUM(cantidad_meta) as total1
                    FROM p_ejecucion_presupuestaria
                    LEFT JOIN p_ejecucion_fisica using(id_actividad)
                    LEFT JOIN p_actividades using (id_actividad)
                    LEFT JOIN p_acciones using (id_accion)
                    LEFT JOIN p_proyectos using (id_proyecto)
                    WHERE id_proyecto=$id_proyecto
                    GROUP BY 1,2,3,4
                    ORDER BY 1 ASC";
        $query = $this->db->query($sql);
        return $query;
     }
}
?>