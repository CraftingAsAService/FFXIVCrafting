<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AspirMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aspir:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate and Import built CSV Data';

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
        $this->call('migrate:refresh');
        $this->call('db:seed', ['--class' => 'AspirSeeder']);
    }
}
