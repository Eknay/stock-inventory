# Güvenlik Politikası

## Desteklenen sürümler

Bu depodaki en son sürüm geliştirme amacıyla takip edilir. Üretim kullanımı için güncel sürüm ve düzeltmeler uygulanmalıdır.

## Raporlama

Güvenlik açığını herkese açık bir issue veya forum yorumu olarak paylaşmayın. Sorunu, yeniden üretme adımları, etki alanı ve önerilen düzeltmeyi içeren bir özel raporla proje yöneticisine iletin. Rapor 7 iş günü içinde değerlendirilir ve düzeltme planı paylaşılır.

## Uygulanan önlemler

- PDO prepared statements ve `ATTR_EMULATE_PREPARES => false`
- Girdi doğrulama, sunucu tarafı iş kuralları ve transaction kullanımı
- HTML çıktılarında bağlama uygun escaping
- Oturum tabanlı CSRF token ve `hash_equals()` doğrulaması
- HttpOnly / SameSite çerez ayarları ve HTTPS algılanınca Secure çerez
- CSP, `X-Frame-Options`, `X-Content-Type-Options` ve `Referrer-Policy` başlıkları
- Production ortamında ayrıntılı hata gizleme
- Gerçek kimlik bilgilerinin `config/config.php` ve `.env` dosyalarında tutulması; bu dosyaların Git'e eklenmemesi

## Üretim öncesi kontrol listesi

Bu proje küçük ekipler için geliştirilmiş bir portföy uygulamasıdır ve kullanıcı girişi içermez. Üretime almadan önce aşağıdakiler eklenmelidir:

1. Kimlik doğrulama ve rol bazlı yetkilendirme
2. HTTPS zorunluluğu ve güvenli yedekleme/izleme
3. Hız sınırlama, brute-force koruması ve güvenlik günlüğü
4. CI üzerinde statik analiz, bağımlılık taraması ve otomatik test
5. Gizli bilgiler için üretimde Docker secrets veya yönetilen secret store
