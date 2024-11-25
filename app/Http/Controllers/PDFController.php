<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;

class PDFController
{
    // 向发票文件添加 文字
    public function add()
    {
        // 目标 PDF 文件的 URL
//        $pdfUrl = '';

        // 定义本地文件路径
        Storage::makeDirectory('download');
        $localFilePath = storage_path('app/download');
        $output_file_name = 'download_file' . '.pdf';
        $localFilePath = $localFilePath . '/' . $output_file_name;

        if (!Storage::exists($localFilePath)) {
            // 下载 PDF 文件并保存到本地
            $fileContent = file_get_contents($pdfUrl);
            if ($fileContent === false) {
                die('无法下载 PDF 文件');
            }

            // 定义本地文件路径
            Storage::makeDirectory('download');
            $localFilePath = storage_path('app/download');
            $output_file_name = 'download_file' . '.pdf';
            $localFilePath = $localFilePath . '/' . $output_file_name;
            // 将内容保存到本地
            file_put_contents($localFilePath, $fileContent);
        }

        // 初始化TCPDF
        $pdf = new Fpdi();

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // 加载现有PDF文件
        $pageCount = $pdf->setSourceFile($localFilePath);
        $templateId = $pdf->importPage(1);

        // 横向 A5 尺寸：宽 210mm，高 148mm
        $landscapeA5Size = [210, 140];

// 添加页面并设置为横向 A5
        $pdf->AddPage('L', $landscapeA5Size); // 'L' 表示 Landscape（横向）
        $pdf->useTemplate($templateId);

        // 设置字体、颜色和文本
        $pdf->SetFont('stsongstdlight', '', 24);
        $pdf->SetTextColor(0, 128, 0); // 绿色
        $pdf->SetXY(30, 20);
        $pdf->Write(0, '带量采购');

        // 保存修改后的PDF
        // 7.输出pdf文件
        Storage::makeDirectory('output');
        $localPath = storage_path('app/output');

        $output_file_name = 'pdf_' . time() . '.pdf';

        $pdf->Output($localPath . '/'. $output_file_name, 'F');

        return response()->json(['data' => 'success', 'name' => $output_file_name]);
    }
}
