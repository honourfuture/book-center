<?php

namespace App\Http\Controllers;

use App\Enums\LogEnum;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function shell()
    {
        $logs = LogEnum::LOG_NAME;

        $data['logs'] = $logs;
        return view('tools.index', $data);
    }

    public function do_shell(Request $request)
    {
        $logType = $request->get('logType');
    }
}
