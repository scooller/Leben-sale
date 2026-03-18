# iLeben - Plataforma backend-first para administración de proyectos y plantas

Aplicación backend-first construida con Laravel 12, Filament 5, React 19 y Web Awesome. El sistema opera como mantenedor administrativo de proyectos y plantas, con sincronización periódica contra Salesforce como sistema maestro para entidades comerciales y operacionales.

## 📋 Stack Tecnológico

### Backend
- **Laravel 12** - Framework PHP moderno
- **Filament 5** - Panel administrativo SDUI
- **PHP 8.4** - Lenguaje de programación
- **MySQL 8** - Base de datos
- **Salesforce API** (omniphx/forrest) - Integración CRM

### Frontend
- **React 19** - UI library
- **Vite** - Build tool
- **Web Awesome 3.2.1** - Design system
- **Tailwind CSS 4** - Utility-first CSS
- **GSAP** - Animaciones

### Gestión de Medios
- **Filament Curator** - Gestor centralizado de archivos/imágenes
- **CropperJS** - Editor de imágenes

### Pasarelas de Pago
- **Transbank** - TCPago Chile
- **Mercado Pago** - Latam
- **Manual** - Configuración custom

## 🏗️ Arquitectura

```
app/
├── Models/               # Eloquent Models
├── Filament/
│   ├── Resources/        # CRUD Resources
│   ├── Pages/            # Custom Pages
│   └── Widgets/          # Dashboard Widgets
├── Services/
│   ├── Salesforce/       # Integración SOQL
│   └── Payment/          # Servicios de pasarelas
├── Http/Controllers/     # API endpoints
├── Enums/                # Enums: PaymentGateway, PaymentStatus
└── Contracts/            # Interfaces

resources/
├── css/                  # Estilos Filament + Tailwind
└── js/                   # JavaScript antiguo (deprecated)

frontend/
├── src/
│   ├── components/       # React components
│   ├── pages/            # Page layouts
│   ├── context/          # React Context
│   ├── hooks/            # Custom hooks
│   └── styles/           # SCSS modules
└── dist/                 # Build output (Vite)
```

## 📦 Características Principales

### Operación y Sincronización
- ✅ **Cobertura funcional** - Administra proyectos, plantas, pagos, configuración global y activos multimedia desde el panel Filament
- ✅ **Integración con Salesforce** - Proyectos y plantas se sincronizan desde Salesforce hacia el modelo local mediante servicios y acciones dedicadas
- ✅ **Preservación de datos locales** - La sincronización evita sobrescribir atributos locales sensibles cuando el dato debe mantenerse en la base local
- ✅ **Procesamiento asíncrono** - Exportaciones y notificaciones soportadas sobre cola `database` con notificaciones persistidas en Filament

### Panel Administrativo (Filament)
- ✅ **Autenticación** - Laravel Sanctum + sessions
- ✅ **Proyectos** - CRUD administrativo, filtros operativos, `tipo` multiselección y commerce code por proyecto
- ✅ **Usuarios** - Gestión de cuentas
- ✅ **Plantas** - Catálogo sincronizado con imágenes de portada e interior vía Curator
- ✅ **Pagos** - Registro de transacciones
- ✅ **Configuración Global** - SiteSettings (9+ tabs)
  - General, Banner, Branding, Colores, Tipografía
  - SEO, Contacto, Redes Sociales, Personalización
  - Pasarelas de Pago, Mantenimiento
- ✅ **Gestor de Archivos** - Curator (File Manager centralizado)
- ✅ **Modo Mantenimiento** - RichEditor + HTML mode + Web Awesome dialog

### Integración Salesforce
- ✅ **SOQL Queries** - Consultas a objetos y campos de Salesforce mediante `omniphx/forrest`
- ✅ **Caching** - Cache de resultados SOQL con TTL configurable para reducir carga sobre la API externa
- ✅ **Sincronización** - Acciones y procesos para actualizar proyectos y plantas en el modelo local
- ✅ **Normalización de datos** - Mapeo local de `is_active`, `tipo` y otros atributos requeridos por el panel administrativo
- ✅ **Logging** - Registro y trazabilidad de operaciones de sincronización

