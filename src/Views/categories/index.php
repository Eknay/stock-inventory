<?php

declare(strict_types=1);

/** @var list<array<string, mixed>> $categories */
/** @var array<string, string> $errors */
/** @var array<string, mixed> $old */
?>
<section class="page-heading">
    <div>
        <span class="eyebrow">KATALOJ</span>
        <h1>Kategoriler</h1>
        <p>Ürünleri gruplamak ve filtrelemek için kategori tanımlarını yönetin.</p>
    </div>
</section>

<div class="category-layout">
    <section class="panel">
        <div class="panel-header">
            <div>
                <span class="eyebrow">KATEGORİLER</span>
                <h2><?= e(count($categories)) ?> kategori</h2>
            </div>
        </div>
        <?php if ($categories === []): ?>
            <div class="empty-state compact"><div class="empty-icon">◇</div><h3>Kategori yok</h3><p>İlk kategorinizi ekleyerek başlayın.</p></div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Kategori</th><th>Açıklama</th><th>Ürün</th></tr></thead>
                    <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><strong><?= e($category['name']) ?></strong></td>
                            <td class="muted"><?= e($category['description'] ?: '—') ?></td>
                            <td><span class="category-chip"><?= e($category['product_count']) ?> ürün</span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel category-form-panel">
        <div class="panel-header">
            <div>
                <span class="eyebrow">YENİ KATEGORİ</span>
                <h2>Kategori ekle</h2>
            </div>
        </div>
        <?php if ($errors !== []): ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="/categories" class="stack-form">
            <?= csrf_field() ?>
            <div class="field">
                <label for="name">Kategori adı <span class="required">*</span></label>
                <input id="name" name="name" type="text" maxlength="100" required value="<?= e($old['name'] ?? '') ?>" placeholder="Örn. Elektronik">
            </div>
            <div class="field">
                <label for="description">Açıklama</label>
                <textarea id="description" name="description" rows="4" maxlength="500" placeholder="Kategori hakkında kısa bir not..."><?= e($old['description'] ?? '') ?></textarea>
            </div>
            <button class="button button-primary button-full" type="submit">+ Kategoriyi ekle</button>
        </form>
    </section>
</div>
