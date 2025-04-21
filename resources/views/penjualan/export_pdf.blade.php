<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px 3px;
        }

        th {
            text-align: left;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .p-1 {
            padding: 5px 1px 5px 1px;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }
    </style>
</head>

<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center">
                <img src="{{ public_path('images/polinema-bw.png') }}" class="image">
            </td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN
                    TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold mb-1">POLITEKNIK NEGERI MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341)
                    404420</span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
        </tr>
    </table>

    <h3 class="text-center">LAPORAN DATA PENJUALAN</h3>

    <table class="border-all font-11">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Kode Penjualan</th>
                <th>Pembeli</th>
                <th>Tanggal Penjualan</th>
                <th>Input Oleh</th>
                <th>Total Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan as $p)
                        @php
                            $total = $p->detail_penjualan->sum(function ($d) {
                                return $d->jumlah * $d->harga;
                            });
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $p->penjualan_id }}</td>
                            <td>{{ $p->pembeli }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->penjualan_tanggal)->format('d-m-Y H:i') }}</td>
                            <td>{{ $p->user->username ?? '-' }}</td>
                            <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                        @if($p->detail_penjualan->count())
                            <tr>
                                <td></td>
                                <td colspan="5">
                                    <table width="100%" class="font-10 border-all" style="margin-top: 5px;">
                                        <thead>
                                            <tr>
                                                <th>Nama Barang</th>
                                                <th>Jumlah</th>
                                                <th>Harga Satuan</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($p->detail_penjualan as $detail)
                                                                @php
                                                                    $subtotal = $detail->jumlah * $detail->harga;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $detail->barang->barang_nama ?? '-' }}</td>
                                                                    <td>{{ $detail->jumlah }}</td>
                                                                    <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                                                    <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
            @endforeach
        </tbody>
    </table>
</body>

</html>