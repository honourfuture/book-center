<?php
namespace App\Console\Commands;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;

use Illuminate\Console\Command;

class ReadArticleOpt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:opt {--article_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $storage = Storage::disk('article');

        $files =  $storage->directories('/');

        foreach ($files as $index){
            $article_ids = $storage->directories("/{$index}/");
            foreach ($article_ids as $id){
                $article_id = explode('/', $id)[1];
                $index_opf = $storage->get("/{$id}/index.opf");
                $strXml = iconv('gbk', 'utf-8//IGNORE', $index_opf);
                $strXml = str_replace('ISO-8859-1', 'UTF-8', $strXml);
                @$objXml = simplexml_load_string($strXml);
                $book = json_decode(json_encode($objXml), true);
                print_r($book);
                $article = Article::where('articleid', $article_id)->with(
                    'chapters'
                )->first()->toArray();

                if(!$article){

                }
            }
        }
    }

}
