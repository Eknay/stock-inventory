<?php

declare(strict_types=1);

/** @var list<array<string, mixed>> $products */
/** @var list<array<string, mixed>> $categories */
/** @var array<string, mixed> $filters */
$statusLabels = [
    'available' => 'Yeterli stok',
    'critical' => 'Kritik seviye',
    'out' => 'Tükendi',
];
?>
<section class="page-heading">
    <div>
        <span class="eyebrow">ENVANTER</span>
        <h1>Ürünler</h1>
        <p>Stok miktarlarını ve eşiklerini yönetin; hareketleri ürün detayından kaydedin.</p>
    </div>
    <a class="button button-primary" href="/products/create">+ Yeni ürün</a>
</section>

<section class="panel filter-panel">
    <form class="filter-form" method="get" action="/products">
        <div class="field field-grow">
            <label for="search">Ürün ara</label>
            <div class="input-with-icon">
                <span aria-hidden="true">⌕</span>
                <input id="search" name="search" type="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Ürün adı veya SKU">
            </div>
        </div>
        <div class="field">
            <label for="category_id">Kategori</label>
            <select id="category_id" name="category_id">
                <option value="">Tüm kategoriler</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e($category['id']) ?>" <?= (string) ($filters['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="status">Durum</label>
            <select id="status" name="status">
                <?php foreach (['all' => 'Tüm ürünler', 'available' => 'Yeterli stok', 'critical' => 'Kritik seviye', 'out' => 'Tükendi'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= ($filters['status'] ?? 'all') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="button button-secondary" type="submit">Filtrele</button>
        <a class="button button-ghost" href="/products">Temizle</a>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <span class="eyebrow">KAYITLAR</span>
            <h2><?= e(count($products)) ?> ürün listeleniyor</h2>
        </div>
    </div>
    <?php if ($products === []): ?>
        <div class="empty-state">
            <div class="empty-icon">▦</div>
            <h3>Ürün bulunamadı</h3>
            <p>Arama veya filtre kriterlerinizi değiştirin, ya da yeni bir ürün ekleyin.</p>
            <a class="button button-primary" href="/products/create">+ İlk ürünü ekle</a>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <?php
                    $status = (string) ($product['stock_status'] ?? 'available');
                    $statusLabel = $statusLabels[$status] ?? 'Bilinmiyor';
                    $statusClass = $status === 'critical' ? 'pill-orange' : ($status === 'out' ? 'pill-red' : 'pill-green');
                    ?>
                    <tr>
                        <td>
                            <div class="product-cell">
                                <span class="product-avatar"><?= e(strtoupper(substr((string) ($product['name'] ?? 'U'), 0, 1))) ?></span>
                                <span>
                                    <strong><?= e($product['name']) ?></strong>
                                    <small><?= e($product['sku']) ?></small>
                                </span>
                            </div>
                        </td>
                        <td><span class="category-chip"><?= e($product['category_name']) ?></span></td>
                        <td>
                            <strong class="stock-number"><?= e($product['quantity']) ?></strong>
                            <small class="muted"> <?= e($product['unit']) ?> · eşik <?= e($product['critical_threshold']) ?></small>
                        </td>
                        <td><span class="pill <?= e($statusClass) ?>"><?= e($statusLabel) ?></span></td>
                        <td>
                            <div class="table-actions">
                                <a class="icon-button" href="/products/<?= e($product['id']) ?>/stock" title="Stok hareketi">↕</a>
                                <a class="icon-button" href="/products/<?= e($product['id']) ?>/edit" title="Düzenle">✎</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
