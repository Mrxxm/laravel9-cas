<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrinterController
{
    public function list()
    {
        $command = 'lpstat -p';
        $printer_info = shell_exec($command);

        return response()->json(['data' => $printer_info]);
    }

    public function queue()
    {
        $command = 'lpstat -o';
        $printer_info = shell_exec($command);

        return response()->json(['data' => $printer_info]);
    }

    public function print(Request $request)
    {

        $file_name = $request->get('file_name');
        if (!$file_name) {
            return response()->json(['data' => '文件名称不存在']);
        }

        $printer_name = $request->get('printer_name');
        if (!$printer_name) {
            return response()->json(['data' => '打印机名称不存在']);
        }

        $copies = $request->get('copies') ?? 1;
        if ($copies > 5) {
            return response()->json(['data' => '份数不能超过4份']);
        }

        $localPath = storage_path('app/output');
        $file_path = $localPath . '/' .$file_name;

        $command = [];

        for ($i = 0; $i < $copies; $i++) {
            $command1 = "lp -d $printer_name -P 1 -o media=A4 -o sides=two-sided-long-edge -n 1 $file_path";
            $command2 = "lp -d $printer_name -P 2 -o media=A5 -o sides=two-sided-long-edge -n 1 $file_path";
            $command3 = "lp -d $printer_name -P 3 -o media=A5 -o sides=two-sided-long-edge -n 1 $file_path";
            $command4 = "lp -d $printer_name -P 4 -o media=A5 -o sides=two-sided-long-edge -n 1 $file_path";
            $command1_info = shell_exec($command1);
            $command2_info = shell_exec($command2);
            $command3_info = shell_exec($command3);
            $command4_info = shell_exec($command4);
//            $command1_info = '';
//            $command2_info = '';
//            $command3_info = '';
//            $command4_info = '';
            array_push($command, [
                'NO' => $i * 4 + 1,
                'command' => $command1,
                'no' => $command1_info
            ]);
            array_push($command, [
                'NO' => $i * 4 + 2,
                'command' => $command2,
                'no' => $command2_info
            ]);
            array_push($command, [
                'NO' => $i * 4 + 3,
                'command' => $command3,
                'no' => $command3_info
            ]);
            array_push($command, [
                'NO' => $i * 4 + 4,
                'command' => $command4,
                'no' => $command4_info
            ]);
        }

        return response()->json([
            'command' => $command,
            'data' => 'success']);
    }


}
