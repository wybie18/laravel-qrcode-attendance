<?php

namespace App\Http\Controllers;

use App\Exports\PersonnelsExport;
use App\Exports\PersonnelsTemplateExport;
use App\Http\Requests\StorePersonnelRequest;
use App\Http\Requests\UpdatePersonnelRequest;
use App\Http\Resources\OfficeResource;
use App\Http\Resources\PersonnelResource;
use App\Http\Resources\PositionResource;
use App\Imports\PersonnelsImport;
use App\Models\Office;
use App\Models\Personnel;
use App\Models\Position;
use App\Services\PersonnelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PersonnelController extends Controller
{
    public function __construct(public PersonnelService $personnelService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $personnels = Personnel::query()
            ->with(['office', 'position'])
            ->latest('id')
            ->paginate(15);
            
        $offices = Office::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        
        return Inertia::render('personnel/Index', [
            'personnels' => PersonnelResource::collection($personnels),
            'offices' => OfficeResource::collection($offices),
            'positions' => PositionResource::collection($positions),
        ]);
    }

    public function exportMethod(): BinaryFileResponse
    {
        return Excel::download(new PersonnelsExport(), 'personnels.xlsx');
    }

    public function template(): BinaryFileResponse
    {
        return Excel::download(new PersonnelsTemplateExport(), 'personnels-template.xlsx');
    }

    public function importMethod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new PersonnelsImport(), $validated['file']);

        return back()->with('success', 'Personnels imported successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonnelRequest $request): RedirectResponse
    {
        $this->personnelService->store($request->validated());

        return back()->with('success', 'Personnel created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonnelRequest $request, Personnel $personnel): RedirectResponse
    {
        $this->personnelService->update($personnel, $request->validated());

        return back()->with('success', 'Personnel updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Personnel $personnel): RedirectResponse
    {
        $personnel->delete();

        return back()->with('success', 'Personnel deleted successfully.');
    }
}
