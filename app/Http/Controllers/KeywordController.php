<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Chapter;
use App\Models\ErrorChapter;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KeywordController extends Controller
{
    /**
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get_keyword(Request $request)
    {
        $article_ids = $request->get('article_ids', 19);
        $article_ids = explode(',', $article_ids);

        $articles = Article::whereIn('articleid', $article_ids)->get();

        $target_url = 'http://81.68.159.206:5000/get-article-keyowrds';

        $client = new Client(['headers' => ['Content-Type' => 'application/json']]);

        foreach ($articles as $article){
            $chapters = Chapter::select([
                'chapterid', 'articleid',
                'chaptername', 'lastupdate', 'chapterorder'
            ])->where('articleid', $article->articleid)
                ->orderBy('chapterorder', 'asc')
                ->limit(3)->get();

            $short_id = intval($article->articleid / 1000);
            $storage = Storage::disk('article');
            $keywords = [];

            foreach ($chapters as &$chapter) {
                $chapter_file_path = "{$short_id}/{$article->articleid}/$chapter->chapterid.txt";
                if (!$storage->exists($chapter_file_path)) {
                    $chapter->error_message = ["txt丢失"];
                    continue;
                }
                $chapter_file = $storage->get($chapter_file_path);
                $content = iconv('gbk', 'utf-8//IGNORE', $chapter_file);

                $response = $client->post($target_url, ['body' => json_encode([
                    'text' => $content,
                    'token' => '4uU-4TGT',
                ])]);

                $result = $response->getBody()->getContents();

                $result = json_decode($result, true);


                foreach ($result as $keyword => $occurrences){
                    if(isset($keywords[$keyword])){
                        $keywords[$keyword]['occurrences'] = $occurrences + $keywords[$keyword]['occurrences'];
                        continue;
                    }

                    $keywords[$keyword] = [
                        'keyword' => $keyword,
                        'occurrences' => $occurrences,
                    ];
                }

            }
            $sort_keywords = collect($keywords)->sortByDesc('occurrences')->toArray();
            $sort_keywords = array_slice($sort_keywords, 0, 3);
            $sort_keywords = array_column($sort_keywords, 'keyword');
            $sort_keyword_string = implode(',', $sort_keywords);

            Article::where('articleid', $article->articleid)->update(['keywords' => $sort_keyword_string]);
        }


    }
}
