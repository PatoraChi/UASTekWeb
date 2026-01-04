<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2, .header p { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-danger { color: red; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 10px; }
        .bg-success { background-color: #d1e7dd; color: #0f5132; }
        .bg-warning { background-color: #fff3cd; color: #664d03; }
        .bg-danger { background-color: #f8d7da; color: #842029; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PT. HRIS MAJU MUNDUR</h2>
        <p>Jl. Teknologi No. 1, Jakarta Selatan</p>
        <hr>
        <h3>LAPORAN ABSENSI PEGAWAI</h3>
        <p>Periode: {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th>Nama Pegawai</th>
                <th>Divisi</th>
                <th class="text-center">Jam Masuk</th>
                <th class="text-center">Jam Pulang</th>
                <th class="text-center">Status</th>
                <th class="text-center">Telat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                <td>{{ $row->user->name }}</td>
                <td>{{ $row->user->division->name ?? '-' }}</td>
                <td class="text-center">{{ $row->check_in ?? '-' }}</td>
                <td class="text-center">{{ $row->check_out ?? '-' }}</td>
                <td class="text-center">{{ ucfirst($row->status) }}</td>
                <td class="text-center {{ $row->late_minutes > 0 ? 'text-danger' : '' }}">
                    {{ $row->late_minutes > 0 ? $row->late_minutes.'m' : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: right;">
        <p>Bali, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><strong>{{ Auth::user()->name }}</strong><br>Admin HRD</p>
    </div>
</body>
</html>