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
        $article_id = $this->option('article_id');

        if($article_id){
            $index = intval($article_id / 1000);
            $this->_doIndex($index, [$index.'/'.$article_id]);
            exit;
        }

        $storage = Storage::disk('article');

        $files = $storage->directories('/');
        foreach ($files as $index) {
            $this->_doIndex($index);
        }
    }

    private function _doIndex($index, $article_ids = [])
    {
        $storage = Storage::disk('article');

        if(!$article_ids){
            $article_ids = $storage->directories("/{$index}/");
        }

        foreach ($article_ids as $id) {
            try {
                $article_id = explode('/', $id)[1];
                if ($article_id == 0) {
                    continue;
                }
                $index_opf = $storage->get("/{$id}/index.opf");
                $strXml = iconv('gbk', 'utf-8//IGNORE', $index_opf);
                $strXml = str_replace('ISO-8859-1', 'UTF-8', $strXml);
                @$objXml = simplexml_load_string($strXml);
                $book = json_decode(json_encode($objXml), true);
                if (in_array($article_id, [11533]) || !$book) {
                    continue;
                    logger('opt article book error', [$book]);
                }



                logger('opt article id', [$article_id]);
                $bookName = $book['metadata']['dc-metadata']['dc:Title'];
                $author = $book['metadata']['dc-metadata']['dc:Creator'];

                $article = Article::where('articleid', $article_id)->with(
                    'chapters'
                )->first();

                if (!$article) {
                    logger('article id loss', [$article_id]);
                    echo('article id loss' . $article_id . "\n");
                    continue;
                }

                $article = $article->toArray();

                $optChapters = $book['manifest']['item'];
                $articleChapters = $article['chapters'];

                if (count($optChapters) <> count($articleChapters)) {
                    $chapterIds = array_column($articleChapters, 'chapterid');

                    logger('chapters loss', [
                        'article_id' => $article_id,
                        'book_name' => $bookName,
                        'author' => $author,
                    ]);

                    $order = 0;
                    foreach ($optChapters as $chapter) {
                        $order++;
                        $chapterId = str_replace('.txt', '', $chapter['@attributes']['href']);
                        if (!in_array($chapterId, $chapterIds)) {
                            $size = 0;
                            $chaptertype = 0;
                            if ($chapter['@attributes']['id'] != '正文') {
                                $chapter_file_path = "{$index}/{$article_id}/{$chapterId}.txt";
                                $size = $storage->size($chapter_file_path);
                            } else {
                                $chaptertype = 1;
                            }

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
                                'chaptertype' => $chaptertype,

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
            } catch (\Exception $exception) {
                logger($exception);
                logger('opt exception', [$index]);
                logger('opt exception', [$id]);
            }
        }
    }
}
