<?php $this->extends('layouts/app.blade.php'); ?>

<?php $this->section('css'); ?>
<style>
    .app-sidebar { display: none !important; }
    .app-content { margin-left: 0 !important; }
</style>
<?php $this->endsection(); ?>


<?php $this->section('content'); ?>
<main class="full-screen px-4 py-5 px-md-5 overflow-hidden login-page">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card border border-light mx-auto" style="max-width: 560px;">
                    <div class="card-body px-4 py-5 px-md-5 text-center">
                        <div class="mb-3 text-center">
                            <img src="<?= $this->assets('img/Instagram_logo.svg') ?>" alt="Logo" style="max-width: 220px; width: 100%; height: auto; display: inline-block;" />
                        </div>
                        <p>We're excited to have you get started. First, you need to confirm your account.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
<script src="<?= $this->assets('js/confirm/confirm.js'); ?>"></script>
<?php $this->endsection(); ?>
