<?php

namespace App\Console\Commands;

use App\Models\ArticleLangtail;
use App\Models\NginxAccessLog;
use Diwms\NginxLogAnalyzer\Parse;
use Diwms\NginxLogAnalyzer\NginxAccessLogFormat;
use Diwms\NginxLogAnalyzer\RegexPattern;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class PushFixChapterName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:fixChapterName';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '推送需要更新的小说ids';

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
        $client = new Client();
        $target_url = config('app.url') . "get-mayi-list?";

        $date = date('Ymd', strtotime('-1 day'));

        $response = $client->request('GET', $target_url, [
            'query' => [
                'db_name' => $date, 'format' => 'json'
            ]
        ]);
        $result = $response->getBody()->getContents();
        $result = json_decode($result);

        if (isset($result['local_article_ids']) && $result['local_article_ids']) {
            Artisan::call("push:chapter", [
                '--article_ids' => $result['local_article_ids'],
                '--site' => 'mayi',
                '--limit' => 30,
            ]);
        }
        $target_url = config('app.url') . "day_rule?";
        $response = $client->request('GET', $target_url, [
            'query' => [
                'db_name' => $date
            ]
        ]);

    }

}
