<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Ujian - Ruang {{ $ruang->nama_ruang }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #e2e8f0; }

        /* ─── Toolbar ───────────────────────────────── */
        .toolbar {
            display: flex;
            justify-content: center;
            padding: 16px;
        }
        .btn-print {
            background: #4f46e5;
            color: #fff;
            font-weight: 700;
            padding: 10px 28px;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(79,70,229,.3);
        }
        .btn-print:hover { background: #4338ca; }

        /*
         * A4 portrait: 210mm × 297mm
         * @page margin: 5mm all sides
         * Usable: 200mm × 287mm
         *
         * Layout: 2 cols × 4 rows = 8 cards
         * Gap: 4mm
         * Card W: (200 - 4) / 2 = 98mm
         * Card H: (287 - 3×4) / 4 = (287-12)/4 = 68.75mm ≈ 69mm
         * Ratio: 98/69 ≈ 1.42  → landscape-style card ✓
         */

        .page {
            width: 200mm;
            height: 287mm;
            margin: 20mm auto 24px;
            background: white;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(4, 1fr);
            gap: 4mm;
            overflow: hidden;
            box-shadow: 0 6px 32px rgba(0,0,0,.18);
        }

        /* ─── Single card ───────────────────────────── */
        .card {
            border: 1.5px solid #c7d2fe;
            border-radius: 4mm;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #fff;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .card.empty {
            border-style: dashed;
            border-color: #e2e8f0;
            background: #fafafa;
        }

        /* ── Kop (mengikuti daftar hadir) ──────────── */
        .card-kop {
            padding: 2mm 2.5mm 1mm;
            display: flex;
            align-items: center;
            gap: 2mm;
            flex-shrink: 0;
            font-family: 'Times New Roman', Times, serif;
        }
        .kop-logo {
            width: 11mm;
            height: 11mm;
            object-fit: contain;
            flex-shrink: 0;
        }
        .kop-txt { flex: 1; text-align: center; overflow: hidden; }
        .kop-txt .kop-prov   { font-size: 5.5pt; font-weight: 700; text-transform: uppercase; line-height: 1.2; }
        .kop-txt .kop-dinas  { font-size: 6pt;   font-weight: 700; text-transform: uppercase; line-height: 1.2; }
        .kop-txt .kop-cabang { font-size: 4.5pt; font-weight: 700; text-transform: uppercase; line-height: 1.2; }
        .kop-txt .kop-school { font-size: 8pt;   font-weight: 900; text-transform: uppercase; line-height: 1.3; margin-top: .8mm; }
        .kop-rules { flex-shrink: 0; }
        .kop-line1 { border-top: 2px solid #000; margin: 1mm 0 0.5mm; }
        .kop-line2 { border-top: 1px solid #000; margin-bottom: 0; }

        /* Title band */
        .card-title {
            background: #f8fafc;
            text-align: center;
            font-size: 7pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .2em;
            padding: 2.5mm 0 2mm;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        /* Body — horizontal layout */
        .card-body {
            flex: 1;
            display: flex;
            gap: 3mm;
            padding: 2.5mm 3mm;
            overflow: hidden;
        }

        /* Photo */
        .photo-box {
            width: 18mm;
            flex-shrink: 0;
            border: 1px solid #e2e8f0;
            border-radius: 2.5mm;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
        }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }
        .no-photo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1mm;
            color: #cbd5e1;
            padding: 2mm;
        }
        .no-photo svg { width: 7mm; height: 7mm; }
        .no-photo span { font-size: 4pt; font-weight: 700; text-transform: uppercase; text-align: center; }

        /* Info + creds */
        .info-wrap { flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: space-between; padding-top: 3mm; }

        .info-rows {}
        .irow {
            display: flex;
            gap: 1mm;
            margin-bottom: 1.2mm;
            align-items: baseline;
        }
        .ilabel {
            width: 12mm;
            font-size: 4pt;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            flex-shrink: 0;
        }
        .icolon { font-size: 4pt; color: #64748b; flex-shrink: 0; }
        .ival {
            font-size: 6pt;
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        /* Creds */
        .creds {
            padding-top: 2mm;
            border-top: 1px dashed #c7d2fe;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2mm;
        }
        .cbox {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 2mm;
            padding: 1.5mm 2mm;
            text-align: center;
        }
        .cbox .cl {
            display: block;
            font-size: 4pt;
            font-weight: 700;
            color: #6366f1;
            text-transform: uppercase;
            letter-spacing: .03em;
            margin-bottom: .5mm;
        }
        .cbox .cv {
            display: block;
            font-family: 'Courier New', monospace;
            font-size: 6.5pt;
            font-weight: 900;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cbox.un .cv { color: #4f46e5; }

        /* Footer */
        .card-foot {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 1.2mm 3mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .card-foot span { font-size: 4pt; color: #94a3b8; }
        .card-foot .badge { font-weight: 900; color: #6366f1; }

        /* ─── Print ─────────────────────────────────── */
        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm;
            }
            .toolbar { display: none !important; }
            body { background: white !important; }

            .page {
                width: 200mm;
                height: 287mm;
                margin: 0;
                box-shadow: none;
                page-break-after: always;
                break-after: page;
            }
            .page:last-child {
                page-break-after: avoid;
                break-after: avoid;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Cetak {{ $siswa_list->count() }} Kartu
        </button>
    </div>

    @foreach ($siswa_list->chunk(8) as $chunk)
    <div class="page">

        @foreach ($chunk as $s)
        <div class="card">

            <div class="card-kop">
                {{-- Logo Provinsi (kiri) --}}
                <img class="kop-logo"
                     src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/bf/Coat_of_arms_of_Central_Sulawesi.png/250px-Coat_of_arms_of_Central_Sulawesi.png"
                     alt="Logo Provinsi">

                {{-- Teks Instansi (tengah) --}}
                <div class="kop-txt">
                    <div class="kop-prov">Pemerintah Provinsi Sulawesi Tengah</div>
                    <div class="kop-dinas">Dinas Pendidikan</div>
                    <div class="kop-cabang">Cabang Dinas Dikmen Wilayah II Donggala-Parimo</div>
                    <div class="kop-school">{{ $settings->school_name }}</div>
                </div>

                {{-- Logo Sekolah (kanan) --}}
                @if($settings->logo_url)
                    <img class="kop-logo"
                         src="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}"
                         alt="Logo Sekolah">
                @else
                    <div style="width:11mm;flex-shrink:0;"></div>
                @endif
            </div>
            {{-- Garis ganda kop --}}
            <div style="margin:0 2.5mm;">
                <div class="kop-line1"></div>
                <div class="kop-line2"></div>
            </div>

            <div class="card-title">Kartu Peserta Ujian</div>

            <div class="card-body">
                <div class="photo-box">
                    @if($s->foto)
                        <img src="{{ asset('assets/uploads/users/' . $s->foto) }}" alt="">
                    @else
                        <div class="no-photo">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>Pas Foto</span>
                        </div>
                    @endif
                </div>

                <div class="info-wrap">
                    <div class="info-rows">
                        <div class="irow">
                            <span class="ilabel">Nama</span>
                            <span class="icolon">:</span>
                            <span class="ival" title="{{ $s->nama }}">{{ $s->nama }}</span>
                        </div>
                        <div class="irow">
                            <span class="ilabel">NISN</span>
                            <span class="icolon">:</span>
                            <span class="ival">{{ $s->nisn ?: '-' }}</span>
                        </div>
                        <div class="irow">
                            <span class="ilabel">Kelas</span>
                            <span class="icolon">:</span>
                            <span class="ival">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                        </div>
                        <div class="irow">
                            <span class="ilabel">TTL</span>
                            <span class="icolon">:</span>
                            <span class="ival" title="{{ $s->tempat_lahir }}, {{ $s->tanggal_lahir ? $s->tanggal_lahir->format('d/m/Y') : '-' }}">
                                {{ $s->tempat_lahir ?: '-' }}, {{ $s->tanggal_lahir ? $s->tanggal_lahir->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                        <div class="irow">
                            <span class="ilabel">Ruang</span>
                            <span class="icolon">:</span>
                            <span class="ival">{{ $ruang->nama_ruang }}</span>
                        </div>
                    </div>

                    <div class="creds">
                        <div class="cbox un">
                            <span class="cl">Username</span>
                            <span class="cv">{{ $s->username }}</span>
                        </div>
                        <div class="cbox">
                            <span class="cl">Password</span>
                            <span class="cv">{{ $s->password_view ?? '••••••' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-foot">
                <span>*Simpan sebagai bukti kepesertaan.</span>
                <span class="badge">EXAM CARD</span>
            </div>

        </div>
        @endforeach

        {{-- Pad to fill 8 slots --}}
        @for ($i = count($chunk); $i < 8; $i++)
        <div class="card empty"></div>
        @endfor

    </div>
    @endforeach

</body>
</html>
