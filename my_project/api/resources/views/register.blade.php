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
            <div class="card border border-light mx-auto" style="max-width: 400px; width: 100%">
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <img src="<?= $this->assets('img/Instagram_logo.svg') ?>" alt="Logo" style="max-width: 220px; width: 100%; height: auto; display: inline-block;" />
                    </div>
                    <form action="/register" novalidate>
                        <!-- Name input -->
                        <div class="mb-3">
                            <label for="first_name">First name</label>
                            <input name="first_name" type="text" class="form-control" id="first_name" placeholder="First name" required>
                            <div class="invalid-feedback">
                                Please provide a valid first name.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="last_name">Last name</label>
                            <input name="last_name" type="text" class="form-control" id="last_name" placeholder="Last name" required>
                            <div class="invalid-feedback">
                                Please provide a valid last name.
                            </div>
                        </div>

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
                        <div style="width: 100px">
                            <div class="progress" style="height: 5px"></div>
                        </div>
                        <div class="mt-5">
                            <!-- Submit button -->
                            <button type="submit" class="btn btn-primary btn-block mb-4">
                                Sign up
                            </button>
                            <!-- Login buttons -->
                            <div class="text-center">
                                <span class="text-muted">If you already have account</span>
                                <a href="/login" class="text-primary">
                                    Login
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php $this->endsection(); ?>

<?php $this->section('script'); ?>
<script src="<?= $this->assets('js/auth.js'); ?>"></script>
<?php $this->endsection(); ?>