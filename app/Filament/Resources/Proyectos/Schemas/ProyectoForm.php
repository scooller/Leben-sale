<?php

namespace App\Filament\Resources\Proyectos\Schemas;

use App\Models\Proyecto;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProyectoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información General')
                    ->description('Datos básicos del proyecto')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Proyecto')
                            ->disabled()
                            ->required(),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->disabled()
                            ->rows(3),

                        Select::make('tipo')
                            ->label('Tipo de Proyecto')
                            ->multiple()
                            ->options([
                                'best' => 'Best',
                                'broker' => 'Broker',
                                'home' => 'Home',
                                'icon' => 'Icon',
                                'invest' => 'Invest',
                            ])
                            ->searchable()
                            ->preload(),

                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->disabled(),

                        TextInput::make('comuna')
                            ->label('Comuna')
                            ->disabled(),

                        TextInput::make('provincia')
                            ->label('Provincia')
                            ->disabled(),

                        TextInput::make('region')
                            ->label('Región')
                            ->disabled(),

                        TextInput::make('razon_social')
                            ->label('Razón Social')
                            ->disabled(),

                        TextInput::make('rut')
                            ->label('RUT')
                            ->disabled(),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->disabled(),

                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->disabled(),

                        TextInput::make('pagina_web')
                            ->label('Página Web')
                            ->url()
                            ->disabled(),

                        DatePicker::make('fecha_inicio_ventas')
                            ->label('Fecha Inicio Ventas')
                            ->disabled(),

                        TextInput::make('fecha_entrega')
                            ->label('Fecha de Entrega')
                            ->disabled(),

                        TextInput::make('etapa')
                            ->label('Etapa')
                            ->disabled(),

                        TextInput::make('horario_atencion')
                            ->label('Horario de Atención')
                            ->disabled(),

                        Toggle::make('is_active')
                            ->label('Activo')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Pago y Financiamiento')
                    ->description('Configuración de códigos de comercio, descuentos, precios y opciones de financiamiento')
                    ->schema([
                        Select::make('transbank_commerce_code')
                            ->label('Código de Comercio Transbank Mall')
                            ->helperText('Código único de comercio para Transbank en este proyecto')
                            ->options(function () {
                                $codes = config('payments.gateways.transbank.commerce_codes', []);

                                if (empty($codes)) {
                                    return [];
                                }

                                // Crear opciones: code => "Nombre del Proyecto - CODE"
                                $options = [];
                                foreach ($codes as $slug => $code) {
                                    // Buscar el proyecto por slug
                                    $proyecto = Proyecto::where('slug', $slug)->first();

                                    // Si encuentra proyecto, mostrar nombre + código
                                    // Si no, mostrar slug + código como fallback
                                    if ($proyecto) {
                                        $label = "{$proyecto->name}";
                                    } else {
                                        $label = "{$slug}";
                                    }

                                    $options[$code] = $label;
                                }

                                return $options;
                            })
                            ->searchable()
                            ->nullable(),

                        Section::make('Descuentos por Producto Principal')
                            ->schema([
                                TextInput::make('dscto_m_x_prod_principal_porc')
                                    ->label('Descuento (%)')
                                    ->numeric()
                                    ->disabled()
                                    ->suffix('%'),

                                TextInput::make('dscto_m_x_prod_principal_uf')
                                    ->label('Descuento (UF)')
                                    ->numeric()
                                    ->disabled()
                                    ->suffix('UF'),
                            ])
                            ->columns(2),

                        Section::make('Descuentos por Bodega')
                            ->schema([
                                TextInput::make('dscto_m_x_bodega_porc')
                                    ->label('Descuento (%)')
                                    ->numeric()
                                    ->disabled()
                                    ->suffix('%'),

                                TextInput::make('dscto_m_x_bodega_uf')
                                    ->label('Descuento (UF)')
                                    ->numeric()
                                    ->disabled()
                                    ->suffix('UF'),
                            ])
                            ->columns(2),

                        Section::make('Descuentos por Estacionamiento')
                            ->schema([
                                TextInput::make('dscto_m_x_estac_porc')
                                    ->label('Descuento (%)')
                                    ->numeric()
                                    ->disabled()
                                    ->suffix('%'),

                                TextInput::make('dscto_m_x_estac_uf')
                                    ->label('Descuento (UF)')
                                    ->numeric()
                                    ->disabled()
                                    ->suffix('UF'),
                            ])
                            ->columns(2),

                        Section::make('Descuentos por Otros Productos')
                            ->schema([
                                TextInput::make('dscto_max_otros_porc')
                                    ->label('Descuento (%)')
                                    ->numeric()
                                    ->disabled()
                                    ->suffix('%'),

                                TextInput::make('dscto_max_otros_prod_uf')
                                    ->label('Descuento (UF)')
                                    ->numeric()
                                    ->disabled()
                                    ->suffix('UF'),
                            ])
                            ->columns(2),

                        TextInput::make('dscto_maximo_aporte_leben')
                            ->label('Descuento Máximo por Aporte Leben')
                            ->numeric()
                            ->disabled()
                            ->suffix('%'),

                        Section::make('Opciones de Financiamiento')
                            ->description('Plazos disponibles de financiamiento en años')
                            ->schema([
                                TextInput::make('n_anos_1')
                                    ->label('Opción 1 (años)')
                                    ->numeric()
                                    ->disabled(),

                                TextInput::make('n_anos_2')
                                    ->label('Opción 2 (años)')
                                    ->numeric()
                                    ->disabled(),

                                TextInput::make('n_anos_3')
                                    ->label('Opción 3 (años)')
                                    ->numeric()
                                    ->disabled(),

                                TextInput::make('n_anos_4')
                                    ->label('Opción 4 (años)')
                                    ->numeric()
                                    ->disabled(),
                            ])
                            ->columns(4),

                        Section::make('Reserva Exigida')
                            ->schema([
                                TextInput::make('valor_reserva_exigido_defecto_peso')
                                    ->label('Valor Defecto ($)')
                                    ->numeric()
                                    ->prefix('$'),

                                TextInput::make('valor_reserva_exigido_min_peso')
                                    ->label('Valor Mínimo ($)')
                                    ->numeric()
                                    ->disabled()
                                    ->prefix('$'),
                            ])
                            ->columns(2),

                        TextInput::make('tasa')
                            ->label('Tasa de Interés (%)')
                            ->numeric()
                            ->disabled()
                            ->suffix('%'),

                        Toggle::make('entrega_inmediata')
                            ->label('Entrega Inmediata')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }
}