### Frontend React
- ✅ **Home Page** - Hero section + banner promocional
- ✅ **Maintenance Mode** - Modal con Web Awesome dialog
- ✅ **SiteConfig Context** - Datos globales (logo, theme, etc)
- ✅ **Responsive Design** - Mobile-first con Web Awesome
- ✅ **Themes** - 11 temas Web Awesome preinstalados

### Gestión de Medios (Curator)
- ✅ **Centralizado** - Single File Manager en `/admin/media`
- ✅ **Integrado** - Logo, favicon, banner, maintenance images
- ✅ **Plantas con imágenes** - Campos de portada e interior integrados al mantenedor de plantas
- ✅ **RichEditor** - Attachments vía AttachCuratorMediaPlugin
- ✅ **Database** - Tabla `curator` para metadata de archivos
- ✅ **Editor** - CropperJS para redimensionar

## 🔄 Últimos Cambios Relevantes

- Sincronización de proyectos alineada con el esquema local mediante soporte para `is_active` y `tipo`
- Campo `tipo` persistido como arreglo JSON y expuesto en Filament como multiselect con valores `invest`, `broker`, `icon`
- Corrección de filtros operativos en los listados de proyectos y plantas
- Incorporación de imágenes de portada e interior para plantas usando Filament Curator
- Activación de `databaseNotifications()` en Filament para notificaciones de exportaciones en cola
- Aislamiento del entorno de testing con SQLite y validación de suite completa

## � API REST

Base URL: `/api/v1`

### Autenticación

#### POST `/api/v1/login`
Autenticación de usuario.
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

#### POST `/api/v1/register`
Registro de nuevo usuario.
```json
{
  "name": "Usuario",
  "email": "user@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

#### POST `/api/v1/logout`
Cerrar sesión (requiere autenticación).

### Proyectos

#### GET `/api/v1/proyectos`
Lista de proyectos disponibles.

**Query Parameters:**
- `perPage` - Registros por página (default: 15)
- `page` - Número de página

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Proyecto Torre Central",
      "descripcion": "...",
      "direccion": "Av. Principal 123",
      "comuna": "Santiago",
      "region": "Metropolitana",
      "etapa": "En Construcción",
      "fecha_entrega": "2026-12-31"
    }
  ],
  "total": 10,
  "per_page": 15,
  "current_page": 1
}
```

#### GET `/api/v1/proyectos/{id}`
Detalle de un proyecto específico.

### Plantas

#### GET `/api/v1/plantas?proyecto_id={id}`
Lista de plantas filtradas por proyecto (consumo recomendado para frontend).

**Query Parameters:**
- `proyecto_id` o `project_id` - Filtrar por ID de proyecto (local). **Recomendado/esperado en consumo de catálogo.**
- `salesforce_proyecto_id` - Filtrar por ID Salesforce del proyecto
- `disponible` o `available` - Disponibilidad (`1`, `true`, `yes`, `si` para disponibles | `0`, `false`, `no` para no disponibles)
- `programa` - Dormitorios (`1`, `2`, `3`, `4`, `ST`)
- `programa2` - Baños (`1`, `2`, `3`)
- `min_precio` - Precio mínimo
- `max_precio` - Precio máximo
- `perPage` - Registros por página (default: 12)
- `page` - Número de página

Notas de payload:
- La API incluye `cover_image_media` e `interior_image_media` con el objeto completo de Curator.
- También expone `cover_image_url` e `interior_image_url` como atajos para consumir la URL principal.

Nota de consumo:
- Para listar plantas de un proyecto específico, usa siempre `/api/v1/plantas?proyecto_id=X`.

