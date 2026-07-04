<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PDFService
{
    /**
     * Generate a PDF report for a completed test attempt.
     */
    public function generateCareerReport($attempt)
    {
        $data = [
            'attempt' => $attempt,
            'user' => $attempt->user ?? auth()->user(),
            'test' => $attempt->test,
            'date' => now()->format('d M Y')
        ];

        $pdf = Pdf::loadView('student.reports.pdf', $data);
        return $pdf->download('RiseStar_AI_Career_Report_' . $attempt->id . '.pdf');
    }
}
