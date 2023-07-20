<?php
namespace App\Console\Commands;
use App\Models\Article;
use App\Models\Chapter;
use Illuminate\Support\Facades\Storage;

use Illuminate\Console\Command;

class ReadArticleOpt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:opt';

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
        logger('article id loss', [111]);die;

        $storage = Storage::disk('article');

        $files =  $storage->directories('/');

        foreach ($files as $index){
            $article_ids = $storage->directories("/{$index}/");
            foreach ($article_ids as $id){
                $article_id = explode('/', $id)[1];
                if($article_id == 0){
                    continue;
                }
                $index_opf = $storage->get("/{$id}/index.opf");
                $strXml = iconv('gbk', 'utf-8//IGNORE', $index_opf);
                $strXml = str_replace('ISO-8859-1', 'UTF-8', $strXml);
                @$objXml = simplexml_load_string($strXml);
                $book = json_decode(json_encode($objXml), true);

                $bookName = $book['metadata']['dc-metadata']['dc:Title'];
                $author = $book['metadata']['dc-metadata']['dc:Creator'];

                $article = Article::where('articleid', $article_id)->with(
                    'chapters'
                )->first();

                if(in_array($article_id, [101])){
                    continue;
                }

                if(!$article){
                    logger('article id loss', [$article_id]);
                    echo('article id loss' . $article_id."\n");
                    continue;
                }

                $article = $article->toArray();

                $optChapters = $book['manifest']['item'];
                $articleChapters = $article['chapters'];

                if(count($optChapters) <> count($articleChapters)){
                    $chapterIds = array_column($articleChapters, 'chapterid');

                    logger('chapters loss', [
                        'article_id' => $article_id,
                        'book_name' => $bookName,
                        'author' => $author,
                    ]);

                    $order = 0;
                    foreach ($optChapters as $chapter){
                        $order++;
                        $chapterId = str_replace('.txt', '', $chapter['@attributes']['href']);
                        if(!in_array($chapterId, $chapterIds)){

                            $chapter_file_path = "{$index}/{$article_id}/{$chapterId}.txt";
                            $size = $storage->size($chapter_file_path);
                            $insert = [
                                'chapterid' => $chapterId,
                                'articleid' => $article_id,
                                'articlename' => $bookName,
                                'posterid' => 1,
                                'poster' => 'admin',
                                'postdate' => time(),
                                'lastupdate' => 1602259200,
                                'chaptername' => $chapter['@attributes']['id'],
                                'chapterorder' => $order,
                                'size' => $size,

                            ];

                            logger('chapters loss', [
                                'article_id' => $article_id,
                                'book_name' => $bookName,
                                'author' => $author,
                                'opt' => $chapter,
                                'insert' => $insert,
                            ]);

                            Chapter::insert($insert);
                        }
                    }
                }
            }
        }
    }

}
