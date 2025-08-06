<?php

namespace App\Services;

use App\Repositories\VisitorRepository;
use Illuminate\Support\Facades\Log;

class VisitorService
{
    protected VisitorRepository $repo;

    public function __construct(VisitorRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Yeni ziyaretçi kaydının oluşturulması ve loglanması
     */ 
    public function store(array $data, $user)
    {
        try {
            // Kayıt işlemi yapılır
            $this->repo->createVisit($data, $user);

            // Log kaydı
            Log::channel('visitor')->info('Ziyaret kaydı oluşturuldu', [
                'user' => $user->username,
                'tc' => $data['tc_no'],
                'ip' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // GENEL HATA LOGU
            Log::channel('visitor')->error('Ziyaret kaydı sırasında hata oluştu', [
                'message' => $e->getMessage(),
                'user' => $user->username,
                'ip' => request()->ip(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e; 
        }
    }

    /**
     * Var olan kaydın güncellenmesi ve loglanması
     */
    public function update($id, array $data, $user)
    {
        try {
            // Güncelleme işlemi yapılır
            $this->repo->updateVisit($id, $data);
            
            // Log kaydı
            Log::channel('visitor')->info('Ziyaret kaydı güncellendi', [
                'user' => $user->username,
                'tc' => $data['tc_no'],
                'ip' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // GENEL HATA LOGU
            Log::channel('visitor')->error('Ziyaret güncelleme sırasında hata oluştu', [
                'message' => $e->getMessage(),
                'user' => $user->username,
                'ip' => request()->ip(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * TC' ye bağlı kullanıcının getirilmesi
     */
    public function getVisitorData($tc, $user)
    {
        $data = $this->repo->getVisitorDataByTc($tc);
        return response()->json($data);
    }
}
