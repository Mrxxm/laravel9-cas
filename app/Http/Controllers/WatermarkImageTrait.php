<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

trait WatermarkImageTrait
{
    public function gePDFWatermarkImage(string $name): string
    {
        if ($name === '') {
            $name = '碎肉拌面';
        }

        $name = $this->replaceIllegalSymbolOnFileOrPath($name);

        Storage::disk('local')->makeDirectory('pdf_watermarks');

        if (!Storage::disk('local')->exists('pdf_watermarks/' . $name . '.png')) {
            if (!$this->makeImage($name)) {
                throw new \Exception('水印生成失败');
            }
        }

        return Storage::disk('local')->path('pdf_watermarks/' . $name . '.png');

    }

    /**
     * @param string $name
     * @param int $width
     * @param int $height
     * @param int $wm_mgx
     * @param int $wm_mgy
     *
     * @return bool
     */
    private function makeImage(string $name = '', int $width = 794, int $height = 1090, int $wm_mgx = 80, int $wm_mgy = 100): bool
    {
        $nameLength = mb_strlen($name);

        // 字体
        $font = storage_path('fonts/SourceHanSansCN-Normal.otf');

        $textImg = imagecreatetruecolor($nameLength * 17, 34);
        imagesavealpha($textImg, true);

        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($textImg, 255, 255, 255, 127);
        imagefill($textImg, 0, 0, $bg);

        // 颜色
        $col = imagecolorallocatealpha($textImg, 66, 66, 66, 0);

        imagettftext($textImg, 13, 0, 0, 17, $col, $font, $name); // 写TTF文字到图中

        $textImg = imagerotate($textImg, 45, $bg);

        $img = imagecreatetruecolor($width, $height);
        imagesavealpha($img, true);

        // 拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 0);
        imagefill($img, 0, 0, $bg);


        $maxRows = imagesx($textImg) ? ceil($width / (imagesx($textImg) + $wm_mgx)) : 1;
        $maxCols = imagesy($textImg) ? ceil($height / (imagesy($textImg) + $wm_mgy)) : 1;

        for ($col = 0; $col <= $maxCols; $col++) {
            for ($row = 0; $row <= $maxRows; $row++) {
                $wmX = ($wm_mgx + imagesx($textImg)) * $col;
                $wmY = ($wm_mgy + imagesy($textImg)) * $row;
                // Water mark process
                imagecopymerge($img, $textImg, $wmX, $wmY, 0, 0, imagesx($textImg), imagesy($textImg), 50);
            }
        }

        $result = imagepng($img, Storage::disk('local')->path('pdf_watermarks/' . $name . '.png'));

        //销毁资源
        imagedestroy($textImg);
        imagedestroy($img);

        return $result;
    }

    /**
     * 替换文件或文件夹不可使用的字符为全角字符
     *
     * @param $name
     *
     * @return string|string[]
     */
    public function replaceIllegalSymbolOnFileOrPath($name): array|string
    {
        return str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'],
            ['＼', '／', '：', '＊', '？', '＂', '＜', '＞', '｜'], $name);
    }

}