**Ejemplos:**
```bash
# Plantas disponibles de un proyecto
GET /api/v1/plantas?proyecto_id=X&disponible=1

# Plantas con 2 dormitorios y 2 baños
GET /api/v1/plantas?programa=2&programa2=2

# Plantas en rango de precio
GET /api/v1/plantas?min_precio=5000&max_precio=10000

# Plantas no disponibles (reservadas)
GET /api/v1/plantas?disponible=0
```

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "salesforce_product_id": "01t5e000001AbcDEF",
      "salesforce_proyecto_id": "a015e000001XyZABC",
      "name": "101",
      "product_code": "PLANT-1001",
      "orientacion": "Norte",
      "programa": "2 dormitorios",
      "programa2": "2 baños",
      "piso": "10",
      "precio_base": "5000.00",
      "precio_lista": "5500.00",
      "superficie_total_principal": "75.50",
      "superficie_interior": "68.30",
      "superficie_util": "65.20",
      "superficie_terraza": "12.50",
      "superficie_vendible": "73.80",
      "opportunity_id": "0065e000001OpXYZ",
      "is_active": true,
      "last_synced_at": "2026-03-09T15:30:00.000000Z",
      "created_at": "2026-03-01T10:00:00.000000Z",
      "updated_at": "2026-03-09T15:30:00.000000Z",
      "proyecto": {
        "id": 3,
        "name": "Proyecto Torre Central",
        "tipo": [],
        "direccion": "Av. Principal 123",
        "comuna": "Santiago",
        "provincia": "Santiago",
        "region": "Metropolitana",
        "pagina_web": "https://proyecto.test",
        "etapa": "En Construcción",
        "horario_atencion": "Lunes a Viernes 9:00 - 18:00",
        "entrega_inmediata": false,
        "is_active": true
      },
      "active_reservation": null,
      "cover_image_media": {
        "type": "image/jpeg",
        "title": "Modelo A1 - 101",
        "url": "http://127.0.0.1:8000/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg",
        "thumbnail_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=crop&fm=webp&h=200&w=200&s=...",
        "medium_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=crop&fm=webp&h=640&w=640&s=...",
        "large_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=contain&fm=webp&h=1024&w=1024&s=..."
      },
      "interior_image_media": {
        "type": "image/jpeg",
        "title": "Modelo A1 - 101",
        "url": "http://127.0.0.1:8000/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg",
        "thumbnail_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=crop&fm=webp&h=200&w=200&s=...",
        "medium_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=crop&fm=webp&h=640&w=640&s=...",
        "large_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=contain&fm=webp&h=1024&w=1024&s=..."
      },
      "cover_image_url": "http://127.0.0.1:8000/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg",
      "interior_image_url": "http://127.0.0.1:8000/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg"
    }
  ],
  "total": 45,
  "per_page": 12,
  "current_page": 1,
  "last_page": 4,
  "from": 1,
  "to": 12
}
```

#### GET `/api/v1/plantas/{id}`
Detalle de una planta específica.

**Respuesta:**
```json
{
  "id": 1,
  "salesforce_product_id": "01t5e000001AbcDEF",
  "salesforce_proyecto_id": "a015e000001XyZABC",
  "name": "101",
  "product_code": "PLANT-1001",
  "orientacion": "Norte",
  "programa": "2 dormitorios",
  "programa2": "2 baños",
  "piso": "10",
  "precio_base": "5000.00",
  "precio_lista": "5500.00",
  "superficie_total_principal": "75.50",
  "superficie_interior": "68.30",
  "superficie_util": "65.20",
  "superficie_terraza": "12.50",
  "superficie_vendible": "73.80",
  "opportunity_id": "0065e000001OpXYZ",
  "is_active": true,
  "last_synced_at": "2026-03-09T15:30:00.000000Z",
  "created_at": "2026-03-01T10:00:00.000000Z",
  "updated_at": "2026-03-09T15:30:00.000000Z",
  "proyecto": {
    "id": 3,
    "name": "Proyecto Torre Central",
    "tipo": [],
    "direccion": "Av. Principal 123",
    "comuna": "Santiago",
    "provincia": "Santiago",
    "region": "Metropolitana",
    "pagina_web": "https://proyecto.test",
    "etapa": "En Construcción",
    "horario_atencion": "Lunes a Viernes 9:00 - 18:00",
    "entrega_inmediata": false,
    "is_active": true
  },
  "active_reservation": null,
  "cover_image_media": {
    "type": "image/jpeg",
    "title": "Modelo A1 - 101",
    "url": "http://127.0.0.1:8000/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg",
    "thumbnail_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=crop&fm=webp&h=200&w=200&s=...",
    "medium_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=crop&fm=webp&h=640&w=640&s=...",
    "large_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=contain&fm=webp&h=1024&w=1024&s=..."
  },
  "interior_image_media": {
    "type": "image/jpeg",
    "title": "Modelo A1 - 101",
    "url": "http://127.0.0.1:8000/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg",
    "thumbnail_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=crop&fm=webp&h=200&w=200&s=...",
    "medium_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=crop&fm=webp&h=640&w=640&s=...",
    "large_url": "/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg?fit=contain&fm=webp&h=1024&w=1024&s=..."
  },
  "cover_image_url": "http://127.0.0.1:8000/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg",
  "interior_image_url": "http://127.0.0.1:8000/curator/ad4cb644-57ef-4a77-ad9e-c68ad3d94b9e.jpg"
}
```

### Configuración

#### GET `/api/v1/site-config`
Configuración global del sitio (pública).

**Respuesta:**
```json
{
  "site_name": "iLeben",
  "theme": "default",
  "primary_color": "#0066cc",
  "logo_url": "https://...",
  "maintenance_mode": false
}
```

### Pasarelas de Pago

#### GET `/api/v1/payment-gateways`
Lista de pasarelas de pago disponibles.

**Respuesta:**
```json
[
  {
    "key": "transbank",
    "name": "Transbank",
    "enabled": true
  },
  {
    "key": "mercadopago",
    "name": "Mercado Pago",
    "enabled": true
  }
]
```

### Reservas (Requiere autenticación)

#### POST `/api/v1/reservations`
Reservar una planta temporalmente.

**Body:**
```json
{
  "plant_id": 1,
  "session_token": "unique-session-token"
}
```

#### DELETE `/api/v1/reservations/{sessionToken}`
Liberar una reserva.

#### GET `/api/v1/reservations/plant/{plantId}`
Verificar estado de reserva de una planta.

### Pagos (Requiere autenticación)

#### POST `/api/v1/checkout`
Iniciar proceso de checkout.

**Body:**
```json
{
  "plant_id": 1,
  "gateway": "transbank",
  "session_token": "unique-session-token"
}
```

#### GET `/api/v1/payments`
Lista de pagos del usuario autenticado.

#### GET `/api/v1/payments/{id}`
Detalle de un pago específico.

### Autenticación de API

Las rutas protegidas requieren el header:
```
Authorization: Bearer {token}
```

El token se obtiene en la respuesta de `/api/v1/login`.

## �🚀 Instalación

### Requisitos
- PHP 8.4+
- Composer 2.x
- Node.js 18+
- MySQL 8+

### Setup Inicial

```bash
# 1. Clonar repo
git clone <repo-url> sale-ileben
cd sale-ileben

