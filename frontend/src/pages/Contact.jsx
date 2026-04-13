import { useEffect, useMemo, useState } from 'react';
import { useSiteConfig } from '../contexts/SiteConfigContext';
import contactSubmissionsService from '../services/contactSubmissions';
import SiteHeader from '../components/SiteHeader';
import '../styles/contact.scss' with { type: 'css' };

function Contact({ onNavigate, currentPath }) {
  const { config } = useSiteConfig();
  const [values, setValues] = useState({});
  const [submitting, setSubmitting] = useState(false);
  const [submitSuccess, setSubmitSuccess] = useState('');
  const [submitError, setSubmitError] = useState('');

  const socialLinks = useMemo(() => {
    const social = config?.social || {};

    return [
      {
        key: 'facebook',
        label: 'Facebook',
        icon: 'facebook',
        url: social.facebook,
      },
      {
        key: 'instagram',
        label: 'Instagram',
        icon: 'instagram',
        url: social.instagram,
      },
      {
        key: 'linkedin',
        label: 'LinkedIn',
        icon: 'linkedin-in',
        url: social.linkedin,
      },
      {
        key: 'youtube',
        label: 'YouTube',
        icon: 'youtube',
        url: social.youtube,
      },
      {
        key: 'twitter',
        label: 'X',
        icon: 'x-twitter',
        url: social.twitter,
      },
    ].filter((item) => Boolean(item.url));
  }, [config?.social]);

  const title = config?.contact_page?.title || 'Contacto';
  const subtitle = config?.contact_page?.subtitle || 'Estamos para ayudarte';
  const content = config?.contact_page?.content;
  const formFields = useMemo(() => {
    const configuredFields = config?.contact_page?.form_fields;

    if (!Array.isArray(configuredFields) || configuredFields.length === 0) {
      return [
        { key: 'name', label: 'Nombre', type: 'text', required: true, placeholder: 'Ingresa tu nombre completo' },
        { key: 'email', label: 'Email', type: 'email', required: true, placeholder: 'correo@dominio.cl' },
        { key: 'message', label: 'Mensaje', type: 'textarea', required: true, placeholder: 'Escribe tu consulta...' },
      ];
    }

    return configuredFields
      .filter((field) => field && field.key)
      .map((field) => ({
        key: `${field.key}`.trim(),
        label: `${field.label || field.key}`.trim(),
        type: `${field.type || 'text'}`.trim(),
        placeholder: `${field.placeholder || ''}`,
        required: Boolean(field.required),
      }))
      .filter((field) => field.key !== '');
  }, [config?.contact_page?.form_fields]);

  useEffect(() => {
    const nextValues = {};

    formFields.forEach((field) => {
      nextValues[field.key] = '';
    });

    setValues(nextValues);
  }, [formFields]);

  const handleFieldChange = (fieldKey, value) => {
    setValues((current) => ({
      ...current,
      [fieldKey]: value,
    }));
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    setSubmitSuccess('');
    setSubmitError('');
    setSubmitting(true);

    try {
      await contactSubmissionsService.create(values);
      setSubmitSuccess('Tu mensaje fue enviado correctamente.');

      const resetValues = {};
      formFields.forEach((field) => {
        resetValues[field.key] = '';
      });
      setValues(resetValues);
    } catch (error) {
      const message = error?.response?.data?.message || 'No pudimos enviar tu mensaje. Intenta nuevamente.';
      setSubmitError(message);
    } finally {
      setSubmitting(false);
    }
  };

  const renderField = (field) => {
    const value = values[field.key] || '';

    if (field.type === 'textarea') {
      return (
        <wa-textarea
          key={field.key}
          value={value}
          rows="5"
          placeholder={field.placeholder || undefined}
          required={field.required}
          onInput={(event) => handleFieldChange(field.key, event.target.value || '')}
        >
          <span slot="label">{field.label}</span>
        </wa-textarea>
      );
    }

    const inputType = field.type === 'email' ? 'email' : field.type === 'number' ? 'number' : field.type === 'tel' ? 'tel' : 'text';

    return (
      <wa-input
        key={field.key}
        type={inputType}
        value={value}
        placeholder={field.placeholder || undefined}
        required={field.required}
        onInput={(event) => handleFieldChange(field.key, event.target.value || '')}
      >
        <span slot="label">{field.label}</span>
      </wa-input>
    );
  };

  return (
    <div className="contact-page">
      <SiteHeader config={config} currentPath={currentPath} onNavigate={onNavigate} />

      <section className="contact-hero home-container">
        <wa-card appearance="filled" className="contact-hero-card">
          <div className="wa-stack wa-gap-s">
            <h1>{title}</h1>
            <p>{subtitle}</p>
          </div>
        </wa-card>
      </section>

      <section className="home-container contact-grid">
        <wa-card appearance="outlined" className="contact-content-card">
          <div className="wa-stack wa-gap-m">
            <h2>Escríbenos</h2>
            {content ? (
              <div dangerouslySetInnerHTML={{ __html: content }} />
            ) : (
              <p>Pronto publicaremos toda la información de contacto.</p>
            )}

            <form className="contact-form wa-stack wa-gap-s" onSubmit={handleSubmit}>
              {formFields.map(renderField)}

              {submitSuccess && (
                <wa-callout variant="success">
                  <wa-icon slot="icon" name="circle-check"></wa-icon>
                  {submitSuccess}
                </wa-callout>
              )}

              {submitError && (
                <wa-callout variant="danger">
                  <wa-icon slot="icon" name="triangle-exclamation"></wa-icon>
                  {submitError}
                </wa-callout>
              )}

              <wa-button type="submit" variant="brand" disabled={submitting}>
                {submitting ? 'Enviando...' : 'Enviar mensaje'}
              </wa-button>
            </form>
          </div>
        </wa-card>

        <wa-card appearance="filled" className="contact-info-card">
          <div className="wa-stack wa-gap-s">
            <h2>Información</h2>

            {config?.contact?.email && (
              <a href={`mailto:${config.contact.email}`} className="contact-link">
                <wa-icon name="envelope"></wa-icon>
                {config.contact.email}
              </a>
            )}

            {config?.contact?.phone && (
              <a href={`tel:${config.contact.phone}`} className="contact-link">
                <wa-icon name="phone"></wa-icon>
                {config.contact.phone}
              </a>
            )}

            {config?.contact?.address && (
              <div className="contact-link contact-address">
                <wa-icon name="location-dot"></wa-icon>
                <span>{config.contact.address}</span>
              </div>
            )}

            {socialLinks.length > 0 && (
              <div className="wa-stack wa-gap-xs">
                <span className="wa-color-text-quiet">Redes sociales</span>
                <div className="wa-cluster wa-gap-xs">
                  {socialLinks.map((socialItem) => (
                    <wa-button
                      appearance="filled"
                      key={socialItem.key}
                      href={socialItem.url}
                      target="_blank"
                      rel="noopener noreferrer"
                      aria-label={socialItem.label}
                      pill
                    >
                      <wa-icon name={socialItem.icon} family="brands"></wa-icon>
                    </wa-button>
                  ))}
                </div>
              </div>
            )}

            <wa-button variant="brand" onClick={() => onNavigate?.('/')}>
              Volver al Home
            </wa-button>
          </div>
        </wa-card>
      </section>
    </div>
  );
}

export default Contact;
