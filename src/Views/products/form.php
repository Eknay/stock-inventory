<?php

declare(strict_types=1);

/** @var array<string, mixed> $product */
/** @var list<array<string, mixed>> $categories */
/** @var array<string, string> $errors */
/** @var string $mode */
$isEdit = $mode === 'edit';
$action = $isEdit ? '/products/' . (int) $product['id'] : '/products';
?>
<section class="page-heading">
    <div>
        <span class="eyebrow"><?= $isEdit ? 'ÜRÜN KAYDI' : 'YENİ KAYIT' ?></span>
        <h1><?= $isEdit ? 'Ürünü düzenle' : 'Yeni ürün ekle' ?></h1>
        <p><?= $isEdit ? 'Ürün bilgilerini ve kritik stok eşiğini güncelleyin.' : 'Ürünü tanımlayın ve başlangıç stok miktarını girin.' ?></p>
    </div>
    <a class="button button-ghost" href="/products">← Listeye dön</a>
</section>

<?php if ($errors !== []): ?>
    <div class="alert alert-error" role="alert">
        <strong>Formda düzeltilmesi gereken alanlar var.</strong>
        <ul>
            <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<section class="panel form-panel">
    <form method="post" action="<?= e($action) ?>" class="stack-form" data-product-form>
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field field-span-2">
                <label for="name">Ürün adı <span class="required">*</span></label>
                <input id="name" name="name" type="text" maxlength="150" required value="<?= e($product['name'] ?? '') ?>" placeholder="Örn. USB-C kablo">
            </div>
            <div class="field">
                <label for="sku">SKU <span class="required">*</span></label>
                <input id="sku" name="sku" type="text" maxlength="32" required value="<?= e($product['sku'] ?? '') ?>" placeholder="EKB-USB-001" data-sku-input>
                <small class="field-hint">Büyük harf, rakam, nokta, alt çizgi ve tire kullanabilirsiniz.</small>
            </div>
            <div class="field">
                <label for="category_id">Kategori <span class="required">*</span></label>
                <select id="category_id" name="category_id" required>
                    <option value="">Kategori seçin</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= e($category['id']) ?>" <?= (string) ($product['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>>
                            <?= e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="unit">Birim <span class="required">*</span></label>
                <input id="unit" name="unit" type="text" maxlength="20" required value="<?= e($product['unit'] ?? 'adet') ?>" placeholder="adet">
            </div>
            <div class="field">
                <label for="critical_threshold">Kritik stok eşiği <span class="required">*</span></label>
                <input id="critical_threshold" name="critical_threshold" type="number" min="0" max="1000000000" required value="<?= e($product['critical_threshold'] ?? 0) ?>">
                <small class="field-hint">Miktar bu eşiğe düşerse ürün kritik olarak işaretlenir.</small>
            </div>
            <?php if (!$isEdit): ?>
                <div class="field">
                    <label for="quantity">Başlangıç stok miktarı <span class="required">*</span></label>
                    <input id="quantity" name="quantity" type="number" min="0" max="1000000000" required value="<?= e($product['quantity'] ?? 0) ?>">
                    <small class="field-hint">Bu miktar açılış giriş hareketi olarak kaydedilir.</small>
                </div>
            <?php else: ?>
                <div class="field">
                    <label>Mevcut stok</label>
                    <div class="readonly-value"><strong><?= e($product['quantity'] ?? 0) ?></strong> <?= e($product['unit'] ?? 'adet') ?></div>
                    <small class="field-hint">Miktarı değiştirmek için stok giriş/çıkış hareketi oluşturun.</small>
                </div>
            <?php endif; ?>
            <div class="field field-span-2">
                <label for="description">Açıklama</label>
                <textarea id="description" name="description" rows="4" maxlength="1000" placeholder="Ürün hakkında kısa bir not..."><?= e($product['description'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="form-actions">
            <a class="button button-ghost" href="/products">Vazgeç</a>
            <button class="button button-primary" type="submit"><?= $isEdit ? 'Değişiklikleri kaydet' : 'Ürünü ekle' ?></button>
        </div>
    </form>
</section>
