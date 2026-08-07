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
        return $this->fetchReportRows($this->type, $this->startDate, $this->endDate)
            ->map(fn ($row) => array_values($row));
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return $this->reportHeadings($this->type);
    }

    public function title(): string
    {
        return $this->reportLabel($this->type);
    }
}
