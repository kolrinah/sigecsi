<?php

/*
 * Clase para exportar Consolidado de Proyectos a pdf
 */
 require_once 'tcpdf/config/tcpdf_config.php';
 require_once 'tcpdf/tcpdf.php';

// extendemos la Clase TCPF con funciones personalizadas
class ProyectosPDF extends TCPDF {

    // Membrete
    public function Header() 
    {    
        $image_file = 'imagenes/admirable.jpg';
	$this->Image($image_file, 20, 5, 176, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
                
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('Reiza Garcia');
        $this->SetTitle('Reportes Consolidados');
        $this->SetSubject('Requerimientos de Insumos');     
     
        // set default header data
        //$this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
        
        // set header and footer fonts
        //$this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
     
        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
     
        // set margins
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set image scale factor
        //$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // ---------------------------------------------------------
    }
    
    public function cargarData($proyectos, $yearpoa)
    {        
        $this->AddPage();
        // set margins
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // COLOCAMOS EL TITULO DEL REPORTE

        $this->SetFont('helvetica', '', 14);          
        $this->Write(0, "Consolidado de Proyectos para el año $yearpoa", '', 0, 'C', true, 0, false, false, 0);
                  
        // CONSTRUIMOS LA TABLA     
        $fill=false;
        $aprobado=0;
        $planificado=0;        
        $this->_cabeceras(); 
        $pagActual = $this->getPage();
        
        foreach($proyectos->result() as $p)
        {                                       
            $unidad  = mb_convert_case(trim($p->descripcion), MB_CASE_UPPER,  "UTF-8");
            $proyecto= mb_convert_case(trim($p->obj_esp), MB_CASE_UPPER,  "UTF-8");
            $h = 4;
            // Evaluamos si denart tiene mas de 40 caracteres
            if (strlen($unidad)>40 || strlen($proyecto) > 50 ) $h *= 1.5;  
            if (strlen($unidad)>80 || strlen($proyecto) > 90 ) $h *= 1.5;  
            if (strlen($unidad)>120 || strlen($proyecto) > 130 ) $h *= 1.3;
            if (strlen($proyecto) > 170 ) $h *= 1.3;
            if (strlen($proyecto) > 210 ) $h *= 1.3;
            if (strlen($proyecto) > 250 ) $h *= 1.3;
             
            $this->Cell(.1, $h, '', 0, 0, 'C', $fill); 
            if ($pagActual!=  $this->getPage()) {$this->_cabeceras();
                                                 $pagActual = $this->getPage();}
              
            $this->Cell(10, $h, trim($p->codigo), 0, 0, 'C', $fill,'',1, false, 'T', 'T'); 
           
            if (strlen($unidad)>40 || strlen($proyecto) > 50 )
            {
              $this->MultiCell(50, $h, $unidad."\n", 0, 'J', $fill, 0,
                                '','',true, 1, false, true, 0, 'C',true);  
            }
            else $this->Cell(50, $h, $unidad, 0, 0,'L', $fill,'',1, false, 'T', 'T');  
            $this->Cell(15, $h, trim($p->cod_proy), 0, 0, 'C', $fill,'',1, false, 'T', 'T'); 
            if (strlen($unidad)>40 || strlen($proyecto) > 50 )
            {
              $this->MultiCell(60, $h, $proyecto."\n", 0, 'J', $fill, 0,
                                '','',true, 1, false, true, 0, 'C',true);  
            }
            else $this->Cell(60, $h, $proyecto, 0, 0,'L', $fill,'',1, false, 'T', 'T'); 
            $this->Cell(20, $h, number_format($p->monto_aprobado,2,',','.'), 0, 0, 'R', $fill,'',1, false, 'T', 'T'); 
            $this->Cell(20, $h, number_format($p->total,2,',','.'), 0, 1, 'R', $fill,'',1, false, 'T', 'T');               
                            
            $fill=!$fill;
            $aprobado+=$p->monto_aprobado;
            $planificado+=$p->total;
        }
        //PIE DE TABLA
        $this->_pie($aprobado, $planificado);  
    }
    
    private function _cabeceras()
    {
        // CABECERA DE LA TABLA
        // Colores, ancho de columna y font
	$this->SetFillColor(193, 28, 22);
	$this->SetTextColor(255);
	$this->SetDrawColor(128, 0, 0);
	$this->SetLineWidth(0.3);
	$this->SetFont('helvetica', '', 6);
	// Header
        $this->Cell(.1, 5, '', 0, 0, 'C', 1);
        $this->Cell( 60, 5, 'UNIDAD ADMINISTRATIVA', 1, 0, 'C', 1);
        $this->Cell(15, 5, 'CODIGO', 1, 0, 'C', 1);
        $this->Cell(60, 5, 'NOMBRE DEL PROYECTO', 1, 0, 'C', 1);
        $this->Cell(20, 5, 'APROBADO (Bs.)', 1, 0, 'C', 1);
        $this->Cell(20, 5, 'PLANIFICADO (Bs.)', 1, 1, 'C', 1);	
        
        $this->SetFillColor(242, 242, 242);
	$this->SetTextColor(0);
    }
    
    private function _pie($aprobado=0, $planificado=0)
    {
        // FOOTER DE LA TABLA
        // Colores, ancho de columna y font
	$this->SetFillColor(193, 28, 22);
	$this->SetTextColor(255);
	$this->SetDrawColor(128, 0, 0);
	$this->SetLineWidth(0.3);
	$this->SetFont('helvetica', '', 6);
	// Header
        $this->Cell(.1, 5, '', 0, 0, 'C', 1);        
        $this->Cell(135, 5, 'T O T A L E S  (Bs.)', 0, 0, 'R', 1);
        $this->Cell(20, 5, number_format($aprobado, 2, ',', '.'), 0, 0, 'R', 1);
        $this->Cell(20, 5, number_format($planificado, 2, ',', '.'), 0, 1, 'R', 1,'',1);
        
        $this->SetFillColor(242, 242, 242);
	$this->SetTextColor(0);        
    }
}

?>
