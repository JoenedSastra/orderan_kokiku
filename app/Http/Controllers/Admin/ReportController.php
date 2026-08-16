<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportExport;
use App\Http\Controllers\Concerns\HasReportData;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    use HasReportData;

    public function index(Request $request): View
    {
        [$type, $startDate, $endDate] = $this->resolveFilters($request);

        $rows = $this->fetchReportRows($type, $startDate, $endDate);

        return view('admin.reports.index', [
            'type'      => $type,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'rows'      => $rows,
            'headings'  => $this->reportHeadings($type),
            'label'     => $this->reportLabel($type),
        ]);
    }

    public function exportPdf(Request $request)
    {
        [$type, $startDate, $endDate] = $this->resolveFilters($request);

        $rows = $this->fetchReportRows($type, $startDate, $endDate);

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'type'      => $type,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'rows'      => $rows,
            'headings'  => $this->reportHeadings($type),
            'label'     => $this->reportLabel($type),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-' . $type . '-' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        [$type, $startDate, $endDate] = $this->resolveFilters($request);

        return Excel::download(
            new ReportExport($type, $startDate, $endDate),
            'laporan-' . $type . '-' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    private function resolveFilters(Request $request): array
    {
        $type      = $request->input('type', 'barang_masuk');
        
        // Backward compatibility for old URLs
        if ($type === 'total_stock_kitchen') {
            $type = 'barang_keluar_kitchen';
        } elseif ($type === 'total_stock_kasir') {
            $type = 'barang_keluar_kasir';
        }
        
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        return [$type, $startDate, $endDate];
    }
}
