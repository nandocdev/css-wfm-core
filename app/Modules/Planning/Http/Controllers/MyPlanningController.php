<?php

declare(strict_types=1);

namespace App\Modules\Planning\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Planning\Actions\GetMyCurrentScheduleAction;
use App\Modules\Planning\Actions\GetMyExceptionsAction;
use App\Modules\Planning\Actions\GetMyScheduleHistoryAction;
use App\Modules\Security\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MyPlanningController extends Controller {
    public function __construct(
        private GetMyCurrentScheduleAction $getMyCurrentScheduleAction,
        private GetMyScheduleHistoryAction $getMyScheduleHistoryAction,
        private GetMyExceptionsAction $getMyExceptionsAction,
    ) {
    }

    public function current(Request $request): View {
        /** @var User|null $user */
        $user = $request->user();
        abort_if($user === null, 403);

        $payload = $this->getMyCurrentScheduleAction->execute((int) $user->id);

        return view('planning::operator.current', $payload);
    }

    public function history(Request $request): View {
        /** @var User|null $user */
        $user = $request->user();
        abort_if($user === null, 403);

        $history = $this->getMyScheduleHistoryAction->execute((int) $user->id);

        return view('planning::operator.history', [
            'history' => $history,
        ]);
    }

    public function exceptions(Request $request): View {
        /** @var User|null $user */
        $user = $request->user();
        abort_if($user === null, 403);

        $payload = $this->getMyExceptionsAction->execute((int) $user->id);

        return view('planning::operator.exceptions', $payload);
    }
}
