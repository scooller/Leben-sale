<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('plants', 'opportunity_id') || Schema::hasColumn('plants', 'superficie_vendible')) {
            Schema::table('plants', function (Blueprint $table) {
                $columns = [];

                if (Schema::hasColumn('plants', 'opportunity_id')) {
                    $columns[] = 'opportunity_id';
                }

                if (Schema::hasColumn('plants', 'superficie_vendible')) {
                    $columns[] = 'superficie_vendible';
                }

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (
            Schema::hasColumn('proyectos', 'dscto_m_x_prod_principal_porc')
            || Schema::hasColumn('proyectos', 'dscto_m_x_prod_principal_uf')
            || Schema::hasColumn('proyectos', 'dscto_m_x_bodega_porc')
            || Schema::hasColumn('proyectos', 'dscto_m_x_bodega_uf')
            || Schema::hasColumn('proyectos', 'dscto_m_x_estac_porc')
            || Schema::hasColumn('proyectos', 'dscto_m_x_estac_uf')
            || Schema::hasColumn('proyectos', 'dscto_max_otros_porc')
            || Schema::hasColumn('proyectos', 'dscto_max_otros_prod_uf')
            || Schema::hasColumn('proyectos', 'dscto_maximo_aporte_leben')
            || Schema::hasColumn('proyectos', 'n_anos_1')
            || Schema::hasColumn('proyectos', 'n_anos_2')
            || Schema::hasColumn('proyectos', 'n_anos_3')
            || Schema::hasColumn('proyectos', 'n_anos_4')
            || Schema::hasColumn('proyectos', 'tasa')
        ) {
            Schema::table('proyectos', function (Blueprint $table) {
                $columns = [];

                foreach ([
                    'dscto_m_x_prod_principal_porc',
                    'dscto_m_x_prod_principal_uf',
                    'dscto_m_x_bodega_porc',
                    'dscto_m_x_bodega_uf',
                    'dscto_m_x_estac_porc',
                    'dscto_m_x_estac_uf',
                    'dscto_max_otros_porc',
                    'dscto_max_otros_prod_uf',
                    'dscto_maximo_aporte_leben',
                    'n_anos_1',
                    'n_anos_2',
                    'n_anos_3',
                    'n_anos_4',
                    'tasa',
                ] as $column) {
                    if (Schema::hasColumn('proyectos', $column)) {
                        $columns[] = $column;
                    }
                }

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('plants', 'opportunity_id')) {
            Schema::table('plants', function (Blueprint $table) {
                $table->string('opportunity_id')->nullable()->after('superficie_util');
            });
        }

        if (! Schema::hasColumn('plants', 'superficie_vendible')) {
            Schema::table('plants', function (Blueprint $table) {
                $table->decimal('superficie_vendible', 10, 2)->default(0)->after('superficie_terraza');
            });
        }

        Schema::table('proyectos', function (Blueprint $table) {
            if (! Schema::hasColumn('proyectos', 'dscto_m_x_prod_principal_porc')) {
                $table->decimal('dscto_m_x_prod_principal_porc', 5, 2)->default(0)->after('horario_atencion');
            }

            if (! Schema::hasColumn('proyectos', 'dscto_m_x_prod_principal_uf')) {
                $table->decimal('dscto_m_x_prod_principal_uf', 8, 2)->default(0)->after('dscto_m_x_prod_principal_porc');
            }

            if (! Schema::hasColumn('proyectos', 'dscto_m_x_bodega_porc')) {
                $table->decimal('dscto_m_x_bodega_porc', 5, 2)->default(0)->after('dscto_m_x_prod_principal_uf');
            }

            if (! Schema::hasColumn('proyectos', 'dscto_m_x_bodega_uf')) {
                $table->decimal('dscto_m_x_bodega_uf', 8, 2)->default(0)->after('dscto_m_x_bodega_porc');
            }

            if (! Schema::hasColumn('proyectos', 'dscto_m_x_estac_porc')) {
                $table->decimal('dscto_m_x_estac_porc', 5, 2)->default(0)->after('dscto_m_x_bodega_uf');
            }

            if (! Schema::hasColumn('proyectos', 'dscto_m_x_estac_uf')) {
                $table->decimal('dscto_m_x_estac_uf', 8, 2)->default(0)->after('dscto_m_x_estac_porc');
            }

            if (! Schema::hasColumn('proyectos', 'dscto_max_otros_porc')) {
                $table->decimal('dscto_max_otros_porc', 5, 2)->default(0)->after('dscto_m_x_estac_uf');
            }

            if (! Schema::hasColumn('proyectos', 'dscto_max_otros_prod_uf')) {
                $table->decimal('dscto_max_otros_prod_uf', 8, 2)->default(0)->after('dscto_max_otros_porc');
            }

            if (! Schema::hasColumn('proyectos', 'dscto_maximo_aporte_leben')) {
                $table->decimal('dscto_maximo_aporte_leben', 5, 2)->default(0)->after('dscto_max_otros_prod_uf');
            }

            if (! Schema::hasColumn('proyectos', 'n_anos_1')) {
                $table->integer('n_anos_1')->nullable()->after('dscto_maximo_aporte_leben');
            }

            if (! Schema::hasColumn('proyectos', 'n_anos_2')) {
                $table->integer('n_anos_2')->nullable()->after('n_anos_1');
            }

            if (! Schema::hasColumn('proyectos', 'n_anos_3')) {
                $table->integer('n_anos_3')->nullable()->after('n_anos_2');
            }

            if (! Schema::hasColumn('proyectos', 'n_anos_4')) {
                $table->integer('n_anos_4')->nullable()->after('n_anos_3');
            }

            if (! Schema::hasColumn('proyectos', 'tasa')) {
                $table->decimal('tasa', 10, 6)->nullable()->after('valor_reserva_exigido_min_peso');
            }
        });
    }
};
