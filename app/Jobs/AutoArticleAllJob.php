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

class AutoArticleAllJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $article_id;


    public $timeout = 86400;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($article_id)
    {
        $this->article_id = $article_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $configs = config("spider");
            $sites = array_keys($configs);
            foreach ($sites as $site){
                Artisan::call("push:article", [
                    '--article_ids' => $this->article_id,
                    '--site' => $site,
                    '--limit' => 30,
                ]);
            }
        }catch (FixChapterException $e){

        }

    }
}