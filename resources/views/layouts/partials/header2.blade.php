    <!-- ================= NAVBAR ================= -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top navbar-blur">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('anasayfa') }}">
                <i class="fa fa-star-half-empty" style="font-size:28px;"></i> Bilgi Yıldızı
            </a>
            <button class="navbar-toggler" type="button"
                aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="{{ route('anasayfa') }}"><i class="fa fa-home"></i> Ana Sayfa</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('blog.public.index') }}"><i class="fa fa-newspaper-o"></i> İlan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cv.create') }}"><i class="fa fa-id-card"></i> CV Oluştur</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('sinav.lessons.index') }}"><i class="fa fa-envelope"></i> Soru Çözme Platformu</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('survey.public.index') }}"><i class="fa fa-bar-chart"></i> Anketler</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('sosial.feed') }}"><i class="fa fa-envelope"></i> Mülakat Hazırlığı</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contact_public_index') }}"><i class="fa fa-envelope"></i> İletişim</a></li>
                </ul>
            </div>
        </div>
    </nav>
