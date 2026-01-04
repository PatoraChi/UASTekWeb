<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji</title>
    <style>
        body { font-family: monospace; padding: 20px; border: 1px solid #000; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 20px; }
        .info { width: 100%; margin-bottom: 20px; }
        .rincian { width: 100%; border-collapse: collapse; }
        .rincian td { padding: 5px; }
        .total { border-top: 2px dashed #000; border-bottom: 2px dashed #000; font-weight: bold; font-size: 1.2em; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PT. HRIS MAJU MUNDUR</h2>
        <p>SLIP GAJI PEGAWAI</p>
        <p>Periode: {{ $payroll->month }} {{ $payroll->year }}</p>
    </div>

    <table class="info">
        <tr>
            <td>Nama: <strong>{{ $payroll->user->name }}</strong></td>
            <td class="text-right">Divisi: {{ $payroll->user->division->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jabatan: {{ $payroll->user->position ?? '-' }}</td>
            <td class="text-right">Tgl Cetak: {{ date('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="rincian">
        <tr>
            <td>Gaji Pokok</td>
            <td class="text-right">Rp {{ number_format($payroll->basic_salary, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Tunjangan (Jabatan + Makan + Transport)</td>
            <td class="text-right">Rp {{ number_format($payroll->allowances, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Lembur / Overtime</td>
            <td class="text-right">Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="color:red;">Potongan (Telat & Alpha)</td>
            <td class="text-right" style="color:red;">- Rp {{ number_format($payroll->deductions, 0, ',', '.') }}</td>
        </tr>
        <tr><td colspan="2"><br></td></tr>
        <tr class="total">
            <td>TOTAL DITERIMA (TAKE HOME PAY)</td>
            <td class="text-right">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div style="margin-top: 50px; text-align: center;">
        <p>Penerima,</p>
        <br><br>
        <p>({{ $payroll->user->name }})</p>
    </div>
</body>
</html>