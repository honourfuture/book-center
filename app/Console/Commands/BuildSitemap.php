<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

class BuildSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '构建Sitemap';

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
        /** @var SitemapService $sitemapService */
        $sitemapService = app('SitemapService');
        $sitemapService->build_update_sitemap();
    }

}
