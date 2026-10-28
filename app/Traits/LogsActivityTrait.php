<?php

namespace App\Traits;

use App\LogWebsite;
use Illuminate\Support\Facades\Auth;

trait LogsActivityTrait
{
    /**
     * Boot the trait to listen for model events.
     */
    public static function bootLogsActivityTrait()
    {
        static::created(function ($model) {
            $model->logAction('CREATE', 'Menambahkan data ' . class_basename($model) . ' baru.');
        });

        static::updated(function ($model) {
            $model->logAction('UPDATE', 'Mengubah data ' . class_basename($model) . '.');
        });

        static::deleted(function ($model) {
            $model->logAction('DELETE', 'Menghapus data ' . class_basename($model) . '.');
        });
    }

    /**
     * Handle the logging action.
     *
     * @param string $action
     * @param string $keterangan
     * @return void
     */
    protected function logAction($action, $keterangan)
    {
        // Add identifier (name, title, or ID) to description if available
        $identifier = $this->name ?? $this->title ?? $this->code ?? $this->id ?? null;
        if ($identifier) {
            $keterangan .= " ($identifier)";
        }

        // Only log if there's an authenticated user
        if (Auth::check()) {
            LogWebsite::create([
                'users_id' => Auth::id(),
                'action' => $action,
                'keterangan' => $keterangan,
            ]);
        }
    }
}
