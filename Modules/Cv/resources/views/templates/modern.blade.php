<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>{{ $cv->full_name }} - CV</title>

    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            background: #eaeaea;
            margin: 0;
            padding: 0;
        }

        .cv-container {
            width: 900px;
            margin: 40px auto;
            background: #fff;
            display: flex;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 30%;
            background: #2c3e50;
            color: #fff;
            padding: 30px 20px;
        }

        .sidebar h2 {
            margin: 0;
            font-size: 22px;
        }

        .sidebar p {
            font-size: 13px;
            margin: 6px 0;
        }

        .sidebar hr {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.2);
            margin: 15px 0;
        }

        /* ===== CONTENT ===== */
        .content {
            width: 70%;
            padding: 30px 35px;
        }

        h3.section-title {
            font-size: 18px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 4px;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .item {
            margin-bottom: 18px;
        }

        .item strong {
            display: block;
            font-size: 14px;
        }

        .item span {
            font-size: 12px;
            color: #666;
        }

        .item p {
            font-size: 13px;
            margin-top: 5px;
        }

        /* ===== SKILLS ===== */
        .skill {
            margin-bottom: 8px;
            font-size: 13px;
        }

        .skill-bar {
            height: 6px;
            background: #ddd;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 3px;
        }

        .skill-level {
            height: 6px;
            background: #1abc9c;
        }
    </style>
</head>
<body>

<div class="cv-container">

    {{-- ===== SIDEBAR ===== --}}
    <div class="sidebar">
   @if($cv->photo)
    <img src="{{ $cv->photo_url }}"
         style="
            width:100px;
            height:100px;
            border-radius:50%;
            object-fit:cover;
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

        {{-- ===== YETENEKLER ===== --}}
        @if($cv->skills->count())
            <hr>
            <h3>Yetenekler</h3>

            @foreach($cv->skills as $skill)
                <div class="skill">
                    {{ $skill->name }}
                    <div class="skill-bar">
                        <div class="skill-level" style="width: {{ $skill->level * 25 }}%"></div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="content">

        {{-- HAKKIMDA --}}
        @if($cv->about)
            <h3 class="section-title">Hakkımda</h3>
            {!! \Modules\Cv\Support\HtmlSanitizer::sanitize($cv->about) !!}
        @endif

        {{-- DENEYİMLER --}}
        @if($cv->experiences->count())
            <h3 class="section-title">İş Deneyimleri</h3>

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
            <h3 class="section-title">Eğitim</h3>

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
