import api from '../lib/api';

/**
 * Servicio para obtener la configuración del sitio
 */
class SiteConfigService {
  constructor() {
    this.config = null;
    this.loading = false;
  }

  /**
   * Obtener la configuración del sitio desde la API
   * @param {boolean} forceRefresh - Si es true, ignora caché en memoria
   * @returns {Promise<Object>}
   */
  async getConfig(forceRefresh = false) {
    if (!forceRefresh && this.config) {
      return this.config;
    }

    this.loading = true;
    try {
      const previewToken = this.getPreviewToken();
      const params = forceRefresh ? { _t: Date.now() } : {};

      if (previewToken) {
        params.preview_token = previewToken;
      }

      const response = await api.get('/site-config', {
        params: Object.keys(params).length > 0 ? params : undefined,
        headers: forceRefresh
          ? {
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache',
            'Expires': '0',
          }
          : undefined,
      });

      this.config = response.data;

      return this.config;
    } catch (error) {
      console.error('[SiteConfigService] error al cargar /site-config', error);
      throw error;
    } finally {
      this.loading = false;
    }
  }

  /**
   * Limpiar caché de configuración
   */
  clearCache() {
    this.config = null;
  }

  /**
   * Inyectar CSS personalizado
   * @param {string} css - CSS personalizado
   */
  injectCustomCSS(css) {
    if (!css) return;

    const styleId = 'site-custom-css';
    let styleElement = document.getElementById(styleId);

    if (!styleElement) {
      styleElement = document.createElement('style');
      styleElement.id = styleId;
      document.head.appendChild(styleElement);
    }

    styleElement.textContent = css;
  }

  injectManagedScripts(scripts, { containerId, target = 'head' }) {
    if (typeof document === 'undefined') return;

    const normalizedScripts = `${scripts ?? ''}`.trim();
    const existingContainer = document.getElementById(containerId);

    if (normalizedScripts === '') {
      if (existingContainer) {
        existingContainer.remove();
      }

      return;
    }

    const targetElement = target === 'footer'
      ? (document.body || document.documentElement)
      : document.head;

    if (!targetElement) return;

    const container = existingContainer || document.createElement('div');
    container.id = containerId;
    container.setAttribute('data-managed-site-script', target);

    if (!existingContainer) {
      targetElement.appendChild(container);
    }

    container.replaceChildren();

    const hasMarkup = /<[^>]+>/.test(normalizedScripts);

    if (!hasMarkup) {
      const inlineScript = document.createElement('script');
      inlineScript.text = normalizedScripts;
      container.appendChild(inlineScript);

      return;
    }

    const template = document.createElement('template');
    template.innerHTML = normalizedScripts;

    const fragment = template.content.cloneNode(true);

    fragment.querySelectorAll('script').forEach((scriptNode) => {
      const executableScript = document.createElement('script');

      [...scriptNode.attributes].forEach(({ name, value }) => {
        executableScript.setAttribute(name, value);
      });

      executableScript.text = scriptNode.text;
      scriptNode.replaceWith(executableScript);
    });

    container.appendChild(fragment);
  }

  injectHeaderScripts(scripts) {
    this.injectManagedScripts(scripts, {
      containerId: 'site-header-scripts',
      target: 'head',
    });
  }

  injectFooterScripts(scripts) {
    this.injectManagedScripts(scripts, {
      containerId: 'site-footer-scripts',
      target: 'footer',
    });
  }

  /**
   * Establecer favicon
   * @param {string} faviconUrl - URL del favicon
   */
  setFavicon(faviconUrl) {
    if (!faviconUrl) return;

    let link = document.querySelector("link[rel~='icon']");
    if (!link) {
      link = document.createElement('link');
      link.rel = 'icon';
      document.head.appendChild(link);
    }
    link.href = faviconUrl;
  }

  /**
   * Establecer título del sitio
   * @param {string} title - Título del sitio
   */
  setTitle(title) {
    if (title) {
      document.title = title;
    }
  }

  /**
   * Establecer meta tags
   * @param {Object} seo - Objeto con información SEO
   */
  setMetaTags(seo) {
    if (!seo) return;

    // Meta description
    if (seo.site_description) {
      this.setMetaTag('description', seo.site_description);
    }

    // Meta keywords
    if (seo.meta_keywords) {
      this.setMetaTag('keywords', seo.meta_keywords);
    }

    // Meta author
    if (seo.meta_author) {
      this.setMetaTag('author', seo.meta_author);
    }

    // Open Graph image
    if (seo.og_image) {
      this.setMetaTag('og:image', seo.og_image, 'property');
    }
  }

  /**
   * Establecer un meta tag
   * @param {string} name - Nombre del meta tag
   * @param {string} content - Contenido del meta tag
   * @param {string} attribute - Atributo (name o property)
   */
  setMetaTag(name, content, attribute = 'name') {
    if (!content) return;

    let meta = document.querySelector(`meta[${attribute}="${name}"]`);
    if (!meta) {
      meta = document.createElement('meta');
      meta.setAttribute(attribute, name);
      document.head.appendChild(meta);
    }
    meta.setAttribute('content', content);
  }

  getPreviewToken() {
    if (typeof window === 'undefined') {
      return null;
    }

    const token = new URLSearchParams(window.location.search).get('preview_token');

    return token ? token.trim() : null;
  }
}

export const siteConfigService = new SiteConfigService();
export default siteConfigService;
