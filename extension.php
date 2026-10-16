<?php
// SPDX-License-Identifier: MIT
// KLXM Studio – PDF-Engine. Copyright (C) 2026 KLXM and contributors (see LICENSE)
/**
 * Erweiterung „pdf“: stellt dompdf (LGPL-2.1, eigenes Paket – nicht Teil des MIT-Kerns) für Core\Pdf bereit.
 * Installiert = verfügbar: Das Manifest lädt die Bibliothek, sobald der Kern die installierten Erweiterungen liest –
 * ein Einschalten je Website ist nicht nötig. Danach liefern Pdf::available() true und Pdf::render() echte PDFs.
 * Installation: composer require klxm/studio-pdf (Kern) oder im Ordner extensions/pdf „composer install“.
 * Prüfen: php bin/console pdf:selftest (sobald eingeschaltet) bzw. Administration → Funktionen & Erweiterungen.
 */
declare(strict_types=1);

// dompdf: über Composer des Kerns oder eigenes vendor/ dieser Erweiterung
if (!class_exists(\Dompdf\Dompdf::class) && is_file(__DIR__ . '/vendor/autoload.php')) require_once __DIR__ . '/vendor/autoload.php';

return [
    'name' => 'pdf',
    'label' => 'PDF-Engine (dompdf)',
    'version' => '0.1.0',
    'requires' => '>=1.0.0',
    'description' => 'Stellt dompdf für die PDF-Erzeugung des Kerns bereit (Core\Pdf) – Report, AVV und andere Erweiterungen erzeugen damit PDF-Dateien. Installiert genügt; Einschalten nur für den Selbsttest.',
    'author' => 'KLXM Crossmedia GmbH and contributors',
    'license' => 'MIT (Bibliothek dompdf: LGPL-2.1)',
    'risk' => 'Keine Anfragen an fremde Dienste: Bilder und Schriften aus dem Netz sind abgeschaltet, PHP im HTML ebenso. Freie Schriften lädt der Kern einmal von Fontsource (cdn.jsdelivr.net), wenn ein Aufrufer eine anfordert.',
    'provides' => ['PDF-Engine dompdf für Core\Pdf', 'Befehl pdf:selftest'],
    'commands' => ['pdf:selftest'],
    'requirements' => fn(): array => [
        __('PDF-Engine: dompdf installiert') => class_exists(\Dompdf\Dompdf::class),
        __('PDF-Engine: PHP-Erweiterung mbstring') => extension_loaded('mbstring'),
        __('PDF-Engine: PHP-Erweiterung gd (Bilder)') => extension_loaded('gd') ? true : null,
    ],
    'boot' => function (Core\Extension $x): void {
        $x->command('pdf:selftest', 'PDF-Engine: dompdf vorhanden, Beispiel-PDF erzeugen, keine Dateien aus dem Netz', function (array $args): int {
            $ok = 0; $fail = [];
            $chk = function (string $what, bool $v) use (&$ok, &$fail): void { $v ? $ok++ : $fail[] = $what; };
            $chk('Engine verfügbar', Core\Pdf::available() && Core\Pdf::engine() === 'dompdf');
            try {
                $pdf = Core\Pdf::render('<h1>Test äöü €</h1><p>KLXM Studio</p><img src="https://example.org/x.png" alt="">', ['title' => 'Selbsttest', 'pages' => true]);
                $chk('PDF erzeugt', str_starts_with($pdf, '%PDF-') && strlen($pdf) > 800);
                $chk('Titel gesetzt', str_contains($pdf, 'Selbsttest'));
            } catch (\Throwable $e) {
                $fail[] = 'Rendern: ' . $e->getMessage();
            }
            foreach ($fail as $f) echo "  FEHLER: $f\n";
            echo "  $ok Prüfungen bestanden, " . count($fail) . " fehlgeschlagen\n";
            return $fail ? 1 : 0;
        });
    },
];
