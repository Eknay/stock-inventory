<?php

declare(strict_types=1);

/** @var array<string, int> $summary */
/** @var list<array<string, mixed>> $recentMovements */
/** @var list<array<string, mixed>> $categoryBreakdown */
$critical = (int) ($summary['critical_stock'] ?? 0);
$outOfStock = (int) ($summary['out_of_stock'] ?? 0);
?>
<section class="page-heading page-heading-dashboard">
    <div>
        <span class="eyebrow">GENEL BAKIŞ</span>
        <h1>Stok durumu, tek bakışta.</h1>
        <p>Ürünlerinizin mevcut miktarını ve son hareketleri takip edin.</p>
    </div>
    <div class="heading-actions">
        <a class="button button-secondary" href="/products">Ürün listesini aç</a>
        <a class="button button-primary" href="/products/create">+ Ürün ekle</a>
    </div>
</section>

<section class="stat-grid" aria-label="Stok özeti">
    <article class="stat-card stat-card-blue">
        <div class="stat-icon">▦</div>
        <div>
            <span class="stat-label">Toplam ürün</span>
            <strong class="stat-value"><?= e(format_number($summary['total_products'] ?? 0)) ?></strong>
            <span class="stat-hint">kayıtlı ürün çeşidi</span>
        </div>
    </article>
    <article class="stat-card stat-card-green">
        <div class="stat-icon">◫</div>
        <div>
            <span class="stat-label">Toplam stok</span>
            <strong class="stat-value"><?= e(format_number($summary['total_units'] ?? 0)) ?></strong>
            <span class="stat-hint">toplam stok birimi</span>
        </div>
    </article>
    <article class="stat-card stat-card-orange">
        <div class="stat-icon">!</div>
        <div>
            <span class="stat-label">Kritik stok</span>
            <strong class="stat-value"><?= e(format_number($critical)) ?></strong>
            <span class="stat-hint">eşiğe ulaşan ürün</span>
        </div>
    </article>
    <article class="stat-card stat-card-red">
        <div class="stat-icon">×</div>
        <div>
            <span class="stat-label">Tükenen ürün</span>
            <strong class="stat-value"><?= e(format_number($outOfStock)) ?></strong>
            <span class="stat-hint">stoktan çıkan ürün</span>
        </div>
    </article>
</section>

<div class="dashboard-grid">
    <section class="panel panel-wide">
        <div class="panel-header">
            <div>
                <span class="eyebrow">HAREKETLER</span>
                <h2>Son stok hareketleri</h2>
            </div>
            <a class="text-link" href="/products">Tümünü gör →</a>
        </div>
        <?php if ($recentMovements === []): ?>
            <div class="empty-state compact">
                <div class="empty-icon">↕</div>
                <h3>Henüz hareket yok</h3>
                <p>Ürün ekledikçe giriş ve çıkış hareketleri burada görünecek.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Hareket</th>
                        <th>Miktar</th>
                        <th>Tarih</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentMovements as $movement): ?>
                        <?php $isIn = ($movement['type'] ?? '') === 'in'; ?>
                        <tr>
                            <td>
                                <strong><?= e($movement['product_name'] ?? '') ?></strong>
                                <small><?= e($movement['sku'] ?? '') ?></small>
                            </td>
                            <td><span class="pill <?= $isIn ? 'pill-green' : 'pill-red' ?>"><?= $isIn ? 'Giriş' : 'Çıkış' ?></span></td>
                            <td class="quantity <?= $isIn ? 'text-green' : 'text-red' ?>"><?= $isIn ? '+' : '−' ?><?= e($movement['quantity'] ?? 0) ?> <?= e($movement['unit'] ?? '') ?></td>
                            <td class="muted"><?= e(date('d.m.Y H:i', strtotime((string) ($movement['created_at'] ?? '')))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <span class="eyebrow">DAĞILIM</span>
                <h2>Kategori özeti</h2>
            </div>
            <a class="text-link" href="/categories">Yönet →</a>
        </div>
        <div class="category-list">
            <?php if ($categoryBreakdown === []): ?>
                <p class="muted">Henüz kategori bulunmuyor.</p>
            <?php else: ?>
                <?php foreach ($categoryBreakdown as $category): ?>
                    <div class="category-row">
                        <div class="category-row-main">
                            <span class="category-dot"></span>
                            <strong><?= e($category['category_name'] ?? '') ?></strong>
                        </div>
                        <div class="category-row-meta">
                            <span><?= e($category['product_count'] ?? 0) ?> ürün</span>
                            <strong><?= e(format_number($category['total_units'] ?? 0)) ?> birim</strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>
