<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HtmlToPdfController
{
    use WatermarkImageTrait;

    /**
     * html转pdf
     *
     * @return JsonResponse
     *
     * @throws \Mpdf\MpdfException
     */
    public function convertToPdf(): JsonResponse
    {
        // 1.实例化Mpdf对象
        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'useSubstitutions' => true,
            'useAdobeCJK'      => true,
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
            'simpleTables'     => false, // 转pdf表格单元格边框消失
            'mgl'              => 15,
            'mgr'              => 15,
            'mgt'              => 16,
            'mgb'              => 16,
            'mgh'              => 9,
            'mgf'              => 9,
            'orientation'      => 'P'
        ]);

        // 2.生成水印
        $waterMark = $this->gePDFWatermarkImage('碎肉拌面');

        // 3.设置水印
        if ($waterMark ?? '') {
            $mpdf->SetWatermarkImage($waterMark, 0.2,'D', 'F');
            $mpdf->showWatermarkImage = true;
        }

        $data = [
            't_name'  => 'TTT',
            'c_name'  => 'CCC',
            'report_sn'     => 'sn_xxx001',
            'type'          => 'cso_report_audit',
            'submit_at'     => '2024-08-20',
            'title'         => 'title-xxx'
        ];

        // 4.渲染html文件
        $mpdf->WriteHTML(view('pdf', $data)->render());

        // 5.输出pdf文件
        Storage::makeDirectory('output');
        $localPath = storage_path('app/output');

        $output_file_name = 'pfd_' . time() . '.pdf';

        $mpdf->Output($localPath . '/'. $output_file_name, 'F');

        return response()->json('success');
    }

    public function convertToZip()
    {

    }
}
