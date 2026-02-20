<?php

namespace App\Models;
use Codedge\Fpdf\Fpdf\Fpdf;

use Illuminate\Database\Eloquent\Model;

class Pdf extends Fpdf
{
      function Header()
    {
        // Logo
        $this->Image(asset('img/headerReport.png'),0,0,210);
        $this->SetFont('Arial','B',12);
        $this->Ln(20);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10, "Reporte creado con Smart VC - ".date('d/m/Y - H:i:s'),0,0,'L');
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
    }
}
