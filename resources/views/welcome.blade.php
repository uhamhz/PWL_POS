@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <!-- Row 1: Simplified Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-gradient-info shadow">
                    <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Barang</span>
                        <span class="info-box-number">{{ $totalBarang }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-gradient-success shadow">
                    <span class="info-box-icon"><i class="fas fa-dolly-flatbed"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Stok</span>
                        <span class="info-box-number">{{ $totalStok }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-gradient-warning shadow">
                    <span class="info-box-icon"><i class="fas fa-cash-register"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Penjualan</span>
                        <span class="info-box-number">{{ $totalPenjualan }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-gradient-danger shadow">
                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pendapatan</span>
                        <span class="info-box-number">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Charts and Tables -->
        <div class="row">
            <!-- Grafik Penjualan -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Grafik Penjualan Bulanan</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="grafikPenjualan" height="305"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barang Stok Menipis -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Barang Stok Menipis</h3>
                        <span class="badge badge-light">{{ count($stokMinimal) }} items</span>
                    </div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-hover table-striped">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th class="text-right">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stokMinimal as $barang)
                                    <tr>
                                        <td><i class="fas fa-cube text-info mr-1"></i>{{ $barang->barang_nama }}</td>
                                        <td class="text-right">
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>{{ $barang->stok_tersedia }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Stok aman semua</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(count($stokMinimal) > 0)
                        <div class="card-footer text-center">
                            <a href="{{ url('stok') }}" class="btn btn-sm btn-danger">Kelola Stok</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Row 3: Best Sellers and Recent Sales -->
        <div class="row">
            <!-- Barang Terlaris (Simplified) -->
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title">6 Barang Terlaris</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Barang</th>
                                    <th class="text-right">Jumlah Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($penjualanTerlaris as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->barang->barang_nama ?? '-' }}</td>
                                        <td class="text-right">{{ $item->total_jumlah }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada penjualan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Penjualan Terbaru (Simplified) -->
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-purple text-white">
                        <h3 class="card-title">5 Penjualan Terbaru</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-purple btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Pembeli</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($penjualanTerbaru as $penjualan)
                                    <tr>
                                        <td>{{ $penjualan->penjualan_tanggal }}</td>
                                        <td>{{ $penjualan->penjualan_id }}</td>
                                        <td>{{ $penjualan->pembeli }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ url('penjualan') }}" class="btn btn-sm btn-purple">Lihat Semua</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .bg-purple {
            background-color: #6f42c1 !important;
        }

        .btn-purple {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }

        .btn-purple:hover {
            background-color: #5a32a3;
            border-color: #5a32a3;
        }

        .info-box {
            border-radius: 0.5rem;
            min-height: 80px;
        }

        .info-box-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            width: 70px;
        }

        .info-box-content {
            padding: 10px;
        }

        .info-box-number {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .card-header {
            border-bottom: none;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        .card {
            border-radius: 0.5rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Grafik Penjualan
            const salesChart = document.getElementById('grafikPenjualan');
            if (salesChart) {
                new Chart(salesChart, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                        datasets: [{
                            label: 'Jumlah Penjualan',
                            data: @json($dataChart),
                            backgroundColor: 'rgba(60, 141, 188, 0.7)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: "rgba(0, 0, 0, .05)"
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush