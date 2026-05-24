@extends('layouts.app2')

@section('title', $title ?? 'Sosial')

@section('content')
<style>
    .sosial-shell {
        --sosial-bg: #f4f7fb;
        --sosial-surface: rgba(255, 255, 255, 0.94);
        --sosial-surface-strong: #ffffff;
        --sosial-dark: #0f172a;
        --sosial-muted: #64748b;
        --sosial-line: rgba(15, 23, 42, 0.08);
        --sosial-blue: #2563eb;
        --sosial-cyan: #0891b2;
        --sosial-gold: #f59e0b;
        --sosial-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        --sosial-radius-xl: 1.7rem;
        --sosial-radius-lg: 1.25rem;
        --sosial-radius-md: 1rem;
        color: var(--sosial-dark);
    }

    .sosial-shell a {
        text-decoration: none;
    }

    .sosial-shell .sosial-topbar {
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1.4rem;
        padding: 0.9rem 1.1rem;
        box-shadow: 0 28px 70px rgba(15, 23, 42, 0.2);
        backdrop-filter: blur(14px);
    }

    .sosial-shell .sosial-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        color: #fff;
        font-weight: 800;
        letter-spacing: 0.02em;
    }

    .sosial-shell .sosial-brand-mark {
        width: 42px;
        height: 42px;
        border-radius: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #38bdf8, #2563eb);
        color: #fff;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22);
    }

    .sosial-shell .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.18);
    }

    .sosial-shell .navbar-toggler:focus {
        box-shadow: none;
    }

    .sosial-shell .sosial-nav-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-radius: 999px;
        color: rgba(248, 250, 252, 0.76);
        font-weight: 600;
        transition: 0.22s ease;
    }

    .sosial-shell .sosial-nav-link:hover,
    .sosial-shell .sosial-nav-link.active {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
    }

    .sosial-shell .sosial-nav-badge {
        align-items: center;
        background: #ef4444;
        border: 2px solid rgba(15, 23, 42, 0.9);
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        font-size: 0.72rem;
        font-weight: 800;
        height: 22px;
        justify-content: center;
        line-height: 1;
        min-width: 22px;
        padding: 0 0.38rem;
    }

    .sosial-shell .sosial-message-toast {
        align-items: center;
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 1rem;
        bottom: 1.25rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.25);
        color: #fff;
        display: none;
        gap: 0.8rem;
        max-width: min(360px, calc(100vw - 2rem));
        padding: 0.9rem 1rem;
        position: fixed;
        right: 1.25rem;
        z-index: 1080;
    }

    .sosial-shell .sosial-message-toast.show {
        display: flex;
    }

    .sosial-shell .sosial-user-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.45rem 0.65rem 0.45rem 0.45rem;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 999px;
        color: #fff;
        font-weight: 600;
    }

    .sosial-shell .sosial-user-chip img {
        width: 34px;
        height: 34px;
        object-fit: cover;
        border-radius: 999px;
        border: 2px solid rgba(255, 255, 255, 0.15);
    }

    .sosial-shell .sosial-main {
        padding: 1.5rem 0 2.5rem;
    }

    .sosial-shell .sosial-page-hero {
        position: relative;
        overflow: hidden;
        padding: 1.05rem 1.2rem;
        border-radius: var(--sosial-radius-xl);
        background:
            radial-gradient(circle at top right, rgba(8, 145, 178, 0.22), transparent 28%),
            radial-gradient(circle at left bottom, rgba(245, 158, 11, 0.18), transparent 30%),
            linear-gradient(135deg, #0f172a 0%, #1d4ed8 62%, #2563eb 100%);
        color: #fff;
        box-shadow: 0 30px 75px rgba(15, 23, 42, 0.18);
    }

    .sosial-shell .sosial-page-hero::after {
        content: '';
        position: absolute;
        inset: auto -10% -45% auto;
        width: 280px;
        height: 280px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.09);
        filter: blur(10px);
        pointer-events: none;
        z-index: 0;
    }

    .sosial-shell .sosial-page-hero > * {
        position: relative;
        z-index: 1;
    }

    .sosial-shell .sosial-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.42rem 0.8rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sosial-shell .sosial-hero-title {
        font-size: clamp(1.55rem, 3vw, 2.45rem);
        line-height: 1.08;
        font-weight: 800;
        margin: 0.7rem 0 0.5rem;
        max-width: 16ch;
    }

    .sosial-shell .sosial-hero-copy {
        max-width: 62ch;
        color: rgba(255, 255, 255, 0.82);
        font-size: 1rem;
        margin-bottom: 0;
    }

    .sosial-shell .sosial-hero-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.9rem;
    }

    .sosial-shell .sosial-hero-metric {
        padding: 1rem;
        border-radius: 1.1rem;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .sosial-shell .sosial-hero-metric-value {
        display: block;
        font-size: 1.35rem;
        font-weight: 800;
    }

    .sosial-shell .sosial-hero-metric-label {
        color: rgba(255, 255, 255, 0.74);
        font-size: 0.86rem;
    }

    .sosial-shell .sosial-surface {
        background: var(--sosial-surface);
        border: 1px solid var(--sosial-line);
        border-radius: var(--sosial-radius-lg);
        box-shadow: var(--sosial-shadow);
    }

    .sosial-shell .sosial-panel {
        padding: 1.25rem;
    }

    .sosial-shell .sosial-panel-title {
        font-size: 1.02rem;
        font-weight: 800;
        margin-bottom: 0.35rem;
    }

    .sosial-shell .sosial-panel-copy {
        color: var(--sosial-muted);
        font-size: 0.94rem;
        margin-bottom: 0;
    }

    .sosial-shell .sosial-composer {
        position: sticky;
        top: 92px;
        overflow: hidden;
    }

    .sosial-shell .sosial-composer-head {
        padding: 1.2rem 1.2rem 1rem;
        background:
            radial-gradient(circle at top right, rgba(8, 145, 178, 0.14), transparent 34%),
            linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(245, 158, 11, 0.08));
        border-bottom: 1px solid var(--sosial-line);
    }

    .sosial-shell .sosial-composer-body {
        padding: 1.2rem;
    }

    .sosial-shell .sosial-composer-type-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .sosial-shell .sosial-choice {
        position: relative;
        display: block;
        min-width: 0;
    }

    .sosial-shell .sosial-choice input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .sosial-shell .sosial-choice-card {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        height: 100%;
        min-height: 132px;
        width: 100%;
        padding: 0.95rem;
        border-radius: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.24);
        background: #fff;
        transition: 0.2s ease;
        cursor: pointer;
        overflow: hidden;
    }

    .sosial-shell .sosial-choice-card:hover {
        transform: translateY(-1px);
        border-color: rgba(37, 99, 235, 0.28);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
    }

    .sosial-shell .sosial-choice input:checked + .sosial-choice-card {
        border-color: rgba(37, 99, 235, 0.5);
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(8, 145, 178, 0.1));
        box-shadow: 0 18px 30px rgba(37, 99, 235, 0.12);
    }

    .sosial-shell .sosial-choice-title {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-weight: 800;
        margin-bottom: 0.35rem;
        line-height: 1.2;
    }

    .sosial-shell .sosial-choice-title i {
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        border-radius: 0.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: linear-gradient(135deg, #2563eb, #0891b2);
    }

    .sosial-shell .sosial-choice-copy {
        display: block;
        color: var(--sosial-muted);
        font-size: 0.84rem;
        line-height: 1.45;
        margin: 0;
        overflow-wrap: anywhere;
    }

    .sosial-shell .sosial-composer-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.45rem;
        font-weight: 700;
    }

    .sosial-shell .sosial-counter {
        color: var(--sosial-muted);
        font-size: 0.78rem;
        font-weight: 700;
    }

    .sosial-shell .sosial-helper-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.7rem;
    }

    .sosial-shell .sosial-helper-card {
        padding: 0.85rem;
        border-radius: 1rem;
        background: #f8fafc;
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .sosial-shell .sosial-helper-card strong {
        display: block;
        margin-bottom: 0.2rem;
        font-size: 0.88rem;
    }

    .sosial-shell .sosial-helper-card span {
        display: block;
        color: var(--sosial-muted);
        font-size: 0.8rem;
        line-height: 1.45;
    }

    .sosial-shell .sosial-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.5rem 0.82rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.1);
        color: var(--sosial-blue);
        font-size: 0.82rem;
        font-weight: 700;
    }

    .sosial-shell .sosial-chip-soft {
        background: rgba(15, 23, 42, 0.06);
        color: var(--sosial-dark);
    }

    .sosial-shell .sosial-btn-primary,
    .sosial-shell .sosial-btn-secondary,
    .sosial-shell .sosial-btn-ghost {
        border-radius: 999px;
        padding: 0.75rem 1rem;
        font-weight: 700;
        border: 0;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .sosial-shell .sosial-btn-primary {
        color: #fff;
        background: linear-gradient(135deg, #2563eb, #0891b2);
        box-shadow: 0 18px 35px rgba(37, 99, 235, 0.22);
    }

    .sosial-shell .sosial-btn-secondary {
        color: var(--sosial-dark);
        background: rgba(15, 23, 42, 0.08);
    }

    .sosial-shell .sosial-btn-ghost {
        color: var(--sosial-dark);
        background: transparent;
        border: 1px solid var(--sosial-line);
    }

    .sosial-shell .sosial-btn-primary:hover,
    .sosial-shell .sosial-btn-secondary:hover,
    .sosial-shell .sosial-btn-ghost:hover {
        transform: translateY(-1px);
    }

    .sosial-shell .sosial-stack {
        display: grid;
        gap: 1rem;
    }

    .sosial-shell .sosial-feed-stack {
        display: grid;
        gap: 1.1rem;
    }

    .sosial-shell .sosial-feed-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .sosial-shell .sosial-sidebar {
        position: sticky;
        top: 108px;
        display: grid;
        gap: 1rem;
    }

    .sosial-shell .sosial-trend-list {
        display: grid;
        gap: 0.35rem;
        margin-top: 0.9rem;
    }

    .sosial-shell .sosial-trend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.8rem;
        padding: 0.72rem 0;
        color: var(--sosial-dark);
        border-top: 1px solid rgba(15, 23, 42, 0.06);
    }

    .sosial-shell .sosial-trend-item:first-child {
        border-top: 0;
        padding-top: 0;
    }

    .sosial-shell .sosial-trend-main {
        min-width: 0;
    }

    .sosial-shell .sosial-trend-name {
        display: block;
        color: var(--sosial-dark);
        font-weight: 800;
        line-height: 1.2;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sosial-shell .sosial-trend-meta {
        display: block;
        margin-top: 0.2rem;
        color: var(--sosial-muted);
        font-size: 0.8rem;
        line-height: 1.25;
    }

    .sosial-shell .sosial-trend-count {
        flex: 0 0 auto;
        min-width: 2.1rem;
        padding: 0.35rem 0.55rem;
        border-radius: 999px;
        color: var(--sosial-blue);
        background: rgba(37, 99, 235, 0.08);
        font-size: 0.78rem;
        font-weight: 800;
        text-align: center;
    }

    .sosial-shell .sosial-post-card {
        overflow: hidden;
    }

    .sosial-shell .sosial-post-media {
        width: 100%;
        height: 340px;
        overflow: hidden;
        background: linear-gradient(180deg, #e2e8f0, #cbd5e1);
    }

    .sosial-shell .sosial-post-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sosial-shell .sosial-post-media--carousel {
        background: #0f172a;
    }

    .sosial-shell .sosial-post-media--carousel .carousel-inner,
    .sosial-shell .sosial-post-media--carousel .carousel-item {
        height: 340px;
        background: #0f172a;
    }

    .sosial-shell .sosial-post-media--carousel img {
        object-fit: contain;
    }

    .sosial-shell .sosial-post-carousel-indicators [data-bs-target] {
        width: 30px;
        height: 30px;
        margin: 0 4px;
        border-radius: 999px;
        text-indent: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        background-color: rgba(15, 23, 42, 0.52);
    }

    .sosial-shell .sosial-post-carousel-indicators .active {
        background-color: rgba(15, 23, 42, 0.92);
    }

    .sosial-shell .sosial-avatar {
        width: 46px;
        height: 46px;
        border-radius: 999px;
        object-fit: cover;
        flex: 0 0 46px;
    }

    .sosial-shell .sosial-avatar-sm {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        object-fit: cover;
        flex: 0 0 34px;
    }

    .sosial-shell .sosial-avatar-lg {
        width: 84px;
        height: 84px;
        border-radius: 999px;
        object-fit: cover;
        flex: 0 0 84px;
        border: 4px solid rgba(255, 255, 255, 0.28);
    }

    .sosial-shell .sosial-post-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .sosial-shell .sosial-post-body {
        white-space: pre-wrap;
        color: #1e293b;
        line-height: 1.7;
    }

    .sosial-shell .sosial-comment-preview,
    .sosial-shell .sosial-comment-card {
        background: #f8fafc;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1rem;
    }

    .sosial-shell .sosial-comment-card {
        padding: 1rem;
        position: relative;
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, transform 0.2s ease;
    }

    .sosial-shell .sosial-comment-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: linear-gradient(180deg, #2563eb 0%, #14b8a6 100%);
        opacity: 0.72;
    }

    .sosial-shell .sosial-comment-card.is-expanded {
        background: #ffffff;
        border-color: rgba(59, 130, 246, 0.28);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    .sosial-shell .sosial-comment-card-depth-1::before {
        background: linear-gradient(180deg, #14b8a6 0%, #22c55e 100%);
    }

    .sosial-shell .sosial-comment-card-depth-2::before,
    .sosial-shell .sosial-comment-card-depth-3::before,
    .sosial-shell .sosial-comment-card-depth-4::before {
        background: linear-gradient(180deg, #f59e0b 0%, #fb7185 100%);
    }

    .sosial-shell .sosial-comment-card-head {
        position: relative;
        z-index: 1;
    }

    .sosial-shell .sosial-comment-toggle {
        border: 0;
        background: transparent;
        padding: 0;
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        width: min(100%, 34rem);
        text-align: left;
        color: inherit;
    }

    .sosial-shell .sosial-comment-toggle-copy {
        min-width: 0;
        flex: 1 1 auto;
    }

    .sosial-shell .sosial-comment-toggle-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        background: rgba(148, 163, 184, 0.14);
        transition: transform 0.2s ease, background-color 0.2s ease, color 0.2s ease;
    }

    .sosial-shell .sosial-comment-toggle[aria-expanded="true"] .sosial-comment-toggle-icon {
        transform: rotate(180deg);
        background: rgba(37, 99, 235, 0.12);
        color: #2563eb;
    }

    .sosial-shell .sosial-comment-body {
        position: relative;
        z-index: 1;
        margin-top: 0.9rem;
        padding-top: 0.9rem;
        border-top: 1px solid rgba(148, 163, 184, 0.18);
    }

    .sosial-shell .sosial-comment-text {
        color: #334155;
        line-height: 1.75;
    }

    .sosial-shell .sosial-comment-depth-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.15rem 0.55rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.06);
        color: #475569;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .sosial-shell .sosial-thread-toggle {
        border-color: rgba(148, 163, 184, 0.24);
        background: rgba(255, 255, 255, 0.72);
    }

    .sosial-shell .sosial-comment-thread-wrap {
        margin-top: 1rem;
    }

    .sosial-shell .sosial-comment-thread {
        margin-top: 1rem;
        padding-left: 1rem;
        border-left: 1px solid rgba(148, 163, 184, 0.22);
        position: relative;
    }

    .sosial-shell .sosial-comment-thread::before {
        content: "";
        position: absolute;
        left: -1px;
        top: 0;
        bottom: 0;
        width: 1px;
        background: linear-gradient(180deg, rgba(37, 99, 235, 0.36) 0%, rgba(20, 184, 166, 0.1) 100%);
    }

    .sosial-shell .sosial-form-control,
    .sosial-shell .sosial-form-select {
        border-radius: 1rem;
        border-color: rgba(148, 163, 184, 0.28);
        padding: 0.85rem 1rem;
        background: #fff;
    }

    .sosial-shell .sosial-form-control:focus,
    .sosial-shell .sosial-form-select:focus {
        border-color: rgba(37, 99, 235, 0.45);
        box-shadow: 0 0 0 0.22rem rgba(37, 99, 235, 0.12);
    }

    .sosial-shell .sosial-search {
        position: relative;
    }

    .sosial-shell .sosial-search .fa {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--sosial-muted);
    }

    .sosial-shell .sosial-search input {
        padding-left: 2.7rem;
    }

    .sosial-shell .sosial-empty {
        text-align: center;
        padding: 2rem 1.25rem;
        color: var(--sosial-muted);
    }

    .sosial-shell .sosial-empty-icon {
        width: 62px;
        height: 62px;
        margin: 0 auto 1rem;
        border-radius: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(37, 99, 235, 0.08);
        color: var(--sosial-blue);
        font-size: 1.4rem;
    }

    .sosial-shell .sosial-zoomable {
        cursor: zoom-in;
    }

    .sosial-shell .sosial-zoom-modal-img {
        max-height: 90vh;
        width: 100%;
        object-fit: contain;
        background: #000;
    }

    .sosial-shell #sosialImageZoomCarousel .carousel-inner,
    .sosial-shell #sosialImageZoomCarousel .carousel-item {
        background: #000;
    }

    @media (max-width: 991.98px) {
        .sosial-shell .sosial-topbar {
            border-radius: 1.2rem;
        }

        .sosial-shell .sosial-page-hero {
            padding: 0.95rem 1rem;
        }

        .sosial-shell .sosial-hero-metrics {
            grid-template-columns: 1fr;
        }

        .sosial-shell .sosial-post-media,
        .sosial-shell .sosial-post-media--carousel .carousel-inner,
        .sosial-shell .sosial-post-media--carousel .carousel-item {
            height: 280px;
        }

        .sosial-shell .sosial-composer {
            position: static;
        }

        .sosial-shell .sosial-sidebar {
            position: static;
        }

        .sosial-shell .sosial-composer-type-grid,
        .sosial-shell .sosial-helper-grid {
            grid-template-columns: 1fr;
        }

        .sosial-shell .sosial-choice-card {
            min-height: 0;
        }
    }

    @media (max-width: 575.98px) {
        .sosial-shell .sosial-main {
            padding-top: 1rem;
        }

        .sosial-shell .sosial-page-hero {
            padding: 0.9rem;
            border-radius: 1.35rem;
        }

        .sosial-shell .sosial-panel {
            padding: 1rem;
        }

        .sosial-shell .sosial-post-media,
        .sosial-shell .sosial-post-media--carousel .carousel-inner,
        .sosial-shell .sosial-post-media--carousel .carousel-item {
            height: 220px;
        }

        .sosial-shell .sosial-comment-toggle {
            width: 100%;
        }
    }
</style>

<div class="sosial-shell">
    <div class="container pt-4">
        @include('partials.adsense.ad-unit', [
            'slot' => 'sosial_top',
            'class' => 'mx-auto',
            'style' => 'max-width: 1100px;',
            'insStyle' => 'display:block; text-align:center;',
            'layout' => 'in-article',
            'format' => 'fluid',
            'label' => null,
        ])
        <nav class="navbar navbar-expand-lg sosial-topbar">
            <div class="container-fluid px-0">
                <a class="navbar-brand sosial-brand" href="{{ route('sosial.feed') }}">
                    <span class="sosial-brand-mark"><i class="fa fa-comments"></i></span>
                    <span>Sosial</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sosialNavbar" aria-controls="sosialNavbar" aria-expanded="false" aria-label="Menüyü aç">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse mt-3 mt-lg-0" id="sosialNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-2">
                        <li class="nav-item">
                            <a class="nav-link sosial-nav-link {{ request()->routeIs('sosial.feed') ? 'active' : '' }}" href="{{ route('sosial.feed') }}">
                                <i class="fa fa-rss"></i> Akış
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sosial-nav-link {{ request()->routeIs('sosial.explore', 'sosial.tags.*') ? 'active' : '' }}" href="{{ route('sosial.explore') }}">
                                <i class="fa fa-compass"></i> Keşfet
                            </a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a class="nav-link sosial-nav-link {{ request()->routeIs('sosial.my') ? 'active' : '' }}" href="{{ route('sosial.my') }}">
                                    <i class="fa fa-pencil-square-o"></i> Paylaşımlarım
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link sosial-nav-link {{ request()->routeIs('sosial.following') ? 'active' : '' }}" href="{{ route('sosial.following') }}">
                                    <i class="fa fa-users"></i> Takip Ettiklerim
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link sosial-nav-link {{ request()->routeIs('sosial.messages.*') ? 'active' : '' }}" href="{{ route('sosial.messages.index') }}">
                                    <i class="fa fa-envelope"></i>
                                    <span>Mesajlar</span>
                                    <span
                                        class="sosial-nav-badge {{ ($unreadSosialMessagesCount ?? 0) > 0 ? '' : 'd-none' }}"
                                        data-sosial-message-badge
                                    >{{ $unreadSosialMessagesCount ?? 0 }}</span>
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link sosial-nav-link" href="{{ route('login') }}">Giriş</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link sosial-nav-link" href="{{ route('register') }}">Kayıt</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link p-0" href="{{ route('sosial.profile.show', auth()->user()) }}">
                                    <span class="sosial-user-chip">
                                        <img src="{{ auth()->user()?->avatarUrl() }}" alt="{{ auth()->user()->name ?? 'Profil' }}">
                                        <span>{{ auth()->user()->name ?? 'Profil' }}</span>
                                    </span>
                                </a>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="sosial-main">
            {{ $slot }}
        </main>

        @include('partials.adsense.ad-unit', [
            'slot' => 'sosial_bottom',
            'class' => 'mx-auto',
            'style' => 'max-width: 1100px;',
            'insStyle' => 'display:block; text-align:center;',
            'layout' => 'in-article',
            'format' => 'fluid',
            'label' => null,
        ])
    </div>

    @auth
        <a class="sosial-message-toast" href="{{ route('sosial.messages.index') }}" data-sosial-message-toast>
            <span class="sosial-brand-mark"><i class="fa fa-envelope"></i></span>
            <span>
                <span class="d-block fw-bold" data-sosial-message-toast-title>Yeni mesaj</span>
                <span class="d-block small text-white-50" data-sosial-message-toast-body>Okunmamış mesajınız var.</span>
            </span>
        </a>
    @endauth

    <div class="modal fade" id="sosialImageZoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-black">
                <div class="modal-body p-0 position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    <div id="sosialImageZoomCarousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-indicators" id="sosialImageZoomCarouselIndicators"></div>
                        <div class="carousel-inner" id="sosialImageZoomCarouselInner"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#sosialImageZoomCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Önceki</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#sosialImageZoomCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Sonraki</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')

    @auth
        <script>
            (function () {
                const badgeEl = document.querySelector('[data-sosial-message-badge]');
                const toastEl = document.querySelector('[data-sosial-message-toast]');
                const toastTitleEl = document.querySelector('[data-sosial-message-toast-title]');
                const toastBodyEl = document.querySelector('[data-sosial-message-toast-body]');
                const unreadUrl = @json(route('sosial.messages.unread-count'));
                const baseTitle = document.title;
                let lastCount = Number(@json($unreadSosialMessagesCount ?? 0));
                let hideTimer = null;

                function setBadge(count) {
                    if (!badgeEl) return;
                    badgeEl.textContent = count > 99 ? '99+' : String(count);
                    badgeEl.classList.toggle('d-none', count <= 0);
                    document.title = count > 0 ? `(${count}) ${baseTitle}` : baseTitle;
                }

                function showToast(count, sender) {
                    if (!toastEl || count <= 0) return;

                    if (toastTitleEl) toastTitleEl.textContent = 'Yeni mesaj';
                    if (toastBodyEl) {
                        toastBodyEl.textContent = sender
                            ? `${sender} size mesaj gönderdi.`
                            : `${count} okunmamış mesajınız var.`;
                    }

                    toastEl.classList.add('show');
                    window.clearTimeout(hideTimer);
                    hideTimer = window.setTimeout(() => toastEl.classList.remove('show'), 6000);
                }

                async function refreshUnreadCount() {
                    try {
                        const res = await fetch(unreadUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        if (!res.ok) return;

                        const json = await res.json();
                        const count = Number(json.count || 0);
                        setBadge(count);

                        if (count > lastCount) {
                            showToast(count, json.latest_sender || '');
                        }

                        lastCount = count;
                    } catch (error) {
                        // Notification polling should not interrupt page usage.
                    }
                }

                setBadge(lastCount);
                window.setInterval(refreshUnreadCount, 15000);
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) refreshUnreadCount();
                });
            })();
        </script>
    @endauth

    <script>
        (function () {
            const modalEl = document.getElementById('sosialImageZoomModal');
            const carouselEl = document.getElementById('sosialImageZoomCarousel');
            const indicatorsEl = document.getElementById('sosialImageZoomCarouselIndicators');
            const innerEl = document.getElementById('sosialImageZoomCarouselInner');
            if (!modalEl || !carouselEl || !indicatorsEl || !innerEl || !window.bootstrap) return;

            const modal = new bootstrap.Modal(modalEl);
            let carousel = null;

            function clearCarousel() {
                if (carousel) {
                    carousel.dispose();
                    carousel = null;
                }
                indicatorsEl.innerHTML = '';
                innerEl.innerHTML = '';
            }

            function buildCarousel(group, activeIndex) {
                clearCarousel();

                const selector = `[data-sosial-zoom-group="${CSS.escape(group)}"][data-sosial-zoom-src]`;
                const thumbs = Array.from(document.querySelectorAll(selector));
                const items = thumbs.map((el) => ({
                    src: el.getAttribute('data-sosial-zoom-src'),
                    alt: el.getAttribute('alt') || '',
                })).filter((x) => !!x.src);

                if (!items.length) return;

                items.forEach((item, i) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.setAttribute('data-bs-target', '#sosialImageZoomCarousel');
                    btn.setAttribute('data-bs-slide-to', String(i));
                    btn.setAttribute('aria-label', `Slide ${i + 1}`);
                    if (i === activeIndex) {
                        btn.classList.add('active');
                        btn.setAttribute('aria-current', 'true');
                    }
                    indicatorsEl.appendChild(btn);

                    const slide = document.createElement('div');
                    slide.className = 'carousel-item' + (i === activeIndex ? ' active' : '');
                    const img = document.createElement('img');
                    img.className = 'd-block w-100 sosial-zoom-modal-img';
                    img.src = item.src;
                    img.alt = item.alt;
                    slide.appendChild(img);
                    innerEl.appendChild(slide);
                });

                carousel = new bootstrap.Carousel(carouselEl, { interval: false, ride: false, wrap: true, touch: true });
            }

            document.addEventListener('click', function (e) {
                const target = e.target?.closest?.('[data-sosial-zoom-src]');
                if (!target) return;
                const src = target.getAttribute('data-sosial-zoom-src');
                if (!src) return;
                e.preventDefault();
                e.stopPropagation();
                const group = target.getAttribute('data-sosial-zoom-group') || 'default';
                const activeIndex = Number(target.getAttribute('data-sosial-zoom-index') || '0') || 0;
                buildCarousel(group, activeIndex);
                modal.show();
            });

            modalEl.addEventListener('hidden.bs.modal', clearCarousel);
        })();
    </script>

    <script>
        (function () {
            if (!('IntersectionObserver' in window)) return;

            function initInfiniteScroll(wrapper) {
                const listEl = wrapper.querySelector('[data-sosial-infinite]');
                const paginationEl = wrapper.querySelector('[data-sosial-pagination]');
                const loadingEl = wrapper.querySelector('[data-sosial-loading]');
                const endEl = wrapper.querySelector('[data-sosial-end]');
                if (!listEl || !paginationEl) return;

                function getNextUrlFromPagination(el) {
                    const link = el?.querySelector('a[rel="next"]') || el?.querySelector('a[aria-label="Next"]') || el?.querySelector('a[aria-label="Sonraki"]');
                    return link ? link.href : '';
                }

                let nextUrl = listEl.getAttribute('data-next-url') || getNextUrlFromPagination(paginationEl);
                if (!nextUrl) return;
                paginationEl.classList.add('d-none');

                const sentinel = document.createElement('div');
                sentinel.setAttribute('data-sosial-sentinel', '');
                wrapper.appendChild(sentinel);

                let isLoading = false;
                let observer = null;

                async function loadMore() {
                    if (isLoading || !nextUrl) return;
                    isLoading = true;
                    if (loadingEl) loadingEl.classList.remove('d-none');

                    try {
                        const res = await fetch(nextUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        if (!res.ok) throw new Error('İstek başarısız oldu');

                        const html = await res.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const newList = doc.querySelector('[data-sosial-infinite]');
                        const newPagination = doc.querySelector('[data-sosial-pagination]');

                        if (newList) {
                            Array.from(newList.children).forEach((item) => listEl.appendChild(item));
                        }
                        if (newPagination) {
                            paginationEl.innerHTML = newPagination.innerHTML;
                        }

                        nextUrl = newList ? (newList.getAttribute('data-next-url') || '') : '';
                        if (!nextUrl) nextUrl = getNextUrlFromPagination(paginationEl);
                        if (!nextUrl && observer) {
                            observer.disconnect();
                            if (endEl) endEl.classList.remove('d-none');
                        }
                    } catch (err) {
                        paginationEl.classList.remove('d-none');
                        if (observer) observer.disconnect();
                    } finally {
                        isLoading = false;
                        if (loadingEl) loadingEl.classList.add('d-none');
                    }
                }

                observer = new IntersectionObserver((entries) => {
                    if (entries.some((entry) => entry.isIntersecting)) loadMore();
                }, { rootMargin: '200px 0px' });

                observer.observe(sentinel);
            }

            document.querySelectorAll('[data-sosial-infinite-wrapper]').forEach(initInfiniteScroll);
        })();
    </script>
</div>
@endsection
