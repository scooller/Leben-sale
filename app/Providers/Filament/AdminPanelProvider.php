<?php

namespace App\Providers\Filament;

use AchyutN\FilamentLogViewer\FilamentLogViewer;
use AlizHarb\ActivityLog\ActivityLogPlugin;
use AlizHarb\ActivityLog\Widgets\ActivityChartWidget;
use AlizHarb\ActivityLog\Widgets\LatestActivityWidget;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\PaymentsChartWidget;
use App\Filament\Widgets\PaymentStatusChartWidget;
use App\Filament\Widgets\SyncPlantsWidget;
use App\Filament\Widgets\SyncProjectsWidget;
use App\Filament\Widgets\UsersChartWidget;
use App\Models\SiteSetting;
use BinaryBuilds\CommandRunner\CommandRunnerPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Solo obtener configuración si la tabla ya existe (después de migraciones)
        $settings = null;
        if (Schema::hasTable('site_settings')) {
            $settings = SiteSetting::current();
            // Cargar las relaciones de media
            if ($settings) {
                $settings->load(['faviconMedia', 'logoMedia']);
            }
        }

        $defaultWidgets = [
            AccountWidget::class,
            UsersChartWidget::class,
            ActivityChartWidget::class,
            LatestActivityWidget::class,
            PaymentsChartWidget::class,
            PaymentStatusChartWidget::class,
            SyncPlantsWidget::class,
            SyncProjectsWidget::class,
        ];

        $widgetOrder = $settings?->dashboard_widget_order;
        $widgetOrder = is_array($widgetOrder) ? array_values($widgetOrder) : [];

        if (! empty($widgetOrder)) {
            $ordered = array_values(array_filter($widgetOrder, fn (string $widget): bool => in_array($widget, $defaultWidgets, true)));
            $missing = array_values(array_diff($defaultWidgets, $ordered));
            $widgets = array_merge($ordered, $missing);
        } else {
            $widgets = $defaultWidgets;
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->databaseNotifications()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->brandName($settings?->site_name ?? 'iLeben')
            ->favicon($settings?->faviconMedia?->url)
            ->brandLogo($settings?->logoMedia?->url)
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => '#eb0029',
                'danger' => '#eb0029',
                'warning' => '#eb0029',
                'gray' => '#343a40',
                'info' => '#000000',
                'success' => '#000000',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets($widgets)
            ->plugins([
                \Awcodes\Curator\CuratorPlugin::make()
                    ->label('Archivos')
                    ->pluralLabel('Archivos')
                    ->navigationIcon('heroicon-o-photo')
                    ->navigationGroup('Contenido')
                    ->navigationSort(1),
                ActivityLogPlugin::make()
                    ->label('Logs')
                    ->pluralLabel('Logs')
                    ->navigationGroup('Monitoreo')
                    ->navigationSort(1),
                FilamentLogViewer::make()
                    ->authorize(fn (): bool => Auth::user()?->isAdmin() ?? false)
                    ->navigationGroup('Monitoreo')
                    ->navigationIcon('heroicon-o-document-text')
                    ->navigationLabel('Log Viewer')
                    ->navigationSort(2),
                CommandRunnerPlugin::make()
                    ->authorize(fn (): bool => Auth::user()?->isAdmin() ?? false)
                    ->navigationGroup('Herramientas')
                    ->navigationLabel('Command Runner')
                    ->navigationIcon('heroicon-o-command-line')
                    ->navigationSort(1),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
