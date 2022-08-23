<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeBundleView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate index,create,show,edit view files.';

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

        if (!File::exists(resource_path('views/') . $this->argument('name'))) {
            @File::makeDirectory(resource_path('views/' . $this->argument('name')), 0777);
        }
        return collect([

            'create', 'index', 'show', 'edit',
        ])
            ->map(function ($item) {

                return file_put_contents(
                    resource_path('views/' . $this->argument('name') . '/' . $item . '.blade.php'),
                    file_get_contents(app_path('Console/stubs/view.plain.stub'))
                );
            });
    }
}
