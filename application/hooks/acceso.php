<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Acceso
{   
    function identificado(){
        $this->CI =&get_instance();
        
      // if($this->CI->session->userdata('aprobado')==true ) redirect('principal_ctrl');
        
       if($this->CI->session->userdata('aprobado')!=true ) redirect('acceso_ctrl');
    }
}

/* End of file acceso.php */
/* Location: ./application/hooks/acceso.php */?>