<?php

namespace App\Jobs;

use App\Enums\QueueNameEnum;
use App\Exceptions\FixChapterException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class AutoArticleMayiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 86400;

    private $article_id;

    private $site;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($article_id, $site)
    {
        $this->article_id = $article_id;
        $this->site = $site;
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
            ]);
        } catch (FixChapterException $e) {
        }

    }
}