<?php

namespace App\Filament\Resources\POS\Pages;

use App\Filament\Resources\POS\POResource;
use App\Mail\StatusKerjasamaMail;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EditPO extends EditRecord
{
    protected static string $resource = POResource::class;

    protected array $statusSebelum = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->statusSebelum = [
            'status_po'        => $this->record->status_po,
            'status_kerjasama' => $this->record->status_kerjasama,
        ];

        unset($data['nomor_po']);

        $originalId = $this->record->id_pengajuan;

        if (isset($data['id_pengajuan']) && $data['id_pengajuan'] != $originalId) {
            abort(403, 'id_pengajuan atau perusahaan tidak sesuai');
        }

        $data['id_pengajuan'] = $originalId;

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record->fresh(); // ← cukup sekali

        // Log aktivitas manual
        activity()
            ->causedBy(Auth::user())
            ->performedOn($record)
            ->withProperties([
                'old'        => $this->statusSebelum,
                'attributes' => [
                    'status_po'        => $record->status_po,
                    'status_kerjasama' => $record->status_kerjasama,
                ],
            ])
            ->log('PO diperbarui');

        // Kirim email jika status berubah
        $statusPoChanged        = $record->status_po !== $this->statusSebelum['status_po'];
        $statusKerjasamaChanged = $record->status_kerjasama !== $this->statusSebelum['status_kerjasama'];

        if ($statusPoChanged || $statusKerjasamaChanged) {
            $this->kirimEmailStatusUpdate($record);
        }
    }

    protected function kirimEmailStatusUpdate($record): void
    {
        try {
            $kerjasama = $record->kerjasama;

            if (!$kerjasama || empty($kerjasama->email)) {
                Log::warning('Email kerjasama tidak ditemukan untuk PO ID: ' . $record->id_po);
                return;
            }

            $data = [
                'nama_perusahaan'         => $kerjasama->nama_perusahaan,
                'alamat_perusahaan'       => $kerjasama->alamat_perusahaan,
                'penanggung_jawab'        => $kerjasama->penanggung_jawab,
                'jabatan'                 => $kerjasama->jabatan,
                'telepon'                 => $kerjasama->telepon,
                'email'                   => $kerjasama->email,
                'ruang_lingkup_kerjasama' => $kerjasama->ruang_lingkup_kerjasama,
                'tanggal_pengajuan'       => $kerjasama->tanggal_pengajuan,
                'status_pengajuan'        => $kerjasama->status_pengajuan,
                'alasan'                  => $kerjasama->alasan,
                'nomor_po'                => $record->nomor_po,
                'versi_po'                => $record->versi_po,
                'tanggal_po'              => $record->tanggal_po,
                'status_po'               => $record->status_po,
                'status_kerjasama'        => $record->status_kerjasama,
                'status_po_lama'          => $this->statusSebelum['status_po'],
                'status_kerjasama_lama'   => $this->statusSebelum['status_kerjasama'],
            ];

            Mail::to($kerjasama->email)->send(new StatusKerjasamaMail($data));

            Notification::make()
                ->title('✅ Email berhasil dikirim ke ' . $kerjasama->email)
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error('Gagal kirim email status PO: ' . $e->getMessage());

            Notification::make()
                ->title('❌ Gagal mengirim email')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil')
            ->body('Purchase Order berhasil diperbarui.')
            ->success();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Save Changes')
                ->icon('heroicon-o-check-circle')
                ->color('primary'),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
