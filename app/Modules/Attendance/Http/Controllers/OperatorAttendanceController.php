<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Actions\GetMyAttendanceDataAction;
use App\Modules\Attendance\Http\Requests\AttendanceHistoryFilterRequest;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

final class OperatorAttendanceController extends Controller {
    public function __construct(
        private GetMyAttendanceDataAction $getMyAttendanceDataAction,
    ) {
    }

    public function index(AttendanceHistoryFilterRequest $request): View {
        $user = $request->user();
        abort_if($user === null, 403);

        $fromDate = (string) ($request->validated('from_date') ?? CarbonImmutable::today()->subDays(30)->toDateString());
        $toDate = (string) ($request->validated('to_date') ?? CarbonImmutable::today()->toDateString());

        $payload = $this->getMyAttendanceDataAction->execute((int) $user->id, $fromDate, $toDate);

        return view('attendance::operator.index', [
            ...$payload,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);
    }
}
