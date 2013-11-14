<?php

/*
 * Clase para exportar Consolidado de Proyectos a pdf
 */
 require_once 'tcpdf/config/tcpdf_config.php';
 require_once 'tcpdf/tcpdf.php';

// extendemos la Clase TCPF con funciones personalizadas
class PresupuestoPDF extends TCPDF {

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
    
    public function cargarData($partidas, $yearpoa)
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
        $this->Write(0, "Presupuesto de Proyectos para el año $yearpoa", '', 0, 'C', true, 0, false, false, 0);
                  
        // CONSTRUIMOS LA TABLA     
        $fill=true;      
        $this->_cabeceras(); 
        $pagActual = $this->getPage();
        
        foreach($partidas->result() as $p)
        {               
            if ($p->id_partida == 'TOTAL')
            {  //PIE DE TABLA
               $this->_pie($p->monto); 
            }
            else
            {
               $this->SetTextColor(0); 
               if (substr($p->id_partida, -2) != '00' )
               {  // Partida SubEspecifica
                  $this->SetFillColor(255); 
                  $this->SetTextColor(100);
               }
               else $this->SetFillColor(255); // Partida Especifica
               
               if (substr($p->id_partida, -5) == '00.00') $this->SetFillColor(240);//Partida Generica
               
               if (substr($p->id_partida, -8) == '00.00.00') $this->SetFillColor(200);//Partida General
                
               $h=4;
               $p->denominacion=trim($p->denominacion);
               // Evaluamos si denart tiene mas de 50 caracteres
               if (strlen($p->denominacion)>95) $h *= 1.5;  
               if (strlen($p->denominacion)>190) $h *= 1.3;  
               if (strlen($p->denominacion)>290) $h *= 1.3;               
               
               $this->Cell(.1, $h, '', 0, 0, 'C', $fill); 
               if ($pagActual!=  $this->getPage()) {$this->_cabeceras();
                                                 $pagActual = $this->getPage();}
               $this->Cell(20, $h, $p->id_partida, 0, 0, 'C', $fill,'',1, false, 'T', 'T');  
               
               if (strlen($p->denominacion)>95)
               {
                 $this->MultiCell(125, $h, $p->denominacion."\n", 0, 'J', $fill, 0,
                                  '','',true, 1, false, true, 0, 'C',true);  
               }
               else $this->Cell(125, $h, $p->denominacion, 0, 0,'L', $fill,'',1, false, 'T', 'T');               
               
               $this->Cell(30, $h, number_format($p->monto,2,',','.'), 0, 1, 'R', $fill,'',1, false, 'T', 'T');
            }
        }
 
    }
    
    private function _cabeceras()
    {
        // CABECERA DE LA TABLA
        // Colores, ancho de columna y font
	$this->SetFillColor(193, 28, 22);
	$this->SetTextColor(255);
	$this->SetDrawColor(128, 0, 0);
	$this->SetLineWidth(0.3);
	$this->SetFont('helvetica', '', 7);
	// Header
        $this->Cell(.1, 5, '', 0, 0, 'C', 1);
        $this->Cell( 20, 5, 'PARTIDA', 1, 0, 'C', 1);
        $this->Cell(125, 5, 'DENOMINACIÓN', 1, 0, 'C', 1);        
        $this->Cell(30, 5, 'MONTO (Bs.)', 1, 1, 'C', 1);	
        
        $this->SetFillColor(242, 242, 242);
	$this->SetTextColor(0);
    }
    
    private function _pie($monto=0)
    {
        // FOOTER DE LA TABLA
        // Colores, ancho de columna y font
	$this->SetFillColor(193, 28, 22);
	$this->SetTextColor(255);
	$this->SetDrawColor(128, 0, 0);
	$this->SetLineWidth(0.3);
	$this->SetFont('helvetica', '', 7);
	// Header
        $this->Cell(.1, 5, '', 0, 0, 'C', 1);        
        $this->Cell(145, 5, 'P R E S U P U E S T O   T O T A L  (Bs.)', 0, 0, 'R', 1);
        $this->Cell(30, 5, number_format($monto, 2, ',', '.'), 0, 1, 'R', 1,'',1);
        
        $this->SetFillColor(242, 242, 242);
	$this->SetTextColor(0);        
    }
}

?>
