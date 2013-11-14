<?php

/*
 * Clase para exportar requerimientos de insumo a pdf
 */
 require_once 'tcpdf/config/tcpdf_config.php';
 require_once 'tcpdf/tcpdf.php';

// extendemos la Clase TCPF con funciones personalizadas
class PersonalPDF extends TCPDF {

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
    
    public function cargarData($personal, $yearpoa)
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
        $this->Write(0, "Consolidado de Requerimientos de Personal para el año $yearpoa", '', 0, 'C', true, 0, false, false, 0);
        
        //var_dump($this->getMargins());
        
        $n=$personal->num_rows();
        $personal=$personal->result();
    
        // CONSTRUIMOS LA TABLA     
        $i=0;    
        $ac=$personal[$i]->accion_centralizada;
        
        while($i<$n)
        {
            // SUBTITULO DEL REPORTE
            $this->Ln(5);
            $subtit=(($ac=='t')? 'Personal Requerido por Acción Centralizada':
                                 'Personal Requerido por Proyectos');
                    
            $this->SetTextColor(0);
            $this->SetFont('helvetica', '', 10);
            $this->Write(0, $subtit, '', 0, 'L', true, 0, false, false, 0);
            
            $this->_cabeceras();            
            $fill=false;
            
            $pagActual = $this->getPage();
            $machos=0;
            $hembras=0;        
            while($ac==$personal[$i]->accion_centralizada && $i<$n)
            {                                      
              $art= mb_convert_case(trim($personal[$i]->personal), MB_CASE_UPPER,  "UTF-8");
              $h = 5;
              
              $this->Cell(.1, $h, '', 0, 0, 'C', $fill); 
              if ($pagActual!=  $this->getPage()) {$this->_cabeceras();
                                                   $pagActual = $this->getPage();}
              
              $this->Cell(40, $h, trim($personal[$i]->tipo_personal), 0, 0, 'L', $fill,'',1); 
              $this->Cell(90, $h, trim($personal[$i]->personal), 0, 0, 'L', $fill,'',1);
              $this->Cell(15, $h, $personal[$i]->femenino, 0, 0, 'R', $fill,'',1);
              $this->Cell(15, $h, $personal[$i]->masculino, 0, 0, 'R', $fill,'',1);
              $this->Cell(15, $h, ($personal[$i]->femenino +
                                   $personal[$i]->masculino), 0, 1, 'R', $fill,'',1); 
              
              $hembras+=$personal[$i]->femenino;
              $machos+=$personal[$i]->masculino;
              $i++;
              if ($i==$n) break;
              $fill=!$fill;
            }
            @$ac=$personal[$i]->accion_centralizada;
            //PIE DE TABLA
            $this->_pie($hembras, $machos);
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
	$this->SetFont('helvetica', '', 8);
	// Header
        $this->Cell(.1, 5, '', 0, 0, 'C', 1);
        $this->Cell(40, 5, 'TIPO DE PERSONAL', 1, 0, 'C', 1);
        $this->Cell(90, 5, 'PERSONAL REQUERIDO', 1, 0, 'C', 1);
        $this->Cell(15, 5, 'F', 1, 0, 'C', 1);
        $this->Cell(15, 5, 'M', 1, 0, 'C', 1);
        $this->Cell(15, 5, 'TOTAL', 1, 1, 'C', 1);	
        
        $this->SetFillColor(242, 242, 242);
	$this->SetTextColor(0);
    }
    
    private function _pie($hembras, $machos)
    {
        // FOOTER DE LA TABLA
        // Colores, ancho de columna y font
	$this->SetFillColor(193, 28, 22);
	$this->SetTextColor(255);
	$this->SetDrawColor(128, 0, 0);
	$this->SetLineWidth(0.3);
	$this->SetFont('helvetica', '', 8);
	// Header
        $this->Cell(.1, 5, '', 0, 0, 'C', 1);
        $this->Cell(40, 5, '', 0, 0, 'C', 1);
        $this->Cell(90, 5, 'T O T A L E S', 0, 0, 'C', 1);
        $this->Cell(15, 5, $hembras, 0, 0, 'R', 1);
        $this->Cell(15, 5, $machos, 0, 0, 'R', 1);       
        $this->Cell(15, 5, $hembras + $machos, 0, 1, 'R', 1,'',1);
        
        $this->SetFillColor(242, 242, 242);
	$this->SetTextColor(0);        
    }
}

?>
