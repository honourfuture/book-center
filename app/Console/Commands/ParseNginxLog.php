<?php

namespace App\Console\Commands;

use App\Models\NginxAccessLog;
use Diwms\NginxLogAnalyzer\Parse;
use Diwms\NginxLogAnalyzer\NginxAccessLogFormat;
use Diwms\NginxLogAnalyzer\RegexPattern;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ParseNginxLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:nginx-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '分析nginx log';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        NginxAccessLog::truncate();
        $storage = Storage::disk('nginx_log');
        $parse = new Parse(new NginxAccessLogFormat(), new RegexPattern());
        foreach ($storage->allFiles() as $file_name) {
            $logs = [];
            $lines = $storage->get($file_name);

            foreach (preg_split("/((\r?\n)|(\r\n?))/", $lines) as $key => $line) {
                try {
                    $log = $parse->line($line);
                } catch (\Exception $e) {
                    logger('行', [$line]);
                    logger($e);
                    continue;
                }

                $log = json_decode(json_encode($log), true);

                $date = $log['time_local'];
                $timestamp = date('Y-m-d H:i:s', strtotime($date));

                $date = date('Y-m-d', strtotime($date));
                if(strpos($log['url'], 'read') !== false){
                        preg_match('/read\/(\d+)\//', $log['url'], $matches);
                        if(!isset($matches[1])){
                            continue;
                        }
                        $article_id = $matches[1];
                        $article_id = $article_id - 5;
                }else {
                    preg_match('/\/\d+_(\d+)\//', $log['url'], $matches);
                    if(!isset($matches[1])){
                        continue;
                    }
                    $article_id = $matches[1];
                }

                $source = 'Baidu';
                if (strpos($log['http_referer'], 'baidu') !== false) {
                    $source = 'Baidu';
                }
                if (strpos($log['http_referer'], 'sm.cn') !== false) {
                    $source = 'Shenma';
                }
                if (strpos($log['http_referer'], 'sogou') !== false) {
                    $source = 'Sogou';
                }
                if (strpos($log['http_referer'], '360') !== false) {
                    $source = '360';
                }

                $log = [
                    'remote_addr' => $log['remote_addr'],
                    'remote_user' => $log['remote_user'],
                    'time_local' => $log['time_local'],
                    'http' => $log['http'],
                    'status' => $log['status'],
                    'bytes_sent' => $log['bytes_sent'],
                    'http_referer' => $log['http_referer'],
                    'http_user_agent' => $log['http_user_agent'],
                    'time' => $timestamp,
                    'date' => $date,
                    'request' => $log['request'],
                    'url' => $log['url'],
                    'source' => $source,
                    'article_id' => $article_id,
                ];

                $log['md5_unique'] = md5(implode("", $log));

                $logs[] = $log;
            }

            $chunk_logs = array_chunk($logs, 500);

            foreach ($chunk_logs as $chunk_log) {
                NginxAccessLog::insertIgnore($chunk_log);
            }
//            $storage->delete($file_name);
        }
    }

}