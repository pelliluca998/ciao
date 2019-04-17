<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportExportWeek implements FromView, WithEvents {

  public function __construct($params){
    $this->params = $params;
  }
  public function view(): View{
    return view('report::weekreport2', ['input' => $this->params]);
  }

  public function registerEvents(): array
  {
    return [
      BeforeExport::class => function(BeforeExport $event) {
        $event->writer->getDelegate()->getProperties()->setCreator("Parrocchia");
      },
      AfterSheet::class => function(AfterSheet $event) {
        $event->sheet->getDelegate()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $cellStyle = [
          'borders' => [
            'outline' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
              'color' => ['argb' => 'FFFF0000'],
            ],
          ]
        ];

        $last_column = $event->sheet->getHighestDataColumn();

        foreach(range('A', $last_column) as $columnID){
          $event->sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $event->sheet->mergeCells('A1:'.$last_column.'1'); //Nome oratorio
        $event->sheet->mergeCells('A2:'.$last_column.'2'); //Nome evento
        $event->sheet->mergeCells('A3:'.$last_column.'3'); //report iscrizioni
        $event->sheet->getRowDimension('2')->setRowHeight(30);
        $event->sheet->getStyle('A1:'.$last_column.'3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $event->sheet->getStyle('A4:'.$last_column.'4')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFFFD700');


      },
    ];
  }
}

?>
