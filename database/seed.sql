-- Güvenli, sentetik demo verisi. Müşteri veya gizli gerçek bilgi içermez.

USE stock_inventory;

START TRANSACTION;

INSERT IGNORE INTO categories (id, name, description) VALUES
    (1, 'Elektronik', 'Bilgisayar ve günlük teknoloji ürünleri'),
    (2, 'Kırtasiye', 'Ofis ve eğitim ihtiyaçları'),
    (3, 'Temizlik', 'Günlük temizlik ve kişisel bakım ürünleri');

INSERT IGNORE INTO products (
    id, category_id, sku, name, description, quantity, critical_threshold, unit
) VALUES
    (1, 1, 'EKB-USB-001', 'USB-C kablo', '1 metre USB-C şarj ve veri kablosu', 120, 30, 'adet'),
    (2, 1, 'EKB-MSE-002', 'Kablosuz mouse', 'Ofis kullanımı için kablosuz mouse', 18, 10, 'adet'),
    (3, 1, 'EKB-KLV-003', 'Klavye', 'Kompakt mekanik klavye', 8, 12, 'adet'),
    (4, 2, 'KIR-A4-004', 'A4 kâğıt', '80 g/m² beyaz A4 kâğıt', 45, 10, 'paket'),
    (5, 2, 'KIR-KLM-005', 'Tükenmiş kalem', 'Mavi uçlu ofis kalemi', 0, 20, 'adet'),
    (6, 2, 'KIR-DEF-006', 'Sunum defteri', 'A5 sunum ve not defteri', 25, 8, 'adet'),
    (7, 3, 'TEM-CAY-007', 'Çay bardağı', 'Seramik beyaz çay bardağı', 8, 15, 'adet'),
    (8, 3, 'TEM-MEN-008', 'Mendil paketi', 'Yumuşak kutu mendil paketi', 60, 20, 'paket'),
    (9, 3, 'TEM-SAM-009', 'Şampuan', 'Günlük kullanım şampuanı', 14, 15, 'adet');

INSERT IGNORE INTO stock_movements (id, product_id, type, quantity, note, created_at) VALUES
    (1, 1, 'in', 120, 'Açılış stoğu', '2026-01-05 09:00:00'),
    (2, 2, 'in', 30, 'Açılış stoğu', '2026-01-05 09:05:00'),
    (3, 2, 'out', 12, 'Ofis dağıtımı', '2026-01-12 14:20:00'),
    (4, 3, 'in', 20, 'Açılış stoğu', '2026-01-05 09:10:00'),
    (5, 3, 'out', 12, 'Demo masası kullanımı', '2026-01-18 11:45:00'),
    (6, 4, 'in', 80, 'Açılış stoğu', '2026-01-05 09:15:00'),
    (7, 4, 'out', 35, 'Kırtasiye çıkışı', '2026-01-20 16:10:00'),
    (8, 5, 'in', 40, 'Açılış stoğu', '2026-01-05 09:20:00'),
    (9, 5, 'out', 40, 'Tükenen demo stoğu', '2026-01-19 10:30:00'),
    (10, 6, 'in', 25, 'Açılış stoğu', '2026-01-05 09:25:00'),
    (11, 7, 'in', 30, 'Açılış stoğu', '2026-01-05 09:30:00'),
    (12, 7, 'out', 22, 'Misafir kahvaltı seti', '2026-01-16 12:00:00'),
    (13, 8, 'in', 60, 'Açılış stoğu', '2026-01-05 09:35:00'),
    (14, 9, 'in', 14, 'Açılış stoğu', '2026-01-05 09:40:00');

COMMIT;
