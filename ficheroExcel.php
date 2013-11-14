<?php 
	$str = $_POST['datos_a_enviar'];
        if (mb_detect_encoding($str ) == 'UTF-8') {
            $str = mb_convert_encoding($str , "HTML-ENTITIES", "UTF-8");
        }
        
        header("Content-type: application/vnd.ms-excel; charset=utf-8;");  
	header("Content-Disposition: filename=consolidado.xls");  
        header("Pragma: no-cache");
	header("Expires: 0");  
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false); // required for certain browsers 
	echo $str;  
?>