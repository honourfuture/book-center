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
        $count = Article::select(['articleid'])->orderby('articleid', 'asc')->count();
        $total = 0;
        Article::select(['articleid'])->orderby('articleid', 'asc')->chunk(500, function ($articles) use (&$i, &$lines, &$page, &$total, $count) {
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
                $total++;

                $i++;
                $index = intval($article->articleid / 1000);
                $href = "/{$index}_{$article->articleid}/";
                $new_article_id = $article->articleid + 13;
                $new_href = "/biquge_{$new_article_id}/";


                foreach ($urls as $key => $url){
                    $static_url = $url['old'].$href.' '.$url['new'].$new_href."\n";
                    $lines[$key][] = $static_url;
                }
                if($i == 40000 || $total == $count){
                    foreach ($lines as $key => $line){
                        Storage::put("/{$key}_{$page}.txt", $line);
                    }
                    $lines = [
                        'pc' => [],
                        'wap' => [],
                        'top' => []
                    ];
                    $page++;
                    $i=0;
                }
            }
        });

        $i = 0;
        $lines = [
            'pc' => [],
            'wap' => [],
            'top' => []
        ];
        $page = 1;
        $article_ids = Article::select(['articleid'])->where('allvisit', '>', 0)->pluck('articleid');
        $count = Chapter::select(['articleid', 'chapterid'])->where('size', '>', 0)->whereIn('articleid', $article_ids)->count();
        $total = 0;
        Chapter::select(['articleid', 'chapterid'])->where('size', '>', 0)->whereIn('articleid', $article_ids)->chunk(500, function ($chapters) use (&$i, &$lines, &$page, &$total, $count) {
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

            foreach ($chapters as $chapter){
                $total++;
                $i++;
                $index = intval($chapter->articleid / 1000);
                $href = "/{$index}_{$chapter->articleid}/{$chapter->chapterid}.html";
                $new_article_id = $chapter->articleid + 13;

                $chapter_id = $chapter->chapterid + 13;

                $new_href = "/biquge_{$new_article_id}/{$chapter_id}.html";

                foreach ($urls as $key => $url){
                    $static_url = $url['old'].$href.' '.$url['new'].$new_href."\n";
                    $lines[$key][] = $static_url;
                }

                if($i == 40000 || $total == $count){
                    foreach ($lines as $key => $line){
                        Storage::put("/{$key}_chapter_{$page}.txt", $line);
                    }
                    $lines = [
                        'pc' => [],
                        'wap' => [],
                        'top' => []
                    ];
                    $page++;
                    $i=0;
                }
            }
        });
    }

}