# 2. Instalar dependencias backend
composer install

# 3. Configurar .env
cp .env.example .env
php artisan key:generate

# 4. Configurar Salesforce en .env
# SF_AUTH_METHOD=username-password
# SF_CONSUMER_KEY=xxx
# SF_INSTANCE_URL=https://xxx.salesforce.com

# 5. Ejecutar migraciones
php artisan migrate

# 6. Instalar dependencias frontend
cd frontend
npm install

# 7. Build frontend
npm run build
cd ..

# 8. Crear link de storage
php artisan storage:link

# 9. Crear admin user en tinker
php artisan tinker
# User::create(['name' => 'Admin', 'email' => 'admin@ileben.com', 'password' => Hash::make('password')])
```

## 📚 Documentación Específica

- [Pagos & Pasarelas](PAYMENTS.md) - Sistema de pagos completo
- [Salesforce Integration](app/Services/Salesforce/README.md) - Integración CRM
- [Filament Resources](app/Filament/Resources/README.md) - Admin resources

## 🔧 Tareas Comunes

### Sincronizar plantas desde Salesforce
```bash
php artisan app:sync-plants
```

### Limpiar caché y compilar
```bash
php artisan optimize:clear
cd frontend && npm run build && cd ..
```

### Activar modo mantenimiento
```bash
php artisan tinker
# SiteSetting::set('maintenance_mode', true);
# SiteSetting::set('maintenance_message', '<h1>Estamos en mantenimiento</h1>');
```

### Compilar frontend en desarrollo
```bash
cd frontend
npm run dev    # Watch mode
npm run build  # Producción
```

### Servir aplicación en desarrollo
```bash
php artisan serve
# Frontend: http://localhost:5173
# Panel Filament: http://localhost:8000/admin
```

## 🗄️ Base de Datos

### Tablas Principales
- `users` - Usuarios del sistema
- `site_settings` - Configuración global (ID = 1)
- `curator` - Archivos/media centralizados
- `payments` - Transacciones de pago
- `projects` - Proyectos disponibles
- `plants` - Catálogo de plantas

### Relaciones
```
User → has many Payments
Payment → belongs to User
SiteSetting → belongs to Media (1:1 vía logo_id, favicon_id, etc)
Project → has Transbank commerce code
```

## 🔐 Seguridad

- ✅ CSRF protection (VerifyCsrfToken)
- ✅ Rate limiting en API
- ✅ Sanctum tokens para API
- ✅ Validation en todos los forms
- ✅ Signature verification en webhooks
- ✅ HTML sanitization en RichEditor
- ✅ Idempotent payment webhooks

## 📊 Monitoreo

### Logs
```bash
tail -f storage/logs/laravel.log
```

### Database Queries
```bash
php artisan tinker
# DB::listen(fn($query) => dump($query->sql, $query->bindings));
```

### Testing DB segura (sin tocar desarrollo)

- Los tests usan una conexión dedicada: `sqlite_testing`.
- Archivo de base de datos de tests: `database/testing.sqlite`.
- `RefreshDatabase` borra y reconstruye solo esa base de tests.
- No uses tu base de desarrollo para pruebas.

Comandos recomendados:

```bash
# Crear archivo SQLite de tests (una vez)
New-Item -Path database/testing.sqlite -ItemType File -Force

# Ejecutar tests usando .env.testing/phpunit.xml
php artisan test --compact
```

## 🎨 Customización

### Agregar nuevo tema Web Awesome
1. Editar `resources/css/filament/admin/theme.css`
2. Tema disponible en Configuración → Colores

### Agregar nueva payment gateway
1. Crear `app/Services/Payment/NuevaGatewayService.php`
2. Implementar `PaymentGatewayInterface`
3. Registrar en `PaymentGatewayManager::class`

### Agregar nueva Filament Resource
```bash
php artisan make:filament-resource NombreRecurso --generate
```

## 📝 Convenciones

- Models: Singular, PascalCase (User, Payment)
- Tables: Plural, snake_case (users, payments)
- Fields: snake_case (first_name, user_id)
- Enums: PascalCase (PaymentGateway, PaymentStatus)
- Services: `Service` suffix (PaymentService)

## 🤝 Contribuciones

Este proyecto sigue:
- [Laravel Boost Guidelines](AGENTS.md)
- [Copilot Instructions](.github/copilot-instructions.md)
- [Skills](.github/skills/)

## 📄 Licencia

Todos los derechos reservados - iLeben © 2026

---

**Última actualización:** 9 Mar 2026  
**Versión:** 1.0.0
