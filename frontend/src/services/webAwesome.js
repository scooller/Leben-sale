/**
 * Servicio para inicializar Web Awesome y aplicar temas dinámicos
 * https://webawesome.com/docs/themes
 * https://webawesome.com/docs/color-palettes
 * 
 * Web Awesome soporta personalización mediante CSS Custom Properties (Design Tokens)
 */
// Import Web Awesome Pro base utilities and styles
import { setBasePath } from '@awesome.me/webawesome-pro/dist/webawesome.js'
import '@awesome.me/webawesome-pro/dist/styles/webawesome.css'

// Configurar basePath para que Web Awesome encuentre assets (Font Awesome icons, etc.)
// Según documentación oficial: https://webawesome.com/docs/installation#setting-the-base-path
// Cuando usas bundler, debes configurar explícitamente la ruta a los assets
// 
// Opciones para Vite:
// - En desarrollo: Vite sirve desde node_modules directamente
// - En producción: Assets se copian a dist/assets/
// 
// Vite copia automáticamente los assets de Web Awesome al build, 
// pero necesitamos decirle a Web Awesome dónde buscarlos.
// Usamos import.meta.env para detectar el entorno
if (import.meta.env.DEV) {
  // Desarrollo: apuntar a node_modules
  setBasePath('/node_modules/@awesome.me/webawesome-pro/dist')
} else {
  // Producción: Vite copiará los assets, intentar auto-detección
  // Si los iconos no cargan, ajustar esta ruta manualmente
  setBasePath('/assets')
}

// Importar componentes que se usan en la aplicación
// Según la documentación oficial, cada componente debe importarse explícitamente
// https://webawesome.com/docs/installation
import '@awesome.me/webawesome-pro/dist/components/animation/animation.js'
import '@awesome.me/webawesome-pro/dist/components/badge/badge.js'
import '@awesome.me/webawesome-pro/dist/components/button/button.js'
import '@awesome.me/webawesome-pro/dist/components/button-group/button-group.js'
import '@awesome.me/webawesome-pro/dist/components/callout/callout.js'
import '@awesome.me/webawesome-pro/dist/components/card/card.js'
import '@awesome.me/webawesome-pro/dist/components/details/details.js'
import '@awesome.me/webawesome-pro/dist/components/dialog/dialog.js'
import '@awesome.me/webawesome-pro/dist/components/divider/divider.js'
import '@awesome.me/webawesome-pro/dist/components/icon/icon.js'
import '@awesome.me/webawesome-pro/dist/components/input/input.js'
import '@awesome.me/webawesome-pro/dist/components/option/option.js'
import '@awesome.me/webawesome-pro/dist/components/radio/radio.js'
import '@awesome.me/webawesome-pro/dist/components/radio-group/radio-group.js'
import '@awesome.me/webawesome-pro/dist/components/select/select.js'
import '@awesome.me/webawesome-pro/dist/components/skeleton/skeleton.js'
import '@awesome.me/webawesome-pro/dist/components/tag/tag.js'

const themeImports = {
  default: () => import('@awesome.me/webawesome-pro/dist/styles/themes/default.css'),
  awesome: () => import('@awesome.me/webawesome-pro/dist/styles/themes/awesome.css'),
  shoelace: () => import('@awesome.me/webawesome-pro/dist/styles/themes/shoelace.css'),
  active: () => import('@awesome.me/webawesome-pro/dist/styles/themes/active.css'),
  brutalist: () => import('@awesome.me/webawesome-pro/dist/styles/themes/brutalist.css'),
  glossy: () => import('@awesome.me/webawesome-pro/dist/styles/themes/glossy.css'),
  matter: () => import('@awesome.me/webawesome-pro/dist/styles/themes/matter.css'),
  mellow: () => import('@awesome.me/webawesome-pro/dist/styles/themes/mellow.css'),
  playful: () => import('@awesome.me/webawesome-pro/dist/styles/themes/playful.css'),
  premium: () => import('@awesome.me/webawesome-pro/dist/styles/themes/premium.css'),
  tailspin: () => import('@awesome.me/webawesome-pro/dist/styles/themes/tailspin.css'),
}

class WebAwesomeService {
  static themePromise = null;
  static currentTheme = null;

  /**
   * Aplicar paleta de colores de Web Awesome
   * Paletas disponibles: default, bright, shoelace, rudimentary, elegant, mild, natural, anodized, vogue
   * Ref: https://webawesome.com/docs/color-palettes
   * 
   * @param {string} paletteName - Nombre de la paleta
   */
  static applyPalette(paletteName = 'default') {
    const htmlElement = document.documentElement;
    
    // Remover clases de paletas anteriores
    htmlElement.classList.remove(
      'wa-palette-default',
      'wa-palette-bright',
      'wa-palette-shoelace',
      'wa-palette-rudimentary',
      'wa-palette-elegant',
      'wa-palette-mild',
      'wa-palette-natural',
      'wa-palette-anodized',
      'wa-palette-vogue'
    );
    
    htmlElement.classList.add(`wa-palette-${paletteName}`);
  }

