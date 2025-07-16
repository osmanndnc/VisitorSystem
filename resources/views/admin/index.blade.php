<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ziyaretçi Listesi</title>
    <style>
        body {
            background: #f4f5f7;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
        }
        .logo-area {
            text-align: center;
            margin-top: 48px;
            margin-bottom: 12px;
        }
        .logo-area img {
            width: 300px;
            vertical-align: middle;
        }
        .center-box {
            max-width: 950px;
            margin: 32px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 36px 32px;
            text-align: center;
            overflow-x: auto;
        }
        h2 {
            color: #222;
            margin-top: 0;
            margin-bottom: 18px;
            font-size: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            color: #222;
            min-width: 600px;
        }
        th, td {
            padding: 9px 10px;
            border-bottom: 1px solid #e1e4e8;
            text-align: left;
        }
        th {
            background: #f7f7f7;
            color: #444;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.2px;
        }
        tr:hover {
            background: #f4f5f7;
        }
        label {
            font-size: 15px;
            color: #444;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        input[type="checkbox"] {
            width: 15px;
            height: 15px;
            cursor: pointer;
            accent-color: #232a36;
            vertical-align: middle;
        }
        button {
            background: #232a36;
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 18px;
            transition: background 0.2s, box-shadow 0.2s;
            font-weight: bold;
            letter-spacing: 1px;
            box-shadow: 0 2px 8px rgba(35,42,54,0.08);
        }
        button:hover {
            background: #1a1d2e;
            box-shadow: 0 4px 16px rgba(35,42,54,0.16);
        }
    </style>
</head>
<body>
<div class="logo-area">
    <img src="/images/ata_icon.png" alt="Atatürk Üniversitesi">
</div>
<div class="center-box">
    <h2>Ziyaretçi Listesi</h2>

    <form method="POST" action="{{ route('admin.fields') }}">
        @csrf
        <table>
            <tr>
                <th>ID</th>
                <th>
                    <label>
                        Ad-Soyad
                        <input type="checkbox" name="fields[]" value="name" {{ !empty($fields) && in_array('name', $fields) ? 'checked' : '' }}>
                    </label>
                </th>
                <th>
                    <label>
                        TC
                        <input type="checkbox" name="fields[]" value="tc_no" {{ !empty($fields) && in_array('tc_no', $fields) ? 'checked' : '' }}>
                    </label>
                </th>
                <th>
                    <label>
                        Telefon Numarası
                        <input type="checkbox" name="fields[]" value="phone" {{ !empty($fields) && in_array('phone', $fields) ? 'checked' : '' }}>
                    </label>
                </th>
                <th>
                    <label>
                        Plaka
                        <input type="checkbox" name="fields[]" value="plate" {{ !empty($fields) && in_array('plate', $fields) ? 'checked' : '' }}>
                    </label>
                </th>
                <th>
                    <label>
                        Onaylayan
                        <input type="checkbox" name="fields[]" value="approved_by" {{ !empty($fields) && in_array('approved_by', $fields) ? 'checked' : '' }}>
                    </label>
                </th>
            </tr>
            @if(isset($visitors) && count($visitors))
                @foreach($visitors as $visitor)
                    <tr>
                        <td>{{ $visitor->id }}</td>
                        @if(empty($fields) || in_array('name', $fields))<td>{{ $visitor->name }}</td>@endif
                        @if(empty($fields) || in_array('tc_no', $fields))<td>{{ $visitor->tc_no }}</td>@endif
                        @if(empty($fields) || in_array('phone', $fields))<td>{{ $visitor->phone }}</td>@endif
                        @if(empty($fields) || in_array('plate', $fields))<td>{{ $visitor->plate }}</td>@endif
                        @if(empty($fields) || in_array('approved_by', $fields))
                            <td>{{ $visitor->approver ? $visitor->approver->name : '-' }}</td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </table>
        <button type="submit">Göster</button>
    </form>
</div>
</body>
</html>