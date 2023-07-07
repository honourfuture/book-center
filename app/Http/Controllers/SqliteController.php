<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SqliteController extends Controller
{
    public function get_empty_article()
    {
        $article_ids = $this->_articles();
        $all_sqlite = config('database.all_sqlite');
        $update = [];
        $update_local = [];

        foreach ($all_sqlite as $sqlite){
            foreach ($article_ids as $ids){
                $taskLog = DB::connection($sqlite)->table('taskLog')
                    ->whereNotIn('EXID', [130, 132])
                    ->whereIn('NID', $ids)
                    ->get()
                    ->toArray();

                foreach ($taskLog as $log){
//                    $update[$log->RULEFILE][] = $log->GETID .'|'. $log->TASKFILE.'|'. $log->EXID;
                    $update[$log->RULEFILE][] = $log->GETID;
                    $update_local[$log->RULEFILE][] = $log->NID;
                }
            }
        }

        foreach ($update as $key => $ids){
            $update[$key] = implode(',', array_unique($ids));
        }
        foreach ($update_local as $key => $ids){
            $update_local[$key] = implode(',', array_unique($ids));
        }

        print_r($update);
    }

    private function _articles()
    {

        $article_ids = [2,33,173,70135,70136,70075,277,335,320,334,53064,353,486,713,759,867,4449,897,968,1141,1218];
        return array_chunk($article_ids, 50);
    }
}
