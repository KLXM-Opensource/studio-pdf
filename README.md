# KLXM Studio – PDF-Engine (dompdf)

Stellt [dompdf](https://github.com/dompdf/dompdf) für die PDF-Erzeugung des Kerns bereit (`Core\Pdf`). Der Kern bleibt
MIT-lizenziert ohne LGPL-Abhängigkeit; wer PDFs braucht (Report, AVV, Rechnungen …), installiert dieses Paket.

**Installiert = verfügbar.** Das Manifest lädt dompdf, sobald der Kern die installierten Erweiterungen liest – ein
Einschalten je Website ist nicht nötig. Ohne dieses Paket meldet `Core\Pdf::available()` `false`, und Aufrufer bieten
stattdessen „Drucken“ an.

## Installation

```bash
composer require klxm/studio-pdf             # über Composer des Kerns
# oder: Ordner nach extensions/pdf, darin
composer install --no-dev
```

## Nutzung (im Kern dokumentiert: Entwicklerhandbuch → PDF)

```php
$bytes = Core\Pdf::render($html, ['paper' => 'A4', 'font' => 'lato', 'title' => 'Report', 'pages' => true]);
return Core\Pdf::response($bytes, 'Report-2026-10.pdf');
```

Sicherheit: keine Dateien aus dem Netz (`isRemoteEnabled` aus), kein PHP im HTML, Zugriff nur auf den eigenen Ordner
(chroot) – Bilder als `data:`-URL einbetten. Selbsttest: `php bin/console pdf:selftest` (Erweiterung dafür einschalten).

## Lizenz

MIT (dieses Paket). dompdf und seine Abhängigkeiten: LGPL-2.1 bzw. eigene Lizenzen – siehe `vendor/`.
