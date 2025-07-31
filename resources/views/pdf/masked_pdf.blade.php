<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>
        @php
            $dateFilterTitles = [
                'daily' => 'Günlük',
                'monthly' => 'Aylık',
                'yearly' => 'Yıllık',
                '' => 'Tüm',
                null => 'Tüm',
            ];
            $displayDateFilter = $dateFilterTitles[$dateFilter ?? ''] ?? 'Tüm';
        @endphp
        {{ $displayDateFilter }} Ziyaretçi Raporu - PDF
    </title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url({{ storage_path('fonts/DejaVuSans.ttf') }}) format('truetype');
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
    <h2>{{ $displayDateFilter }} Ziyaretçi Raporu</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ekleyen</th>
                @foreach ($fieldsForBlade as $field)
                    <th>
                        @switch($field)
                            @case('entry_time') Giriş Tarihi @break
                            @case('name') Ad-Soyad @break
                            @case('tc_no') T.C. No @break
                            @case('phone') Telefon @break
                            @case('plate') Plaka @break
                            @case('purpose') Ziyaret Sebebi @break
                            @case('person_to_visit') Ziyaret Edilen Kişi @break
                            @default {{ ucfirst(str_replace('_', ' ', $field)) }}
                        @endswitch
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row['id'] ?? '-' }}</td>
                    <td>{{ $row['approved_by'] ?? '-' }}</td>
                    @foreach ($fieldsForBlade as $field)
                        <td>{{ $row[$field] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
