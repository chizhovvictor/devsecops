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
            <div class="d-none d-lg-block col-lg-6 mb-5 mb-lg-0 text-center text-lg-left" style="z-index: 10">
                <img src="<?= $this->assets('img/landing-3x.png') ?>" alt="image" class="w-100"/>
            </div>

            <div class="col-12 col-lg-6 mb-5 mb-lg-0 position-relative">
                <div class="card border border-light bg-none">
                    <div class="card-body px-4 py-5 px-md-5">
                        <div class="mb-3 text-center">
                            <img src="<?= $this->assets('img/Instagram_logo.svg') ?>" alt="Logo" style="max-width: 220px; width: 100%; height: auto; display: inline-block;" />
                        </div>

                        <form action="/login" novalidate>

                            <!-- Email input -->
                            <div class="mb-3">
                                <label for="email">Email</label>
                                <input name="email" type="email" class="form-control" id="email" placeholder="Email" required>
                                <div class="invalid-feedback">
                                    Please provide a valid email.
                                </div>
                            </div>

                            <!-- Password input -->
                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input name="password" type="password" class="form-control" id="password" placeholder="Password" required minlength="8">
                                <div class="invalid-feedback">
                                    Password must be more than 8 characters.
                                </div>
                            </div>

                            <div class="mt-5">
                                <!-- Submit button -->
                                <button type="submit" class="btn btn-primary btn-block mb-4">
                                    Sign in
                                </button>
                                <hr>
                                <!-- Recovery buttons -->
                                <div class="text-center">
                                    <span class="text-muted">If you forgot password</span>
                                    <a href="/recovery" class="text-primary">
                                        Recovery password
                                    </a>
                                    <span class="text-muted">or</span>
                                    <a href="/register" class="text-primary">
                                        Register
                                    </a>
                                </div>
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
<script src="<?= $this->assets('js/auth.js'); ?>"></script>
<?php $this->endsection(); ?>