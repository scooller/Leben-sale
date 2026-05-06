<?php

namespace Database\Seeders;

use App\Models\BrokerBenefit;
use App\Models\BrokerCategory;
use Illuminate\Database\Seeder;

class BrokerCategorySeeder extends Seeder
{
    /**
     * Beneficios por sección. Cada entrada define:
     * section  → nombre de la sección
     * title    → nombre del beneficio
     * black    → 'included' | 'not_applicable'
     * gold     → 'included' | 'not_applicable'
     * silver   → 'included' | 'not_applicable'
     *
     * @var array<int, array{section: string, title: string, black: string, gold: string, silver: string}>
     */
    private array $benefits = [
        // COMUNICACIÓN
        ['section' => 'Comunicación', 'title' => 'Contacto Semanal KAM / Mailing',                           'black' => 'included', 'gold' => 'included', 'silver' => 'included'],
        ['section' => 'Comunicación', 'title' => 'Contacto Quincenal / Mailing Comisiones y Avances Leben',   'black' => 'included', 'gold' => 'included', 'silver' => 'included'],
        ['section' => 'Comunicación', 'title' => 'Invitación a Pre Lanzamientos',                             'black' => 'included', 'gold' => 'included', 'silver' => 'not_applicable'],
        ['section' => 'Comunicación', 'title' => 'Canal de Difusión Mail / Whats App',                        'black' => 'included', 'gold' => 'included', 'silver' => 'included'],
        ['section' => 'Comunicación', 'title' => 'Reuniones Semanales de Seguimiento',                        'black' => 'included', 'gold' => 'included', 'silver' => 'not_applicable'],
        ['section' => 'Comunicación', 'title' => 'Agenda de Apoyo Exclusivo Cierres',                         'black' => 'included', 'gold' => 'not_applicable', 'silver' => 'not_applicable'],

        // CAPACITACIÓN
        ['section' => 'Capacitación', 'title' => 'Capacitación Drive e Información Leben',                    'black' => 'included', 'gold' => 'included', 'silver' => 'included'],
        ['section' => 'Capacitación', 'title' => 'Capacitaciones Exclusivas Lanzamientos',                    'black' => 'included', 'gold' => 'included', 'silver' => 'not_applicable'],
        ['section' => 'Capacitación', 'title' => 'Capacitación Equipo',                                       'black' => 'included', 'gold' => 'not_applicable', 'silver' => 'not_applicable'],

        // NEGOCIO
        ['section' => 'Negocio', 'title' => 'Promesa Asistida Online o Presencial',                           'black' => 'included', 'gold' => 'included', 'silver' => 'included'],
        ['section' => 'Negocio', 'title' => 'Gestión Hipotecaria',                                            'black' => 'included', 'gold' => 'included', 'silver' => 'included'],
        ['section' => 'Negocio', 'title' => 'Stock Exclusivo de Unidades Especiales',                         'black' => 'included', 'gold' => 'included', 'silver' => 'included'],
        ['section' => 'Negocio', 'title' => 'Excepciones Comerciales en Venta Volumen',                       'black' => 'included', 'gold' => 'not_applicable', 'silver' => 'not_applicable'],

        // VISITAS
        ['section' => 'Visitas', 'title' => 'Visitas Previo Agendamiento con 24 Hrs',                         'black' => 'included', 'gold' => 'included', 'silver' => 'included'],
        ['section' => 'Visitas', 'title' => 'Free Pass de Ingreso y Visita',                                  'black' => 'included', 'gold' => 'not_applicable', 'silver' => 'not_applicable'],

        // PAGOS
        ['section' => 'Pagos', 'title' => 'Pago a 30 días, posterior a Emisión de Factura',                   'black' => 'not_applicable', 'gold' => 'not_applicable', 'silver' => 'included'],
        ['section' => 'Pagos', 'title' => 'Pago a 20 días, posterior a Emisión de Factura',                   'black' => 'not_applicable', 'gold' => 'included',        'silver' => 'not_applicable'],
        ['section' => 'Pagos', 'title' => 'Pago a 15 días, posterior a Emisión de Factura',                   'black' => 'included',       'gold' => 'not_applicable', 'silver' => 'not_applicable'],
        ['section' => 'Pagos', 'title' => 'Posibilidad de Factorizar',                                        'black' => 'included', 'gold' => 'not_applicable', 'silver' => 'not_applicable'],
        ['section' => 'Pagos', 'title' => 'Pago de Comisión Diferenciada',                                    'black' => 'included', 'gold' => 'not_applicable', 'silver' => 'not_applicable'],

        // BENEFICIOS ADICIONALES
        ['section' => 'Beneficios Adicionales', 'title' => 'Asistencia a Evento de Premiación Anual',             'black' => 'included', 'gold' => 'included',        'silver' => 'not_applicable'],
        ['section' => 'Beneficios Adicionales', 'title' => 'Presupuesto de Apoyo a Ventas Privadas Leben',        'black' => 'included', 'gold' => 'included',        'silver' => 'not_applicable'],
        ['section' => 'Beneficios Adicionales', 'title' => 'Perfil Broker en Plataforma JetBrokers',              'black' => 'included', 'gold' => 'not_applicable', 'silver' => 'not_applicable'],
        ['section' => 'Beneficios Adicionales', 'title' => 'Beneficios Exclusivos a Mejores Participantes de Equipo Broker', 'black' => 'included', 'gold' => 'not_applicable', 'silver' => 'not_applicable'],
    ];

    public function run(): void
    {
        $categories = [
            [
                'name' => 'Partner Black',
                'slug' => 'partner-black',
                'headline' => 'Ventas sobre 15.000 UF en un período de 3 meses',
                'sort_order' => 1,
                'key' => 'black',
            ],
            [
                'name' => 'Partner Gold',
                'slug' => 'partner-gold',
                'headline' => 'Ventas desde 10.000 hasta 15.000 UF en un período de 3 meses',
                'sort_order' => 2,
                'key' => 'gold',
            ],
            [
                'name' => 'Partner Silver',
                'slug' => 'partner-silver',
                'headline' => 'Ventas desde 0 hasta 10.000 UF en un período de 3 meses',
                'sort_order' => 3,
                'key' => 'silver',
            ],
        ];

        foreach ($categories as $catData) {
            $key = $catData['key'];
            unset($catData['key']);

            /** @var BrokerCategory $category */
            $category = BrokerCategory::firstOrCreate(
                ['slug' => $catData['slug']],
                array_merge($catData, ['is_active' => true]),
            );

            foreach ($this->benefits as $sort => $benefit) {
                BrokerBenefit::firstOrCreate(
                    [
                        'broker_category_id' => $category->id,
                        'title' => $benefit['title'],
                        'section' => $benefit['section'],
                    ],
                    [
                        'status' => $benefit[$key],
                        'sort_order' => $sort + 1,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
