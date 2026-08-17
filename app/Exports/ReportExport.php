<?php

namespace App\Exports;

use App\Http\Controllers\Concerns\HasReportData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    use HasReportData;

    public function __construct(
        protected string $type,
        protected string $startDate,
        protected string $endDate
    ) {
    }

    public function collection()
    {
        $rows = $this->fetchReportRows($this->type, $this->startDate, $this->endDate);
        
        $mappedRows = [];
        $i = 1;
        foreach ($rows as $row) {
            $mappedRows[] = array_merge([$i++], array_values($row));
        }
        
        return collect($mappedRows);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return array_merge(['No'], $this->reportHeadings($this->type));
    }

    public function title(): string
    {
        return $this->reportLabel($this->type);
    }
}
