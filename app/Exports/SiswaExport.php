<?php

namespace App\Exports;

use App\Models\CalonSiswa;
use App\Models\Jurusan;
use App\Helpers\AlamatHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected ?string $status;
    protected ?int $jurusanId;
    protected ?int $periodeId;
    protected ?string $bulan; // format 'YYYY-MM'

    public function __construct(
        ?string $status = null,
        ?int $jurusanId = null,
        ?int $periodeId = null,
        ?string $bulan = null
    ) {
        $this->status    = $status;
        $this->jurusanId = $jurusanId;
        $this->periodeId = $periodeId;
        $this->bulan     = $bulan;
    }

    public function collection()
    {
        return CalonSiswa::with([
                'pendaftaranJurusan.jurusan',
                'alamatCalonSiswa.alamat',
                'relasiSiswa.wali.alamat',
            ])
            ->when($this->status, fn($q) => $q->where('status_penerimaan', $this->status))
            ->when($this->jurusanId, fn($q) =>
                $q->whereHas('pendaftaranJurusan', fn($qj) =>
                    $qj->where('id_jurusan', $this->jurusanId)->where('urutan_pilihan', 1)
                )
            )
            ->when($this->periodeId, fn($q) => $q->where('id_periode', $this->periodeId))
            ->when($this->bulan, fn($q) =>
                $q->whereRaw("DATE_FORMAT(tanggal_daftar, '%Y-%m') = ?", [$this->bulan])
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Pendaftaran',
            'NISN',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tempat, Tanggal Lahir',
            'Asal Sekolah',
            'Tahun Lulus',
            'Email',
            'Nomor HP',
            'Jurusan',
            'Alamat Siswa',
            'Alamat Orang Tua',
            'Status Penerimaan',
            'Tanggal Daftar',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $pilihan1 = $row->pendaftaranJurusan->where('urutan_pilihan', 1)->first();

        $alamatSiswaRecord = $row->alamatCalonSiswa->first();
        $wali              = $row->relasiSiswa->first()?->wali;

        $alamatSiswaTxt = AlamatHelper::formatAlamatSiswaUntukExport($alamatSiswaRecord, $wali);
        $alamatOrtuTxt  = AlamatHelper::formatLengkap($wali?->alamat);

        return [
            $no,
            $row->nomor_pendaftaran,
            $row->nisn,
            $row->nama_lengkap,
            $row->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            "{$row->tempat_lahir}, " . $row->tanggal_lahir->format('d-m-Y'),
            $row->asal_sekolah,
            $row->tahun_lulus,
            $row->email,
            $row->nomor_hp,
            $pilihan1?->jurusan?->nama_jurusan ?? '-',
            $alamatSiswaTxt,
            $alamatOrtuTxt,
            $row->status_penerimaan,
            $row->tanggal_daftar?->format('d-m-Y') ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        if ($this->bulan) {
            return 'Siswa ' . \Carbon\Carbon::createFromFormat('Y-m', $this->bulan)->translatedFormat('F Y');
        }

        return 'Data Siswa PPDB';
    }
}