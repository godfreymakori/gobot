<?php

namespace App\Console\Commands;

use App\Notifications\CommitCount;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

class CountCommits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count:commits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count commit of a github profile and notify';

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
     * @return mixed
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\CurlException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    public function handle()
    {
        $github_profile = config('gobot.github_profile');

        $dom = new Dom;
        $dom->loadFromUrl($github_profile);
        $day_counts = $dom->getElementsByTag('.day');
        $last_day_index = count($day_counts) - 1;
        $commit_count = $dom->find('rect[data-count]', $last_day_index)->{'data-count'};

        $message = $commit_count . ' ' . Str::plural('commit', $commit_count) . ' for ' . Carbon::now()->format('M j').' as at '.Carbon::now()->format('g:i A');

        Notification::route('slack', config('gobot.notification.slack_hook'))
            ->notify(new CommitCount($message));

    }
}
