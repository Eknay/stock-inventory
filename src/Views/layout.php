<?php

declare(strict_types=1);

/** @var string $content */
/** @var string $title */
/** @var string $page */
/** @var string $appName */
/** @var list<array{type: string, message: string}> $flashes */
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ürün ve stok hareketlerini yönetmek için basit envanter takip paneli">
    <title><?= e($title) ?> | <?= e($appName) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/app.js" defer></script>
</head>
<body data-page="<?= e($page) ?>">
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <span class="brand-mark">S</span>
            <span>
                <strong><?= e($appName) ?></strong>
                <small>Envanter paneli</small>
            </span>
        </div>
        <nav class="main-nav" aria-label="Ana navigasyon">
            <a class="nav-link <?= $page === 'dashboard' ? 'is-active' : '' ?>" href="/">
                <span class="nav-icon">⌂</span> Dashboard
            </a>
            <a class="nav-link <?= $page === 'products' ? 'is-active' : '' ?>" href="/products">
                <span class="nav-icon">▦</span> Ürünler
            </a>
            <a class="nav-link <?= $page === 'categories' ? 'is-active' : '' ?>" href="/categories">
                <span class="nav-icon">◇</span> Kategoriler
            </a>
        </nav>
        <div class="sidebar-footer">
            <span class="status-dot"></span>
            <span>Stok sistemi aktif</span>
        </div>
    </aside>

    <div class="main-column">
        <header class="topbar">
            <button class="menu-toggle" type="button" aria-label="Menüyü aç/kapat" data-menu-toggle>
                <span></span><span></span><span></span>
            </button>
            <div class="topbar-title">
                <span class="eyebrow">STOK YÖNETİMİ</span>
                <strong><?= e($title) ?></strong>
            </div>
            <a class="button button-primary button-small" href="/products/create">+ Yeni ürün</a>
        </header>

        <main class="page-content">
            <?php foreach (($flashes ?? []) as $flash): ?>
                <div class="alert alert-<?= e($flash['type'] ?? 'info') ?>" role="status">
                    <span><?= e($flash['message'] ?? '') ?></span>
                </div>
            <?php endforeach; ?>
            <?= $content ?>
        </main>

        <footer class="footer">
            <span>Stok / Envanter Takip</span>
            <span>PHP 8 & MySQL ile geliştirildi</span>
        </footer>
    </div>
</div>
</body>
</html>
