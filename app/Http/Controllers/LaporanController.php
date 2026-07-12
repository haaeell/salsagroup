<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->mode ?? 'periode';
        $jenis = $request->jenis ?? 'pembelian';
        $jenis = $mode === 'tahunan' ? 'pembelian' : $jenis;
        $dari = $request->dari ?? now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? now()->endOfMonth()->format('Y-m-d');
        $tahun = (int) ($request->tahun ?? now()->year);
        $bulanDipilih = collect($request->bulan ?? [now()->month])
            ->map(fn($bulan) => (int) $bulan)
            ->filter(fn($bulan) => $bulan >= 1 && $bulan <= 12)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $availableYears = range(now()->year - 5, now()->year + 1);
        rsort($availableYears);

        $monthOptions = collect(range(1, 12))->map(fn($bulan) => [
            'value' => $bulan,
            'label' => Carbon::create()->month($bulan)->translatedFormat('F'),
        ])->all();

        $viewData = [
            'mode' => $mode,
            'jenis' => $jenis,
            'dari' => $dari,
            'sampai' => $sampai,
            'tahun' => $tahun,
            'bulanDipilih' => $bulanDipilih,
            'availableYears' => $availableYears,
            'monthOptions' => $monthOptions,
        ];

        if ($mode === 'tahunan') {
            return view('laporan.index', array_merge($viewData, $this->buildAnnualReport($jenis, $tahun, $bulanDipilih)));
        }

        return view('laporan.index', array_merge($viewData, $this->buildPeriodReport($jenis, $dari, $sampai)));
    }

    public function cetak(Request $request)
    {
        $mode = $request->mode ?? 'periode';
        $jenis = $request->jenis ?? 'pembelian';
        $jenis = $mode === 'tahunan' ? 'pembelian' : $jenis;

        if ($mode === 'tahunan') {
            $tahun = (int) ($request->tahun ?? now()->year);
            $bulanDipilih = collect($request->bulan ?? [now()->month])
                ->map(fn($bulan) => (int) $bulan)
                ->filter(fn($bulan) => $bulan >= 1 && $bulan <= 12)
                ->unique()
                ->sort()
                ->values()
                ->all();

            $payload = array_merge([
                'mode' => $mode,
                'jenis' => $jenis,
                'tahun' => $tahun,
                'bulanDipilih' => $bulanDipilih,
            ], $this->buildAnnualReport($jenis, $tahun, $bulanDipilih));

            $pdf = Pdf::loadView('laporan.cetak', $payload)->setPaper('a4', 'landscape');

            return $pdf->stream('laporan-tahunan-' . $tahun . '.pdf');
        }

        $dari = $request->dari ?? now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? now()->endOfMonth()->format('Y-m-d');

        $payload = array_merge([
            'mode' => $mode,
            'jenis' => $jenis,
            'dari' => $dari,
            'sampai' => $sampai,
        ], $this->buildPeriodReport($jenis, $dari, $sampai));

        $pdf = Pdf::loadView('laporan.cetak', $payload);

        return $pdf->stream('laporan-' . $jenis . '.pdf');
    }

    private function buildPeriodReport(string $jenis, string $dari, string $sampai): array
    {
        $data = collect();
        $summary = $this->emptySummary();
        $trend = collect();
        $topProduk = collect();
        $kategoriBreakdown = collect();

        $status = $jenis === 'pembelian' ? 'selesai' : ($jenis === 'pemesanan' ? 'proses' : null);

        if ($jenis === 'barang_masuk') {
            $data = BarangMasuk::with('barang')
                ->whereBetween('tanggal_masuk', [$dari, $sampai])
                ->get();

            $summary['total_pesanan'] = $data->count();
            $summary['total_item_terjual'] = $data->sum('jumlah');

            $trend = $data->groupBy(fn($item) => Carbon::parse($item->tanggal_masuk)->format('Y-m-d'))
                ->map(fn($rows, $tanggal) => [
                    'tanggal' => $tanggal,
                    'total' => $rows->sum('jumlah'),
                ])
                ->sortKeys()
                ->values();
        } elseif ($status) {
            $data = Pesanan::with('detailPesanan.barang.kategori')
                ->where('status', $status)
                ->whereBetween('tanggal', [$dari, $sampai])
                ->orderBy('tanggal')
                ->get();

            $summary = $this->buildSummaryFromOrders($data);

            $trend = $data->groupBy(fn($item) => Carbon::parse($item->tanggal)->format('Y-m-d'))
                ->map(fn($rows, $tanggal) => [
                    'tanggal' => $tanggal,
                    'total' => $rows->sum('total_harga'),
                    'jumlah_pesanan' => $rows->count(),
                ])
                ->sortKeys()
                ->values();

            $topProduk = $data->flatMap->detailPesanan
                ->groupBy('nama_barang')
                ->map(fn($rows, $nama) => [
                    'nama' => $nama,
                    'jumlah' => $rows->sum('jumlah'),
                    'total' => $rows->sum(fn($detail) => $detail->jumlah * $detail->harga),
                ])
                ->sortByDesc('jumlah')
                ->take(5)
                ->values();

            $barangByKode = Barang::with('kategori')->get()->keyBy('kode');

            $kategoriBreakdown = $data->flatMap->detailPesanan
                ->groupBy(fn($detail) => $this->categoryNameForDetail($detail, $barangByKode))
                ->map(fn($rows, $kategori) => [
                    'kategori' => $kategori,
                    'total' => $rows->sum(fn($detail) => $detail->jumlah * $detail->harga),
                ])
                ->sortByDesc('total')
                ->values();
        }

        return [
            'data' => $data,
            'summary' => $summary,
            'trend' => $trend,
            'topProduk' => $topProduk,
            'kategoriBreakdown' => $kategoriBreakdown,
            'annualReport' => null,
        ];
    }

    private function buildAnnualReport(string $jenis, int $tahun, array $bulanDipilih): array
    {
        $status = $jenis === 'pembelian' ? 'selesai' : ($jenis === 'pemesanan' ? 'proses' : null);
        $data = collect();
        $summary = $this->emptySummary();
        $annualReport = null;
        $trend = collect();
        $topProduk = collect();
        $kategoriBreakdown = collect();

        if ($status && count($bulanDipilih) > 0) {
            $data = Pesanan::with('detailPesanan.barang.kategori')
                ->where('status', $status)
                ->whereYear('tanggal', $tahun)
                ->whereIn(\DB::raw('MONTH(tanggal)'), $bulanDipilih)
                ->orderBy('tanggal')
                ->get();

            $summary = $this->buildSummaryFromOrders($data);
            $annualReport = $this->compileAnnualCategoryReport($data, $tahun, $bulanDipilih);
            $topProduk = $data->flatMap->detailPesanan
                ->groupBy('nama_barang')
                ->map(fn($rows, $nama) => [
                    'nama' => $nama,
                    'jumlah' => $rows->sum('jumlah'),
                    'total' => $rows->sum(fn($detail) => $detail->jumlah * $detail->harga),
                ])
                ->sortByDesc('jumlah')
                ->take(5)
                ->values();

            $kategoriBreakdown = collect($annualReport['chart']['datasets'] ?? [])
                ->map(fn($dataset) => [
                    'kategori' => $dataset['label'],
                    'total' => collect($dataset['data'])->sum(),
                ])
                ->sortByDesc('total')
                ->values();

            $trend = collect($annualReport['months'] ?? [])->map(function ($monthRow) {
                return [
                    'tanggal' => $monthRow['label'],
                    'total' => $monthRow['overall']['pendapatan'],
                    'jumlah_pesanan' => $monthRow['jumlah_pesanan'],
                ];
            });
        }

        return [
            'data' => $data,
            'summary' => $summary,
            'trend' => $trend,
            'topProduk' => $topProduk,
            'kategoriBreakdown' => $kategoriBreakdown,
            'annualReport' => $annualReport,
        ];
    }

    private function buildSummaryFromOrders(Collection $orders): array
    {
        $summary = $this->emptySummary();

        $summary['total_pesanan'] = $orders->count();
        $summary['total_pendapatan'] = $orders->sum('total_harga');
        $summary['total_item_terjual'] = $orders->flatMap->detailPesanan->sum('jumlah');
        $summary['rata_rata_transaksi'] = $summary['total_pesanan'] > 0
            ? $summary['total_pendapatan'] / $summary['total_pesanan']
            : 0;
        $summary['total_modal'] = $orders->flatMap->detailPesanan->sum(fn($detail) => $this->modalForDetail($detail));
        $summary['total_laba'] = $summary['total_pendapatan'] - $summary['total_modal'];

        return $summary;
    }

    private function compileAnnualCategoryReport(Collection $orders, int $tahun, array $bulanDipilih): array
    {
        $barangByKode = Barang::with('kategori')->get()->keyBy('kode');

        $details = $orders->flatMap(function ($order) use ($barangByKode) {
            return $order->detailPesanan->map(function ($detail) use ($order, $barangByKode) {
                return [
                    'bulan' => Carbon::parse($order->tanggal)->month,
                    'kategori' => $this->categoryNameForDetail($detail, $barangByKode),
                    'pendapatan' => $detail->jumlah * $detail->harga,
                    'modal' => $this->modalForDetail($detail),
                ];
            });
        });

        $categories = $details->pluck('kategori')->unique()->sort()->values();

        $months = collect($bulanDipilih)->map(function ($bulan) use ($details, $categories, $orders) {
            $monthDetails = $details->where('bulan', $bulan)->values();
            $categoryRows = $categories->mapWithKeys(function ($kategori) use ($monthDetails) {
                $categoryDetails = $monthDetails->where('kategori', $kategori);
                $pendapatan = $categoryDetails->sum('pendapatan');
                $modal = $categoryDetails->sum('modal');

                return [$kategori => [
                    'pendapatan' => $pendapatan,
                    'modal' => $modal,
                    'laba' => $pendapatan - $modal,
                ]];
            });

            $pendapatanTotal = $monthDetails->sum('pendapatan');
            $modalTotal = $monthDetails->sum('modal');

            return [
                'bulan' => $bulan,
                'label' => Carbon::create()->month($bulan)->translatedFormat('F'),
                'jumlah_pesanan' => $orders->filter(fn($order) => Carbon::parse($order->tanggal)->month === $bulan)->count(),
                'categories' => $categoryRows,
                'overall' => [
                    'pendapatan' => $pendapatanTotal,
                    'modal' => $modalTotal,
                    'laba' => $pendapatanTotal - $modalTotal,
                ],
            ];
        })->values();

        $summaryRows = collect([
            'Jumlah' => fn(Collection $values) => $values->sum(),
            'Rata-rata' => fn(Collection $values) => $values->count() > 0 ? $values->avg() : 0,
            'Tertinggi' => fn(Collection $values) => $values->count() > 0 ? $values->max() : 0,
            'Terendah' => fn(Collection $values) => $values->count() > 0 ? $values->min() : 0,
        ])->map(function ($resolver, $label) use ($categories, $months) {
            $categorySummary = $categories->mapWithKeys(function ($kategori) use ($months, $resolver) {
                $pendapatanValues = $months->map(fn($row) => $row['categories'][$kategori]['pendapatan'] ?? 0);
                $modalValues = $months->map(fn($row) => $row['categories'][$kategori]['modal'] ?? 0);
                $labaValues = $months->map(fn($row) => $row['categories'][$kategori]['laba'] ?? 0);

                return [$kategori => [
                    'pendapatan' => (int) round($resolver($pendapatanValues)),
                    'modal' => (int) round($resolver($modalValues)),
                    'laba' => (int) round($resolver($labaValues)),
                ]];
            });

            $overallPendapatan = $months->map(fn($row) => $row['overall']['pendapatan']);
            $overallModal = $months->map(fn($row) => $row['overall']['modal']);
            $overallLaba = $months->map(fn($row) => $row['overall']['laba']);

            return [
                'label' => $label,
                'categories' => $categorySummary,
                'overall' => [
                    'pendapatan' => (int) round($resolver($overallPendapatan)),
                    'modal' => (int) round($resolver($overallModal)),
                    'laba' => (int) round($resolver($overallLaba)),
                ],
            ];
        })->values();

        $chart = [
            'labels' => $months->pluck('label')->values(),
            'datasets' => $categories->map(function ($kategori) use ($months) {
                return [
                    'label' => $kategori,
                    'data' => $months->map(fn($row) => $row['categories'][$kategori]['laba'] ?? 0)->values(),
                ];
            })->values(),
            'overall' => $months->map(fn($row) => $row['overall']['laba'])->values(),
        ];

        return [
            'tahun' => $tahun,
            'categories' => $categories->values(),
            'months' => $months,
            'summary_rows' => $summaryRows,
            'chart' => $chart,
        ];
    }

    private function emptySummary(): array
    {
        return [
            'total_pesanan' => 0,
            'total_pendapatan' => 0,
            'total_item_terjual' => 0,
            'rata_rata_transaksi' => 0,
            'total_modal' => 0,
            'total_laba' => 0,
        ];
    }

    private function modalForDetail($detail): int
    {
        if (!empty($detail->fifo_layers)) {
            return collect($detail->fifo_layers)->sum(function ($layer) {
                return ((int) ($layer['jumlah'] ?? 0)) * ((int) ($layer['harga_beli'] ?? 0));
            });
        }

        return $detail->jumlah * $detail->harga_modal;
    }

    private function categoryNameForDetail($detail, Collection $barangByKode): string
    {
        $kategori = optional(optional($detail->barang)->kategori)->nama;

        if ($kategori) {
            return $kategori;
        }

        $barang = $barangByKode->get($detail->kode_barang);

        return optional(optional($barang)->kategori)->nama ?? 'Tanpa Kategori';
    }
}
