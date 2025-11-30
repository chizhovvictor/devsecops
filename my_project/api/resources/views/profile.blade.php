<?php $this->extends('layouts/app.blade.php'); ?>

<?php $this->section('content'); ?>
<main class="profile-page">
    <header class="profile-header mb-4">
        <div class="container d-flex align-items-center">
            <div class="profile-avatar me-3">
                <img src="<?= $this->assets('img/avatar.jpg') ?>" alt="Avatar" class="rounded-circle" style="width:88px;height:88px;object-fit:cover;" />
            </div>
            <div>
                <h2 class="mb-1"><?= htmlspecialchars($this->v('username')) ?></h2>
                <div class="text-muted"><?= htmlspecialchars($this->v('email')) ?></div>
            </div>
        </div>
    </header>

    <section class="container">
        <div class="profile-tabs mb-3">
            <button type="button" class="tab active" data-tab="posts" aria-label="Публикации">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" class="tab-icon"><title>Публикации</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M3 3H21V21H3z"></path><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M9.01486 3 9.01486 21"></path><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M14.98514 3 14.98514 21"></path><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M21 9.01486 3 9.01486"></path><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M21 14.98514 3 14.98514"></path></svg>
            </button>
            <button type="button" class="tab" data-tab="liked" aria-label="Понравилось">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" class="tab-icon"><title>Сохраненное</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M20 21 12 13.44 4 21 4 3 20 3 20 21z"></path></svg>
            </button>
        </div>
    <div id="profile-empty"></div>
    <div id="profile-gallery" class="profile-gallery"></div>
    </section>
</main>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
    <script>
        // expose server user id for profile.js
        window.__PROFILE_USER_ID__ = <?= (int) $this->v('user_id') ?>;
    </script>
    <script src="<?= $this->assets('js/profile.js') ?>"></script>
<?php $this->endsection(); ?>
