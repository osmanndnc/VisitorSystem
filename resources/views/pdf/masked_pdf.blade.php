<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>
        @if ($reportTitle)
            {{ $reportTitle }} Ziyaretçi Raporu - PDF
        @else
            Ziyaretçi Raporu - PDF
        @endif
    </title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url("{{ asset('fonts/DejaVuSans.ttf') }}") format('truetype');
        }
        body, table, th, td {
            font-family: 'DejaVu Sans', sans-serif;
        }
        body {
            margin: 20px;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            color: #003366;
            margin-bottom: 1rem;
        }
        p {
            text-align: center;
            font-style: italic;
            color: #555;
            margin-top: -10px;
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            word-wrap: break-word;
        }
        th, td {
            border: 1px solid #003366;
            padding: 8px 6px;
            text-align: center;
        }
        th {
            background-color: #003366;
            color: white;
        }
    </style>
</head>
<body>
    @if ($reportTitle)
        <h2>{{ $reportTitle }} Ziyaretçi Raporu</h2>
        @if ($reportRange)
            <p>({{ $reportRange }})</p>
        @endif
    @else
        <h2>Ziyaretçi Raporu</h2>
        @if ($reportRange)
            <p>({{ $reportRange }})</p>
        @endif
    @endif

    <table>
        <thead>
            <tr>
                <th>Kayıt No</th>
                @foreach ($fieldsForBlade as $field)
                    <th>
                        @switch($field)
                            @case('entry_time') Giriş Tarihi @break
                            @case('name') Ad-Soyad @break
                            @case('tc_no') T.C. No @break
                            @case('phone') Telefon @break
                            @case('plate') Plaka @break
                            @case('purpose') Ziyaret Sebebi @break
                            @case('department') Ziyaret Edilen Birim @break
                            @case('person_to_visit') Ziyaret Edilen Kişi @break
                            @case('approved_by') Ekleyen @break
                            @default {{ ucfirst(str_replace('_', ' ', $field)) }}
                        @endswitch
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @foreach ($fieldsForBlade as $field)
                        <td>{{ $row[$field] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>