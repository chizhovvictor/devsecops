<?php if ($this->v('footer')): ?>
<footer class="py-3">
    <ul class="nav justify-content-center">
        <?php foreach ($this->v('footer')['links'] as ['name' => $name, 'link' => $link]): ?>
        <li class="nav-item">
            <a href="<?=$link?>" class="nav-link px-4 text-secondary"><small><?=$name?></small></a>
        </li>
        <?php endforeach; ?>
        <?php if ($this->v('username')): ?>
        <li class="nav-item">
            <a href="#" class="nav-link px-4 text-secondary" onclick="logout()"><small>Logout</small></a>
        </li>
        <?php endif; ?>
    </ul>
    <p class="mb-0 text-secondary text-center"><small>© 2024 <?=$this->v('footer')['company']?></small></p>
</footer>
<?php endif; ?>