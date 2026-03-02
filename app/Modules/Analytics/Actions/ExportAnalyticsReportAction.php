<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Actions;

use Symfony\Component\HttpFoundation\Response;

final readonly class ExportAnalyticsReportAction {
    /**
     * @param array<string, mixed> $dataset
     */
    public function execute(array $dataset, string $reportType, string $format): Response {
        $rows = match ($reportType) {
            'executive' => (array) ($dataset['executiveRows'] ?? []),
            'management' => (array) ($dataset['managementRows'] ?? []),
            'coordinator' => (array) ($dataset['coordinatorRows'] ?? []),
            default => [],
        };

        $extension = $format === 'excel' ? 'xls' : 'csv';
        $delimiter = $format === 'excel' ? "\t" : ',';
        $mime = $format === 'excel' ? 'application/vnd.ms-excel' : 'text/csv';
        $filename = sprintf('analytics_%s_%s.%s', $reportType, now()->format('Ymd_His'), $extension);

        return response()->streamDownload(function () use ($rows, $delimiter): void {
            $output = fopen('php://output', 'w');

            if ($rows === []) {
                fputcsv($output, ['sin_datos'], $delimiter);
                fclose($output);
                return;
            }

            $headers = array_keys((array) $rows[0]);
            fputcsv($output, $headers, $delimiter);

            foreach ($rows as $row) {
                $values = array_map(static fn($value): string => (string) $value, array_values((array) $row));
                fputcsv($output, $values, $delimiter);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => $mime,
        ]);
    }
}
