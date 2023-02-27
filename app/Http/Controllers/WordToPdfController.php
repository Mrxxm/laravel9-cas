<?php

namespace App\Http\Controllers;

use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;

class WordToPdfController
{
    public function convertToPdf()
    {
        // 更改字体设置
//        $fontStyle = new Font();
//        $fontStyle->setName('Calibri');
//        $fontStyle->setSize(10);
//        $fontStyle->setColor('000000');

        // 设置缓存路径，这个路径必须存在且可写
        Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));
        Settings::setPdfRendererName('TCPDF');
//        Settings::setOutputEscapingEnabled(false);
//        Settings::setOutputEscapingEnabled(true);
//        Settings::setDefaultFontName('Calibri');

        // 读取Word文档
        $phpWord = IOFactory::load(public_path('1.8.8服务凭证补充需求文档211025.docx'));
//        $phpWord = IOFactory::load(storage_path('app/public/平方.docx'));


        // 将Word文档转换为PDF并保存到文件中
        $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->setFont('simli');
        $font = $pdfWriter->getFont();
//        dd($font);
        $pdfWriter->save(storage_path('app/public/平方.pdf'));

        return response()->download(storage_path('app/public/平方.pdf'));
    }
}
