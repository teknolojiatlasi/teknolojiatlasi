<div class="top_nav">
  <div class="nav_menu">
    <div class="nav toggle">
      <a id="menu_toggle"><i class="fas fa-bars"></i></a>
    </div>

    <nav class="nav navbar-nav">
      <ul class="navbar-right">
        <li class="nav-item dropdown open" style="padding-left: 15px;">
          <a href="javascript:;" class="user-profile dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            {{ auth()->user()?->name ?? 'Kullanıcı' }}
            <span class="fas fa-angle-down"></span>
          </a>

          <div class="dropdown-menu dropdown-usermenu float-end">
            <a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item">
                <i class="fas fa-sign-out-alt float-end"></i> Çıkış
              </button>
            </form>
          </div>
        </li>
      </ul>
    </nav>
  </div>
</div>

