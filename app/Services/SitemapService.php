<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 17:43
 */

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SitemapService
{
    private $engine = 'baidu';

    private $url;

    private $site_type;

    private $sitemap_config = [
        'wap' => [
            'url' => 'http://m.juzhishuwu.com',
            'engines' => [
                'baidu' => [
                    'sort',
                    'update',
                    'update_list',
                    'full',
                    'top',
                    'list',
                ]
            ],
            'site_type' => 'mobile'
        ],
        'pc' => [
            'url' => 'http://www.juzhishuwu.com',
            'engines' => [
                'baidu' => ['list'],
                'sitemap' => ['list'],
            ],
            'site_type' => 'pc'
        ]
    ];

    public function build_update_sitemap()
    {
        $storage = Storage::disk('sitemap');
        foreach ($this->sitemap_config as $site_type => $site) {
            $this->url = $site['url'];
            $this->site_type = $site['site_type'];

            foreach ($site['engines'] as $engine => $categories) {
                $this->engine = $engine;

                foreach ($categories as $category) {
                    $content = '';
                    $links = $this->_get_links($category);
                    if ($category == 'update_list') {
                        $array_length = count($links);
                        $part_length = floor($array_length / 10);

                        // 创建一个空数组来保存拆分后的数组
                        $result_array = array();

                        // 循环遍历10次以创建10个部分，并将它们添加到结果数组中
                        for ($i = 0; $i < 10; $i++) {
                            // 如果不是最后一个部分，则使用$part_length作为长度
                            if ($i != 9) {
                                $result_array[$i] = array_slice($links, $i * $part_length, $part_length);
                            } // 如果是最后一个部分，则使用剩余的元素数量作为长度
                            else {
                                $result_array[$i] = array_slice($links, $i * $part_length);
                            }
                        }
                        foreach ($result_array as $key => $value) {
                            $content = '';
                            $index = $key + 1;

                            $content .= $this->_set_sitemap_header();
                            $content .= $this->_set_sitemap_body($value);
                            $content .= $this->_set_sitemap_footer();
                            $storage->put("{$engine}/{$category}_{$index}.xml", $content);
                        }
                        continue;
                    }

                    $content .= $this->_set_sitemap_header();
                    $content .= $this->_set_sitemap_body($links);
                    $content .= $this->_set_sitemap_footer();
                    $storage->put("{$engine}/{$category}.xml", $content);
                }

            }

        }
    }

    /**
     * @param $category
     * @return array
     */
    private function _get_links($category)
    {
        switch ($category) {
            case 'list':
            case 'update_list':
                $articles = Article::select(['articleid', 'lastupdate'])->get();
                return $this->_article_cover_links($articles,);
            case 'sort':
                $sort_orders = Article::select([
                    DB::raw('COUNT(articleid) as total'),
                    'sortid',
                ])->where('sortid', '<>', 0)->groupBy(['sortid'])->get();
                return $this->_sort_cover_links($sort_orders);
                break;
            default:
                return [];
                break;
        }
    }

    private function _sort_cover_links($sort_orders)
    {
        $links = [];

        foreach ($sort_orders as $order) {
            $page_total = ceil($order['total'] / 20);
            for ($i = 1; $i <= $page_total; $i++) {
                $links[] = [
                    'loc' => "{$this->url}/sort/{$order['sortid']}_{$i}/",
                    'lastmod' => date('Y-m-d H:i:s'),
                    'changefreq' => 'daily',
                    'priority' => 0.9
                ];
            }
        }

        return $links;
    }

    private function _article_cover_links($articles)
    {
        $links = [];
        foreach ($articles as $article) {
            $last_update = $article['lastupdate'] ? date('Y-m-d H:i:s', $article['lastupdate']) : date('Y-m-d H:i:s');

            $links[] = [
                'loc' => "{$this->url}/xs/{$article['articleid']}.html",
                'lastmod' => $last_update,
                'changefreq' => 'daily',
                'priority' => 0.9
            ];
        }

        return $links;
    }

    private function _set_sitemap_header()
    {
        $sitemap_start = '<?xml version="1.0" encoding="UTF-8" ?>';

        if ($this->engine == 'baidu') {
            $sitemap_start .= "\r\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
xmlns:mobile="http://www.baidu.com/schemas/sitemap-mobile/1/">';
        } else {
            $sitemap_start .= "\r\n" . '<urlset>';
        }

        return $sitemap_start;
    }

    private function _set_sitemap_body($links)
    {
        $body = '';
        foreach ($links as $link) {
            $body .= "\r\n<url>";
            $body .= "\r\n\t<loc>{$link['loc']}</loc>";
            if ($this->engine == 'baidu') {
                $body .= "\r\n\t<mobile:mobile type=\"{$this->site_type}\" />";
            }
            $body .= "\r\n\t<lastmod>{$link['lastmod']}</lastmod>";
            $body .= "\r\n\t<lastmod>{$link['lastmod']}</lastmod>";
            $body .= "\r\n\t<changefreq>{$link['changefreq']}</changefreq>";
            $body .= "\r\n</url>";
        }


        return $body;

    }

    private function _set_sitemap_footer()
    {
        return "\r\n" . '</urlset>';
    }

}
