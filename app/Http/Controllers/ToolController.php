<?php

namespace App\Http\Controllers;

use App\Enums\LogEnum;
use App\Enums\SpiderEnum;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function shell()
    {
        $logs = LogEnum::LOG_NAME;

        $data['logs'] = $logs;
        $data['logTypes'] = LogEnum::LOG_SHELL;
        $data['spiders'] = SpiderEnum::toArray();

        return view('tools.index', $data);
    }

    public function do_shell(Request $request)
    {
        $logName = $request->get('log_name');
        $logDate = $request->get('log_date');
        $spider = $request->get('spider');
        $logType =  $request->get('log_type');

        $enDate = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jue', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $logDate = explode('-', $logDate);

        $enDate = $enDate[$logDate[1] - 1];
        $monthDay = $logDate[2].'/'.$enDate;

        $shell = LogEnum::LOG_SHELL[$logType];
        $filePath = LogEnum::LOG_PATH . $logName;

        $shell = str_replace('{spider}', $spider, $shell);
        $shell = str_replace('{month_day}', $monthDay, $shell);
        $shell = str_replace('{file_path}', $filePath, $shell);

        return response()->json([
            'code' => 200,
            'data' => ['shell' => $shell]
        ]);
    }
}
