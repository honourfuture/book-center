<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Chapter;
use App\Services\SitemapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BuildSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '构建Sitemap';

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
        $i = 0;
        $lines = [
            'pc' => [],
            'wap' => [],
            'top' => []
        ];
        $page = 1;

        Article::select(['articleid'])->orderby('articleid', 'asc')->chunk(500, function ($articles) use (&$i, &$lines, &$page) {
            $urls = [
                'pc' => [
                    'old' => 'https://www.tieshuw.com',
                    'new' => 'https://www.lexinren.top'
                ],
                'wap' => [
                    'old' => 'https://m.tieshuw.com',
                    'new' => 'https://m.lexinren.top'
                ],
                'top' => [
                    'old' => 'https://tieshuw.com',
                    'new' => 'https://lexinren.top'
                ],
            ];

            foreach ($articles as $article){
                $i++;
                $index = intval($article->articleid / 1000);
                $href = "/{$index}_{$article->articleid}/";
                $new_article_id = $article->articleid + 13;
                $new_href = "/biquge_{$new_article_id}/";


                foreach ($urls as $key => $url){
                    $static_url = $url['old'].$href.' '.$url['new'].$new_href."\n";
                    $lines[$key][] = $static_url;
                }

                if($i == 2000){
                    foreach ($lines as $key => $line){
                        Storage::put("/{$key}_{$page}.txt", $line);
                    }
                    $lines = [
                        'pc' => [],
                        'wap' => [],
                        'top' => []
                    ];
                    $page++;
                    echo $page;

                    $i=0;
                }
            }
        });


//        Chapter::select(['articleid', 'chapterid'])->chunk(500, function ($articles) use ($i) {
//
//
//        });
    }

}
