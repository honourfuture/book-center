<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\NginxAccessLog;
use App\Models\SourceArticle;
use Diwms\NginxLogAnalyzer\Parse;
use Diwms\NginxLogAnalyzer\NginxAccessLogFormat;
use Diwms\NginxLogAnalyzer\RegexPattern;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SourceUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'source:update {--site=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'source:update';

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
        DB::statement("ALTER TABLE source_articles ADD COLUMN `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY;");

        SourceArticle::where('article_name', '')->delete();

        if ($this->option('site') == 'tt') {
            $this->_tt();
        }

        if ($this->option('site') == '69shu') {
            $this->_69shu();
        }
        DB::statement("ALTER TABLE source_articles DROP COLUMN `id`");
    }

    private function _tt()
    {
        SourceArticle::select('*')->chunk(1000, function ($source_articles) {
            foreach ($source_articles as $article) {
                $pattern = '/","copyright":".*/';
                $article_name = preg_replace($pattern, '', $article->article_name);
                $article_name = trim($article_name);
                $pattern = '/https:\/\/www\.ttshuba\.cc\/info-(\d+)\.html/';

                preg_match($pattern, $article->origin_url, $matches);
                $id = $matches[1];

                if ($id) {
                    SourceArticle::where('id', $article->id)->update([
                        'article_name' => $article_name,
                        'article_id' => $id,
                        'source' => 'tt'
                    ]);
                    $this->info($article_name);
                }
            }
        });
    }

    private function _69shu()
    {
        SourceArticle::select('*')->chunk(1000, function ($source_articles) {
            foreach ($source_articles as $article) {

                $pattern = '/","copyright":".*/';
                $article_name = preg_replace($pattern, '', $article->article_name);
                $article_name = trim($article_name);
                $pattern = '/https:\/\/69shuba\.cx\/book\/(\d+)\.htm/';

                $id = $matches[1] ?? 0;
                if (!$id) {
                    $pattern = '/https:\/\/69shu\.me\/book\/(\d+)\.htm/';
                    preg_match($pattern, $article->origin_url, $matches);
                    $id = $matches[1];
                }

                if ($id) {
                    SourceArticle::where('id', $article->id)->update([
                        'article_name' => $article_name,
                        'article_id' => $id,
                        'source' => '69shu',
                        'origin_url' => 'https://69shu.me/book/' . $id . '.htm'
                    ]);
                    $this->info($article_name);
                }
            }
        });
    }
}
