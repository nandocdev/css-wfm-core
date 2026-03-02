<?php

declare(strict_types=1);

namespace App\Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Employee\Actions\GetMyEmployeeProfileAction;
use App\Modules\Security\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MyEmployeeProfileController extends Controller {
    public function __construct(
        private GetMyEmployeeProfileAction $getMyEmployeeProfileAction,
    ) {
    }

    public function show(Request $request): View {
        /** @var User|null $user */
        $user = $request->user();
        abort_if($user === null, 403);

        $employee = $this->getMyEmployeeProfileAction->execute((int) $user->id);

        return view('employee::profile.my-profile', [
            'employee' => $employee,
        ]);
    }
}
