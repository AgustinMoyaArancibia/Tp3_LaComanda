<?php

use Fpdf\Fpdf;

class PDF extends Fpdf
{

    // Cabecera de página
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial','B',8);
        $w = $this->GetStringWidth("La Comanda")+6;
        $this->SetX((210-$w)/2);
        $this->SetDrawColor(34,139,34);
        $this->SetFillColor(46,137,87);
        $this->SetTextColor(0,0,128);
        $this->SetLineWidth(1);
        $this->Cell($w,9,"La Comanda",1,1,'C',true);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-18);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(128);
        // Número de página
        $this->Cell(0,10,'Leandro Noguera',0,0,'C');
    }
}


?>