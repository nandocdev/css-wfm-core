<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Actions\BuildAnalyticsMonitoringDataAction;
use App\Modules\Analytics\Actions\ExportAnalyticsReportAction;
use App\Modules\Analytics\Http\Requests\ExportAnalyticsMonitoringRequest;
use App\Modules\Analytics\Http\Requests\IndexAnalyticsMonitoringRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

final class AnalyticsMonitoringController extends Controller {
    public function __construct(
        private BuildAnalyticsMonitoringDataAction $buildAnalyticsMonitoringDataAction,
        private ExportAnalyticsReportAction $exportAnalyticsReportAction,
    ) {
    }

    public function index(IndexAnalyticsMonitoringRequest $request): View {
        $user = $request->user();
        abort_if($user === null, 403);

        return view('analytics::monitoring.index', $this->buildAnalyticsMonitoringDataAction->execute((int) $user->id, $request->validated()));
    }

    public function export(ExportAnalyticsMonitoringRequest $request): Response {
        $user = $request->user();
        abort_if($user === null, 403);

        $dataset = $this->buildAnalyticsMonitoringDataAction->execute((int) $user->id, $request->validated());

        return $this->exportAnalyticsReportAction->execute(
            $dataset,
            (string) $request->validated('report_type'),
            (string) $request->validated('format'),
        );
    }
}
