<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Refresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Full Cache Refresh';

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
        // Clear various laravel caches
        if ( ! is_file(storage_path('framework/down')))
            $this->call('opcache:clear');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('cache:clear');
        $this->call('view:clear');
    }

}
