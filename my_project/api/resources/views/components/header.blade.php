<?php if ($this->v('header')): ?>
<?php $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/'; ?>
<nav class="app-sidebar" aria-label="Main navigation">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="home" viewBox="0 0 24 24">
            <path d="m21.762 8.786-7-6.68a3.994 3.994 0 0 0-5.524 0l-7 6.681A4.017 4.017 0 0 0 1 11.68V19c0 2.206 1.794 4 4 4h3.005a1 1 0 0 0 1-1v-7.003a2.997 2.997 0 0 1 5.994 0V22a1 1 0 0 0 1 1H19c2.206 0 4-1.794 4-4v-7.32a4.02 4.02 0 0 0-1.238-2.894Z"></path>
        </symbol>
        <symbol id="profile" viewBox="0 0 24 24">
            <path d="M21 11h-8V3a1 1 0 1 0-2 0v8H3a1 1 0 1 0 0 2h8v8a1 1 0 1 0 2 0v-8h8a1 1 0 1 0 0-2Z"></path>
        </symbol>
        <symbol id="settings" viewBox="0 0 24 24">
            <circle cx="12" cy="12" fill="none" r="8.635" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></circle>
            <path d="M14.232 3.656a1.269 1.269 0 0 1-.796-.66L12.93 2h-1.86l-.505.996a1.269 1.269 0 0 1-.796.66m-.001 16.688a1.269 1.269 0 0 1 .796.66l.505.996h1.862l.505-.996a1.269 1.269 0 0 1 .796-.66M3.656 9.768a1.269 1.269 0 0 1-.66.796L2 11.07v1.862l.996.505a1.269 1.269 0 0 1 .66.796m16.688-.001a1.269 1.269 0 0 1 .66-.796L22 12.93v-1.86l-.996-.505a1.269 1.269 0 0 1-.66-.796M7.678 4.522a1.269 1.269 0 0 1-1.03.096l-1.06-.348L4.27 5.587l.348 1.062a1.269 1.269 0 0 1-.096 1.03m11.8 11.799a1.269 1.269 0 0 1 1.03-.096l1.06.348 1.318-1.317-.348-1.062a1.269 1.269 0 0 1 .096-1.03m-14.956.001a1.269 1.269 0 0 1 .096 1.03l-.348 1.06 1.317 1.318 1.062-.348a1.269 1.269 0 0 1 1.03.096m11.799-11.8a1.269 1.269 0 0 1-.096-1.03l.348-1.06-1.317-1.318-1.062.348a1.269 1.269 0 0 1-1.03-.096" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2"></path>
        </symbol>
        <symbol id="logout" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
        </symbol>
    </svg>

    <div class="sidebar-brand mb-4">
        <a href="/" class="d-block">
            <img src="<?=$this->assets('img/Instagram_logo.svg')?>" alt="Logo" class="img-fluid sidebar-logo-desktop" />
            <img src="<?=$this->assets('img/instagram-53.svg')?>" alt="Logo" class="img-fluid sidebar-logo-tablet" />
        </a>
    </div>

    <ul class="nav flex-column w-100 h-100">
        <?php if ($this->v('username')): ?>
            <li class="nav-item">
                <a href="/" class="nav-link d-flex align-items-center<?= $currentPath === '/' ? ' active' : '' ?>"><svg width="20" height="20" fill="currentColor" class="me-2"><use xlink:href="#home"></use></svg><span class="nav-label">Home</span></a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link d-flex align-items-center<?= strpos($currentPath, '/profile') === 0 ? ' active' : '' ?>"><svg width="20" height="20" fill="currentColor" class="me-2"><use xlink:href="#profile"></use></svg><span class="nav-label">Create</span></a>
            </li>
            <li class="nav-item">
                <a href="/profile" class="nav-link d-flex align-items-center">
                    <img src="<?=$this->assets('img/avatar.jpg')?>" class="rounded-circle" alt="Avatar" style="width:28px; height:28px;" />
                    <span class="nav-label ms-2">Profile</span>
                </a>
            </li>
            <!-- Settings and Logout moved into More dropdown -->
            <li class="nav-item mt-auto mb-3 more-item position-relative">
                <a href="#" class="nav-link d-flex align-items-center more-toggle" onclick="event.preventDefault(); document.getElementById('sidebar-more-dropdown').classList.toggle('show');">
                    <svg aria-label="Настройки" class="x1lliihq x1n2onr6 x5n08af" fill="currentColor" height="24" role="img" viewBox="0 0 24 24" width="24"><title>Настройки</title><path d="M3.5 6.5h17a1.5 1.5 0 0 0 0-3h-17a1.5 1.5 0 0 0 0 3Zm17 4h-17a1.5 1.5 0 0 0 0 3h17a1.5 1.5 0 0 0 0-3Zm0 7h-17a1.5 1.5 0 0 0 0 3h17a1.5 1.5 0 0 0 0-3Z"></path></svg>
                    <span class="nav-label">More</span>
                </a>

                <div id="sidebar-more-dropdown" class="more-dropdown" aria-hidden="true">
                    <a href="/setting/profile" class="more-link<?= strpos($currentPath, '/setting') === 0 ? ' active' : '' ?>">
                        <svg width="20" height="20" fill="currentColor" class="mr-3"><use xlink:href="#settings"></use></svg>
                        Settings
                    </a>
                    <hr style="margin: 8px 0; background: rgba(255,255,255,0.1);">
                    <a href="#" class="more-link logout" onclick="event.preventDefault(); logout(event)">Logout</a>
                </div>
            </li>
        <?php else: ?>
            <?php foreach ($this->v('header')['links'] as ['name' => $name, 'link' => $link]): ?>
                <?php $linkPath = parse_url($link, PHP_URL_PATH) ?: $link; ?>
                <li class="nav-item">
                    <a href="<?=$link?>" class="nav-link d-flex align-items-center<?= strpos($currentPath, $linkPath) === 0 ? ' active' : '' ?>"><svg width="20" height="20" fill="currentColor" class="me-2"><use xlink:href="#home"></use></svg><span class="nav-label"><?=$name?></span></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>