  /**
   * Aplicar colores semánticos específicos de Web Awesome
   * Estructura: wa-{semantic}-{color}
   * Semantic: brand, neutral, success, warning, danger
   * Colors: red, orange, yellow, green, cyan, blue, indigo, purple, pink, gray
   * 
   * Ref: https://webawesome.com/docs/color-palettes#semantic-color-overrides
   * 
   * @param {Object} colors - Objeto con los colores semánticos del backend
   * @param {string} colors.semantic_brand_color - Color para brand
   * @param {string} colors.semantic_neutral_color - Color para neutral
   * @param {string} colors.semantic_success_color - Color para success
   * @param {string} colors.semantic_warning_color - Color para warning
   * @param {string} colors.semantic_danger_color - Color para danger
   */
  static applySemanticColors(colors = {}) {
    const htmlElement = document.documentElement;
    
    // Mapeo de colores semánticos a clases CSS
    const semanticGroups = ['brand', 'neutral', 'success', 'warning', 'danger'];
    const availableColors = ['red', 'orange', 'yellow', 'green', 'cyan', 'blue', 'indigo', 'purple', 'pink', 'gray'];
    
    // Remover todas las clases de colores semánticos anteriores
    semanticGroups.forEach(group => {
      availableColors.forEach(color => {
        htmlElement.classList.remove(`wa-${group}-${color}`);
      });
    });
    
    // Aplicar nuevos colores semánticos
    if (colors.semantic_brand_color) {
      htmlElement.classList.add(`wa-brand-${colors.semantic_brand_color}`);
    }
    
    if (colors.semantic_neutral_color) {
      htmlElement.classList.add(`wa-neutral-${colors.semantic_neutral_color}`);
    }
    
    if (colors.semantic_success_color) {
      htmlElement.classList.add(`wa-success-${colors.semantic_success_color}`);
    }
    
    if (colors.semantic_warning_color) {
      htmlElement.classList.add(`wa-warning-${colors.semantic_warning_color}`);
    }
    
    if (colors.semantic_danger_color) {
      htmlElement.classList.add(`wa-danger-${colors.semantic_danger_color}`);
    }
  }

  /**
   * Aplicar tipografía personalizada
   * Configura las CSS Custom Properties de Web Awesome para fuentes
   * Ref: https://webawesome.com/docs/tokens/typography
   * 
   * Web Awesome Typography Tokens:
   * - --wa-font-family-body: Fuente para texto general del body
   * - --wa-font-family-heading: Fuente para headings (h1-h6)
   * - --wa-font-size-*: Escalas de tamaño (2x-small a 4x-large)
   * - --wa-font-weight-*: Pesos (light, normal, semibold, bold)
   * - --wa-letter-spacing-*: Espaciado entre letras (dense, normal, loose)
   * - --wa-line-height-*: Altura de línea (dense, normal, loose)
   * 
   * @param {Object} fonts - Objeto con fuentes del backend
   * @param {string} fonts.font_family_body - Fuente para texto general
   * @param {string} fonts.font_family_heading - Fuente para encabezados
   */
  static applyFonts(fonts = {}) {
    if (!fonts.font_family_body && !fonts.font_family_heading) {
      return;
    }

    const htmlElement = document.documentElement;

    // Aplicar fuente del cuerpo según documentación oficial de Web Awesome
    if (fonts.font_family_body) {
      htmlElement.style.setProperty('--wa-font-family-body', fonts.font_family_body);
    }

    // Aplicar fuente de encabezados según documentación oficial de Web Awesome
    if (fonts.font_family_heading) {
      htmlElement.style.setProperty('--wa-font-family-heading', fonts.font_family_heading);
    }
  }

  /**
   * Aplicar tema predefinido de Web Awesome como clase en <html>
   * Temas disponibles: default, awesome, shoelace, active, brutalist, glossy, matter, mellow, playful, premium, tailspin
   * Ref: https://webawesome.com/docs/themes
   * 
   * @param {string} themeName - Nombre del tema predefinido
   */
  static async applyPrebuiltTheme(themeName = 'default') {
    const theme = themeImports[themeName] ? themeName : 'default';

    if (this.currentTheme === theme && this.themePromise) {
      return this.themePromise;
    }

    if (this.themePromise) {
      await this.themePromise;
    }

    this.themePromise = this.runApplyTheme(theme);
    return this.themePromise;
  }

  static async runApplyTheme(theme) {
    await themeImports[theme]();
    const htmlElement = document.documentElement;

    // Remover clases de temas anteriores
    htmlElement.classList.remove(
      'wa-theme-default',
      'wa-theme-awesome',
      'wa-theme-shoelace',
      'wa-theme-active',
      'wa-theme-brutalist',
      'wa-theme-glossy',
      'wa-theme-matter',
      'wa-theme-mellow',
      'wa-theme-playful',
      'wa-theme-premium',
      'wa-theme-tailspin'
    );

    htmlElement.classList.add(`wa-theme-${theme}`);
    this.currentTheme = theme;
    // console.log(`Web Awesome: Tema "${theme}" aplicado exitosamente.`);
  }
}

export default WebAwesomeService;
