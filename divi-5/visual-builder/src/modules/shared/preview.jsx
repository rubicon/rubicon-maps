import React from 'react';

export const PreviewShell = ({ accent, eyebrow, title, items, children }) => (
  <div
    style={{
      border: `1px solid ${accent}`,
      borderRadius: '18px',
      padding: '20px',
      background: 'linear-gradient(180deg, rgba(247,249,252,0.98) 0%, rgba(255,255,255,0.98) 100%)',
      boxShadow: '0 18px 40px rgba(15, 23, 42, 0.08)',
    }}
  >
    <div
      style={{
        display: 'inline-flex',
        alignItems: 'center',
        padding: '4px 10px',
        borderRadius: '999px',
        background: accent,
        color: '#ffffff',
        fontSize: '11px',
        fontWeight: 700,
        letterSpacing: '0.08em',
        textTransform: 'uppercase',
      }}
    >
      {eyebrow}
    </div>
    <h3
      style={{
        margin: '14px 0 10px',
        color: '#0f172a',
        fontSize: '22px',
        lineHeight: 1.2,
      }}
    >
      {title}
    </h3>
    <div
      style={{
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(160px, 1fr))',
        gap: '10px',
        marginBottom: '16px',
      }}
    >
      {items.map((item) => (
        <div
          key={item.label}
          style={{
            borderRadius: '12px',
            background: '#eff3f8',
            padding: '10px 12px',
          }}
        >
          <div style={{ fontSize: '11px', textTransform: 'uppercase', letterSpacing: '0.08em', color: '#64748b', marginBottom: '4px' }}>
            {item.label}
          </div>
          <div style={{ fontSize: '14px', color: '#0f172a', fontWeight: 600 }}>
            {item.value || 'All'}
          </div>
        </div>
      ))}
    </div>
    {children}
  </div>
);
