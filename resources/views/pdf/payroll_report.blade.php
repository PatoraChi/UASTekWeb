<!DOCTYPE html>
<html>
<head>
    <title>Laporan Gaji</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 5px; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PT. HRIS MAJU MUNDUR</h2>
        <h3>LAPORAN REKAP GAJI</h3>
        {{-- Variable $month sekarang sudah berisi Nama Bulan (January) --}}
        <p>Periode: {{ $month }} {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr style="background-color: #eee;">
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Divisi</th>
                <th>Gaji Pokok</th>
                <th>Tunjangan</th>
                <th>Lembur</th>
                <th>Potongan</th>
                <th>Total Bersih</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->user->name }}</td>
                <td>{{ $p->user->division->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($p->basic_salary, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($p->allowances, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($p->overtime_pay, 0, ',', '.') }}</td>
                <td class="text-right" style="color:red;">{{ number_format($p->deductions, 0, ',', '.') }}</td>
                <td class="text-right bold">{{ number_format($p->net_salary, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #eee; font-weight:bold;">
                <td colspan="7" class="text-right">TOTAL PENGELUARAN</td>
                <td class="text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>