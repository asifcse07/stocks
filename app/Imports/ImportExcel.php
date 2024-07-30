<?php

namespace App\Imports;

use App\Models\ProductData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Console\View\Components\Factory;

class ImportExcel implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
    use Importable;


    protected Factory $command;
    protected int $isTestMode;

    public function __construct(Factory $command, int $isTestMode)
    {
        $this->command = $command;
        $this->isTestMode = $isTestMode;
    }

    /**
     * @param collection $rows
     */
    public function collection(Collection $rows): void
    {
        $errors = [];
        $totalProduct = $rows->count();
        foreach ($rows->toArray() as $row) {
//            if(!is_int($row['stock'])){
//                $errors[$row['product_code']] = $row['product_name'] . ' stock value is not integer';
//            }
            $row['stock'] = (int) $row['stock'];
            $row['cost_in_gbp'] = $this->stringToFloat($row['cost_in_gbp']);
            if($row['cost_in_gbp'] < 5 && $row['stock'] < 10){
                $errors[] = $row['product_name'] . ' skipped, cause it\'s stock value is less than 10 and cost is less than 5.';
                $this->command->error($row['product_name'] . ' skipped.');
                continue;
            }
            if($row['cost_in_gbp'] > 1000){
                $errors[] = $row['product_name'] . ' skipped, cause it\'s cost is greater than 1000.';
                $this->command->error($row['product_name'] . ' skipped.');
                continue;
            }
            //add data in tblProductData
            if($this->isTestMode == 0){
                ProductData::add($row);
            }
            $this->command->info($row['product_name'] . ' added.');
        }

        if($this->isTestMode == 1){
            $this->command->warn('Script in test mode, no product added into table.');
        }

        $this->command->twoColumnDetail('FILE PROCESS: ', '<fg=green;options=bold>DONE</>');

        $errors = collect($errors);
        $this->command->info('Total ' . ($rows->count() - $errors->count()) . ' product added and ' . $errors->count() . ' skipped.');
        $errors->each(function ($message){
            $this->command->error($message);
            Log::channel('custom')->info($message);
        });
        $this->command->alert(' Check the custom log file in storage/log folder for detail report. Thank you');
    }

    /**
     * it will handle all the csv related configuration like special character, delimiter etc
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => PHP_EOL,
            'use_bom' => true,
            'include_separator_line' => false,
            'excel_compatibility' => false,
        ];
    }

    /**
     * @return string|array
     */
    public function uniqueBy(): array|string
    {
        return 'strProductCode';
    }

    //remove currency from cost
    public function stringToFloat($numberString): float
    {
        return floatval(preg_replace("/[^0-9.]/", '', $numberString));
    }
}
