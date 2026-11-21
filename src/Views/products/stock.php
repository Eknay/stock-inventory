<?php

declare(strict_types=1);

/** @var array<string, mixed> $product */
/** @var list<array<string, mixed>> $movements */
/** @var array<string, string> $errors */
?>
<section class="page-heading">
    <div>
        <span class="eyebrow">STOK HAREKETİ</span>
        <h1><?= e($product['name']) ?></h1>
        <p><?= e($product['sku']) ?> · <?= e($product['category_name']) ?></p>
    </div>
    <a class="button button-ghost" href="/products">← Ürün listesine dön</a>
</section>

<div class="stock-layout">
    <section class="panel stock-summary-card">
        <div class="stock-big-number">
            <span class="stat-label">Mevcut stok</span>
            <strong><?= e($product['quantity']) ?></strong>
            <span><?= e($product['unit']) ?></span>
        </div>
        <div class="stock-threshold">
            <span>Kritik eşik</span>
            <strong><?= e($product['critical_threshold']) ?> <?= e($product['unit']) ?></strong>
        </div>
        <a class="button button-secondary button-full" href="/products/<?= e($product['id']) ?>/edit">Ürün bilgilerini düzenle</a>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <span class="eyebrow">YENİ İŞLEM</span>
                <h2>Stok girişi / çıkışı</h2>
            </div>
        </div>
        <?php if ($errors !== []): ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="/products/<?= e($product['id']) ?>/stock" class="stack-form">
            <?= csrf_field() ?>
            <div class="field">
                <label for="type">Hareket türü <span class="required">*</span></label>
                <div class="movement-type-options">
                    <label class="radio-card radio-card-in">
                        <input type="radio" name="type" value="in" checked>
                        <span><strong>Stok girişi</strong><small>Miktar artar</small></span>
                    </label>
                    <label class="radio-card radio-card-out">
                        <input type="radio" name="type" value="out">
                        <span><strong>Stok çıkışı</strong><small>Miktar azalır</small></span>
                    </label>
                </div>
            </div>
            <div class="field">
                <label for="quantity">Miktar <span class="required">*</span></label>
                <input id="quantity" name="quantity" type="number" min="1" max="1000000000" required placeholder="Örn. 10">
            </div>
            <div class="field">
                <label for="note">Not</label>
                <textarea id="note" name="note" rows="3" maxlength="500" placeholder="Hareketin kısa açıklaması (isteğe bağlı)"></textarea>
            </div>
            <button class="button button-primary button-full" type="submit">Hareketi kaydet</button>
        </form>
    </section>
</div>

<section class="panel movements-panel">
    <div class="panel-header">
        <div>
            <span class="eyebrow">GEÇMİŞ</span>
            <h2>Bu ürünün hareketleri</h2>
        </div>
        <span class="muted"><?= e(count($movements)) ?> kayıt</span>
    </div>
    <?php if ($movements === []): ?>
        <div class="empty-state compact"><div class="empty-icon">↕</div><h3>Henüz hareket yok</h3><p>İlk stok girişinizi yukarıdaki formdan yapabilirsiniz.</p></div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Tarih</th><th>Hareket</th><th>Miktar</th><th>Not</th></tr></thead>
                <tbody>
                <?php foreach ($movements as $movement): ?>
                    <?php $isIn = ($movement['type'] ?? '') === 'in'; ?>
                    <tr>
                        <td class="muted"><?= e(date('d.m.Y H:i', strtotime((string) ($movement['created_at'] ?? '')))) ?></td>
                        <td><span class="pill <?= $isIn ? 'pill-green' : 'pill-red' ?>"><?= $isIn ? 'Giriş' : 'Çıkış' ?></span></td>
                        <td class="quantity <?= $isIn ? 'text-green' : 'text-red' ?>"><?= $isIn ? '+' : '−' ?><?= e($movement['quantity']) ?> <?= e($product['unit']) ?></td>
                        <td class="muted"><?= e($movement['note'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
