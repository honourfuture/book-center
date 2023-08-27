<?php

namespace App\Console\Commands;

use App\Models\NginxAccessLog;
use App\Models\SourceArticle;
use Diwms\NginxLogAnalyzer\Parse;
use Diwms\NginxLogAnalyzer\NginxAccessLogFormat;
use Diwms\NginxLogAnalyzer\RegexPattern;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SourceUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'source:update';

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
        $source_articles = SourceArticle::select('*')->whereNull('article_id')->chunk(1000, function ($source_articles) {

            foreach ($source_articles as $article) {
                $pattern = '/","copyright":".*/';
                $article_name = preg_replace($pattern, '', $article->article_name);
                $article_name = trim($article_name);
                $pattern = '/https:\/\/www\.ttshuba\.org\/info-(\d+)\.html/';

                preg_match($pattern, $article->origin_url, $matches);

                $id = $matches[1];

                SourceArticle::where('id', $article->id)->update([
                    'article_name' => $article_name,
                    'article_id' => $id,
                    'source' => 'tt'
                ]);
                $this->info($article_name);
            }
        });
    }

}
