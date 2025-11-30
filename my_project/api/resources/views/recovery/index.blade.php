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
                    <div class="card-body px-4 py-5 px-md-5">
                        <div class="mb-3 text-center">
                            <img src="<?= $this->assets('img/Instagram_logo.svg') ?>" alt="Logo" style="max-width: 220px; width: 100%; height: auto; display: inline-block;" />
                        </div>
                        <form action="/recovery" novalidate>
                            <p class="text-muted text-center">Recover your password to regain secure access to your account. Your privacy and security are our top priorities.</p>
                            <!-- Email input -->
                            <div class="mt-4">
                                <label for="email">Your email address</label>
                                <input name="email" type="email" class="form-control" id="email" placeholder="Email" required>
                                <div class="invalid-feedback">
                                    Please provide a valid email.
                                </div>
                            </div>

                            <div class="mt-5">
                                <!-- Submit button -->
                                <button type="submit" class="btn btn-primary btn-block mb-4">
                                    Recovery
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
<script src="<?= $this->assets('js/recovery/index.js'); ?>"></script>
<?php $this->endsection(); ?>
