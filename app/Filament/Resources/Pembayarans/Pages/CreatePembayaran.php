<?php

namespace App\Filament\Resources\Pembayarans\Pages;

use App\Filament\Resources\Pembayarans\PembayaranResource;
use App\Mail\PembayaranMail;
use App\Models\POModel;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreatePembayaran extends CreateRecord
{
    protected static string $resource = PembayaranResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $valid = POModel::where('id_po', $data['id_po'])
            ->where('status_kerjasama', 'selesai')
            ->where('status_po', 'final')
            ->exists();

        if (!$valid) {
            abort(403, 'PO tidak valid atau belum memenuhi syarat pembayaran');
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->kirimEmailPembayaran($this->record);
    }

    protected function kirimEmailPembayaran($record): void
    {
        try {
            $po        = $record->po;
            $kerjasama = $po?->kerjasama;

            if (!$kerjasama || empty($kerjasama->email)) {
                Log::warning('Email kerjasama tidak ditemukan untuk Pembayaran ID: ' . $record->id_pembayaran);
                return;
            }

            // Hitung status pelunasan otomatis
            $totalBayar = \App\Models\PembayaranModel::where('id_po', $record->id_po)
                ->sum('jumlah_bayar');

            $hargaDeal  = $po->harga_deal;
            $sisa       = max(0, $hargaDeal - $totalBayar);

            $statusPelunasan = match (true) {
                $totalBayar <= 0            => 'unpaid',
                $totalBayar >= $hargaDeal   => 'paid',
                default                     => 'partial',
            };

            $data = [
                // Dari tb_pengajuan_kerjasama
                'nama_perusahaan'         => $kerjasama->nama_perusahaan,
                'penanggung_jawab'        => $kerjasama->penanggung_jawab,
                'jabatan'                 => $kerjasama->jabatan,
                'telepon'                 => $kerjasama->telepon,
                'email'                   => $kerjasama->email,
                'ruang_lingkup_kerjasama' => $kerjasama->ruang_lingkup_kerjasama,

                // Dari tb_po
                'nomor_po'                => $po->nomor_po,
                'harga_deal'              => $hargaDeal,

                // Dari tb_pembayaran
                'termin'                  => $record->termin,
                'jumlah_bayar'            => $record->jumlah_bayar,
                'tanggal_pembayaran'      => $record->tanggal_pembayaran,
                'metode'                  => $record->metode,
                'keterangan'              => $record->keterangan,

                // Kalkulasi
                'total_terbayar'          => $totalBayar,
                'sisa_tagihan'            => $sisa,
                'status_pelunasan'        => $statusPelunasan,
            ];

            Mail::to($kerjasama->email)->send(new PembayaranMail($data));

            Notification::make()
                ->title('✅ Email konfirmasi pembayaran dikirim ke ' . $kerjasama->email)
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error('Gagal kirim email pembayaran: ' . $e->getMessage());

            Notification::make()
                ->title('❌ Gagal mengirim email pembayaran')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil')
            ->body('Pembayaran berhasil ditambahkan.')
            ->success();
    }

    protected function getFormActions(): array
    {
        return [

            $this->getCreateFormAction()
                ->label('Create')
                ->icon('heroicon-o-check-circle')
                ->color('primary'),

            $this->getCreateAnotherFormAction()
                ->label('Create & Create Another')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->icon('heroicon-o-x-mark')
                ->color('gray'),

        ];
    }
}
