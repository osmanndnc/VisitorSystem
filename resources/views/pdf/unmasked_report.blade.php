<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $fullReportTitle }}</title>
    <style>
        /* PDF için özel stiller */
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Türkçe karakter desteği için font */
            margin: 2cm;
            font-size: 10px;
        }
        h1 {
            text-align: center;
            color: #003366;
            font-size: 20px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            word-wrap: break-word; /* Uzun kelimeler bölünsün */
        }
        th {
            background-color: #003366;
            color: white;
            font-weight: bold;
        }
        .date-info {
            text-align: right;
            font-size: 9px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="date-info">Tarih: {{ Carbon\Carbon::now()->format('d.m.Y H:i') }}</div>
    <h1>{{ $fullReportTitle }}</h1>

    <table>
        <thead>
            <tr>
                @foreach ($pdfHeadings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($pdfData as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>