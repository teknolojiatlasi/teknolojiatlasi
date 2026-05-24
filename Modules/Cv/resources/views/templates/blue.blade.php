<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>{{ $cv->full_name }} - CV</title>
    <style>
        body{font-family:DejaVu Sans,Arial,sans-serif;background:#f0f2f5;margin:0;padding:0}
        .cv{width:900px;margin:35px auto;background:#fff;display:flex;box-shadow:0 0 14px rgba(0,0,0,.12)}
        .sidebar{width:30%;background:#173a63;color:#fff;padding:28px 22px}
        .photo{width:135px;height:135px;border-radius:10px;object-fit:cover;display:block;margin:0 auto 14px;background:#fff}
        .name{font-size:20px;margin:0}
        .title{font-size:12px;margin:6px 0 0;opacity:.9}
        .sidebar hr{border:none;border-top:1px solid rgba(255,255,255,.25);margin:14px 0}
        .sidebar p{font-size:12px;margin:6px 0;word-break:break-word}
        .sidebar a{color:#fff}

        .content{width:70%;padding:28px 30px}
        .section-title{font-size:15px;margin:0 0 10px;padding-bottom:6px;border-bottom:2px solid #173a63;color:#173a63}
        .item{margin-bottom:14px}
        .item strong{display:block;font-size:13px}
        .item .meta{font-size:11px;color:#555;margin-top:2px}
        .item p{font-size:12px;margin:6px 0 0}
        ul{padding-left:18px;margin:6px 0}
        li{font-size:12px;margin:4px 0}
    </style>
</head>
<body>

<div class="cv">
    <div class="sidebar">
        @if($cv->photo)
            <img class="photo" src="{{ $cv->photo_url }}" alt="photo">
        @endif

        <h2 class="name">{{ $cv->full_name }}</h2>
        @if($cv->title)<div class="title">{{ $cv->title }}</div>@endif

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
            <h3 class="section-title">Profesyonel Özet</h3>
            {!! \Modules\Cv\Support\HtmlSanitizer::sanitize($cv->about) !!}
        @endif

        @if($cv->experiences->count())
            <h3 class="section-title" style="margin-top:18px;">İş Deneyimi</h3>
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
            <h3 class="section-title" style="margin-top:18px;">Eğitim</h3>
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
