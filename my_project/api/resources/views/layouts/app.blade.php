<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>App - Photo overlay tool</title>
    <?php $this->include('components/meta.blade.php'); ?>
    <?php $this->yield('css'); ?>
</head>
<body class="background-radial-gradient">
    <?php $this->include('components/header.blade.php'); ?>

    <div class="app-content">
        <?php $this->yield('content'); ?>

        <?php $this->include('components/footer.blade.php'); ?>
    </div>

    <!-- Scripts -->
    <script src="<?= $this->assets('js/index.js') ?>"></script>
    <script src="<?= $this->assets('js/handle.js') ?>"></script>
    <script src="<?= $this->assets('js/component.js') ?>"></script>
    <script src="<?= $this->assets('js/create.js') ?>"></script>
    <?php $this->yield('script'); ?>
</body>
</html>