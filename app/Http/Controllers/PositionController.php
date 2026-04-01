<?php

namespace App\Http\Controllers;

use App\Exports\PositionsExport;
use App\Exports\PositionsTemplateExport;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Imports\PositionsImport;
use App\Http\Resources\PositionResource;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $positions = Position::query()
            ->latest('id')
            ->paginate(6);

        return Inertia::render('positions/Index', [
            'positions' => PositionResource::collection($positions),
        ]);
    }

    public function export(): BinaryFileResponse
    {
        return Excel::download(new PositionsExport(), 'positions.xlsx');
    }

    public function template(): BinaryFileResponse
    {
        return Excel::download(new PositionsTemplateExport(), 'positions-template.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new PositionsImport(), $validated['file']);

        return back()->with('success', 'Positions imported successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request): RedirectResponse
    {
        Position::query()->create($request->validated());

        return back()->with('success', 'Position created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position): JsonResponse
    {
        return response()->json($position);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePositionRequest $request, Position $position): RedirectResponse
    {
        $position->update($request->validated());

        return back()->with('success', 'Position updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return back()->with('success', 'Position deleted successfully.');
    }
}
