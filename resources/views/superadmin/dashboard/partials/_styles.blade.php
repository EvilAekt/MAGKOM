<style>
    /* Dashboard SuperAdmin: hanya memakai token Design System (docs/07). Awalan sa- */
    .sa-page { display: flex; flex-direction: column; gap: 1.25rem; }

    /* Header */
    .sa-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; }
    .sa-header__meta { display: flex; align-items: center; flex-wrap: wrap; gap: 0.625rem; }
    .sa-header__title { font-size: 1.375rem; font-weight: var(--fw-bold); color: var(--neutral-800); margin: 0 0 0.25rem; letter-spacing: -0.01em; }
    .sa-header__desc { font-size: 0.875rem; color: var(--neutral-600); margin: 0; line-height: 1.5; max-width: 760px; }
    .sa-header__actions { display: flex; align-items: center; flex-wrap: wrap; gap: 0.625rem; }
    .sa-live { display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; font-weight: var(--fw-semibold); color: var(--green-700); }
    .sa-period { display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.8125rem; color: var(--neutral-600); }
    .sa-period select { min-height: 40px; padding: 0 0.625rem; border: 1px solid var(--neutral-400); border-radius: 8px; background: var(--neutral-0); color: var(--neutral-800); font: inherit; font-size: 0.8125rem; }
    .sa-btn { display: inline-flex; align-items: center; gap: 0.5rem; min-height: 40px; padding: 0 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: var(--fw-semibold); text-decoration: none; border: 1px solid transparent; cursor: pointer; }
    .sa-btn--primary { background: var(--blue-600); color: var(--neutral-0); }
    .sa-btn--primary:hover { background: var(--blue-700); }
    .sa-btn--secondary { background: var(--neutral-0); color: var(--neutral-800); border-color: var(--neutral-400); }
    .sa-btn--secondary:hover { background: var(--neutral-200); }

    .sa-shortcuts { display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
    .sa-shortcuts__label { font-size: 0.75rem; font-weight: var(--fw-semibold); color: var(--neutral-600); text-transform: uppercase; letter-spacing: 0.04em; }

    /* Card */
    .sa-card { background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle); min-width: 0; }
    .sa-card__head { display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; margin-bottom: 1rem; padding-bottom: 0.875rem; border-bottom: 1px solid var(--neutral-200); }
    .sa-card__title { font-size: 1rem; font-weight: var(--fw-bold); color: var(--neutral-800); margin: 0; }
    .sa-card__subtitle { font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0; }
    .sa-link { display: inline-flex; align-items: center; gap: 0.25rem; min-height: 32px; font-size: 0.8125rem; font-weight: var(--fw-semibold); color: var(--blue-600); text-decoration: none; white-space: nowrap; }
    .sa-link:hover { color: var(--blue-700); text-decoration: underline; }

    /* Perlu tindakan */
    .sa-attention { background: var(--amber-100); border: 1px solid var(--amber-300); border-radius: 12px; padding: 1rem 1.25rem; }
    .sa-attention--clear { background: var(--green-100); border-color: var(--green-600); }
    .sa-attention__title { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: var(--fw-bold); color: var(--amber-700); margin: 0 0 0.75rem; }
    .sa-attention__chips { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .sa-chip { display: inline-flex; align-items: center; gap: 0.5rem; min-height: 44px; padding: 0 0.875rem; border-radius: 999px; background: var(--neutral-0); border: 1px solid var(--neutral-400); font-size: 0.8125rem; color: var(--neutral-800); text-decoration: none; }
    .sa-chip:hover { box-shadow: var(--shadow-raised); }
    .sa-chip__count { font-weight: var(--fw-bold); }
    .sa-chip--warning { border-color: var(--amber-300); } .sa-chip--warning .sa-chip__count { color: var(--amber-700); }
    .sa-chip--danger { border-color: var(--red-300); } .sa-chip--danger .sa-chip__count { color: var(--red-700); }
    .sa-chip--info { border-color: var(--blue-tint-line); } .sa-chip--info .sa-chip__count { color: var(--blue-700); }

    /* Grid */
    .sa-grid-kpi { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
    .sa-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
    .sa-grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.75rem; }
    @media (max-width: 1100px) { .sa-grid-kpi, .sa-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 800px) { .sa-grid-2 { grid-template-columns: minmax(0, 1fr); } }
    @media (max-width: 560px) { .sa-grid-kpi, .sa-grid-4 { grid-template-columns: minmax(0, 1fr); } }

    /* Stat */
    .sa-stat { display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; }
    .sa-stat__main { display: flex; flex-direction: column; gap: 0.25rem; min-width: 0; }
    .sa-stat__label { font-size: 0.8125rem; font-weight: var(--fw-semibold); color: var(--neutral-600); }
    .sa-stat__value { font-size: 2rem; font-weight: var(--fw-bold); line-height: 1.15; color: var(--neutral-800); }
    .sa-stat__hint { font-size: 0.75rem; font-weight: var(--fw-semibold); color: var(--neutral-600); }
    .sa-stat__meta { font-size: 0.75rem; color: var(--neutral-600); }
    .sa-stat__icon { display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 10px; flex-shrink: 0; }
    .sa-stat--blue .sa-stat__icon { background: var(--blue-tint); color: var(--blue-700); }
    .sa-stat--amber .sa-stat__icon { background: var(--amber-200); color: var(--amber-700); }
    .sa-stat--green .sa-stat__icon { background: var(--green-100); color: var(--green-700); }
    .sa-stat--red .sa-stat__icon { background: var(--red-200); color: var(--red-700); }
    .sa-stat--mini { padding: 0.875rem 1rem; }
    .sa-stat--mini .sa-stat__value { font-size: 1.5rem; }

    /* Badge */
    .sa-badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.625rem; border-radius: 999px; font-size: 0.75rem; font-weight: var(--fw-semibold); line-height: 1.5; white-space: nowrap; }
    .sa-badge--neutral { background: var(--neutral-200); color: var(--neutral-700); }
    .sa-badge--info { background: var(--blue-tint); color: var(--blue-700); }
    .sa-badge--success { background: var(--green-100); color: var(--green-700); }
    .sa-badge--warning { background: var(--amber-200); color: var(--amber-700); }
    .sa-badge--danger { background: var(--red-200); color: var(--red-700); }

    /* List */
    .sa-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; }
    .sa-list__item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid var(--neutral-200); }
    .sa-list__item:last-child { border-bottom: 0; padding-bottom: 0; }
    .sa-list__body { flex: 1; min-width: 0; }
    .sa-list__title { font-size: 0.875rem; font-weight: var(--fw-semibold); color: var(--neutral-800); margin: 0; overflow: hidden; text-overflow: ellipsis; }
    .sa-list__sub { font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0; overflow: hidden; text-overflow: ellipsis; }
    .sa-list__time { font-size: 0.75rem; color: var(--neutral-600); white-space: nowrap; }
    .sa-avatar { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; background: var(--blue-tint); color: var(--blue-700); font-size: 0.75rem; font-weight: var(--fw-bold); flex-shrink: 0; }
    .sa-dot { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; background: var(--neutral-200); color: var(--neutral-700); }
    .sa-dot--success { background: var(--green-100); color: var(--green-700); }
    .sa-dot--danger { background: var(--red-200); color: var(--red-700); }
    .sa-dot--info { background: var(--blue-tint); color: var(--blue-700); }

    /* Tabel & progress */
    .sa-table-wrap { overflow-x: auto; }
    .sa-table { width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left; }
    .sa-table th { background: var(--neutral-200); color: var(--neutral-700); font-weight: var(--fw-semibold); padding: 0.625rem 0.75rem; white-space: nowrap; }
    .sa-table td { padding: 0.625rem 0.75rem; border-bottom: 1px solid var(--neutral-200); color: var(--neutral-800); }
    .sa-progress { height: 8px; border-radius: 999px; background: var(--neutral-200); overflow: hidden; }
    .sa-progress > span { display: block; height: 100%; border-radius: 999px; background: var(--blue-600); }
    .sa-progress--warning > span { background: var(--amber-700); }
    .sa-progress--danger > span { background: var(--red-600); }
    .sa-bar-row { display: grid; grid-template-columns: minmax(0, 1fr) 2.5rem; gap: 0.25rem 0.75rem; align-items: center; margin-bottom: 0.75rem; }
    .sa-bar-row__label { font-size: 0.8125rem; color: var(--neutral-800); }
    .sa-bar-row__value { font-size: 0.8125rem; font-weight: var(--fw-bold); text-align: right; }
    .sa-bar-row .sa-progress { grid-column: 1 / -1; }

    .sa-empty { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1.5rem 1rem; color: var(--neutral-600); text-align: center; }
    .sa-empty p { margin: 0; font-size: 0.8125rem; }

    .sa-link:focus-visible, .sa-btn:focus-visible, .sa-chip:focus-visible, .sa-period select:focus-visible, .tab-btn:focus-visible { box-shadow: var(--focus-ring); outline: none; }
    .sa-tabs-wrap { position: sticky; top: 0; z-index: 5; }
</style>
