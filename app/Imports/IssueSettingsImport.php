<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class IssueSettingsImport implements WithHeadingRow, WithChunkReading
{

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 500;
    }
}
