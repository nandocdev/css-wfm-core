<?php

declare(strict_types=1);

namespace App\Modules\Organization\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Organization\Actions\CreateDepartmentAction;
use App\Modules\Organization\Actions\CreateDirectorateAction;
use App\Modules\Organization\Actions\CreatePositionAction;
use App\Modules\Organization\Actions\GetOrganizationHierarchyAction;
use App\Modules\Organization\Http\Requests\StoreDepartmentRequest;
use App\Modules\Organization\Http\Requests\StoreDirectorateRequest;
use App\Modules\Organization\Http\Requests\StorePositionRequest;
use App\Modules\Security\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class OrganizationStructureController extends Controller {
    public function __construct(
        private GetOrganizationHierarchyAction $getOrganizationHierarchyAction,
        private CreateDirectorateAction $createDirectorateAction,
        private CreateDepartmentAction $createDepartmentAction,
        private CreatePositionAction $createPositionAction,
    ) {
    }

    public function index(Request $request): View {
        /** @var User|null $user */
        $user = $request->user();

        abort_if($user === null || !$user->hasAnyRole(['Administrador', 'Director', 'Jefe', 'Coordinador']), 403);

        $payload = $this->getOrganizationHierarchyAction->execute();

        return view('organization::structure.index', [
            'directorates' => $payload['directorates'],
            'hierarchyRoots' => $payload['hierarchyRoots'],
            'canManageStructure' => $user->hasRole('Administrador'),
        ]);
    }

    public function storeDirectorate(StoreDirectorateRequest $request): RedirectResponse {
        $this->createDirectorateAction->execute($request->validated());

        return back()->with('status', 'Dirección creada correctamente.');
    }

    public function storeDepartment(StoreDepartmentRequest $request): RedirectResponse {
        $this->createDepartmentAction->execute($request->validated());

        return back()->with('status', 'Departamento creado correctamente.');
    }

    public function storePosition(StorePositionRequest $request): RedirectResponse {
        $this->createPositionAction->execute($request->validated());

        return back()->with('status', 'Cargo creado correctamente.');
    }
}
