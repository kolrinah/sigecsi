<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script');
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *  SISTEMA DE GESTION Y CONTROL DEL SERVICIO INTERNO                *
 *  DESARROLLADO POR: ING.REIZA GARCÍA                               *
 *                    ING.HÉCTOR MARTÍNEZ                            *
 *  PARA:  MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES   *
 *  FECHA: ENERO DE 2013                                             *
 *  FRAMEWORK PHP UTILIZADO: CodeIgniter Version 2.1.3               *
 *                           http://ellislab.com/codeigniter         *
 *  TELEFONOS PARA SOPORTE: 0416-9052533 / 0212-5153033              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class Principal extends CI_Controller {

    function __construct() 
    {
        parent::__construct(); 
        $this->load->model('Insumos');
    }
    
    function index()
    {
     date_default_timezone_set('America/Caracas');
   
     $data=array('data'=>json_encode($this->session->all_userdata()));
     $data['titulo']='Principal';
     $data['contenido']='principal/principal';     
     $data['data']='<br/><h1>Bienvenido al Sistema de Gestión<br/> y Control del Sevicio Interno</h1>';
     $this->load->view('plantillas/plantilla_general',$data);     
    }
}
?>