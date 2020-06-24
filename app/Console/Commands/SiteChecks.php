<?php

namespace App\Console\Commands;

use App\Notifications\SiteCheck;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

class SiteChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sites:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     */
    public function handle()
    {
        //

        $check_sites = config('gobot.check_sites');
        $sites_array = explode(',', $check_sites);

        $issue_sites = [];
        if (count($sites_array)) {
            foreach ($sites_array as $site_info) {

                $site_data = explode(';', $site_info);
                $url = current($site_data);
                $expected_title = strtolower(end($site_data));

                $dom = new Dom;
                $dom->loadFromUrl($url);
                $title = $dom->find('title')[0];
                $title_text = strtolower($title->text);

                if (!Str::contains($title_text, $expected_title)) {
                    $issue_sites[] = $url;
                }

            }
        }

        if (count($issue_sites)) {

            $issue_sites_text = implode(',', $issue_sites);
            $message = 'Issue with the following ' . Str::plural('site', count($issue_sites)) . ': ' . $issue_sites_text;

            Notification::route('slack', config('gobot.notification.slack_hook'))
                ->notify(new SiteCheck($message));
        }

    }
}
