<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReportExportController extends Controller
{
    /**
     * Excel dışa aktarımı (maskeli/maskesiz) – sade log formatı.
     */
    public function export(Request $request)
    {
        try {
            // Alanları hazırla
            $fields = $request->input('fields', []);
            if (is_string($fields)) {
                $fields = explode(',', $fields);
            }
            $fields = array_values(array_filter(array_map('trim', $fields)));

            $dateFilter = $request->input('date_filter', '');
            $sortOrder  = $request->input('sort_order', 'desc');
            $unmasked   = $request->boolean('unmasked');

            // Geçersiz sort_order uyarısı
            if (!in_array($sortOrder, ['asc', 'desc'], true)) {
                Log::channel('admin')->warning('Geçersiz sıralama', $this->logContext([
                    'action'  => 'excel_export',
                    'status'  => 'warning',
                    'message' => 'sort_order geçersiz, desc olarak değiştirildi',
                    'given'   => $sortOrder,
                ]));
                $sortOrder = 'desc';
            }

            // Boş alan listesi uyarısı
            if (empty($fields)) {
                Log::channel('admin')->warning('Dışarı aktarma alanları boş', $this->logContext([
                    'action'  => 'excel_export',
                    'status'  => 'warning',
                    'message' => 'Alan listesi boş, varsayılan alanlar kullanılacak',
                ]));
            }

            // Tek başarı logu
            Log::channel('admin')->info('Excel raporu tamamlandı', $this->logContext([
                'action'      => 'excel_export',
                'status'      => 'success',
                'fields'      => $fields,
                'date_filter' => $dateFilter,
                'sort_order'  => $sortOrder,
                'unmasked'    => $unmasked,
            ]));

            return Excel::download(
                new ReportExport($fields, $dateFilter, $sortOrder, $unmasked),
                'rapor.xlsx'
            );

        } catch (Throwable $e) {
            Log::channel('admin')->error('Excel raporu oluşturulamadı', $this->logContext([
                'action'  => 'excel_export',
                'status'  => 'failed',
                'message' => $e->getMessage(),
            ]));
            throw $e;
        }
    }

    /**
     * Ortak log context bilgisi.
     */
    private function logContext(array $extra = []): array
    {
        $user = auth()->user();

        return array_merge([
            'user_id'  => $user->id ?? null,
            'username' => $user->username ?? 'Anonim',
            'ip'       => request()->ip(),
            'time'     => now()->toDateTimeString(),
        ], $extra);
    }
}
