<?php
namespace App\Console\Commands;
use Illuminate\Support\Facades\Storage;

use Illuminate\Console\Command;

class FixArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:fix {--article_id=}';

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
                $article_id = explode('/', $id)[0];
                $index_opf = $storage->get("/{$id}/index.opf");
                $strXml = iconv('gbk', 'utf-8//IGNORE', $index_opf);
                @$objXml = simplexml_load_string($strXml);
                print_r($strXml);die;
            }
        }
    }

    private function xml2arr($simxml){
        $simxml = (array)$simxml;//强转
        foreach($simxml as $k => $v){
            if(is_array($v) || is_object($v)){
                $simxml[$k] = xml2arr($v);
            }
        }
        return $simxml;
    }

}
