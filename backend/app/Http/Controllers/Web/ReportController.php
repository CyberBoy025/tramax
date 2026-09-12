<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ReportsSummaryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\ReportsAdminController — same
// ReportsSummaryService, so both surfaces render identical numbers.
class ReportController extends Controller
{
    public function __construct(private ReportsSummaryService $reports) {}

    public function index(Request $request): View
    {
        return view('admin.reports.index', [
            'summary' => $this->reports->buildFor($request->user()),
        ]);
    }
}
