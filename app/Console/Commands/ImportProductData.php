<?php

namespace App\Console\Commands;

use App\Imports\ImportExcel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportProductData extends Command
{
    /**
     * The name and signature of the console command.
     * @param fileName csv file path
     * @param isTestMode 1 or 0, for 1 it will not insert any data in table
     * @var string
     */
    protected $signature = 'app:import-product-data {fileName} {isTestMode?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process products CSV file data and insert into table';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->components->info('File processing started.');

        $file = $this->argument('fileName');
        $isTestMode = $this->argument('isTestMode') ?? 0;

        if (!Storage::disk('local')->exists('uploads/'.$file)) {
            $this->components->error('The specified file does not exist.');
            return;
        }

        Excel::import(new ImportExcel($this->components, $isTestMode), Storage::disk('local')->path('uploads/'.$file));
    }
}
