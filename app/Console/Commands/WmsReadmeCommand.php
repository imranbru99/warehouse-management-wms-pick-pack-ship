<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WmsReadmeCommand extends Command
{
    protected $signature = 'wms:readme';

    protected $description = 'Regenerate the README Modules feature section from modules/*/module.json';

    public function handle(): int
    {
        $readmePath = base_path('README.md');

        if (! File::exists($readmePath)) {
            $this->error('README.md not found.');

            return self::FAILURE;
        }

        $readme = File::get($readmePath);
        $section = $this->buildSection();

        $start = '<!-- wms:modules-start -->';
        $end = '<!-- wms:modules-end -->';

        if (str_contains($readme, $start) && str_contains($readme, $end)) {
            $pattern = '/'.preg_quote($start, '/').'.*?'.preg_quote($end, '/').'/s';
            $readme = preg_replace($pattern, $start."\n".$section."\n".$end, $readme) ?? $readme;
        } else {
            $readme = preg_replace(
                '/(## Modules\n\n)(.*?)(\n## Packages)/s',
                '$1'.$start."\n".$section."\n".$end.'$3',
                $readme,
                1,
                $count
            ) ?? $readme;

            if ($count === 0) {
                $this->error('Could not locate Modules section in README.');

                return self::FAILURE;
            }
        }

        File::put($readmePath, $readme);
        $this->info('README modules section updated.');

        return self::SUCCESS;
    }

    private function buildSection(): string
    {
        $files = glob(base_path('modules/*/module.json')) ?: [];
        usort($files, function (string $a, string $b): int {
            $dataA = json_decode((string) file_get_contents($a), true) ?: [];
            $dataB = json_decode((string) file_get_contents($b), true) ?: [];
            $orderA = (int) ($dataA['sort_order'] ?? 999);
            $orderB = (int) ($dataB['sort_order'] ?? 999);

            return $orderA <=> $orderB ?: strcmp($dataA['key'] ?? '', $dataB['key'] ?? '');
        });

        $lines = [
            'Every business module is toggleable per tenant via **Module Manager** (`/app` → Settings). Catalog lives in `modules/*/module.json` (source of truth for README + public Features).',
            '',
            'Regenerate this list: `php artisan wms:readme`',
            '',
        ];

        foreach ($files as $file) {
            /** @var array<string, mixed> $data */
            $data = json_decode((string) file_get_contents($file), true) ?: [];
            $key = (string) ($data['key'] ?? basename(dirname($file)));
            $name = (string) ($data['name'] ?? $key);
            $description = (string) ($data['description'] ?? '');
            $features = $data['features'] ?? [];

            $lines[] = "### {$name} (`{$key}`)";
            if ($description !== '') {
                $lines[] = '';
                $lines[] = $description;
            }
            if (is_array($features) && $features !== []) {
                $lines[] = '';
                foreach ($features as $feature) {
                    $lines[] = '- '.(string) $feature;
                }
            }
            $lines[] = '';
        }

        return rtrim(implode("\n", $lines));
    }
}
