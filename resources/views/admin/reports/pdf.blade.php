<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #111; }
        h2 { margin: 0 0 2px 0; }
        .subtitle { color: #555; margin-bottom: 16px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px 7px; text-align: left; }
        th { background: #f0f0f0; }
        .empty { text-align: center; color: #888; padding: 20px 0; }
    </style>
</head>
<body>
    <h2>Orderan Kokiku &mdash; {{ $label }}</h2>
    <div class="subtitle">
        Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}<br>
        Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headings as $h)
                <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                @foreach($row as $value)
                <td>{{ $value }}</td>
                @endforeach
            </tr>
            @empty
            <tr><td colspan="{{ count($headings) }}" class="empty">Tidak ada data pada rentang tanggal ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
