<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ziyaretçi Listesi</title>
</head>
<body>
    
    <h2>Ziyaretçiler</h2>
    <table border="5" cellpadding="6">
    <tr>
        <th>ID</th>
        <th>Ad-Soyad</th>
        <th>TC No</th>
        <th>Telefon</th>
        <th>Plaka</th>
        <th>Onaylayan</th>
        <th>İşlemler</th>
        
    </tr>
    @foreach($visitors as $visitors)
        <tr>
            <td>{{$visitor->id}}</td>
            <td>{{$visitor->name}}</td>
            <td>{{$visitor->tc-no}}</td>
            <td>{{$visitor->phone}}</td>
            <td>{{$visitor->plate}}</td> 
            <td>{{$visitor->approved_by}}</td>
            <td>
                <a href="{{route('visitors.edit',$visitor->id)}}">Düzenle</a>|
                <form action="{{route('visitors.destroy',$visitor->id)}}" method="POST" style="display:inline;"></form>
                    @csrf
                    @method('DELETE')
                    <button type="sumbit" onclick="return confrim('Silmek istediğinize emin misiniz?')">Sil</button>
                </form>

            </td>
                 
        </tr>
    @endforeach

    </table>
</body>
</html>