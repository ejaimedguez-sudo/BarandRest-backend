<?php

namespace App\Console\Commands;

use App\Services\CatalogImageCleanupService;
use Illuminate\Console\Command;

class CleanupCatalogOrphanImagesCommand extends Command
{
    protected $signature = 'catalog:cleanup-orphan-images
        {--older-than-minutes=1440 : Solo limpiar archivos con esta antiguedad minima}
        {--dry-run : Simula sin borrar archivos}';

    protected $description = 'Limpia imagenes de catalogo subidas y no vinculadas a productos o menu items';

    public function handle(CatalogImageCleanupService $catalogImageCleanup): int
    {
        $olderThanMinutes = (int) $this->option('older-than-minutes');
        $dryRun = (bool) $this->option('dry-run');

        $result = $catalogImageCleanup->cleanupUnlinkedManagedFiles($olderThanMinutes, $dryRun);

        $modeLabel = $dryRun ? 'DRY RUN' : 'EJECUCION';
        $this->info("[{$modeLabel}] Escaneadas: {$result['scanned']}");
        $this->info("[{$modeLabel}] Eliminadas: ".count($result['deleted']));
        $this->line("[{$modeLabel}] Conservadas (referenciadas): ".count($result['kept_referenced']));
        $this->line("[{$modeLabel}] Conservadas (recientes): ".count($result['kept_recent']));

        if ($dryRun && count($result['deleted']) > 0) {
            $this->line('[DRY RUN] Archivos candidatos:');
            foreach ($result['deleted'] as $path) {
                $this->line('- '.$path);
            }
        }

        return self::SUCCESS;
    }
}
