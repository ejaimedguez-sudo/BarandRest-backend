<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 6px; font-size: 12px; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  <h2>Reporte - {{ date('Y-m-d H:i') }}</h2>
  @if(file_exists(public_path('images/logo.png')))
    <div><img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px"></div>
  @endif
  <table>
    <thead>
      <tr>
        @if(!empty($rows) && is_array($rows) && isset($rows[0]) && is_array($rows[0]))
          @foreach(array_keys($rows[0]) as $h)
            <th>{{ $h }}</th>
          @endforeach
        @else
          <th>Dato</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @if(!empty($rows) && is_array($rows))
        @foreach($rows as $r)
          <tr>
            @if(is_array($r))
              @foreach($r as $c)
                <td>{{ $c }}</td>
              @endforeach
            @else
              <td>{{ is_scalar($r) ? $r : json_encode($r) }}</td>
            @endif
          </tr>
        @endforeach
        @if(!empty($totals) && is_array($totals))
          <tr>
            @if(!empty($rows) && is_array($rows[0]))
              @foreach(array_keys($rows[0]) as $k)
                <td style="font-weight:bold">{{ $totals[$k] ?? '' }}</td>
              @endforeach
            @else
              <td style="font-weight:bold">Totales: {{ implode(' | ', $totals) }}</td>
            @endif
          </tr>
        @endif
      @endif
    </tbody>
  </table>
</body>
</html>
