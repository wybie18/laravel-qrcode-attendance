<?php

namespace App\Http\Controllers;

use App\Exports\OfficesExport;
use App\Exports\OfficesTemplateExport;
use App\Http\Requests\StoreOfficeRequest;
use App\Http\Requests\UpdateOfficeRequest;
use App\Imports\OfficesImport;
use App\Http\Resources\OfficeResource;
use App\Models\Office;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $offices = Office::query()
            ->latest('id')
            ->paginate(15);
        
        return Inertia::render('offices/Index', [
            'offices' => OfficeResource::collection($offices),
        ]);
    }

    public function exportMethod(): BinaryFileResponse
    {
        return Excel::download(new OfficesExport(), 'offices.xlsx');
    }

    public function template(): BinaryFileResponse
    {
        return Excel::download(new OfficesTemplateExport(), 'offices-template.xlsx');
    }

    public function importMethod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new OfficesImport(), $validated['file']);

        return back()->with('success', 'Offices imported successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfficeRequest $request): RedirectResponse
    {
        Office::query()->create($request->validated());

        return back()->with('success', 'Office created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficeRequest $request, Office $office): RedirectResponse
    {
        $office->update($request->validated());

        return back()->with('success', 'Office updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Office $office): RedirectResponse
    {
        $office->delete();

        return back()->with('success', 'Office deleted successfully.');
    }
}
