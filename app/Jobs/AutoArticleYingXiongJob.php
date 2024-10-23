<?php

namespace App\Jobs;

use App\Exceptions\FixChapterException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class AutoArticleYingXiongJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $article_id;

    private $site;

    private $limit;

    public $timeout = 86400;

    public $tries = 1;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($article_id, $site, $limit = 0)
    {
        $this->article_id = $article_id;
        $this->site = $site;
        $this->limit = $limit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Artisan::call("fix:chapter", [
                '--article_id' => $this->article_id,
                '--site' => $this->site,
                '--limit' => $this->limit,
            ]);
        }catch (FixChapterException $e){

        }catch (\Exception $e){
            logger('9999', ['article_id' => $this->article_id, 'site' => $this->site, 'limit' => $this->limit]);
            logger($e);
        }
    }
}
