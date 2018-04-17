<?php

require_once 'include/PHPExcel.php';
require_once 'include/PHPExcel/Writer/Excel2007.php';

class Index extends AController
{
    private $model;
    private $data;

    public function __construct()
    {
        $this->model = new Model($_POST['url']);

    }

    private function report()
    {
        if($_POST['url']){
            $this->data = $this->model->getReport();
            $this->downloadXLS();
            return $this->data;
        }else{
            return false;
        }

    }

    private function downloadXLS()
    {
        if($this->data) {
            $pExcel = new PHPExcel();
            $pExcel->setActiveSheetIndex(0);
            $aSheet = $pExcel->getActiveSheet();

            $aSheet->setTitle('Отчет');

            $pExcel->getDefaultStyle()->getFont()->setName('Arial');
            $pExcel->getDefaultStyle()->getFont()->setSize(12);

            $aSheet->getColumnDimension('A')->setWidth(10);
            $aSheet->getColumnDimension('B')->setWidth(30);
            $aSheet->getColumnDimension('C')->setWidth(10);
            $aSheet->getColumnDimension('D')->setWidth(30);
            $aSheet->getColumnDimension('E')->setWidth(60);

            $aSheet->getRowDimension('1')->setRowHeight(20);
            $aSheet->setCellValue('A1', '№');
            $aSheet->setCellValue('B1', 'Название проверки');
            $aSheet->setCellValue('C1', 'Статус');
            $aSheet->setCellValue('D1', '');
            $aSheet->setCellValue('E1', 'Текущее состояние');

            $style_split_row = array(
                'fill' => array(
                    'type' => PHPExcel_STYLE_FILL::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'EEEEEE'
                    )
                )
            );
            $style_header_row = array(
                'fill' => array(
                    'type' => PHPExcel_STYLE_FILL::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'AFEEEE'
                    )
                )
            );

            $aSheet->getStyle('A1:E1')->applyFromArray($style_header_row)->getFont()->setBold(true);

            $aSheet->mergeCells('A2:E2')->getStyle('A2:E2')->applyFromArray($style_split_row);

            $group1 = 3;
            $group2 = 4;
            $mergeTr = $group1 + 2;
            for ($row = 0; $row < count($this->data); $row++) {
                if ($row < 1) {
                    $aSheet->setCellValue('A' . ($group1), $row + 1);
                    $aSheet->setCellValue('B' . ($group1), $this->data[$row]['name_report']);
                    $aSheet->setCellValue('C' . ($group1), $this->data[$row]['status_report']);

                    if ($this->data[$row]['status_report'] == 'OK') {
                        $aSheet->getStyle('C' . ($group1))->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => '6B8E23')
                                )
                            )
                        );
                    } else {
                        $aSheet->getStyle('C' . ($group1))->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'A52A2A')
                                )
                            )
                        );
                    }

                    $aSheet->setCellValue('D' . ($group1), 'Состояние');
                    $aSheet->setCellValue('D' . ($group2), 'Рекомендации');

                    $aSheet->setCellValue('E' . ($group1), $this->data[$row]['condition']);
                    $aSheet->setCellValue('E' . ($group2), $this->data[$row]['recommendations']);

                    $aSheet->mergeCellsByColumnAndRow(0, $group1, 0, $group2);
                    $aSheet->mergeCellsByColumnAndRow(1, $group1, 1, $group2);
                    $aSheet->mergeCellsByColumnAndRow(2, $group1, 2, $group2);

                    $aSheet->mergeCells("A$mergeTr:E$mergeTr")->getStyle("A$mergeTr:E$mergeTr")
                        ->applyFromArray($style_split_row);
                } else {
                    $mergeTr = $mergeTr + 3;
                    $group1 = $group1 + 3;
                    $group2 = $group1 + 1;
                    $aSheet->setCellValue('A' . ($group1), $row + 1);
                    $aSheet->setCellValue('B' . ($group1), $this->data[$row]['name_report']);
                    $aSheet->setCellValue('C' . ($group1), $this->data[$row]['status_report']);

                    if ($this->data[$row]['status_report'] == 'OK') {
                        $aSheet->getStyle('C' . ($group1))->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => '6B8E23')
                                )
                            )
                        );
                    } else {
                        $aSheet->getStyle('C' . ($group1))->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'A52A2A')
                                )
                            )
                        );
                    }

                    $aSheet->setCellValue('D' . ($group1), 'Состояние');
                    $aSheet->setCellValue('D' . ($group2), 'Рекомендации');

                    $aSheet->setCellValue('E' . ($group1), $this->data[$row]['condition']);
                    $aSheet->setCellValue('E' . ($group2), $this->data[$row]['recommendations']);

                    $aSheet->mergeCellsByColumnAndRow(0, $group1, 0, $group2);
                    $aSheet->mergeCellsByColumnAndRow(1, $group1, 1, $group2);
                    $aSheet->mergeCellsByColumnAndRow(2, $group1, 2, $group2);

                    $aSheet->mergeCells("A$mergeTr:E$mergeTr")->getStyle("A$mergeTr:E$mergeTr")
                        ->applyFromArray($style_split_row);
                }

            }

            $style_table = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
            $aSheet->getStyle('A3:E21')->applyFromArray($style_table)->getAlignment()->setWrapText(true);

            $first_column = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER
                )
            );
            $aSheet->getStyle('A1:A21')->applyFromArray($first_column);
            $aSheet->getStyle('C1:C21')->applyFromArray($first_column);

            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THICK,
                        'color' => array('argb' => 'A9A9A9'),
                    ),
                ),
            );

            for ($i = 2; $i <= 20; $i++) {
                $aSheet->getStyle("A$i:E$i")->applyFromArray($styleArray);
            }

            $objWriter = new PHPExcel_Writer_Excel2007($pExcel);
            $objWriter->save('report.xlsx');
        }
    }


    public function get_body()
    {
        return $this->render('index',
            array(
                'title' => 'Check URL',
                'report' => $this->report()
            )
        );
    }

}