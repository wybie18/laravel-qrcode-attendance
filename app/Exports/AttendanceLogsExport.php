<?php

namespace App\Exports;

use App\Models\AttendanceLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceLogsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    public function collection()
    {
        return AttendanceLog::query()
            ->with(['personnel:id,first_name,middle_name,last_name,email'])
            ->orderByDesc('log_date')
            ->orderByDesc('id')
            ->get([
                'personnel_id',
                'log_date',
                'time_in',
                'time_out',
            ])
            ->map(function (AttendanceLog $attendanceLog): array {
                return [
                    'Last Name' => $attendanceLog->personnel?->last_name,
                    'First Name' => $attendanceLog->personnel?->first_name,
                    'Middle Name' => $attendanceLog->personnel?->middle_name,
                    'Email' => $attendanceLog->personnel?->email,
                    'Date' => $attendanceLog->log_date?->format('F d, Y'),
                    'Time In' => $attendanceLog->time_in?->format('g:i A'),
                    'Time Out' => $attendanceLog->time_out?->format('g:i A'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Last Name',
            'First Name',
            'Middle Name',
            'Email',
            'Date',
            'Time In',
            'Time Out',
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
