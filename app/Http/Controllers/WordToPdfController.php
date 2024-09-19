<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class WordToPdfController
{
    public function convertWordToPdf()
    {
        // 1.获取本地word模版
        $wordPath = 'public/服务协议副本' . '.docx';
        $wordContent = Storage::disk('local')->get($wordPath);
        $wordPath = Storage::disk('local')->path($wordPath);

        // 2.准备word模板中替换内容
        $data = [
            'JName' => '杭州碎肉公司',
            'YName' => '杭州拌面公司',
            'Area'  => '浙江省杭州市滨江区',
            'JDatetime' => date('Y-m-d H:i:s', time()),
            'YDatetime' => date('Y-m-d H:i:s', time()),
            'nullLine'  => '',
        ];

        // 3.保存word文件生成路径
        $defaultPath = 'word';
        Storage::disk('local')->makeDirectory($defaultPath);
        $wordSavePath = $defaultPath . '/'. Str::uuid()->toString() . '.docx';
        $wordLocalPath = Storage::disk('local')->path($wordSavePath);
        Storage::disk('local')->put($wordSavePath, $wordContent);

        // 4.替换内容
        $processor = new TemplateProcessor($wordLocalPath);
        $processor->setValues($data);

        // 生成行
        $processor->cloneRow('line', 3);
        for ($i = 0; $i < 3; $i++) {

            $processor->setValue('line#' . $i + 1, $i + 1);
            $processor->setValue('dName#' . $i + 1, '名称'. $i);
            $processor->setValue('dArea#' . $i + 1, '区域'. $i);
        }

        $processor->saveAs($wordLocalPath);

        // 5.word转pdf
        $pdfName      = date('Y-m-d') . '协议.pdf';
        $pdfLocalPath = Storage::disk('local')->path($defaultPath) .'/'. $pdfName;

        // word->pdf
        $command = "unoconv -f pdf -o {$pdfLocalPath} {$wordLocalPath}";

        // Execute the command
        exec($command, $output, $return_var);
//        $unoconv = Unoconv::create(config('services.unoconv'));
//        $unoconv->transcode($wordLocalPath, 'pdf', $pdfLocalPath);


        return response()->json('success');
    }
}
