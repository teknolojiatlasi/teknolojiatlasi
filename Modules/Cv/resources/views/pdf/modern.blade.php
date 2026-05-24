<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .cv-wrapper {
            width: 100%;
            display: table;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 30%;
            background: #2f2f2f;
            color: #ffffff;
            padding: 20px;
            display: table-cell;
            vertical-align: top;
        }

        .sidebar h2 {
            margin: 0;
            font-size: 18px;
        }

        .sidebar p {
            font-size: 11px;
            margin: 6px 0;
            word-wrap: break-word;
        }

        .sidebar hr {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.3);
            margin: 12px 0;
        }

        /* ===== CONTENT ===== */
        .content {
            width: 70%;
            padding: 25px;
            display: table-cell;
            vertical-align: top;
        }

        h3 {
            font-size: 15px;
            border-bottom: 1px solid #2f2f2f;
            padding-bottom: 4px;
            margin-top: 0;
        }

        .item {
            margin-bottom: 14px;
        }

        .item strong {
            font-size: 12px;
            display: block;
        }

        .item span {
            font-size: 11px;
            color: #555;
        }

        .item p {
            font-size: 11px;
            margin: 4px 0 0;
        }

        /* ===== PAGE BREAK ===== */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

<div class="cv-wrapper">

    {{-- ===== SIDEBAR ===== --}}
    <div class="sidebar">
 @if($cv->photo)
            <img src="{{ $cv->photo_public_path }}"
                 style="
                    width:100px;
                    height:100px;
                    border-radius:50%;
                    display:block;
                    margin:0 auto 15px;
                 ">
        @endif

        <h2>{{ $cv->full_name }}</h2>
        <p>{{ $cv->title }}</p>

        <hr>

        <p><strong>E-posta</strong><br>{{ $cv->email }}</p>
        <p><strong>Telefon</strong><br>{{ $cv->phone }}</p>
        <p><strong>Adres</strong><br>{{ $cv->address }}</p>

        {{-- YETENEKLER --}}
        @if($cv->skills->count())
            <hr>
            <strong>Yetenekler</strong>

            @foreach($cv->skills as $skill)
                <p>
                    {{ $skill->name }}
                    ({{ $skill->level }}/4)
                </p>
            @endforeach
        @endif
    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="content">

        {{-- HAKKIMDA --}}
        @if($cv->about)
            <h3>Hakkımda</h3>
            {!! \Modules\Cv\Support\HtmlSanitizer::sanitize($cv->about) !!}
        @endif

        {{-- DENEYİMLER --}}
        @if($cv->experiences->count())
            <h3>İş Deneyimleri</h3>

            @foreach($cv->experiences as $exp)
                <div class="item">
                    <strong>{{ $exp->company }}</strong>
                    <span>
                        {{ $exp->position }} |
                        {{ $exp->start_date }} -
                        {{ $exp->end_date ?? 'Devam Ediyor' }}
                    </span>
                    <p>{{ $exp->description }}</p>
                </div>
            @endforeach
        @endif

        {{-- EĞİTİMLER --}}
        @if($cv->educations->count())
            <h3>Eğitim</h3>

            @foreach($cv->educations as $edu)
                <div class="item">
                    <strong>{{ $edu->school }}</strong>
                    <span>{{ $edu->degree }} | {{ $edu->year }}</span>
                </div>
            @endforeach
        @endif

    </div>
</div>

</body>
</html>
