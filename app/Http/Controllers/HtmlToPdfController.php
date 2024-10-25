<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Shared\ZipArchive;

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
//            'format'           => 'A4',
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

        // 5.添加电子签章
//        $dianzi = Storage::disk('local')->path('pdf_watermarks/dianzi.jpg');
//        $mpdf->Image($dianzi, 89, 190);

        // 6.生成不同大小页面
//        // 第一页：A4 纵向
//        $mpdf->WriteHTML('<h1>Page 1: A4 Portrait</h1>');
//
//        // 添加第二页：A5 横向
//        $mpdf->AddPage('L',
//           '',
//             '',
//            '',
//             '',
//            '',
//            '',
//            '',
//             '',
//             '',
//            '',
//            '',
//             '',
//             '',
//             '',
//             0,
//             0,
//             0,
//             0,
//            '', 'A5');  // 'L' 表示横向，'A5' 是纸张大小
//        $mpdf->WriteHTML('<h1>Page 2: A5 Landscape</h1>');

        // 添加第三页：自定义纸张大小（210mm x 148mm，纵向）
//        $mpdf->AddPage('P', [210, 148]);  // 自定义纸张尺寸 (宽 x 高)
//        $mpdf->WriteHTML('<h1>Page 3: Custom Size (210mm x 148mm)</h1>');

        // 7.输出pdf文件
        Storage::makeDirectory('output');
        $localPath = storage_path('app/output');

        $output_file_name = 'pdf_' . time() . '.pdf';

        $mpdf->Output($localPath . '/'. $output_file_name, 'F');

        return response()->json(['data' => 'success', 'name' => $output_file_name]);
    }

    /**
     * 生成zip包
     *
     * @return JsonResponse
     *
     * @throws \Mpdf\MpdfException
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function convertToZip(): JsonResponse
    {
        // 1.实例化
        $zip = new ZipArchive();

        // 2.创建目录
        Storage::disk('local')->makeDirectory('output/zip');
        $zip_file_name = 'zip_' . time() . '.zip';
        $zip_file_path = Storage::disk('local')->path( 'output/zip/' . $zip_file_name);

        // 3.打开文件
        $status = $zip->open($zip_file_path, ZipArchive::CREATE);
        if (!$status) {
            throw new \Exception('操作失败');
        }

        // 4.获取pdf
        $pdf = $this->getPdf();

        // 5.写入文件
        $zip->addFile($pdf['path'],  'zip/pdf文件/' . '1.pdf');

        // 6.关闭文件
        $zip->close();

        // 7.移除外层pdf文件(相对路径)
        Storage::disk('local')->delete('output/' . $pdf['name']);

        return response()->json('success');
    }

    /**
     * 获取pdf
     *
     * @return array
     *
     * @throws \Mpdf\MpdfException
     */
    protected function getPdf(): array
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

        $output_file_name = 'pdf_' . time() . '.pdf';

        $mpdf->Output($localPath . '/'. $output_file_name, 'F');

        return ['path' => $localPath . '/'. $output_file_name, 'name' => $output_file_name];
    }

    /**
     * html转pdf
     *
     * @return JsonResponse
     *
     * @throws \Mpdf\MpdfException
     */
    public function convertToPdfCustomer(): JsonResponse
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

        // 6.质检单
        $mpdf->AddPage('',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            0,
            0,
            0,
            0,
            '', 'A4');  // 'L' 表示横向，'A5' 是纸张大小
        // 第一页：A4 纵向
        $mpdf->WriteHTML('<h2 style="text-align: center">质检单-1 </h2>');

        // 添加电子签章
        $dianzi = Storage::disk('local')->path('pdf_watermarks/dianzi1.png');
        $mpdf->Image($dianzi, 145, 230);

        // 添加第二页：A5 横向
        $mpdf->AddPage('L',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            0,
            0,
            0,
            0,
            '', 'A5');  // 'L' 表示横向，'A5' 是纸张大小
        $mpdf->WriteHTML('<h2 style="text-align: center">浙江亚太药业股份有限公司发货单(随货同行单)运输单-2</h2>');

        $mpdf->AddPage('L',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            0,
            0,
            0,
            0,
            '', 'A5');  // 'L' 表示横向，'A5' 是纸张大小

        // 添加电子签章
        $dianzi = Storage::disk('local')->path('pdf_watermarks/dianzi2.png');
        $mpdf->Image($dianzi, 72, 5);
        $mpdf->WriteHTML('<h2 style="text-align: center">电子发票(增值税专用发票)-3 </h2>');

        $mpdf->AddPage('L',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            0,
            0,
            0,
            0,
            '', 'A5');  // 'L' 表示横向，'A5' 是纸张大小
        $mpdf->WriteHTML('<h2 style="text-align: center">浙江长典医药有限公司销售清单-4</h2>');

        // 7.输出pdf文件
        Storage::makeDirectory('output');
        $localPath = storage_path('app/output');

        $output_file_name = 'pdf_' . time() . '.pdf';

        $mpdf->Output($localPath . '/'. $output_file_name, 'F');

        return response()->json(['data' => 'success', 'name' => $output_file_name]);
    }
}
