<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Fazladan beyaz panel kaldırıldı, sadece asıl içerik kutusu kaldı -->
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

        .center-box {
            max-width: 1100px;
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
        tr {
            height: 48px;
        }
        th {
            background: #f7f7f7;
            color: #444;
            font-weight: 800;
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
    </style>
</head>
<body>
<div class="center-box">
    <h2>Ziyaretçi Listesi</h2>
    @php
    $fieldsList = [
        'entry_time' => 'Giriş Tarihi',
        'name' => 'Ad-Soyad',
        'tc_no' => 'TC',
        'phone' => 'Telefon Numarası',
        'plate' => 'Plaka',
        'purpose' => 'Ziyaret Sebebi',
        'person_to_visit' => 'Ziyaret Edilen Kişi',
        'approved_by' => 'Onaylayan'
    ];
    @endphp

    <form method="GET" action="{{ route('admin.reports') }}">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    @foreach($fieldsList as $field => $label)
                        <th>
                            <label>
                                {{ $label }}
                                <input type="checkbox" name="fields[]" value="{{ $field }}" {{ (is_array($fields) && in_array($field, $fields)) ? 'checked' : '' }}>
                            </label>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($visits as $visit)
                    <tr>
                        <td>{{ $visit->id }}</td>
                        @foreach(array_keys($fieldsList) as $field)
                            @if(empty($fields) || in_array($field, $fields))
                                <td>
                                    @switch($field)
                                        @case('entry_time')
                                            {{ $visit->entry_time }}
                                            @break
                                        @case('name')
                                            {{ $visit->visitor->name ?? '-' }}
                                            @break
                                        @case('tc_no')
                                            {{ $visit->visitor->tc_no ?? '-' }}
                                            @break
                                        @case('phone')
                                            {{ $visit->visitor->phone ?? '-' }}
                                            @break
                                        @case('plate')
                                            {{ $visit->visitor->plate ?? '-' }}
                                            @break
                                        @case('purpose')
                                            {{ $visit->purpose }}
                                            @break
                                        @case('person_to_visit')
                                            {{ $visit->person_to_visit }}
                                            @break
                                        @case('approved_by')
                                            @if(isset($visit->approver) && !empty($visit->approver->name))
                                                {{ $visit->approver->name }}
                                            @elseif(!empty($visit->approved_by))
                                                {{ $visit->approved_by }}
                                            @endif
                                            @break
                                    @endswitch
                                </td>
                            @else
                                <td></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit">Göster</button>
    </form>
</div>
</body>
</html>
        </div>
    </div>
</x-app-layout>
