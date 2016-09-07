<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Input;
use Excel;
use DB;
use App\Otrs;

use DateTime;
use DateInterval;
class Otrsexport extends Controller
{
    public function exportExcel($startDate, $endDate)

    {
        $routes = Otrs::dataExportOtrs($startDate, $endDate);
        $data = json_decode(json_encode((array) $routes), true);
        if ($startDate == $endDate)
        {
            $date = $startDate;
        } else {
            $date = $startDate . ' - ' . $endDate;
        }
        return Excel::create($date.' отчеты тех. поддержки', function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Otrs');

            // Chain the setters
            $excel->setCreator(' ')
                ->setCompany(' ');

            // Call them separately
            $excel->setDescription('from Otrs 4');

            $excel->sheet('Отчеты техподдержки', function($sheet) use ($data)

            {
                $sheet->fromArray($data);

            });
        })->download('xls');
    }
    

    public function exportExcelToday()

    {
        $dateToday = new DateTime();
        $date = $dateToday->format('Y-m-d');
        $routes = Otrs::dataExportOtrsToday($date);
        $data = json_decode(json_encode((array) $routes), true);
        return Excel::create($date.' отчеты тех. поддержки', function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Otrs');

            // Chain the setters
            $excel->setCreator(' ')
                ->setCompany(' ');

            // Call them separately
            $excel->setDescription('from Otrs 4');

            $excel->sheet('Отчеты техподдержки', function($sheet) use ($data)

            {
                $sheet->fromArray($data);

            });
        })->download('xls');
    }

    public function exportExcelYesterday()

    {

        $dateToday = new DateTime();
        $date = $dateToday->format('D');
        if ($date == "Sun")
        {
            $dateToday->sub(new DateInterval('P2D'));
            $date = $dateToday->format('Y-m-d');
        } else if ($date == "Mon")
        {
            $dateToday->sub(new DateInterval('P3D'));
            $date = $dateToday->format('Y-m-d');
        } else {
            $dateToday->sub(new DateInterval('P1D'));
            $date = $dateToday->format('Y-m-d');
        }

        $routes = Otrs::dataExportOtrsYesterday($date);
        $data = json_decode(json_encode((array) $routes), true);
        return Excel::create($date.' отчеты тех. поддержки', function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Otrs');

            // Chain the setters
            $excel->setCreator(' ')
                ->setCompany(' ');

            // Call them separately
            $excel->setDescription('from Otrs 4');

            $excel->sheet('Отчеты техподдержки', function($sheet) use ($data)

            {
                $sheet->fromArray($data);

            });
        })->download('xls');
    }

    /*public function orderExcel()

    {
        $routes = Otrs::dataExport();
        $data = json_decode(json_encode((array) $routes), true);

        return Excel::create('example', function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Our new awesome title / Название файла');

            // Chain the setters
            $excel->setCreator('Автор')
                ->setCompany('Компания');

            // Call them separately
            $excel->setDescription('A demonstration to change the file properties / Описание');

            $excel->sheet('Extradition', function($sheet) use ($data)

            {
                $sheet->fromArray($data);

            });

        })->download('xls');
    }*/

    /*public function orderPDF()

    {

        $routes = Cartridge::dataOrderCartridge();
        $data = json_decode(json_encode((array) $routes), true);

        return Excel::create('example', function($excel) use ($data) {

            $excel->sheet('mySheet', function($sheet) use ($data)

            {

                $sheet->fromArray($data);

            });

        })->download("pdf");

    }*/
    
}
