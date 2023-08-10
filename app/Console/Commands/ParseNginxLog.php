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

                $article_id = preg_replace('/.*_(\d+)/', '$1', $log['url']);

                $source = '';
                if (strpos($log['http_user_agent'], 'Baiduspider') !== false) {
                    $source = 'Baidu';
                }
                if (strpos($log['http_user_agent'], 'YisouSpider') !== false) {
                    $source = 'Shenma';
                }
                if (strpos($log['http_user_agent'], 'Sogou') !== false) {
                    $source = 'Sogou';
                }
                $md5_unique = md5($log['remote_addr'] . $timestamp. $log['url']);
                $logs[] = [
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
                    'md5_unique' => md5($md5_unique)
                ];
            }

            $logs = array_chunk($logs, 500);

            foreach ($logs as $log){
                NginxAccessLog::insertIgnore($log);
            }
            $storage->delete($file_name);
        }
    }

}
