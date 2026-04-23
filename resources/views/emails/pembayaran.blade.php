<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Konfirmasi Pembayaran</title>
</head>

<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="620" cellpadding="0" cellspacing="0" style="max-width:620px;width:100%;">

                    {{-- HEADER --}}
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#064e3b 0%,#059669 100%);border-radius:12px 12px 0 0;padding:36px 40px;text-align:center;">
                            <p
                                style="margin:0 0 6px;font-size:12px;color:#a7f3d0;letter-spacing:3px;text-transform:uppercase;font-weight:600;">
                                Konfirmasi Pembayaran</p>
                            <h1 style="margin:0;font-size:26px;font-weight:700;color:#ffffff;line-height:1.3;">💳
                                Pembayaran Diterima</h1>
                            <p style="margin:10px 0 0;font-size:13px;color:#a7f3d0;">
                                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y • H:i') }} WIB
                            </p>
                        </td>
                    </tr>

                    {{-- STATUS PELUNASAN BANNER --}}
                    <tr>
                        <td style="background:#ffffff;padding:0 40px;">
                            @php
                                $status = $data['status_pelunasan'];
                                $bannerBg =
                                    $status === 'paid' ? '#f0fdf4' : ($status === 'partial' ? '#fffbeb' : '#fef2f2');
                                $bannerBorder =
                                    $status === 'paid' ? '#22c55e' : ($status === 'partial' ? '#f59e0b' : '#ef4444');
                                $bannerColor =
                                    $status === 'paid' ? '#166534' : ($status === 'partial' ? '#78350f' : '#991b1b');
                                $bannerIcon = $status === 'paid' ? '✅' : ($status === 'partial' ? '⏳' : '❌');
                                $bannerText =
                                    $status === 'paid'
                                        ? 'Pembayaran telah <strong>LUNAS</strong> sepenuhnya.'
                                        : ($status === 'partial'
                                            ? 'Pembayaran <strong>SEBAGIAN</strong> diterima. Masih terdapat sisa tagihan.'
                                            : 'Pembayaran <strong>BELUM</strong> diterima.');
                            @endphp
                            <div
                                style="background:{{ $bannerBg }};border-left:4px solid {{ $bannerBorder }};border-radius:0 6px 6px 0;padding:14px 18px;margin:24px 0 0;">
                                <p style="margin:0;font-size:14px;color:{{ $bannerColor }};line-height:1.6;">
                                    {{ $bannerIcon }} {!! $bannerText !!}
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- BODY --}}
                    <tr>
                        <td style="background:#ffffff;padding:24px 40px 32px;">

                            {{-- RINGKASAN PEMBAYARAN --}}
                            <p
                                style="margin:0 0 12px;font-size:11px;font-weight:700;color:#059669;letter-spacing:2px;text-transform:uppercase;">
                                💰 Ringkasan Pembayaran
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:28px;">
                                <tr style="background:#f8fafc;">
                                    <th
                                        style="padding:10px 16px;font-size:12px;color:#64748b;font-weight:600;text-align:left;border-bottom:1px solid #e2e8f0;">
                                        Keterangan</th>
                                    <th
                                        style="padding:10px 16px;font-size:12px;color:#64748b;font-weight:600;text-align:right;border-bottom:1px solid #e2e8f0;">
                                        Nominal</th>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f1f5f9;">
                                        Total Nilai PO</td>
                                    <td
                                        style="padding:12px 16px;font-size:13px;color:#374151;text-align:right;border-bottom:1px solid #f1f5f9;">
                                        Rp {{ number_format($data['harga_deal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr style="background:#f8fafc;">
                                    <td
                                        style="padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f1f5f9;">
                                        Pembayaran Termin ke-{{ $data['termin'] }}</td>
                                    <td
                                        style="padding:12px 16px;font-size:13px;color:#059669;font-weight:700;text-align:right;border-bottom:1px solid #f1f5f9;">
                                        + Rp {{ number_format($data['jumlah_bayar'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:12px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f1f5f9;">
                                        Total Terbayar</td>
                                    <td
                                        style="padding:12px 16px;font-size:13px;color:#374151;font-weight:600;text-align:right;border-bottom:1px solid #f1f5f9;">
                                        Rp {{ number_format($data['total_terbayar'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr style="background:{{ $data['sisa_tagihan'] > 0 ? '#fef2f2' : '#f0fdf4' }};">
                                    <td
                                        style="padding:14px 16px;font-size:14px;font-weight:700;color:{{ $data['sisa_tagihan'] > 0 ? '#991b1b' : '#166534' }};">
                                        Sisa Tagihan
                                    </td>
                                    <td
                                        style="padding:14px 16px;font-size:14px;font-weight:700;color:{{ $data['sisa_tagihan'] > 0 ? '#991b1b' : '#166534' }};text-align:right;">
                                        Rp {{ number_format($data['sisa_tagihan'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>

                            {{-- STATUS BADGE --}}
                            <div style="text-align:center;margin-bottom:28px;">
                                @php
                                    $badgeBg =
                                        $status === 'paid'
                                            ? '#dcfce7'
                                            : ($status === 'partial'
                                                ? '#fef9c3'
                                                : '#fee2e2');
                                    $badgeColor =
                                        $status === 'paid'
                                            ? '#166534'
                                            : ($status === 'partial'
                                                ? '#854d0e'
                                                : '#991b1b');
                                    $badgeLabel =
                                        $status === 'paid'
                                            ? 'LUNAS'
                                            : ($status === 'partial'
                                                ? 'SEBAGIAN'
                                                : 'BELUM BAYAR');
                                @endphp
                                <span
                                    style="display:inline-block;padding:8px 28px;border-radius:30px;font-size:14px;font-weight:700;background:{{ $badgeBg }};color:{{ $badgeColor }};letter-spacing:1px;">
                                    Status Pelunasan: {{ $badgeLabel }}
                                </span>
                            </div>

                            <hr style="border:none;border-top:1px solid #f1f5f9;margin:0 0 28px;">

                            {{-- DETAIL PEMBAYARAN --}}
                            <p
                                style="margin:0 0 12px;font-size:11px;font-weight:700;color:#059669;letter-spacing:2px;text-transform:uppercase;">
                                📄 Detail Pembayaran
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                @foreach ([['Nomor PO', $data['nomor_po']], ['Termin', 'Ke-' . $data['termin']], ['Tanggal Pembayaran', \Carbon\Carbon::parse($data['tanggal_pembayaran'])->translatedFormat('d F Y')], ['Metode Pembayaran', ucfirst($data['metode'])]] as $i => [$label, $value])
                                    <tr style="background:{{ $i % 2 === 0 ? '#f8fafc' : '#ffffff' }};">
                                        <td
                                            style="padding:10px 14px;font-size:12px;color:#64748b;font-weight:600;width:38%;">
                                            {{ $label }}</td>
                                        <td
                                            style="padding:10px 14px;font-size:13px;color:#1e293b;font-weight:{{ $label === 'Nomor PO' ? '700' : '400' }};">
                                            {{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>

                            <hr style="border:none;border-top:1px solid #f1f5f9;margin:0 0 28px;">

                            {{-- INFO PERUSAHAAN --}}
                            <p
                                style="margin:0 0 12px;font-size:11px;font-weight:700;color:#059669;letter-spacing:2px;text-transform:uppercase;">
                                🏢 Informasi Perusahaan
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                @foreach ([['Nama Perusahaan', $data['nama_perusahaan']], ['Penanggung Jawab', $data['penanggung_jawab'] ?? '-'], ['Jabatan', $data['jabatan'] ?? '-'], ['Telepon', $data['telepon'] ?? '-'], ['Ruang Lingkup', $data['ruang_lingkup_kerjasama'] ?? '-']] as $i => [$label, $value])
                                    <tr style="background:{{ $i % 2 === 0 ? '#f8fafc' : '#ffffff' }};">
                                        <td
                                            style="padding:10px 14px;font-size:12px;color:#64748b;font-weight:600;width:38%;">
                                            {{ $label }}</td>
                                        <td style="padding:10px 14px;font-size:13px;color:#1e293b;">{{ $value }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            {{-- KETERANGAN --}}
                            @if (!empty($data['keterangan']))
                                <hr style="border:none;border-top:1px solid #f1f5f9;margin:0 0 28px;">
                                <p
                                    style="margin:0 0 12px;font-size:11px;font-weight:700;color:#059669;letter-spacing:2px;text-transform:uppercase;">
                                    📌 Keterangan
                                </p>
                                <div
                                    style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:14px 18px;">
                                    <p style="margin:0;font-size:13px;color:#166534;line-height:1.7;">
                                        {{ $data['keterangan'] }}</p>
                                </div>
                            @endif

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="background:#064e3b;border-radius:0 0 12px 12px;padding:24px 40px;text-align:center;">
                            <p style="margin:0 0 4px;font-size:13px;color:#ffffff;font-weight:600;">
                                {{ config('app.name') }}</p>
                            <p style="margin:0;font-size:11px;color:#6ee7b7;line-height:1.6;">
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
