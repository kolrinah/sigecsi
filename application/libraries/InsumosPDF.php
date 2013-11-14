<?php

/*
 * Clase para exportar requerimientos de insumo a pdf
 */
 require_once 'tcpdf/config/tcpdf_config.php';
 require_once 'tcpdf/tcpdf.php';

// extendemos la Clase TCPF con funciones personalizadas
class InsumosPDF extends TCPDF {

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
    
    public function cargarData($insumos, $yearpoa)
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
        $this->Write(0, "Consolidado de Requerimientos de Insumos para el año $yearpoa", '', 0, 'C', true, 0, false, false, 0);
        
        //var_dump($this->getMargins());
        
        $n=$insumos->num_rows();
        $insumos=$insumos->result();
    
        // CONSTRUIMOS LA TABLA     
        $i=0;    
        $pg=trim($insumos[$i]->partida_generica);
        
        while($i<$n)
        {
            // SUBTITULO DEL REPORTE
            $this->Ln(5);
            $subtit=trim($insumos[$i]->partida_generica).' - '. 
                    mb_convert_case(trim($insumos[$i]->tipo_insumo),MB_CASE_UPPER);
            $this->SetTextColor(0);
            $this->SetFont('helvetica', '', 10);
            $this->Write(0, $subtit, '', 0, 'L', true, 0, false, false, 0);
            
            $this->_cabeceras();            
            $fill=false;
            
            $pagActual = $this->getPage();
            
            while($pg==trim($insumos[$i]->partida_generica) && $i<$n)
            {                                      
              $art= mb_convert_case(trim($insumos[$i]->denart), MB_CASE_UPPER,  "UTF-8");
              $h = 4;
              // Evaluamos si denart tiene mas de 50 caracteres
              if (strlen($art)>50) $h *= 1.5;  
              if (strlen($art)>80) $h *= 1.5;  
              if (strlen($art)>150) $h *= 1.3;
              
              $this->Cell(.1, $h, '', 0, 0, 'C', $fill); 
              if ($pagActual!=  $this->getPage()) {$this->_cabeceras();
                                                   $pagActual = $this->getPage();}
              
              $this->Cell(20, $h, trim($insumos[$i]->codart), 0, 0, 'C', $fill,'',1, false, 'T', 'T'); 
              $this->Cell(12, $h, trim($insumos[$i]->spg_cuenta), 0, 0, 'C', $fill,'',1, false, 'T', 'T'); 
              if (strlen($art)>50)
              {
                $this->MultiCell(60, $h, $art."\n", 0, 'J', $fill, 0,
                                  '','',true, 1, false, true, 0, 'C',true);  
              }
              else $this->Cell(60, $h, $art, 0, 0,'L', $fill,'',1, false, 'T', 'T');  
              $this->Cell(10, $h, trim($insumos[$i]->existencia), 0, 0, 'R', $fill,'',1, false, 'T', 'T'); 
              $this->Cell(22, $h, trim($insumos[$i]->denunimed), 0, 0, 'C', $fill,'',1, false, 'T', 'T'); 
              $this->Cell(10, $h, trim($insumos[$i]->requerido), 0, 0, 'R', $fill,'',1, false, 'T', 'T'); 
              $this->Cell(20, $h, '0,00', 0, 0, 'R', $fill,'',1, false, 'T', 'T'); 
              $this->Cell(20, $h, '0,00', 0, 1, 'R', $fill,'',1, false, 'T', 'T'); 
              
              $i++;
              if ($i==$n) break;
              $fill=!$fill;
            }
            @$pg=trim($insumos[$i]->partida_generica);
            //PIE DE TABLA
            $this->_pie();
            if ($i==$n) break;  
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
	$this->SetFont('helvetica', '', 6);
	// Header
        $this->Cell(.1, 5, '', 0, 0, 'C', 1);
        $this->Cell(20, 5, 'CÓDIGO', 1, 0, 'C', 1);
        $this->Cell(12, 5, 'PARTIDA', 1, 0, 'C', 1);
        $this->Cell(60, 5, 'REQUERIMIENTO', 1, 0, 'C', 1);
        $this->Cell(10, 5, 'EXIST', 1, 0, 'C', 1);
        $this->Cell(22, 5, 'UNIDAD MEDIDA', 1, 0, 'C', 1);
	$this->Cell(10, 5, 'CANT', 1, 0, 'C', 1);
        $this->Cell(20, 5, 'COSTO UNIT Bs.', 1, 0, 'C', 1,'',1);
        $this->Cell(20, 5, 'SUBTOTAL Bs.', 1, 1, 'C', 1,'',1);
        
        $this->SetFillColor(242, 242, 242);
	$this->SetTextColor(0);
    }
    
    private function _pie()
    {
        // FOOTER DE LA TABLA
        // Colores, ancho de columna y font
	$this->SetFillColor(193, 28, 22);
	$this->SetTextColor(255);
	$this->SetDrawColor(128, 0, 0);
	$this->SetLineWidth(0.3);
	$this->SetFont('helvetica', '', 6);
	// Header
        $this->Cell(.1, 3, '', 0, 0, 'C', 1);
        $this->Cell(20, 3, '', 0, 0, 'C', 1);
        $this->Cell(12, 3, '', 0, 0, 'C', 1);
        $this->Cell(60, 3, '', 0, 0, 'C', 1);
        $this->Cell(10, 3, '', 0, 0, 'C', 1);
        $this->Cell(22, 3, '', 0, 0, 'C', 1);
	$this->Cell(10, 3, '', 0, 0, 'C', 1);
        $this->Cell(20, 3, '', 0, 0, 'C', 1,'',1);
        $this->Cell(20, 3, '', 0, 1, 'C', 1,'',1);
        
        $this->SetFillColor(242, 242, 242);
	$this->SetTextColor(0);        
    }
}

?>
