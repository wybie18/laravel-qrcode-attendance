<?php

namespace App\Exports;

use App\Models\Personnel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PersonnelsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    public function collection()
    {
        return Personnel::query()
            ->with(['office:id,name,category', 'position:id,name'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get([
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'phone_number',
                'office_id',
                'position_id',
                'qr_code',
            ])
            ->map(function (Personnel $personnel): array {
                return [
                    'first_name' => $personnel->first_name,
                    'middle_name' => $personnel->middle_name,
                    'last_name' => $personnel->last_name,
                    'email' => $personnel->email,
                    'phone_number' => $personnel->phone_number,
                    'office' => $personnel->office?->name,
                    'office_category' => $personnel->office?->category,
                    'position' => $personnel->position?->name,
                    'qr_code' => $personnel->qr_code,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'phone_number',
            'office',
            'office_category',
            'position',
            'qr_code'
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F2937'],
            ],
            'alignment' => [
                'horizontal' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ]);

        return [];
    }
}
