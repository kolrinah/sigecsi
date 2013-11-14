<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16">    
<nav>
 <div class="grid_16 menu" >
  <table width="100%" border="0">
    <tr style="border-bottom: red solid 3px">
     <td width="40%" style="text-align:left;">
        <img src="<?php echo base_url(); ?>imagenes/lg_gob.png"/>
     </td>
     <td>                        
     <ul>
      <li class="nivel1"><?php echo anchor('acceso/salir', 'Salir', 'title="Salir del Sistema"') ?></li>
        <?php // PARA ENTRAR AL MODULO DE SUPERVISOR DE PLANIFICACIÓN SE REQUIERE:
              // - QUE EL USUARIO PERTENEZCA AL AREA DE PLANES ESTRATEGICOS Y OPERATVOS (id_estructura=48)
              // - O QUE TENGA ROL DE ADMINISTRADOR 
          if ($this->session->userdata('administrador') || intval($this->session->userdata('id_estructura'))==48)
          {     ?>          
      <li class="nivel1"><span> Reportes</span>
       <ul class="nivel2">
          <li><?php echo anchor('#', 'Reportes Estadísticos') ?></li>
          <li><?php echo anchor('reportes_consolidados', 'Reportes Consolidados') ?></li>
          <li><?php echo anchor('reportes_ejecucion', 'Reportes de Ejecución') ?></li>
       </ul>      
      </li>
    <?php }?>
      <li class="nivel1"><span>Ejecución</span>
       <ul class="nivel2">
          <li><?php echo anchor('ejecucion_productos', 'Productos Administrativos', 'title="Ejecución de Productos Administrativos"') ?></li>
          <li><?php echo anchor('ejecucion_proyectos', 'Proyectos', 'title="Ejecución de Proyectos"') ?></li>         
       </ul>
      </li>      
      <li class="nivel1"><span>Planificación</span>
       <ul class="nivel2">
          <li><?php echo anchor('plan_productos', 'Productos Administrativos', 'title="Programación de Productos Administrativos"') ?></li>
          <li><?php echo anchor('plan_proyectos', 'Planificación de Proyectos') ?></li>
          <li><?php echo anchor('requerimiento_personal', 'Requerimiento de Personal') ?></li> 
          <li><?php echo anchor('requerimiento_insumos', 'Requerimiento de Insumos') ?></li>
          <?php
          // PARA ENVIAR POA SE REQUIERE:
          // - QUE EL USUARIO TENGA UN NIVEL IGUAL O SUPERIOR AL DE COORDINADOR (id_nivel=4)
          // - O QUE TENGA ROL DE ADMINISTRADOR
          if ($this->session->userdata('administrador') || intval($this->session->userdata('id_nivel'))<=4)
          {
            echo '<li>';
            echo anchor('enviar_poa', 'Aprobar y Enviar POA');
            echo '</li>';
          }    
          ?>
          
       </ul>
      </li>
      <li class="nivel1"><span>Administración</span>
        <ul class="nivel2">
           <li><?php echo anchor('acceso/cambiar_clave', 'Cambiar Contraseña', 'title="Cambiar Clave"') ?></li>
          <?php
          
          // PARA ADMINISTRAR USUARIOS SE REQUIERE:
          // - QUE EL USUARIO TENGA UN NIVEL IGUAL O SUPERIOR AL DE COORDINADOR (id_nivel=4)
          // - O QUE TENGA ROL DE ADMINISTRADOR
          if ($this->session->userdata('administrador') || intval($this->session->userdata('id_nivel'))<=5)
          {
            echo '<li>';
            echo anchor('adm_usuarios', 'Usuarios', 'title="Administrar Usuarios"');
            echo '</li>';
          }
          
          // PARA ADMINISTRAR PRODUCTOS ADMINISTRATIVOS SE REQUIERE:
          // - QUE EL USUARIO PERTENEZCA AL AREA DE ORGANIZACIÓN Y SISTEMAS (id_estructura=50)
          // - O QUE TENGA ROL DE ADMINISTRADOR
          if ($this->session->userdata('administrador') || intval($this->session->userdata('id_estructura'))==50)
          {  
            echo '<li>';            
            echo anchor('adm_productos', 'Productos Administrativos', 'title="Administración de Productos Administrativos"');
            echo '<li/>';
          }
          
          // PARA ENTRAR AL MODULO DE SUPERVISOR DE PLANIFICACIÓN SE REQUIERE:
          // - QUE EL USUARIO PERTENEZCA AL AREA DE PLANES ESTRATEGICOS Y OPERATVOS (id_estructura=48)
          // - O QUE TENGA ROL DE ADMINISTRADOR
          if ($this->session->userdata('administrador') || intval($this->session->userdata('id_estructura'))==48)
          {  
            echo '<li>';
            echo anchor('supervisor_planes', 'Supervisor de Planificación');
            echo '<li/>'; 
          }
          
          // PARA ENTRAR AL MODULO DE BITÁCROA SE REQUIERE:          
          // - QUE TENGA ROL DE ADMINISTRADOR
          if ($this->session->userdata('administrador'))
          {  
            echo '<li>';
            echo anchor('bitacora', 'Bitácora de Sistema');
            echo '<li/>'; 
            if (intval($this->session->userdata('id_usuario'))==1)
            {  
              echo '<li>';
              echo anchor('adm_tablas', 'Administración de Tablas');
              echo '<li/>'; 
            }
          }
          ?>
        </ul></li>      
    </ul>  
    </td>
  </tr>
  </table>
  </div>
  <div class="clear"></div>
</nav> 
</div>