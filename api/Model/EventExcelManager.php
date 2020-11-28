<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EventExcelManager
{

    public function prepareEventReportData($event){

        $em=new EventManager();
        $phpdate = strtotime( $event["eventstart"]);
        $eventstart = date( 'd.m.Y', $phpdate );

        $rvisitors=$em->getVisitors($event["eventid"]);
        $ruvisitors=$em->getunsubscribedVisitors($event["eventid"]);
        $response=$this->makeEventReport($rvisitors->getBody(),$ruvisitors->getBody(),$event["eventname"],$eventstart);
        return $response;
    }

    public function makeEventReport($visitorsArray,$unsubscribeVisitorsArray,$eventname,$eventstart,$filekey=null){
        if($filekey==null)$filekey=time().bin2hex(random_bytes(12));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row=1;
        $sheet->mergeCells('A'.$row.':H'.$row);
        $sheet->setCellValue('A'.$row, 'Reservationsreport '.$eventname." ".$eventstart);
        $row++;

        $sheet->mergeCells('A'.$row.':H'.$row);
        $date = new DateTime("now", new DateTimeZone('Europe/Berlin') );
        $sheet->setCellValue('A'.$row, "Generiert um ".$date->format('d.m.Y H:i'));
        $row+=2;

        $headerrow=$row;
        $sheet->setCellValue('A'.$row, '#');
        $sheet->setCellValue('B'.$row, 'Vorname');
        $sheet->setCellValue('C'.$row, 'Nachname');
        $sheet->setCellValue('D'.$row, 'Strasse');
        $sheet->setCellValue('E'.$row, 'Ort');
        $sheet->setCellValue('F'.$row, 'E-Mail');
        $sheet->setCellValue('G'.$row, 'Mobile');
        $sheet->setCellValue('H'.$row, 'Status');
        $row++;

        $sheet->fromArray(
            $visitorsArray,  // The data to set
            NULL,        // Array values with this value will not be set
            'A'.$row         // Top left coordinate of the worksheet range where
        //    we want to set these values (default is A1)
        );
        $row+=count($visitorsArray);
        $row++;
        $sheet->mergeCells('A'.$row.':H'.$row);
        $sheet->setCellValue('A'.$row, 'Abgemeldete Besucher');
        $row++;
        $startunsubscribe=$row;
        $sheet->fromArray(
            $unsubscribeVisitorsArray,  // The data to set
            NULL,        // Array values with this value will not be set
            'A'.$row         // Top left coordinate of the worksheet range where
        //    we want to set these values (default is A1)
        );
        $row+=count($unsubscribeVisitorsArray);

        $styleArray = ['font' => [ 'bold' => true,"size"=>28]];
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray);

        $styleArray = ['font' => [ 'bold' => true]];
        $spreadsheet->getActiveSheet()->getStyle('A'.$headerrow.':H'.$headerrow)->applyFromArray($styleArray);

        $styleArray = ['font' => [ 'strikethrough' => true]];
        $spreadsheet->getActiveSheet()->getStyle('A'.$startunsubscribe.':H'.$row)->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(1);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(1);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(1);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $writer = new Xlsx($spreadsheet);
        $filename="data/".$filekey.'.xlsx';
        $writer->save($filename);
        return new Response(["filename"=>$filename]);
    }
}