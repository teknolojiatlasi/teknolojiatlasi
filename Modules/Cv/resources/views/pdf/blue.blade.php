<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <style>
        body{font-family:DejaVu Sans,Arial,sans-serif;font-size:12px;margin:0;padding:0}
        .wrapper{width:100%;display:table}
        .sidebar{width:30%;background:#173a63;color:#fff;padding:20px;display:table-cell;vertical-align:top}
        .content{width:70%;padding:22px 24px;display:table-cell;vertical-align:top}
        .photo{width:120px;height:120px;border-radius:10px;object-fit:cover;display:block;margin:0 auto 12px;background:#fff}
        h2{font-size:17px;margin:0}
        .subtitle{font-size:11px;margin-top:6px;opacity:.9}
        .sidebar hr{border:none;border-top:1px solid rgba(255,255,255,.25);margin:12px 0}
        .sidebar p{font-size:11px;margin:6px 0;word-wrap:break-word}
        .sidebar a{color:#fff}
        .sidebar ul{padding-left:16px;margin:6px 0}
        .sidebar li{font-size:11px;margin:3px 0}

        h3{font-size:14px;margin:0 0 8px;padding-bottom:5px;border-bottom:2px solid #173a63;color:#173a63}
        .item{margin-bottom:12px}
        .item strong{display:block;font-size:12px}
        .meta{font-size:10px;color:#555;margin-top:2px}
        .item p{font-size:11px;margin:4px 0 0}
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        @if($cv->photo)
            <img class="photo" src="{{ $cv->photo_public_path }}" alt="photo">
        @endif

        <h2>{{ $cv->full_name }}</h2>
        @if($cv->title)<div class="subtitle">{{ $cv->title }}</div>@endif

        <hr>

        @if($cv->email)<p><strong>E-posta</strong><br>{{ $cv->email }}</p>@endif
        @if($cv->phone)<p><strong>Telefon</strong><br>{{ $cv->phone }}</p>@endif
        @if($cv->address)<p><strong>Adres</strong><br>{{ $cv->address }}</p>@endif

        @if($cv->skills->count())
            <hr>
            <p><strong>Yetenekler</strong></p>
            <ul>
                @foreach($cv->skills as $skill)
                    <li>{{ $skill->name }} @if($skill->level) ({{ $skill->level }}/4) @endif</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="content">
        @if($cv->about)
            <h3>Profesyonel Özet</h3>
            {!! \Modules\Cv\Support\HtmlSanitizer::sanitize($cv->about) !!}
        @endif

        @if($cv->experiences->count())
            <h3 style="margin-top:14px;">İş Deneyimi</h3>
            @foreach($cv->experiences as $exp)
                <div class="item">
                    <strong>{{ $exp->company }}</strong>
                    <div class="meta">
                        {{ $exp->position }}
                        @if($exp->start_date || $exp->end_date)
                            | {{ $exp->start_date }} - {{ $exp->end_date ?? 'Devam Ediyor' }}
                        @endif
                    </div>
                    @if($exp->description)
                        <p>{{ $exp->description }}</p>
                    @endif
                </div>
            @endforeach
        @endif

        @if($cv->educations->count())
            <h3 style="margin-top:14px;">Eğitim</h3>
            @foreach($cv->educations as $edu)
                <div class="item">
                    <strong>{{ $edu->school }}</strong>
                    <div class="meta">{{ $edu->degree }} @if($edu->year)| {{ $edu->year }}@endif</div>
                </div>
            @endforeach
        @endif
    </div>
</div>

</body>
</html>
