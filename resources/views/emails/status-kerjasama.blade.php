<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Status Kerjasama</title>
</head>

<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="620" cellpadding="0" cellspacing="0" style="max-width:620px;width:100%;">

                    {{-- ======= HEADER ======= --}}
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#1e3a5f 0%,#2d6a9f 100%);border-radius:12px 12px 0 0;padding:36px 40px;text-align:center;">
                            <p
                                style="margin:0 0 6px 0;font-size:12px;color:#a8c8e8;letter-spacing:3px;text-transform:uppercase;font-weight:600;">
                                Notifikasi Sistem</p>
                            <h1 style="margin:0;font-size:26px;font-weight:700;color:#ffffff;line-height:1.3;">Update
                                Status Kerjasama</h1>
                            <p style="margin:10px 0 0;font-size:13px;color:#a8c8e8;">
                                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y • H:i') }} WIB
                            </p>
                        </td>
                    </tr>

                    {{-- ======= ALERT BANNER ======= --}}
                    <tr>
                        <td style="background:#ffffff;padding:0 40px;">
                            <div
                                style="background:#eff6ff;border-left:4px solid #2d6a9f;border-radius:0 6px 6px 0;padding:14px 18px;margin:24px 0 0;">
                                <p style="margin:0;font-size:14px;color:#1e3a5f;line-height:1.6;">
                                    Terdapat <strong>perubahan status</strong> pada kerjasama dengan
                                    <strong>{{ $data['nama_perusahaan'] }}</strong>.
                                    Berikut adalah ringkasan informasi terkini.
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- ======= BODY ======= --}}
                    <tr>
                        <td style="background:#ffffff;padding:24px 40px 32px;">

                            {{-- PERUBAHAN STATUS --}}
                            <p
                                style="margin:0 0 12px;font-size:11px;font-weight:700;color:#2d6a9f;letter-spacing:2px;text-transform:uppercase;">
                                🔄 Perubahan Status
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:28px;">
                                <tr style="background:#f8fafc;">
                                    <th
                                        style="padding:10px 16px;font-size:12px;color:#64748b;font-weight:600;text-align:left;border-bottom:1px solid #e2e8f0;">
                                        Jenis Status</th>
                                    <th
                                        style="padding:10px 16px;font-size:12px;color:#64748b;font-weight:600;text-align:center;border-bottom:1px solid #e2e8f0;">
                                        Sebelum</th>
                                    <th
                                        style="padding:10px 16px;font-size:12px;color:#64748b;font-weight:600;text-align:center;border-bottom:1px solid #e2e8f0;">
                                        Sesudah</th>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:12px 16px;font-size:13px;color:#374151;font-weight:600;border-bottom:1px solid #f1f5f9;">
                                        Status PO</td>
                                    <td style="padding:12px 16px;text-align:center;border-bottom:1px solid #f1f5f9;">
                                        @php $spLama = strtolower($data['status_po_lama'] ?? ''); @endphp
                                        <span
                                            style="
                                        display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:600;
                                        background:{{ $spLama === 'draft' ? '#fef9c3' : ($spLama === 'diajukan' ? '#dbeafe' : ($spLama === 'revisi' ? '#ffedd5' : ($spLama === 'final' ? '#dcfce7' : '#fee2e2'))) }};
                                        color:{{ $spLama === 'draft' ? '#854d0e' : ($spLama === 'diajukan' ? '#1e40af' : ($spLama === 'revisi' ? '#9a3412' : ($spLama === 'final' ? '#166534' : '#991b1b'))) }};
                                    ">{{ ucfirst($data['status_po_lama'] ?? '-') }}</span>
                                    </td>
                                    <td style="padding:12px 16px;text-align:center;border-bottom:1px solid #f1f5f9;">
                                        @php $spBaru = strtolower($data['status_po'] ?? ''); @endphp
                                        <span
                                            style="
                                        display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;
                                        background:{{ $spBaru === 'draft' ? '#fef9c3' : ($spBaru === 'diajukan' ? '#dbeafe' : ($spBaru === 'revisi' ? '#ffedd5' : ($spBaru === 'final' ? '#dcfce7' : '#fee2e2'))) }};
                                        color:{{ $spBaru === 'draft' ? '#854d0e' : ($spBaru === 'diajukan' ? '#1e40af' : ($spBaru === 'revisi' ? '#9a3412' : ($spBaru === 'final' ? '#166534' : '#991b1b'))) }};
                                        box-shadow:0 0 0 2px {{ $spBaru === 'draft' ? '#fde047' : ($spBaru === 'diajukan' ? '#93c5fd' : ($spBaru === 'revisi' ? '#fdba74' : ($spBaru === 'final' ? '#86efac' : '#fca5a5'))) }};
                                    ">{{ ucfirst($data['status_po'] ?? '-') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;font-size:13px;color:#374151;font-weight:600;">Status
                                        Kerjasama</td>
                                    <td style="padding:12px 16px;text-align:center;">
                                        @php $skLama = strtolower($data['status_kerjasama_lama'] ?? ''); @endphp
                                        <span
                                            style="
                                        display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:600;
                                        background:{{ $skLama === 'negosiasi' ? '#dbeafe' : ($skLama === 'deal' ? '#dcfce7' : ($skLama === 'batal' ? '#fee2e2' : ($skLama === 'proses' ? '#fef9c3' : '#f0fdf4'))) }};
                                        color:{{ $skLama === 'negosiasi' ? '#1e40af' : ($skLama === 'deal' ? '#166534' : ($skLama === 'batal' ? '#991b1b' : ($skLama === 'proses' ? '#854d0e' : '#14532d'))) }};
                                    ">{{ ucfirst($data['status_kerjasama_lama'] ?? '-') }}</span>
                                    </td>
                                    <td style="padding:12px 16px;text-align:center;">
                                        @php $skBaru = strtolower($data['status_kerjasama'] ?? ''); @endphp
                                        <span
                                            style="
                                        display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;
                                        background:{{ $skBaru === 'negosiasi' ? '#dbeafe' : ($skBaru === 'deal' ? '#dcfce7' : ($skBaru === 'batal' ? '#fee2e2' : ($skBaru === 'proses' ? '#fef9c3' : '#f0fdf4'))) }};
                                        color:{{ $skBaru === 'negosiasi' ? '#1e40af' : ($skBaru === 'deal' ? '#166534' : ($skBaru === 'batal' ? '#991b1b' : ($skBaru === 'proses' ? '#854d0e' : '#14532d'))) }};
                                        box-shadow:0 0 0 2px {{ $skBaru === 'negosiasi' ? '#93c5fd' : ($skBaru === 'deal' ? '#86efac' : ($skBaru === 'batal' ? '#fca5a5' : ($skBaru === 'proses' ? '#fde047' : '#bbf7d0'))) }};
                                    ">{{ ucfirst($data['status_kerjasama'] ?? '-') }}</span>
                                    </td>
                                </tr>
                            </table>

                            {{-- DIVIDER --}}
                            <hr style="border:none;border-top:1px solid #f1f5f9;margin:0 0 28px;">

                            {{-- INFO PERUSAHAAN --}}
                            <p
                                style="margin:0 0 12px;font-size:11px;font-weight:700;color:#2d6a9f;letter-spacing:2px;text-transform:uppercase;">
                                🏢 Informasi Perusahaan
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                @foreach ([['Nama Perusahaan', $data['nama_perusahaan'] ?? '-'], ['Penanggung Jawab', $data['penanggung_jawab'] ?? '-'], ['Jabatan', $data['jabatan'] ?? '-'], ['Telepon', $data['telepon'] ?? '-'], ['Email', $data['email'] ?? '-'], ['Ruang Lingkup', $data['ruang_lingkup_kerjasama'] ?? '-']] as $i => [$label, $value])
                                    <tr style="background:{{ $i % 2 === 0 ? '#f8fafc' : '#ffffff' }};">
                                        <td
                                            style="padding:10px 14px;font-size:12px;color:#64748b;font-weight:600;width:38%;border-radius:4px;">
                                            {{ $label }}</td>
                                        <td style="padding:10px 14px;font-size:13px;color:#1e293b;">{{ $value }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            {{-- DIVIDER --}}
                            <hr style="border:none;border-top:1px solid #f1f5f9;margin:0 0 28px;">

                            {{-- INFO PO --}}
                            <p
                                style="margin:0 0 12px;font-size:11px;font-weight:700;color:#2d6a9f;letter-spacing:2px;text-transform:uppercase;">
                                📄 Informasi Purchase Order
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                @foreach ([['Nomor PO', $data['nomor_po'] ?? '-'], ['Versi PO', 'v' . ($data['versi_po'] ?? '1')], ['Tanggal PO', !empty($data['tanggal_po']) ? \Carbon\Carbon::parse($data['tanggal_po'])->translatedFormat('d F Y') : '-'], ['Tanggal Pengajuan', !empty($data['tanggal_pengajuan']) ? \Carbon\Carbon::parse($data['tanggal_pengajuan'])->translatedFormat('d F Y') : '-']] as $i => [$label, $value])
                                    <tr style="background:{{ $i % 2 === 0 ? '#f8fafc' : '#ffffff' }};">
                                        <td
                                            style="padding:10px 14px;font-size:12px;color:#64748b;font-weight:600;width:38%;border-radius:4px;">
                                            {{ $label }}</td>
                                        <td
                                            style="padding:10px 14px;font-size:13px;color:#1e293b;font-weight:{{ $label === 'Nomor PO' ? '700' : '400' }};">
                                            {{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>

                            {{-- CATATAN (kondisional) --}}
                            @if (!empty($data['alasan']))
                                <hr style="border:none;border-top:1px solid #f1f5f9;margin:0 0 28px;">
                                <p
                                    style="margin:0 0 12px;font-size:11px;font-weight:700;color:#2d6a9f;letter-spacing:2px;text-transform:uppercase;">
                                    📌 Catatan / Alasan
                                </p>
                                <div
                                    style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:14px 18px;">
                                    <p style="margin:0;font-size:13px;color:#78350f;line-height:1.7;">
                                        {{ $data['alasan'] }}</p>
                                </div>
                            @endif

                        </td>
                    </tr>

                    {{-- ======= FOOTER ======= --}}
                    <tr>
                        <td style="background:#1e3a5f;border-radius:0 0 12px 12px;padding:24px 40px;text-align:center;">
                            <p style="margin:0 0 4px;font-size:13px;color:#ffffff;font-weight:600;">
                                {{ config('app.name') }}</p>
                            <p style="margin:0;font-size:11px;color:#7096b8;line-height:1.6;">
                                Email ini dikirim secara otomatis oleh sistem.<br>Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
