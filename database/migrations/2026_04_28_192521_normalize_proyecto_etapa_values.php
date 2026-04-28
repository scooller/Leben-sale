<?php

use App\Models\Proyecto;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Proyecto::query()
            ->select(['id', 'etapa'])
            ->whereNotNull('etapa')
            ->orderBy('id')
            ->chunkById(200, function ($proyectos): void {
                foreach ($proyectos as $proyecto) {
                    $normalizedEtapa = Proyecto::normalizeEtapa($proyecto->etapa);

                    if ($normalizedEtapa === null || $normalizedEtapa === $proyecto->etapa) {
                        continue;
                    }

                    Proyecto::query()
                        ->whereKey($proyecto->id)
                        ->update(['etapa' => $normalizedEtapa]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
