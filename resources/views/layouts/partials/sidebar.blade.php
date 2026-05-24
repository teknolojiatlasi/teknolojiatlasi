<div class="col-md-3 left_col">
  <div class="left_col scroll-view">
    <div class="navbar nav_title" style="border:0;">
      <a href="{{ route('admin.dashboard') }}" class="site_title">
        <i class="fas fa-cogs"></i> <span>Yönetim</span>
      </a>
    </div>

    <div class="clearfix"></div>

    <div class="profile clearfix">
      <div class="profile_pic">
        <img src="{{ asset('vendor/gentelella/images/img.jpg') }}" alt="..." class="img-circle profile_img">
      </div>
      <div class="profile_info">
        <span>Hoş geldin,</span>
        <h4>{{ auth()->user()?->name ?? 'Kullanıcı' }}</h4>
      </div>
    </div>

    <br>

    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
      <div class="menu_section">
        <h3>Genel</h3>
        <ul class="nav side-menu">
          <li>
            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Yönetim Paneli</a>
          </li>

          <li>
            <a><i class="fas fa-home"></i> İlan <span class="fas fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="{{ route('blog.index') }}">İlan listesi</a></li>
              <li><a href="{{ route('blog.create') }}">Yeni ilan</a></li>
              <li><a href="{{ route('blog.categories.index') }}">Kategoriler</a></li>
              <li><a href="{{ route('blog.comments.index') }}">İlan Yorumları</a></li>
              <li><a href="{{ route('blog.public.index') }}">İlanları Oku</a></li>
            </ul>
          </li>

          <li>
            <a><i class="fas fa-graduation-cap"></i> Sınav <span class="fas fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="{{ route('sinav.lessons.index') }}">Soru Çöz</a></li>
              <li><a href="{{ route('sinav.admin.home') }}">Yönetim</a></li>
              <li><a href="{{ route('sinav.admin.lessons.index') }}">Dersler</a></li>
              <li><a href="{{ route('sinav.admin.questions.import.create') }}">Soruları Yükle</a></li>
              <li><a href="{{ route('sinav.admin.questions.import.json.create') }}">JSON Soru Yükle</a></li>
            </ul>
          </li>

          <li>
            <a><i class="fas fa-envelope"></i> İletişim <span class="fas fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="{{ route('contact_admin_home') }}">İletişim paneli</a></li>
              <li><a href="{{ route('contact_admin_messages_index') }}">Mesajlar</a></li>
              <li><a href="{{ route('contact_admin_settings_edit') }}">Ayarlar</a></li>
            </ul>
          </li>

          <li>
            <a><i class="fas fa-id-card"></i> CV <span class="fas fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="{{ route('cv.create') }}">CV Oluştur</a></li>
            </ul>
          </li>

          <li>
            <a><i class="fas fa-photo-video"></i> Medya <span class="fas fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="{{ route('media.index') }}">Medya listesi</a></li>
            </ul>
          </li>

          <li>
            <a><i class="fas fa-poll"></i> Anket <span class="fas fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="{{ route('survey.index') }}">Anketler</a></li>
            </ul>
          </li>

          <li>
            <a><i class="fas fa-gamepad"></i> Oyun <span class="fas fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="{{ route('game.index') }}">Oyun ana sayfası</a></li>
              <li><a href="{{ route('game.word-pairs.index') }}">Kelime eşleştirme</a></li>
              <li><a href="{{ route('game.puzzle-memory') }}">Memory puzzle</a></li>
              <li><a href="{{ route('game.puzzle') }}">Puzzle</a></li>
            </ul>
          </li>

          <li>
            <a><i class="fas fa-users"></i> Kullanıcılar <span class="fas fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="{{ route('admin.users.index') }}">Kullanıcı listesi</a></li>
            </ul>
          </li>

          <li>
            <a><i class="fas fa-hashtag"></i> Sosial <span class="fas fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li><a href="{{ route('admin.sossial.posts.index') }}">Postlar</a></li>
              <li><a href="{{ route('admin.sossial.tags.index') }}">Etiketler</a></li>
              <li><a href="{{ route('admin.sossial.comments.index') }}">Yorumlar</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>

    <div class="sidebar-footer hidden-small">
      <a data-bs-toggle="tooltip" data-bs-placement="top" title="Ayarlar"><span class="fas fa-cog" aria-hidden="true"></span></a>
      <a data-bs-toggle="tooltip" data-bs-placement="top" title="Tam ekran"><span class="fas fa-expand" aria-hidden="true"></span></a>
      <a data-bs-toggle="tooltip" data-bs-placement="top" title="Kilit"><span class="fas fa-eye-slash" aria-hidden="true"></span></a>
      <form method="POST" action="{{ route('logout') }}" style="display:inline;">
        @csrf
        <button type="submit" data-bs-toggle="tooltip" data-bs-placement="top" title="Çıkış" style="background:none;border:0;padding:0;">
          <span class="fas fa-power-off" aria-hidden="true"></span>
        </button>
      </form>
    </div>
  </div>
</div>
