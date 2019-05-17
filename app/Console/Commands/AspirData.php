<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AspirData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aspir:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Importable CSV Data';

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
        $aspir = new \App\Models\Aspir\Aspir($this);
        $aspir->collectData();
    }
}
