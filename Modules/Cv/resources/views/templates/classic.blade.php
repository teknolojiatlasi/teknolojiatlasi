<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>{{ $cv->full_name }} - CV</title>
    <style>
        body{font-family:DejaVu Sans,Arial,sans-serif;background:#e6e6e6;margin:0;padding:0}
        .cv{width:900px;margin:35px auto;background:#fff;display:flex;box-shadow:0 0 14px rgba(0,0,0,.12)}
        .sidebar{width:32%;background:#bdbdbd;color:#1a1a1a;padding:28px 22px}
        .photo{width:120px;height:120px;border-radius:8px;object-fit:cover;display:block;margin:0 auto 14px;background:#fff}
        .name{font-size:22px;text-align:center;margin:0}
        .title{font-size:13px;text-align:center;margin:6px 0 0}
        .block{margin-top:18px}
        .block h4{font-size:12px;letter-spacing:.6px;text-transform:uppercase;margin:0 0 8px;border-bottom:1px solid rgba(0,0,0,.25);padding-bottom:6px}
        .sidebar p{font-size:12px;margin:6px 0}
        .sidebar ul{padding-left:18px;margin:6px 0}
        .sidebar li{font-size:12px;margin:4px 0}

        .content{width:68%;padding:28px 30px}
        .section-title{font-size:15px;margin:0 0 10px;padding-bottom:6px;border-bottom:1px solid #8c8c8c}
        .item{margin-bottom:14px}
        .item strong{display:block;font-size:13px}
        .item .meta{font-size:11px;color:#555;margin-top:2px}
        .item p{font-size:12px;margin:6px 0 0}
        a{color:#1a1a1a}
    </style>
</head>
<body>

<div class="cv">
    <div class="sidebar">
        @if($cv->photo)
            <img class="photo" src="{{ $cv->photo_url }}" alt="photo">
        @endif

        <h2 class="name">{{ $cv->full_name }}</h2>
        @if($cv->title)<p class="title">{{ $cv->title }}</p>@endif

        <div class="block">
            <h4>İletişim</h4>
            @if($cv->email)<p><strong>E-posta:</strong> {{ $cv->email }}</p>@endif
            @if($cv->phone)<p><strong>Telefon:</strong> {{ $cv->phone }}</p>@endif
            @if($cv->address)<p><strong>Adres:</strong> {{ $cv->address }}</p>@endif
        </div>

        @if($cv->skills->count())
            <div class="block">
                <h4>Yetenekler</h4>
                <ul>
                    @foreach($cv->skills as $skill)
                        <li>{{ $skill->name }} @if($skill->level) ({{ $skill->level }}/4) @endif</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="content">
        @if($cv->about)
            <h3 class="section-title">Hakkımda</h3>
            {!! \Modules\Cv\Support\HtmlSanitizer::sanitize($cv->about) !!}
        @endif

        @if($cv->experiences->count())
            <h3 class="section-title" style="margin-top:18px;">İş Deneyimleri</h3>
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
