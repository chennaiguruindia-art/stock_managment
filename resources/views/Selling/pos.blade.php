@extends('layouts.app')

@section('page-title', 'Sell / POS Terminal')
@section('page-subtitle', 'Scan barcodes or tap products to bill customers in seconds.')

@section('content')
    <style>
        /* POS Terminal Styles */
        /* ===== Classic POS terminal shell ===== */
        .pos-terminal {
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border, #dfe4ee);
            border-radius: var(--radius, 12px);
            overflow: hidden;
            background: #e9edf4;
            box-shadow: 0 2px 10px rgba(16, 24, 40, .08);
        }

        /* --- Dark top bar --- */
        .pos-topbar {
            display: flex;
            align-items: stretch;
            background: linear-gradient(180deg, #121c30 0%, #0a111e 100%);
            color: #fff;
            min-height: 54px;
        }
        .pos-brand {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .5rem .95rem;
            background: #060b14;
        }
        .pos-logo {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: linear-gradient(135deg, #ef4444, #f97316);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.05rem;
            box-shadow: 0 2px 6px rgba(239, 68, 68, .4);
        }
        .pos-brand-name {
            font-weight: 800;
            font-size: .92rem;
            line-height: 1.05;
            letter-spacing: .02em;
            white-space: nowrap;
        }
        .pos-brand-name span {
            display: block;
            font-size: .55rem;
            font-weight: 700;
            letter-spacing: .24em;
            color: #8fa6c9;
            text-transform: uppercase;
        }
        .pos-echo {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .5rem 1rem;
            font-family: ui-monospace, Consolas, monospace;
            font-size: .92rem;
            font-weight: 700;
            color: #e7edf7;
            border-left: 1px solid rgba(255, 255, 255, .08);
            border-right: 1px solid rgba(255, 255, 255, .08);
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .pos-echo .echo-name { color: #ffffff; }
        .pos-echo .echo-mrp { color: #93c5fd; }
        .pos-echo .echo-eq { color: #4ade80; }
        .pos-echo .echo-empty {
            color: #7f90ab;
            font-weight: 600;
            font-size: .8rem;
            font-family: inherit;
        }
        .pos-topright {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: .4rem .95rem;
            text-align: right;
            white-space: nowrap;
        }
        .pos-billno {
            font-size: .62rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #8fa6c9;
        }
        .pos-billno span { color: #fff; font-weight: 800; }
        .pos-clock {
            font-family: ui-monospace, Consolas, monospace;
            font-size: .78rem;
            font-weight: 700;
            color: #4ade80;
        }

        /* --- Bottom quick-entry bar (classic POS item entry row) --- */
        .pos-entry {
            display: flex;
            align-items: center;
            gap: .6rem;
            flex-wrap: wrap;
            background: #ffffff;
            border-top: 1px solid var(--border, #dfe4ee);
            padding: .55rem .8rem;
        }
        .pos-entry .fld { display: flex; flex-direction: column; gap: .18rem; }
        .pos-entry label {
            font-size: .56rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--muted, #66748b);
        }
        .pos-entry input {
            border: 1px solid var(--border-strong, #c5cede);
            border-radius: 6px;
            padding: .4rem .6rem;
            font-size: .86rem;
            height: 34px;
            background: #fff;
            color: var(--text, #0e1726);
        }
        .pos-entry input:focus {
            outline: none;
            border-color: var(--accent, #1554d1);
            box-shadow: 0 0 0 3px rgba(21, 84, 209, .13);
        }
        #barcodeInput {
            min-width: 230px;
            font-family: ui-monospace, Consolas, monospace;
            font-weight: 600;
        }
        #scanQty { width: 66px; text-align: center; font-weight: 800; }
        .pos-entry .entry-add {
            align-self: flex-end;
            border: 0;
            border-radius: 6px;
            background: var(--accent, #1554d1);
            color: #fff;
            font-weight: 800;
            font-size: .84rem;
            height: 34px;
            padding: 0 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            cursor: pointer;
        }
        .pos-entry .entry-add:hover { background: var(--accent-strong, #1043a8); }
        .pos-entry .entry-hint {
            align-self: flex-end;
            font-size: .68rem;
            color: var(--muted, #66748b);
            margin-left: auto;
            height: 34px;
            display: inline-flex;
            align-items: center;
        }
        .pos-entry .entry-hint kbd {
            background: var(--surface-3, #eaeff7);
            border: 1px solid var(--border-strong, #c5cede);
            border-radius: 4px;
            padding: 0 .3rem;
            font-family: inherit;
            font-size: .66rem;
            font-weight: 700;
            margin: 0 .12rem;
        }

        /* --- Blue function-key strip --- */
        .pos-fkeys {
            display: flex;
            gap: 2px;
            background: #060b14;
            padding: 4px 5px;
            overflow-x: auto;
        }
        .pos-fkey {
            flex: 1;
            min-width: 96px;
            border: 0;
            background: linear-gradient(180deg, #2563eb 0%, #1554d1 100%);
            color: #fff;
            font-size: .66rem;
            font-weight: 800;
            letter-spacing: .04em;
            padding: .48rem .3rem;
            border-radius: 3px;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: background .12s ease, transform .1s ease;
        }
        .pos-fkey:hover { background: linear-gradient(180deg, #3b82f6 0%, #1d63e6 100%); }
        .pos-fkey:active { transform: translateY(1px); }
        .pos-fkey kbd {
            background: rgba(255, 255, 255, .2);
            border: 0;
            border-radius: 3px;
            padding: 0 .28rem;
            margin-right: .25rem;
            font-family: inherit;
            font-size: .64rem;
            font-weight: 800;
        }
        .pos-fkey.fkey-cancel { background: linear-gradient(180deg, #64748b 0%, #475569 100%); }
        .pos-fkey.fkey-cancel:hover { background: linear-gradient(180deg, #7c8ba1 0%, #56657c 100%); }
        .pos-fkey.fkey-tender { background: linear-gradient(180deg, #12b76a 0%, #0f9d58 100%); }
        .pos-fkey.fkey-tender:hover { background: linear-gradient(180deg, #17c977 0%, #12ad64 100%); }

        /* --- Bottom status bar --- */
        .pos-status {
            display: flex;
            align-items: center;
            gap: 1.15rem;
            flex-wrap: wrap;
            background: #111a2b;
            color: #9fb0c9;
            font-size: .63rem;
            letter-spacing: .07em;
            text-transform: uppercase;
            padding: .34rem .85rem;
        }
        .pos-status b { color: #e7edf7; font-weight: 700; }
        .pos-status .st-right { margin-left: auto; color: #4ade80; font-weight: 700; }
        .pos-status .st-right::before {
            content: "";
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #4ade80;
            margin-right: .35rem;
            vertical-align: middle;
        }

        .pos-controls {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .pos-search-input {
            flex: 1;
            min-width: 220px;
            border-radius: 8px;
            border: 1px solid var(--border-strong, #c5cede);
            padding: .5rem .9rem .5rem 2.2rem;
            background: #ffffff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2366748b' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat .8rem center;
            font-size: .875rem;
            color: var(--text, #0e1726);
        }

        .pos-search-input:focus { outline: none; border-color: var(--accent, #1554d1); box-shadow: 0 0 0 3px rgba(21, 84, 209, .13); }

        .category-pills {
            display: flex;
            align-items: center;
            gap: .4rem;
            overflow-x: auto;
            padding-bottom: .25rem;
        }

        .category-pill {
            background: #ffffff;
            border: 1px solid var(--border-strong, #c5cede);
            color: var(--muted, #66748b);
            border-radius: 8px;
            padding: .4rem .8rem;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all .15s ease;
        }

        .category-pill:hover { border-color: var(--accent, #1554d1); color: var(--accent, #1554d1); }

        .category-pill.active {
            background: var(--accent, #1554d1);
            color: #ffffff;
            border-color: var(--accent, #1554d1);
        }

        .pos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
            gap: .8rem;
            max-height: 430px;
            overflow-y: auto;
            padding-right: .3rem;
        }

        .pos-card {
            background: #ffffff;
            border: 1px solid var(--border, #dfe4ee);
            border-radius: var(--radius, 10px);
            padding: .8rem;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all .15s ease;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
            user-select: none;
            min-height: 138px;
        }

        .pos-card:hover {
            border-color: var(--accent, #1554d1);
            box-shadow: 0 4px 12px rgba(21, 84, 209, .14);
        }

        .pos-card.out-of-stock {
            opacity: .6;
            cursor: not-allowed;
            background: var(--surface-2, #f7f9fc);
        }

        .pos-card .p-type {
            font-size: .63rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--accent-strong, #1043a8);
            background: var(--accent-soft, #e7eefe);
            padding: .16rem .45rem;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: .35rem;
        }

        .pos-card .p-name {
            font-weight: 700;
            font-size: .88rem;
            margin-bottom: .2rem;
            line-height: 1.25;
            color: var(--text, #0e1726);
        }

        .pos-card .p-price {
            font-weight: 800;
            font-size: 1.02rem;
            color: var(--accent, #1554d1);
            font-variant-numeric: tabular-nums;
        }

        /* ===== Body split: bill grid (left) + totals sidebar (right) ===== */
        .pos-body {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 324px;
            background: var(--border, #dfe4ee);
            gap: 1px;
        }
        @media (max-width: 1199.98px) {
            .pos-body { grid-template-columns: 1fr; }
        }
        .pos-left {
            background: #fff;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        /* --- Current bill grid (classic item table) --- */
        .pos-bill { display: flex; flex-direction: column; background: #fff; }
        .pos-bill-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .6rem;
            padding: .45rem .8rem;
            background: linear-gradient(100deg, #0f1f36 0%, #16294a 100%);
            color: #fff;
        }
        .pos-bill-title {
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
        }
        .pos-bill-title .bill-id {
            color: #8fa6c9;
            font-weight: 700;
            letter-spacing: .06em;
        }
        .pos-bill-title .bill-id b { color: #fff; }
        .cart-items-wrap {
            overflow-y: auto;
            padding: 0;
            min-height: 168px;
            max-height: 320px;
            background: #fff;
        }

        .pos-bill-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .83rem;
        }
        .pos-bill-table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #1b3f9c;
            color: #fff;
            font-size: .6rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .48rem .5rem;
            text-align: left;
            white-space: nowrap;
            border-right: 1px solid rgba(255, 255, 255, .14);
        }
        .pos-bill-table thead th:last-child { border-right: 0; }
        .pos-bill-table td {
            padding: .4rem .5rem;
            border-top: 1px solid #e6ebf4;
            vertical-align: middle;
        }
        .pos-bill-table tbody tr:nth-child(even) { background: #f5f8fd; }
        .pos-bill-table tbody tr:hover { background: #e9f1fe; }
        .pos-bill-table tbody tr.selected {
            background: linear-gradient(180deg, #1d63e6 0%, #1554d1 100%);
            color: #fff;
        }
        .pos-bill-table tbody tr.selected .ci-name { color: #fff; }
        .pos-bill-table tbody tr.selected .ci-flag.with { background: rgba(255,255,255,.18); color:#fff; border-color: rgba(255,255,255,.35); }
        .pos-bill-table tbody tr.selected .ci-flag.without { background: rgba(255,255,255,.16); color:#ffedd5; border-color: rgba(255,255,255,.3); }
        .pos-bill-table tbody tr.selected .text-muted,
        .pos-bill-table tbody tr.selected .col-disc { color: #dbe7ff !important; }
        .pos-bill-table .col-no   { width: 40px; text-align: center; color: var(--muted, #66748b); font-variant-numeric: tabular-nums; }
        .pos-bill-table .col-mrp  { width: 84px; text-align: right; white-space: nowrap; color: var(--muted, #66748b); text-decoration: line-through; font-size: .78rem; }
        .pos-bill-table .col-qty  { width: 106px; text-align: center; }
        .pos-bill-table .col-price{ width: 84px; text-align: right; white-space: nowrap; }
        .pos-bill-table .col-amt  { width: 88px; text-align: right; white-space: nowrap; color: var(--muted, #66748b); }
        .pos-bill-table .col-disc { width: 78px; text-align: right; white-space: nowrap; color: #0b7a53; font-weight: 700; font-size: .78rem; }
        .pos-bill-table .col-net  { width: 100px; text-align: right; white-space: nowrap; font-weight: 800; font-variant-numeric: tabular-nums; }
        .pos-bill-table .col-x    { width: 34px; text-align: center; }
        .pos-bill-table .col-desc { min-width: 150px; }
        .pos-bill-table .ci-name {
            font-weight: 700;
            font-size: .84rem;
            color: var(--text, #0e1726);
            line-height: 1.2;
        }
        .pos-bill-table .row-remove {
            border: 0;
            background: transparent;
            color: #dc2626;
            font-size: .92rem;
            cursor: pointer;
            opacity: .55;
            padding: 0;
        }
        .pos-bill-table .row-remove:hover { opacity: 1; }
        .pos-bill-table tr.selected .row-remove { color: #fecaca; opacity: 1; }
        .pos-bill-table .pos-empty-cell {
            text-align: center;
            padding: 1.6rem 1rem !important;
            color: var(--muted, #66748b);
            font-size: .84rem;
        }
        .pos-bill-table .pos-empty-cell i { display: block; font-size: 1.5rem; opacity: .35; margin-bottom: .3rem; }

        /* --- Item lookup (catalog) panel --- */
        .pos-lookup { border-top: 1px solid var(--border, #dfe4ee); padding: .8rem .9rem .9rem; background: #fff; flex: 1; }
        .pos-lookup-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .6rem;
            margin-bottom: .6rem;
        }
        .pos-lookup-title {
            font-size: .66rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--muted, #66748b);
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }
        .pos-lookup-title i { color: var(--accent, #1554d1); }

        /* --- Right totals sidebar --- */
        .pos-side {
            background: #fff;
            padding: .75rem .7rem;
            display: flex;
            flex-direction: column;
            gap: .6rem;
            min-width: 0;
        }
        .side-card {
            border: 1px solid var(--border, #dfe4ee);
            border-radius: 8px;
            overflow: hidden;
            background: var(--surface-2, #f7f9fc);
        }
        .side-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .5rem;
            padding: .42rem .7rem;
            font-size: .79rem;
            color: var(--muted, #66748b);
            border-bottom: 1px solid #e9edf5;
            font-variant-numeric: tabular-nums;
        }
        .side-row:last-child { border-bottom: 0; }
        .side-row b { color: var(--text, #0e1726); font-weight: 700; }
        .side-row .disc-val { color: #0b7a53; font-weight: 800; }

        /* --- Big green NET block (classic POS) --- */
        .pos-net {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .6rem;
            border-radius: 8px;
            padding: .75rem .9rem;
            background: linear-gradient(180deg, #16b877 0%, #0f9d58 55%, #0b7a53 100%);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(15, 157, 88, .35);
        }
        .pos-net .net-label {
            font-size: .72rem;
            font-weight: 900;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: #d9ffe9;
        }
        .pos-net .net-right { text-align: right; line-height: 1.1; }
        .pos-net .strike-total {
            display: block;
            font-size: .72rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .75);
            text-decoration: line-through;
            margin: 0 0 .1rem;
        }
        .pos-net .net-value {
            font-size: 1.55rem;
            font-weight: 900;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
            display: inline-block;
        }
        .pos-net .net-value.pulse { animation: totalPulse .5s ease; }
        @keyframes totalPulse {
            0% { transform: scale(1); }
            40% { transform: scale(1.09); }
            100% { transform: scale(1); }
        }

        .side-label {
            font-size: .58rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--muted, #66748b);
            margin: .1rem 0 -.2rem;
        }

        .btn-clear-cart {
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .16);
            color: #e2e9f5;
            font-size: .75rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 6px;
            padding: .28rem .6rem;
        }

        .btn-clear-cart:hover { background: rgba(220, 38, 38, .85); border-color: rgba(220, 38, 38, .9); color: #fff; }

        .ci-qty-ctrl {
            display: flex;
            align-items: center;
            gap: .2rem;
            background: var(--surface-3, #eaeff7);
            border-radius: 7px;
            padding: .15rem;
        }

        .ci-qty-btn {
            width: 24px;
            height: 24px;
            border: 0;
            background: #ffffff;
            border-radius: 5px;
            font-weight: 800;
            color: var(--accent, #1554d1);
            cursor: pointer;
            box-shadow: var(--shadow, 0 1px 2px rgba(16, 24, 40, .05));
        }

        .ci-qty-btn:hover { background: var(--accent, #1554d1); color: #fff; }

        .ci-qty-val {
            width: 28px;
            text-align: center;
            border: 0;
            background: transparent;
            font-weight: 700;
            font-size: .85rem;
            font-variant-numeric: tabular-nums;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            font-size: .85rem;
            margin-bottom: .3rem;
            color: var(--muted, #66748b);
        }

        .summary-line b { color: var(--text, #0e1726); font-variant-numeric: tabular-nums; }

        .summary-line.discount-line { align-items: center; }
        .summary-line.discount-line .discount-amount { color: #d97706; }

        /* ===== Unique discount "coupon ticket" ===== */
        .discount-ticket {
            position: relative;
            margin: .15rem 0 .1rem;
            border-radius: 12px;
            padding: .7rem .8rem .75rem;
            background:
                radial-gradient(circle at 100% 0, rgba(16, 185, 129, .10), transparent 42%),
                linear-gradient(135deg, #0b7a53 0%, #0f9d58 55%, #16b877 100%);
            color: #eafff4;
            box-shadow: 0 6px 18px rgba(15, 157, 88, .28);
            overflow: hidden;
            isolation: isolate;
        }
        /* perforated ticket notches on both sides */
        .discount-ticket::before,
        .discount-ticket::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--surface-2, #f7f9fc);
            transform: translateY(-50%);
            z-index: -1;
        }
        .discount-ticket::before { left: -8px; }
        .discount-ticket::after { right: -8px; }
        /* faint diagonal ribbons for a "printed ticket" feel */
        .discount-ticket .rib {
            position: absolute;
            inset: 0;
            background-image: repeating-linear-gradient(115deg, rgba(255,255,255,.09) 0 8px, transparent 8px 20px);
            opacity: .5;
            pointer-events: none;
            z-index: -1;
        }

        .dt-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            margin-bottom: .55rem;
        }
        .dt-label {
            display: inline-flex;
            align-items: center;
            gap: .38rem;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #d6ffe9;
        }
        .dt-label i { color: #a7f3d0; }
        .dt-hint {
            font-size: .66rem;
            color: rgba(234, 255, 244, .78);
            font-weight: 600;
        }

        .disc-presets {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: .35rem;
            margin-bottom: .5rem;
        }
        .disc-chip {
            border: 1px solid rgba(255, 255, 255, .28);
            background: rgba(255, 255, 255, .12);
            color: #f2fff8;
            border-radius: 8px;
            padding: .34rem 0;
            font-size: .8rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            cursor: pointer;
            transition: transform .12s ease, background .15s ease, box-shadow .15s ease;
        }
        .disc-chip:hover { background: rgba(255, 255, 255, .24); transform: translateY(-1px); }
        .disc-chip.active {
            background: #ffffff;
            color: #0b7a53;
            border-color: #ffffff;
            box-shadow: 0 3px 10px rgba(3, 60, 36, .35);
            transform: translateY(-1px);
        }

        .disc-custom-row {
            display: flex;
            align-items: center;
            gap: .45rem;
        }
        .disc-input-wrap {
            display: inline-flex;
            align-items: center;
            flex: 1;
            border: 1px dashed rgba(255, 255, 255, .5);
            background: rgba(255, 255, 255, .14);
            border-radius: 9px;
            overflow: hidden;
            height: 34px;
            transition: box-shadow .15s ease, border-color .15s ease;
        }
        .disc-input-wrap:focus-within {
            border-color: #ffffff;
            border-style: solid;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, .18);
        }
        .disc-prefix {
            padding: 0 .45rem 0 .6rem;
            font-weight: 800;
            color: #c9ffe4;
            font-size: .85rem;
        }
        .discount-percent-input {
            flex: 1;
            min-width: 0;
            border: 0;
            background: transparent;
            text-align: center;
            font-size: .95rem;
            font-weight: 800;
            color: #ffffff;
            height: 100%;
            -moz-appearance: textfield;
        }
        .discount-percent-input::-webkit-outer-spin-button,
        .discount-percent-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .discount-percent-input:focus { outline: none; }
        .discount-percent-input::placeholder { color: rgba(234, 255, 244, .6); font-weight: 700; }
        .disc-suffix {
            padding: 0 .7rem;
            font-size: .9rem;
            font-weight: 800;
            color: #0b7a53;
            background: #d6ffe9;
            height: 100%;
            display: inline-flex;
            align-items: center;
        }
        .disc-clear {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, .3);
            background: rgba(255, 255, 255, .12);
            color: #eafff4;
            border-radius: 9px;
            font-size: .9rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background .15s ease;
        }
        .disc-clear:hover { background: rgba(220, 38, 38, .9); border-color: rgba(220, 38, 38, .95); }

        /* "You saved" ribbon that slides in when a discount is active */
        .savings-ribbon {
            margin-top: .55rem;
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 9px;
            padding: .34rem .6rem;
            animation: savePop .35s cubic-bezier(.2,.9,.3,1.3);
        }
        .savings-ribbon.show { display: flex; }
        .savings-ribbon .sv-left {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: .74rem;
            font-weight: 800;
            color: #f2fff8;
        }
        .savings-ribbon .sv-left i { color: #ffe9a3; }
        .savings-ribbon .sv-amt {
            font-size: .86rem;
            font-weight: 900;
            color: #ffe9a3;
            font-variant-numeric: tabular-nums;
        }
        @keyframes savePop {
            0% { opacity: 0; transform: translateY(-6px) scale(.96); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Totals row that lights up + count-up pulse when a discount applies */
        .summary-line.grand-total {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text, #0e1726);
            margin-top: .4rem;
            padding-top: .5rem;
            border-top: 2px solid var(--accent, #1554d1);
        }
        .summary-line.grand-total b { color: var(--accent, #1554d1); font-size: 1.25rem; }
        .grand-total.discounted b { color: #0b7a53; }
        .grand-total.discounted { border-top-color: #0f9d58; }
        .grand-total b.pulse { animation: totalPulse .5s ease; }
        @keyframes totalPulse {
            0% { transform: scale(1); }
            40% { transform: scale(1.09); }
            100% { transform: scale(1); }
        }
        .strike-total {
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted, #66748b);
            text-decoration: line-through;
            margin-left: .4rem;
        }

        .btn-checkout-pay {
            width: 100%;
            border: 0;
            border-radius: 8px;
            background: linear-gradient(180deg, #2563eb 0%, #1554d1 100%);
            color: #ffffff;
            font-weight: 900;
            font-size: .95rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: .85rem;
            box-shadow: 0 4px 14px rgba(21, 84, 209, .4);
            transition: all .15s ease;
        }

        .btn-checkout-pay:hover:not(:disabled) { background: linear-gradient(180deg, #3b82f6 0%, #1d63e6 100%); }

        .btn-checkout-pay:disabled {
            background: linear-gradient(180deg, #b9c4d6 0%, #9dabc1 100%);
            box-shadow: none;
            cursor: not-allowed;
        }

        /* Small dupatta flag shown inside a cart line */
        .ci-flag {
            display: inline-block;
            font-size: .66rem;
            font-weight: 700;
            letter-spacing: .03em;
            padding: .1rem .4rem;
            border-radius: 5px;
            margin-top: .2rem;
        }
        .ci-flag.with { background: var(--accent-soft, #e7eefe); color: var(--accent-strong, #1043a8); border: 1px solid #cdddfb; }
        .ci-flag.without { background: var(--warn-soft, #fdf1e2); color: var(--warn, #d97706); border: 1px solid #f6ddba; }

        /* POS popup shell (dupatta choice + order confirmation) */
        .pos-modal { border: 0; border-radius: var(--radius, 10px); overflow: hidden; box-shadow: 0 24px 60px rgba(11, 22, 38, .28); }
        .pos-modal-head {
            background: #0f1f36;
            color: #fff;
            padding: .85rem 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .pos-modal-head h6 { font-weight: 700; font-size: .95rem; letter-spacing: .01em; }
        .pos-modal-body { padding: 1.25rem 1.25rem 1rem; background: #fff; }
        .pos-modal-foot {
            background: var(--surface-2, #f7f9fc);
            border-top: 1px solid var(--border, #dfe4ee);
            padding: .8rem 1.1rem;
            display: flex;
            justify-content: flex-end;
            gap: .5rem;
        }
        .pos-modal-foot .btn { border-radius: 8px; font-weight: 600; font-size: .88rem; padding: .5rem .95rem; }
        .pos-modal-pname { font-size: 1.08rem; font-weight: 800; color: var(--text, #0e1726); }

        /* Dupatta choice */
        .dup-choices { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }
        .dup-choice {
            border: 1.5px solid var(--border, #dfe4ee);
            background: #fff;
            border-radius: var(--radius, 10px);
            padding: 1rem .6rem;
            cursor: pointer;
            transition: all .15s ease;
            font-family: inherit;
        }
        .dup-choice i { font-size: 1.35rem; }
        .dup-choice .dup-label { display: block; font-weight: 700; font-size: .88rem; color: var(--text, #0e1726); }
        .dup-choice .dup-price { display: block; font-weight: 800; font-size: 1.15rem; font-variant-numeric: tabular-nums; margin-top: .25rem; }
        .dup-choice .dup-note { display: block; font-size: .7rem; color: var(--muted, #66748b); margin-top: .15rem; }
        .dup-with { border-color: #cdddfb; background: var(--accent-soft, #e7eefe); }
        .dup-with i, .dup-with .dup-price { color: var(--accent, #1554d1); }
        .dup-with:hover { border-color: var(--accent, #1554d1); box-shadow: 0 4px 14px rgba(21, 84, 209, .18); transform: translateY(-1px); }
        .dup-without { border-color: #f6ddba; background: var(--warn-soft, #fdf1e2); }
        .dup-without i, .dup-without .dup-price { color: var(--warn, #d97706); }
        .dup-without:hover { border-color: var(--warn, #d97706); box-shadow: 0 4px 14px rgba(217, 119, 6, .18); transform: translateY(-1px); }

        /* Order confirmation */
        .confirm-table-wrap { border: 1px solid var(--border, #dfe4ee); border-radius: var(--radius, 10px); overflow: hidden; }
        .confirm-table { font-size: .87rem; margin-bottom: 0; }
        .confirm-table thead th {
            background: #0f1f36;
            color: #fff;
            font-size: .68rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 700;
            border: 0;
            padding: .6rem .7rem;
        }
        .confirm-table td { border-color: var(--border, #dfe4ee); padding: .65rem .7rem; }
        .confirm-table tbody tr:last-child td { border-bottom: 1px solid var(--border, #dfe4ee); }
        .confirm-table tfoot td { border: 0; background: var(--surface-2, #f7f9fc); font-variant-numeric: tabular-nums; }
        .confirm-table .confirm-grand td { background: var(--accent-soft, #e7eefe); font-weight: 800; font-size: 1rem; color: var(--accent-strong, #1043a8); }
        .confirm-table .cf-name { font-weight: 600; }
        .confirm-meta {
            margin-top: .9rem;
            display: flex;
            flex-wrap: wrap;
            gap: .5rem .75rem;
            font-size: .78rem;
            color: var(--muted, #66748b);
        }
        .confirm-meta span b { color: var(--text, #0e1726); font-weight: 700; }
        .btn.confirm-ok {
            background: var(--ok, #0f9d58);
            border-color: var(--ok, #0f9d58);
            color: #fff;
        }
        .btn.confirm-ok:hover { background: #0b7d47; border-color: #0b7d47; color: #fff; }

        /* Small linear shop-counter (thermal) bill */
        .thermal-receipt {
            font-family: ui-monospace, Consolas, "Courier New", monospace;
            color: #191114;
            padding: .25rem .15rem 0;
        }
        .th-store { text-align: center; font-weight: 800; font-size: 1.08rem; letter-spacing: .06em; }
        .th-muted { text-align: center; font-size: .7rem; line-height: 1.45; color: #4c4448; }
        .th-dash { border-top: 1px dashed #6b6165; margin: .5rem 0; }
        .th-meta { width: 100%; font-size: .74rem; border-collapse: collapse; }
        .th-meta td { padding: .05rem 0; vertical-align: top; }
        .th-meta td:first-child { width: 82px; white-space: nowrap; }
        .th-item-name { font-weight: 700; font-size: .78rem; margin-top: .35rem; word-break: break-word; line-height: 1.3; }
        .th-row { display: flex; justify-content: space-between; gap: .75rem; font-size: .75rem; }
        .th-totals { margin-top: .2rem; }
        .th-subrow { display: flex; justify-content: space-between; font-size: .74rem; padding: .06rem 0; color: #3d3438; }
        .th-total-row { display: flex; justify-content: space-between; font-weight: 800; font-size: 1.02rem; margin-top: .3rem; border-top: 1px dashed #6b6165; padding-top: .35rem; }
        .th-thanks { text-align: center; font-weight: 800; font-size: .82rem; letter-spacing: .12em; }
        .th-tear { text-align: center; letter-spacing: .35em; color: #8a7f83; font-size: .7rem; }

        @media print {
            @page { size: 80mm auto; margin: 3mm; }
            body * { visibility: hidden; }
            #receiptModal, #receiptModal * { visibility: visible; }
            #receiptModal {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            #receiptModal .modal-dialog { max-width: 74mm !important; margin: 0 !important; width: 100% !important; }
            #receiptModal .modal-content {
                border: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background: #ffffff !important;
            }
            .no-print { display: none !important; }
            .thermal-receipt { font-size: 11px; padding: 0; }
        }
    </style>

    @include('layouts.alerts')

    <div class="pos-terminal">
        <!-- Dark top bar (classic POS header) -->
        <div class="pos-topbar">
            <div class="pos-brand">
                <span class="pos-logo"><i class="bi bi-snow2"></i></span>
                <div class="pos-brand-name">Zyra Billing<span>Enterprise POS</span></div>
            </div>
            <div class="pos-echo" id="posEcho">
                <span class="echo-empty"><i class="bi bi-upc-scan me-1"></i>Ready &mdash; scan a barcode or tap a product to start billing</span>
            </div>
            <div class="pos-topright">
                <div class="pos-billno">Bill No <span>{{ $nextOrderId }}</span></div>
                <div class="pos-clock" id="posClock">--:--:--</div>
            </div>
        </div>

        <!-- Main split: current bill + lookup (left) / totals sidebar (right) -->
        <div class="pos-body">
            <div class="pos-left">
                <!-- Current bill grid -->
                <div class="pos-bill">
                    <div class="pos-bill-head">
                        <span class="pos-bill-title"><i class="bi bi-receipt-cutoff"></i> Current Bill <span class="bill-id">Order ID <b>{{ $nextOrderId }}</b></span></span>
                        <button type="button" class="btn-clear-cart" id="clearCartBtn"><i class="bi bi-trash"></i> Clear &middot; F4</button>
                    </div>
                    <div class="cart-items-wrap" id="cartItemsWrap">
                        <table class="pos-bill-table">
                            <thead>
                                <tr>
                                    <th class="col-no">S.NO</th>
                                    <th class="col-desc">Description</th>
                                    <th class="col-mrp">MRP</th>
                                    <th class="col-qty">Qty</th>
                                    <th class="col-price">Price</th>
                                    <th class="col-amt">Amount</th>
                                    <th class="col-disc">Disc</th>
                                    <th class="col-net">Net Amount</th>
                                    <th class="col-x"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="9" class="pos-empty-cell">
                                        <i class="bi bi-cart-x"></i>
                                        Cart is empty &mdash; scan a barcode or tap a product below to add items.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Item lookup -->
                <div class="pos-lookup">
                    <div class="pos-lookup-head">
                        <span class="pos-lookup-title"><i class="bi bi-grid-3x2-gap-fill"></i> Item lookup &mdash; tap a card or scan to add</span>
                    </div>

                    <!-- Filters -->
                    <div class="pos-controls">
                        <input type="text" id="catalogSearch" class="pos-search-input" placeholder="Search by product name, brand, SKU or barcode...">

                        <div class="category-pills" id="categoryPills">
                            <button class="category-pill active" data-cat="ALL">All Products</button>
                            @php
                                $types = $products->pluck('product_type')->filter()->unique()->values();
                            @endphp
                            @foreach ($types as $t)
                                <button class="category-pill" data-cat="{{ strtolower($t) }}">{{ $t }}</button>
                            @endforeach
                        </div>
                    </div>

            <!-- Products Grid -->
            <div class="pos-grid" id="productGrid">
                @forelse ($products as $product)
                    @php
                        $isOut = $product->stock <= 0;
                        $isLow = $product->stock > 0 && $product->stock < 5;
                        $stockClass = $isOut ? 'text-danger fw-bold' : ($isLow ? 'text-warning fw-bold' : 'text-success');
                        $stockText = $isOut ? 'Out of stock' : ($isLow ? "Low: {$product->stock}" : "Stock: {$product->stock}");
                        $mrpVal = $product->mrp ?? ($product->selling_price + 200);
                    @endphp
                    <div class="pos-card {{ $isOut ? 'out-of-stock' : '' }}"
                         data-id="{{ $product->id }}"
                         data-name="{{ $product->product_name }}"
                         data-brand="{{ $product->brand }}"
                         data-type="{{ strtolower($product->product_type ?? '') }}"
                         data-sku="{{ $product->sku }}"
                         data-barcode="{{ $product->barcode }}"
                         data-stock="{{ $product->stock }}"
                         data-price="{{ $product->selling_price ?? 0 }}"
                         data-mrp="{{ $mrpVal }}"
                         data-dupatta="{{ $product->has_dupatta ? 1 : 0 }}"
                         data-dupatta-discount="{{ $product->dupatta_discount ?? 350 }}"
                         data-last4="{{ strtolower(substr($product->barcode, -4)) }}"
                         data-search="{{ strtolower($product->product_name . ' ' . $product->brand . ' ' . $product->sku . ' ' . $product->barcode . ' ' . $product->color . ' ' . $product->size . ' ' . $product->product_type) }}">

                        <div>
                            @if ($product->product_type)
                                <span class="p-type">{{ $product->product_type }}</span>
                            @endif
                            @if ($product->has_dupatta)
                                <span class="p-type" style="background:var(--accent-soft);color:var(--accent-strong);"><i class="bi bi-scissors me-1"></i>Dupatta</span>
                            @endif
                            <div class="p-name">{{ $product->product_name }}</div>
                            <div class="text-muted" style="font-size:.78rem;">
                                {{ $product->brand }}
                                @if ($product->size) &middot; {{ $product->size }} @endif
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                            <div>
                                <span class="p-price">₹{{ number_format($product->selling_price ?? 0, 2) }}</span>
                                @if ($mrpVal > ($product->selling_price ?? 0))
                                    <span class="text-muted text-decoration-line-through ms-1" style="font-size:.75rem;">₹{{ number_format($mrpVal, 2) }}</span>
                                @endif
                            </div>
                            <span style="font-size:.78rem;" class="{{ $stockClass }}">{{ $stockText }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5" style="grid-column:1/-1;">
                        <i class="bi bi-box-seam fs-1 opacity-50 mb-2 d-block"></i>
                        No products available in catalog.
                    </div>
                @endforelse
                <div id="noMatchMsg" class="text-center text-muted py-5 d-none" style="grid-column: 1 / -1;">
                    <i class="bi bi-search fs-1 opacity-50 mb-2 d-block"></i>
                    No products matching search query.
                </div>
            </div>
                </div>
            </div>

            <!-- Right: totals sidebar (classic POS right panel) -->
            <aside class="pos-side">
                <div class="side-card">
                    <div class="side-row"><span>Total Items</span><b id="totalItemsVal">0</b></div>
                    <div class="side-row"><span>Total Qty</span><b id="totalQtyVal">0</b></div>
                    <div class="side-row"><span>Sub Total</span><b id="subtotalVal">₹0.00</b></div>
                    <div class="side-row"><span>Discount</span><b class="disc-val" id="discountSummaryVal">&minus; ₹0.00</b></div>
                    <div class="side-row"><span>Round Off</span><b>₹0.00</b></div>
                </div>

                <div class="side-label">Discount &mdash; order level</div>

                    <!-- Unique discount "coupon ticket" -->
                    <div class="discount-ticket" id="discountTicket">
                        <span class="rib"></span>
                        <div class="dt-head">
                            <span class="dt-label"><i class="bi bi-ticket-perforated-fill"></i> Discount</span>
                            <span class="dt-hint">tap a preset or type any %</span>
                        </div>
                        <div class="disc-presets" id="discPresets">
                            <button type="button" class="disc-chip" data-pct="5">5%</button>
                            <button type="button" class="disc-chip" data-pct="10">10%</button>
                            <button type="button" class="disc-chip" data-pct="15">15%</button>
                            <button type="button" class="disc-chip" data-pct="20">20%</button>
                            <button type="button" class="disc-chip" data-pct="25">25%</button>
                        </div>
                        <div class="disc-custom-row">
                            <span class="disc-input-wrap">
                                <span class="disc-prefix">−</span>
                                <input type="number" min="0" max="100" step="0.01" id="discountPercentInput" class="discount-percent-input" placeholder="0" value="0">
                                <span class="disc-suffix">%</span>
                            </span>
                            <button type="button" class="disc-clear" id="discClearBtn" title="Clear discount"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <div class="savings-ribbon" id="savingsRibbon">
                            <span class="sv-left"><i class="bi bi-star-fill"></i> You save</span>
                            <span class="sv-amt" id="savingsAmt">₹0.00</span>
                        </div>
                    </div>

                <!-- Big green NET block -->
                <div class="pos-net" id="grandTotalRow">
                    <span class="net-label">Net</span>
                    <span class="net-right">
                        <span class="strike-total" id="strikeTotal" style="display:none;">₹0.00</span>
                        <span class="net-value" id="grandTotalVal">₹0.00</span>
                    </span>
                </div>

                <form method="POST" action="{{ route('pos_checkout') }}" id="checkoutForm">
                    @csrf
                    <input type="hidden" name="cart" id="cartJsonInput">
                    <input type="hidden" name="discount_percent" id="discountPercentInputHidden" value="0">

                    <div class="side-label">Customer</div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <input type="text" name="customer_name" class="form-control form-control-sm" placeholder="Customer Name" maxlength="100" value="{{ old('customer_name') }}" style="border-radius:8px;" required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="customer_mobile" class="form-control form-control-sm" placeholder="Mobile No" maxlength="20" value="{{ old('customer_mobile') }}" style="border-radius:8px;" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <select name="payment_mode" class="form-select form-select-sm fw-semibold" style="border-radius:8px;">
                            <option value="Online UPI" selected>Payment Mode: Online UPI</option>
                            <option value="Counter Cash">Payment Mode: Counter Cash</option>
                            <option value="Credit / Debit Card">Payment Mode: Credit / Debit Card</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-checkout-pay" id="checkoutBtn" disabled>
                        <i class="bi bi-cash-stack me-1"></i> Tender &middot; Complete Order
                    </button>
                </form>
            </aside>
        </div>

        <!-- Bottom quick-entry row (classic POS item entry) -->
        <div class="pos-entry">
            <span class="fld">
                <label for="barcodeInput">Item / Barcode</label>
                <input type="text" id="barcodeInput" placeholder="Scan barcode or type last 4 digits..." autocomplete="off">
            </span>
            <span class="fld">
                <label for="scanQty">Qty</label>
                <input type="number" id="scanQty" class="qty-input" value="1" min="1">
            </span>
            <button id="scanAddBtn" type="button" class="entry-add"><i class="bi bi-plus-circle-fill"></i> Add</button>
            <span class="entry-hint"><kbd>Enter</kbd> adds item &nbsp;&middot;&nbsp; <kbd>F3</kbd> scan &nbsp;&middot;&nbsp; <kbd>F2</kbd> search</span>
        </div>

        <!-- Blue function-key strip -->
        <div class="pos-fkeys">
            <button type="button" class="pos-fkey" data-fk="search"><kbd>F2</kbd>Search</button>
            <button type="button" class="pos-fkey" data-fk="scan"><kbd>F3</kbd>Scan</button>
            <button type="button" class="pos-fkey fkey-cancel" data-fk="cancel"><kbd>F4</kbd>Cancel</button>
            <button type="button" class="pos-fkey fkey-tender" data-fk="tender"><kbd>F5</kbd>Tender</button>
            <button type="button" class="pos-fkey" data-fk="discount"><kbd>F6</kbd>Discount</button>
            <button type="button" class="pos-fkey" data-fk="customer"><kbd>F7</kbd>Customer</button>
            <button type="button" class="pos-fkey" data-fk="clearsearch"><kbd>F10</kbd>Clear Search</button>
        </div>

        <!-- Status bar -->
        <div class="pos-status">
            <span>Operation: <b>Sale</b></span>
            <span>Terminal: <b>T-01</b></span>
            <span>Shift: <b>Day</b></span>
            <span>Cashier: <b>{{ auth()->user()->name ?? 'Admin' }}</b></span>
            <span>Bill Mode: <b>Auto</b></span>
            <span>Items: <b id="statusItems">0</b></span>
            <span class="st-right">Mode: Online</span>
        </div>
    </div>

    <!-- Dupatta choice popup: shown when a product with a dupatta is selected -->
    <div class="modal fade" id="dupattaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content pos-modal">
                <div class="pos-modal-head">
                    <h6 class="mb-0"><i class="bi bi-scissors me-2"></i> Dupatta option</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="pos-modal-body text-center">
                    <div class="pos-modal-pname" id="dupProductName">Product name</div>
                    <div class="text-muted mb-3" style="font-size:.82rem;" id="dupProductMeta"></div>

                    <div class="dup-choices">
                        <button type="button" class="dup-choice dup-with" id="dupWithBtn">
                            <i class="bi bi-check-circle-fill d-block mb-1"></i>
                            <span class="dup-label">With Dupatta</span>
                            <span class="dup-price" id="dupWithPrice">&#8377;0.00</span>
                            <span class="dup-note">Original price</span>
                        </button>
                        <button type="button" class="dup-choice dup-without" id="dupWithoutBtn">
                            <i class="bi bi-scissors d-block mb-1"></i>
                            <span class="dup-label">Without Dupatta</span>
                            <span class="dup-price" id="dupWithoutPrice">&#8377;0.00</span>
                            <span class="dup-note" id="dupSaveNote">Save &#8377;350</span>
                        </button>
                    </div>
                </div>
                <div class="pos-modal-foot">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order confirmation popup: review every item before the order is finally completed -->
    <div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:640px;">
            <div class="modal-content pos-modal">
                <div class="pos-modal-head">
                    <h6 class="mb-0"><i class="bi bi-clipboard-check me-2"></i> Confirm your order</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="pos-modal-body">
                    <p class="text-muted mb-3" style="font-size:.85rem;">
                        Please check all products below. Click <b>Confirm &amp; Complete</b> to finish this order.
                    </p>

                    <div class="table-responsive confirm-table-wrap">
                        <table class="table confirm-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:34px;">#</th>
                                    <th>Product</th>
                                    <th class="text-center" style="width:70px;">Qty</th>
                                    <th class="text-end" style="width:110px;">Rate</th>
                                    <th class="text-end" style="width:120px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="confirmItemsBody"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end text-muted" style="font-size:.82rem;">Subtotal</td>
                                    <td class="text-end" id="confirmSubtotal">&#8377;0.00</td>
                                </tr>
                                <tr id="confirmDiscountRow" style="display:none;">
                                    <td colspan="4" class="text-end" style="font-size:.82rem; color:#0b7a53; font-weight:700;">
                                        <i class="bi bi-ticket-perforated-fill me-1"></i>Discount <span id="confirmDiscountPct"></span>
                                    </td>
                                    <td class="text-end fw-bold" style="color:#0b7a53;" id="confirmDiscount">&#8377;0.00</td>
                                </tr>
                                <tr class="confirm-grand">
                                    <td colspan="4" class="text-end">Total Pay</td>
                                    <td class="text-end" id="confirmTotal">&#8377;0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="confirm-meta" id="confirmMeta"></div>
                </div>
                <div class="pos-modal-foot">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="bi bi-arrow-left me-1"></i> Back to cart</button>
                    <button type="button" class="btn confirm-ok" id="confirmOrderBtn"><i class="bi bi-check-circle-fill me-1"></i> Confirm &amp; Complete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Offline Dress Purchase Bill Copy Modal for POS -->
    @if ($lastOrder)
        @php
            $company = config('invoice.company');
            $invoicePrefix = config('invoice.invoice.prefix', 'ZYRA/24-25/');
            $formattedInvoiceNo = $invoicePrefix . str_pad($lastOrder->id, 6, '0', STR_PAD_LEFT);
            $orderDate = $lastOrder->created_at ? $lastOrder->created_at->format('d/m/Y') : date('d/m/Y');

            $custName = $lastOrder->customer_name ?: '—';
            $custPhone = $lastOrder->customer_mobile ?: '—';

            $totalMrp = 0;
            $totalDiscount = 0;
            foreach ($lastOrder->items as $item) {
                $mrp = $item->product?->mrp ?? ($item->price + 200);
                $lineMrp = $mrp * $item->qty;
                $totalMrp += $lineMrp;
                $totalDiscount += ($lineMrp - $item->total);
            }
            // Include the order-level discount applied at checkout, so the
            // receipt's Discount line always reconciles with the final total.
            $totalDiscount += (float) ($lastOrder->discount_amount ?? 0);
            if ($totalMrp < $lastOrder->total) {
                $totalMrp = $lastOrder->total;
                $totalDiscount = 0;
            }

            if (!function_exists('posNumberToWordsIndian')) {
                function posNumberToWordsIndian($num) {
                    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
                        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
                    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

                    $num = round($num, 2);
                    $rupees = floor($num);
                    $paise = round(($num - $rupees) * 100);

                    if ($rupees == 0) return 'Zero Rupees';

                    $words = [];
                    if ($rupees >= 10000000) {
                        $crore = floor($rupees / 10000000);
                        $rupees %= 10000000;
                        $words[] = ($crore < 20 ? $ones[$crore] : $tens[floor($crore/10)] . ' ' . $ones[$crore%10]) . ' Crore';
                    }
                    if ($rupees >= 100000) {
                        $lakh = floor($rupees / 100000);
                        $rupees %= 100000;
                        $words[] = ($lakh < 20 ? $ones[$lakh] : $tens[floor($lakh/10)] . ' ' . $ones[$lakh%10]) . ' Lakh';
                    }
                    if ($rupees >= 1000) {
                        $thousand = floor($rupees / 1000);
                        $rupees %= 1000;
                        $words[] = ($thousand < 20 ? $ones[$thousand] : $tens[floor($thousand/10)] . ' ' . $ones[$thousand%10]) . ' Thousand';
                    }
                    if ($rupees >= 100) {
                        $hundred = floor($rupees / 100);
                        $rupees %= 100;
                        $words[] = $ones[$hundred] . ' Hundred';
                    }
                    if ($rupees > 0) {
                        $words[] = ($rupees < 20 ? $ones[$rupees] : $tens[floor($rupees/10)] . ' ' . $ones[$rupees%10]);
                    }

                    $res = implode(' ', array_filter($words)) . ' Rupees';
                    if ($paise > 0) {
                        $res .= ' and ' . ($paise < 20 ? $ones[$paise] : $tens[floor($paise/10)] . ' ' . $ones[$paise%10]) . ' Paise';
                    }
                    return $res;
                }
            }

            $amountWords = posNumberToWordsIndian($lastOrder->total);
        @endphp

        <div class="modal fade" id="receiptModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" style="max-width:340px;">
                <div class="modal-content shadow-lg border-0" style="border-radius:10px;">
                    <div class="modal-header border-0 pb-1 no-print">
                        <h6 class="modal-title fw-bold text-danger"><i class="bi bi-check-circle-fill me-1"></i> Order Completed</h6>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm fw-bold text-white px-3" onclick="window.print()" style="background:#1554d1;border-color:#1554d1;">
                                <i class="bi bi-printer me-1"></i> Print Bill
                            </button>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                    </div>
                    <div class="modal-body pt-0 pb-3 px-3 bg-white" style="border-bottom-left-radius:14px;border-bottom-right-radius:14px;">
                        <div class="thermal-receipt">
                            <!-- Store Header -->
                            <div class="th-store">{{ $company['name'] }}</div>
                            <div class="th-muted">{{ $company['tagline'] }}</div>
                            <div class="th-muted">{{ $company['address_line1'] }}, {{ $company['address_line2'] }}</div>
                            <div class="th-muted">{{ $company['address_line3'] }}</div>
                            <div class="th-muted">Ph : {{ $company['phone'] }} | {{ $company['instagram'] }}</div>
                            <div class="th-muted">GSTIN : {{ $company['gstin'] }}</div>

                            <div class="th-dash"></div>

                            <!-- Bill Meta -->
                            <table class="th-meta">
                                <tr><td>Bill No.</td><td>: {{ $formattedInvoiceNo }}</td></tr>
                                <tr><td>Date</td><td>: {{ $lastOrder->created_at ? $lastOrder->created_at->format('d/m/Y h:i A') : $orderDate }}</td></tr>
                                <tr><td>Order ID</td><td>: {{ $lastOrder->order_id }}</td></tr>
                                <tr><td>Customer</td><td>: {{ $custName }}</td></tr>
                                <tr><td>Mobile</td><td>: {{ $custPhone }}</td></tr>
                                <tr><td>Payment</td><td>: {{ $lastOrder->payment_mode ?? 'Counter Cash' }}</td></tr>
                                <tr><td>Sales Exe</td><td>: {{ $company['sales_exec'] }}</td></tr>
                            </table>

                            <div class="th-dash"></div>

                            <!-- Items -->
                            <div class="th-row" style="font-weight:800;border-bottom:1px solid #191114;padding-bottom:.15rem;">
                                <span>ITEM</span>
                                <span style="white-space:nowrap;">QTY x RATE &nbsp;&nbsp; AMOUNT</span>
                            </div>
                            @forelse ($lastOrder->items as $item)
                                <div class="th-item-name">{{ $item->product_name }}</div>
                                @if ($item->dupatta)
                                    <div class="th-muted" style="text-align:left;">- {{ $item->dupatta === 'with' ? 'With Dupatta' : 'Without Dupatta' }}</div>
                                @endif
                                <div class="th-row">
                                    <span>{{ $item->qty }} x &#8377;{{ number_format($item->price, 2) }}</span>
                                    <span>&#8377;{{ number_format($item->total, 2) }}</span>
                                </div>
                            @empty
                                <div class="th-muted">No items found in this order.</div>
                            @endforelse

                            <div class="th-dash"></div>

                            <!-- Totals -->
                            <div class="th-totals">
                                <div class="th-subrow"><span>Total MRP</span><span>&#8377;{{ number_format($totalMrp, 2) }}</span></div>
                                <div class="th-subrow"><span>Discount</span><span>- &#8377;{{ number_format($totalDiscount, 2) }}</span></div>
                                <div class="th-total-row"><span>TOTAL</span><span>&#8377;{{ number_format($lastOrder->total, 2) }}</span></div>
                            </div>

                            <div class="th-muted" style="margin-top:.4rem;font-style:italic;">({{ $amountWords }} Only)</div>

                            <div class="th-dash"></div>

                            <!-- Footer -->
                            <div class="th-muted">{{ config('invoice.invoice.footer_note') }} Exchange within 7 days with original tags.</div>
                            <div class="th-thanks" style="margin-top:.45rem;">THANK YOU ! VISIT AGAIN</div>
                            <div class="th-muted">{{ $company['website'] }} | {{ $company['email'] }}</div>
                            <div class="th-tear" style="margin:.5rem 0 0;">x x x x x x x x x x x</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // POS Cart Logic
        let cart = [];
        const cartItemsWrap = document.getElementById('cartItemsWrap');
        const subtotalVal = document.getElementById('subtotalVal');
        const grandTotalVal = document.getElementById('grandTotalVal');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const cartJsonInput = document.getElementById('cartJsonInput');

        const DUPATTA_DISCOUNT = 350;
        const discountPercentInput = document.getElementById('discountPercentInput');
        const discountPercentHidden = document.getElementById('discountPercentInputHidden');
        const savingsRibbon = document.getElementById('savingsRibbon');
        const savingsAmt = document.getElementById('savingsAmt');
        const grandTotalRow = document.getElementById('grandTotalRow');
        const strikeTotal = document.getElementById('strikeTotal');
        const discPresets = document.getElementById('discPresets');
        const discClearBtn = document.getElementById('discClearBtn');
        const totalItemsVal = document.getElementById('totalItemsVal');
        const totalQtyVal = document.getElementById('totalQtyVal');
        const discountSummaryVal = document.getElementById('discountSummaryVal');
        const statusItems = document.getElementById('statusItems');
        const posEcho = document.getElementById('posEcho');

        let lastTotalShown = 0;   // for the count-up animation
        let countUpRAF = null;
        let countUpFallback = null;

        function getDiscountPercent() {
            let pct = parseFloat(discountPercentInput.value);
            if (isNaN(pct) || pct < 0) pct = 0;
            if (pct > 100) pct = 100;
            return pct;
        }

        // Apply the discount percentage to a subtotal, returns {discount, total}.
        function applyDiscount(subtotal) {
            const pct = getDiscountPercent();
            const discount = Math.round(subtotal * pct) / 100;
            return { pct: pct, discount: discount, total: Math.max(subtotal - discount, 0) };
        }

        function currentCartTotal() {
            return cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        }

        // Smoothly roll the Total Pay figure from its old value to the new one.
        function animateTotal(to) {
            if (countUpRAF) cancelAnimationFrame(countUpRAF);
            clearTimeout(countUpFallback);
            const from = lastTotalShown;
            if (Math.abs(to - from) < 0.01) {
                grandTotalVal.textContent = '₹' + to.toFixed(2);
                lastTotalShown = to;
                return;
            }
            const start = performance.now();
            const dur = 380;
            const step = (now) => {
                const t = Math.min((now - start) / dur, 1);
                const eased = 1 - Math.pow(1 - t, 3); // easeOutCubic
                const val = from + (to - from) * eased;
                grandTotalVal.textContent = '₹' + val.toFixed(2);
                if (t < 1) {
                    countUpRAF = requestAnimationFrame(step);
                } else {
                    grandTotalVal.textContent = '₹' + to.toFixed(2);
                    lastTotalShown = to;
                    countUpRAF = null;
                }
            };
            countUpRAF = requestAnimationFrame(step);

            // Fallback: rAF may be throttled/paused (background tab, non-compositing
            // window). Guarantee the total still lands on the final value.
            clearTimeout(countUpFallback);
            countUpFallback = setTimeout(() => {
                grandTotalVal.textContent = '₹' + to.toFixed(2);
                lastTotalShown = to;
                countUpRAF = null;
            }, dur + 60);
        }

        // Keep preset chips + the ticket UI in sync with the current percentage.
        function syncDiscountUI(pct, discount, total, subtotal) {
            // highlight the matching preset chip (exact integer match only)
            discPresets.querySelectorAll('.disc-chip').forEach(chip => {
                chip.classList.toggle('active', parseFloat(chip.dataset.pct) === pct);
            });

            const active = pct > 0 && discount > 0;
            savingsRibbon.classList.toggle('show', active);
            grandTotalRow.classList.toggle('discounted', active);
            strikeTotal.style.display = active ? '' : 'none';
            if (active) {
                savingsAmt.textContent = '₹' + discount.toFixed(2);
                strikeTotal.textContent = '₹' + subtotal.toFixed(2);
                // little pulse on the total whenever the discount changes
                grandTotalVal.classList.remove('pulse');
                void grandTotalVal.offsetWidth; // reflow to restart the animation
                grandTotalVal.classList.add('pulse');
            }
        }

        // Keep the right sidebar counters and every row's Disc/Net in step
        // with the current order-level discount percentage.
        function updateSideTotals(subtotal, pct) {
            const units = cart.reduce((s, i) => s + i.qty, 0);
            const discount = Math.round(subtotal * pct) / 100;

            if (totalItemsVal) totalItemsVal.textContent = cart.length;
            if (totalQtyVal) totalQtyVal.textContent = units;
            if (discountSummaryVal) {
                discountSummaryVal.textContent = (discount > 0 ? '− ' : '') + '₹' + discount.toFixed(2);
            }
            if (statusItems) statusItems.textContent = cart.length + ' lines · ' + units + ' units';

            cartItemsWrap.querySelectorAll('tr[data-row]').forEach(tr => {
                const netEl = tr.querySelector('.col-net');
                const discEl = tr.querySelector('.col-disc');
                const amtEl = tr.querySelector('.col-amt');
                if (!netEl) return;
                const base = parseFloat(netEl.dataset.base || '0');
                const amt = amtEl ? parseFloat(amtEl.dataset.amt || '0') : 0;
                const net = base * (1 - pct / 100);
                const disc = Math.max(amt - net, 0);
                netEl.textContent = '₹' + net.toFixed(2);
                if (discEl) discEl.textContent = (disc > 0 ? '− ' : '') + '₹' + disc.toFixed(2);
            });
        }

        function refreshTotals() {
            const subtotal = currentCartTotal();
            const d = applyDiscount(subtotal);
            subtotalVal.textContent = '₹' + subtotal.toFixed(2);
            discountPercentHidden.value = d.pct;
            animateTotal(d.total);
            syncDiscountUI(d.pct, d.discount, d.total, subtotal);
            updateSideTotals(subtotal, d.pct);
            return d;
        }

        // Preset chips: tap to set (tap the active one again to clear).
        discPresets.querySelectorAll('.disc-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                const pct = parseFloat(chip.dataset.pct);
                const already = getDiscountPercent() === pct;
                discountPercentInput.value = already ? 0 : pct;
                refreshTotals();
            });
        });

        // Clear button: reset the discount back to zero.
        discClearBtn.addEventListener('click', () => {
            discountPercentInput.value = 0;
            refreshTotals();
        });

        discountPercentInput.addEventListener('input', () => {
            // Clamp as the user types and refresh the totals live.
            let pct = parseFloat(discountPercentInput.value);
            if (!isNaN(pct) && pct > 100) discountPercentInput.value = 100;
            if (!isNaN(pct) && pct < 0) discountPercentInput.value = 0;
            refreshTotals();
        });
        discountPercentInput.addEventListener('blur', () => {
            const pct = getDiscountPercent();
            discountPercentInput.value = pct ? pct : 0;
            refreshTotals();
        });

        function dupattaPrice(basePrice, dupatta, discount) {
            if (dupatta !== 'without') return basePrice;
            const cut = (discount === undefined || discount === null || isNaN(discount))
                ? DUPATTA_DISCOUNT : discount;
            return Math.max(basePrice - cut, 0);
        }

        // Build the classic bill-grid table shell (header + body rows).
        function billTable(rowsHtml) {
            return `
                <table class="pos-bill-table">
                    <thead>
                        <tr>
                            <th class="col-no">S.NO</th>
                            <th class="col-desc">Description</th>
                            <th class="col-mrp">MRP</th>
                            <th class="col-qty">Qty</th>
                            <th class="col-price">Price</th>
                            <th class="col-amt">Amount</th>
                            <th class="col-disc">Disc</th>
                            <th class="col-net">Net Amount</th>
                            <th class="col-x"></th>
                        </tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                </table>`;
        }

        function renderCart() {
            if (cart.length === 0) {
                cartItemsWrap.innerHTML = billTable(`
                    <tr>
                        <td colspan="9" class="pos-empty-cell">
                            <i class="bi bi-cart-x"></i>
                            Cart is empty &mdash; scan a barcode or tap a product below to add items.
                        </td>
                    </tr>`);
                subtotalVal.textContent = '₹0.00';
                animateTotal(0);
                discountPercentHidden.value = getDiscountPercent();
                syncDiscountUI(getDiscountPercent(), 0, 0, 0);
                checkoutBtn.disabled = true;
                cartJsonInput.value = '';
                updateSideTotals(0, getDiscountPercent());
                if (posEcho) {
                    posEcho.innerHTML = '<span class="echo-empty"><i class="bi bi-upc-scan me-1"></i>Ready &mdash; scan a barcode or tap a product to start billing</span>';
                }
                return;
            }

            let html = '';
            let total = 0;

            cart.forEach((item, index) => {
                const lineTotal = item.price * item.qty;
                total += lineTotal;

                const mrp = (item.mrp && item.mrp > 0) ? item.mrp : (item.base_price || item.price);
                const lineMrp = mrp * item.qty;

                let flag = '';
                if (item.dupatta === 'with') {
                    flag = '<span class="ci-flag with">With Dupatta</span>';
                } else if (item.dupatta === 'without') {
                    flag = '<span class="ci-flag without">Without Dupatta &middot; &minus;&#8377;' + (item.discount ?? DUPATTA_DISCOUNT) + '</span>';
                }

                html += `
                    <tr data-row="${index}">
                        <td class="col-no">${index + 1}</td>
                        <td class="col-desc">
                            <div class="ci-name">${item.name}</div>
                            ${flag}
                        </td>
                        <td class="col-mrp">₹${mrp.toFixed(2)}</td>
                        <td class="col-qty">
                            <div class="ci-qty-ctrl">
                                <button type="button" class="ci-qty-btn" onclick="updateQty(${index}, -1)">-</button>
                                <input type="text" class="ci-qty-val" value="${item.qty}" readonly>
                                <button type="button" class="ci-qty-btn" onclick="updateQty(${index}, 1)">+</button>
                            </div>
                        </td>
                        <td class="col-price">₹${item.price.toFixed(2)}</td>
                        <td class="col-amt" data-amt="${lineMrp}">₹${lineMrp.toFixed(2)}</td>
                        <td class="col-disc" data-disc>₹0.00</td>
                        <td class="col-net" data-base="${lineTotal}">₹${lineTotal.toFixed(2)}</td>
                        <td class="col-x">
                            <button type="button" class="row-remove" onclick="removeItem(${index})" title="Remove item"><i class="bi bi-x-circle-fill"></i></button>
                        </td>
                    </tr>`;
            });

            cartItemsWrap.innerHTML = billTable(html);
            refreshTotals();
            checkoutBtn.disabled = false;
            cartJsonInput.value = JSON.stringify(cart);
        }

        // Show the last-added line in the dark top bar (classic POS echo line).
        function updateEcho(item) {
            if (!posEcho || !item) return;
            const mrp = (item.mrp && item.mrp > 0) ? item.mrp : (item.base_price || item.price);
            const lineTotal = item.price * item.qty;
            posEcho.innerHTML =
                `<span class="echo-name">${item.name}</span>` +
                `<span class="echo-mrp">MRP: ₹${mrp.toFixed(2)}</span>` +
                `<span>${item.qty} X ${item.price.toFixed(2)} =</span>` +
                `<span class="echo-eq">₹${lineTotal.toFixed(2)}</span>`;
        }

        function addToCart(product, qty, dupatta) {
            qty = qty || 1;
            dupatta = dupatta || null;
            const key = product.id + '|' + (dupatta || '');
            const price = dupattaPrice(product.price, dupatta, product.discount);

            const existing = cart.find(i => i.key === key);
            if (existing) {
                if (existing.qty + qty > product.stock) {
                    alert('Cannot add more units than available stock (' + product.stock + ').');
                    return;
                }
                existing.qty += qty;
            } else {
                if (qty > product.stock) {
                    alert('Cannot add more units than available stock (' + product.stock + ').');
                    return;
                }
                cart.push({
                    key: key,
                    id: product.id,
                    name: product.name,
                    base_price: product.price,
                    mrp: (product.mrp && product.mrp > 0) ? product.mrp : product.price,
                    price: price,
                    discount: product.discount === undefined ? DUPATTA_DISCOUNT : product.discount,
                    stock: product.stock,
                    qty: qty,
                    dupatta: dupatta
                });
            }
            renderCart();
            updateEcho(cart.find(i => i.key === key));
        }

        // --- Dupatta choice popup -------------------------------------------------
        let pendingSelection = null;
        let dupattaModalInstance = null;
        const dupattaModalEl = document.getElementById('dupattaModal');

        function getDupattaModal() {
            if (!dupattaModalInstance) {
                dupattaModalInstance = new bootstrap.Modal(dupattaModalEl);
            }
            return dupattaModalInstance;
        }

        function requestAddToCart(product, qty) {
            qty = qty || 1;
            if (product.stock <= 0) {
                alert('Item is out of stock.');
                return;
            }
            if (!product.hasDupatta) {
                addToCart(product, qty, null);
                return;
            }

            pendingSelection = { product: product, qty: qty };
            document.getElementById('dupProductName').textContent = product.name;
            document.getElementById('dupProductMeta').textContent =
                (product.meta ? product.meta + ' · ' : '') + 'Qty ' + qty + ' · ' + product.stock + ' in stock';
            document.getElementById('dupWithPrice').textContent = '₹' + product.price.toFixed(2);
            const cut = product.discount === undefined ? DUPATTA_DISCOUNT : product.discount;
            document.getElementById('dupWithoutPrice').textContent =
                '₹' + dupattaPrice(product.price, 'without', cut).toFixed(2);
            document.getElementById('dupSaveNote').innerHTML = 'Save &#8377;' + cut;
            getDupattaModal().show();
        }

        function chooseDupatta(choice) {
            if (!pendingSelection) return;
            const { product, qty } = pendingSelection;
            pendingSelection = null;
            getDupattaModal().hide();
            addToCart(product, qty, choice);
        }

        document.getElementById('dupWithBtn').addEventListener('click', () => chooseDupatta('with'));
        document.getElementById('dupWithoutBtn').addEventListener('click', () => chooseDupatta('without'));
        dupattaModalEl.addEventListener('hidden.bs.modal', () => { pendingSelection = null; });

        // --- Order confirmation popup --------------------------------------------
        const checkoutForm = document.getElementById('checkoutForm');
        const confirmOrderModalEl = document.getElementById('confirmOrderModal');
        let confirmModalInstance = null;
        let orderConfirmed = false;

        function getConfirmModal() {
            if (!confirmModalInstance) {
                confirmModalInstance = new bootstrap.Modal(confirmOrderModalEl);
            }
            return confirmModalInstance;
        }

        function buildConfirmModal() {
            const body = document.getElementById('confirmItemsBody');
            let html = '';
            let total = 0;

            cart.forEach((item, i) => {
                const lineTotal = item.price * item.qty;
                total += lineTotal;

                let dup = '';
                if (item.dupatta === 'with') {
                    dup = '<div class="ci-flag with">With Dupatta</div>';
                } else if (item.dupatta === 'without') {
                    dup = '<div class="ci-flag without">Without Dupatta</div>';
                }

                html += `
                    <tr>
                        <td class="text-muted">${i + 1}</td>
                        <td><div class="cf-name">${item.name}</div>${dup}</td>
                        <td class="text-center">${item.qty}</td>
                        <td class="text-end">₹${item.price.toFixed(2)}</td>
                        <td class="text-end fw-bold">₹${lineTotal.toFixed(2)}</td>
                    </tr>`;
            });

            body.innerHTML = html;
            const d = applyDiscount(total);
            document.getElementById('confirmSubtotal').textContent = '₹' + total.toFixed(2);
            const discountRow = document.getElementById('confirmDiscountRow');
            if (d.pct > 0 && d.discount > 0) {
                discountRow.style.display = '';
                document.getElementById('confirmDiscountPct').textContent = '(' + d.pct + '%)';
                document.getElementById('confirmDiscount').textContent = '− ₹' + d.discount.toFixed(2);
            } else {
                discountRow.style.display = 'none';
            }
            document.getElementById('confirmTotal').textContent = '₹' + d.total.toFixed(2);

            const custName = (checkoutForm.customer_name.value || '').trim();
            const custMobile = (checkoutForm.customer_mobile.value || '').trim();
            const payment = checkoutForm.payment_mode.value;
            const units = cart.reduce((s, i) => s + i.qty, 0);
            const totalCell = document.getElementById('confirmTotal');
            totalCell.style.color = (d.pct > 0 && d.discount > 0) ? '#0b7a53' : '';

            document.getElementById('confirmMeta').innerHTML = `
                <span><b>${units}</b> unit(s)</span>
                <span>Payment: <b>${payment}</b></span>
                ${d.pct > 0 && d.discount > 0 ? `<span style="color:#0b7a53;font-weight:700;"><i class="bi bi-star-fill me-1"></i>You save <b>₹${d.discount.toFixed(2)}</b> (${d.pct}%)</span>` : ''}
                ${custName ? `<span>Customer: <b>${custName}</b></span>` : ''}
                ${custMobile ? `<span>Mobile: <b>${custMobile}</b></span>` : ''}`;
        }

        checkoutForm.addEventListener('submit', (e) => {
            if (orderConfirmed) return;
            e.preventDefault();
            if (cart.length === 0) return;
            buildConfirmModal();
            getConfirmModal().show();
        });

        document.getElementById('confirmOrderBtn').addEventListener('click', () => {
            getConfirmModal().hide();
            orderConfirmed = true;
            checkoutForm.submit();
        });

        window.updateQty = function(index, delta) {
            if (!cart[index]) return;
            const item = cart[index];
            const newQty = item.qty + delta;
            if (newQty <= 0) {
                cart.splice(index, 1);
            } else if (newQty > item.stock) {
                alert('Stock limit reached (' + item.stock + ').');
            } else {
                item.qty = newQty;
            }
            renderCart();
        };

        window.removeItem = function(index) {
            cart.splice(index, 1);
            renderCart();
        };

        document.getElementById('clearCartBtn').addEventListener('click', () => {
            cart = [];
            renderCart();
        });

        // Click Product Card to Add to Cart
        function readCard(card) {
            return {
                id: parseInt(card.dataset.id),
                name: card.dataset.name,
                price: parseFloat(card.dataset.price),
                mrp: parseFloat(card.dataset.mrp || '0'),
                stock: parseInt(card.dataset.stock),
                hasDupatta: card.dataset.dupatta === '1',
                discount: parseFloat(card.dataset.dupattaDiscount || '350'),
                meta: [card.dataset.brand, card.dataset.type].filter(Boolean).join(' · ')
            };
        }

        document.querySelectorAll('.pos-card').forEach(card => {
            card.addEventListener('click', () => {
                if (parseInt(card.dataset.stock) <= 0) return;
                requestAddToCart(readCard(card), 1);
            });
        });

        // Barcode Scanner & Search Filters
        const barcodeInput = document.getElementById('barcodeInput');
        const scanQtyInput = document.getElementById('scanQty');

        function processBarcodeScan() {
            const code = barcodeInput.value.trim().toLowerCase();
            if (!code) return;
            const qty = parseInt(scanQtyInput.value) || 1;

            const cards = Array.from(document.querySelectorAll('.pos-card'));
            let match = cards.find(c => c.dataset.barcode.toLowerCase() === code);

            if (!match && code.length >= 4) {
                const last4 = code.slice(-4);
                match = cards.find(c => c.dataset.last4 === last4);
            }

            if (match) {
                const stock = parseInt(match.dataset.stock);
                if (stock <= 0) {
                    alert('Item is out of stock.');
                } else {
                    requestAddToCart(readCard(match), qty);
                    barcodeInput.value = '';
                    filterProducts();
                }
            } else {
                alert('Product not found for scanned barcode.');
            }
        }

        document.getElementById('scanAddBtn').addEventListener('click', processBarcodeScan);
        barcodeInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                processBarcodeScan();
            }
        });

        // Catalog Search & Category Filter
        const catalogSearch = document.getElementById('catalogSearch');
        const categoryPills = document.querySelectorAll('.category-pill');
        let selectedCategory = 'ALL';

        function filterProducts() {
            const q = catalogSearch.value.trim().toLowerCase();
            const bq = barcodeInput.value.trim().toLowerCase();
            const searchKeyword = q || bq;

            let visibleCount = 0;
            document.querySelectorAll('.pos-card').forEach(card => {
                const matchCat = selectedCategory === 'ALL' || card.dataset.type === selectedCategory;
                const matchSearch = !searchKeyword || card.dataset.search.includes(searchKeyword);
                const show = matchCat && matchSearch;
                card.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            const noMatchMsg = document.getElementById('noMatchMsg');
            if (noMatchMsg) {
                noMatchMsg.classList.toggle('d-none', visibleCount > 0);
            }
        }

        catalogSearch.addEventListener('input', filterProducts);
        barcodeInput.addEventListener('input', filterProducts);

        categoryPills.forEach(pill => {
            pill.addEventListener('click', () => {
                categoryPills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                selectedCategory = pill.dataset.cat;
                filterProducts();
            });
        });

        // --- Row selection highlight (classic POS blue selected row) ---
        cartItemsWrap.addEventListener('click', (e) => {
            const tr = e.target.closest('tr[data-row]');
            if (!tr) return;
            cartItemsWrap.querySelectorAll('tr.selected').forEach(r => r.classList.remove('selected'));
            tr.classList.add('selected');
        });

        // --- Function keys: on-screen blue strip + real F-key shortcuts ---
        function posAction(action) {
            switch (action) {
                case 'search':
                    catalogSearch.focus();
                    catalogSearch.select();
                    break;
                case 'scan':
                    barcodeInput.focus();
                    barcodeInput.select();
                    break;
                case 'cancel':
                    if (cart.length === 0) return;
                    if (confirm('Clear the current bill?')) {
                        cart = [];
                        renderCart();
                    }
                    break;
                case 'tender':
                    if (cart.length === 0) { barcodeInput.focus(); return; }
                    checkoutForm.requestSubmit();
                    break;
                case 'discount':
                    discountPercentInput.focus();
                    discountPercentInput.select();
                    break;
                case 'customer':
                    checkoutForm.customer_name.focus();
                    break;
                case 'clearsearch':
                    catalogSearch.value = '';
                    barcodeInput.value = '';
                    filterProducts();
                    catalogSearch.focus();
                    break;
            }
        }

        document.querySelectorAll('.pos-fkey').forEach(btn => {
            btn.addEventListener('click', () => posAction(btn.dataset.fk));
        });

        const posFkMap = { F2: 'search', F3: 'scan', F4: 'cancel', F5: 'tender', F6: 'discount', F7: 'customer', F10: 'clearsearch' };
        document.addEventListener('keydown', (e) => {
            if (posFkMap[e.key]) {
                e.preventDefault();
                posAction(posFkMap[e.key]);
            }
        });

        // --- Live clock in the dark top bar ---
        const posClock = document.getElementById('posClock');
        function tickClock() {
            if (!posClock) return;
            const now = new Date();
            posClock.textContent =
                now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) +
                ' · ' +
                now.toLocaleTimeString('en-GB');
        }
        tickClock();
        setInterval(tickClock, 1000);

        // Auto show receipt modal if order completed
    </script>
        @if ($lastOrder)
    <script>
            const receiptModalEl = document.getElementById('receiptModal');
            if (receiptModalEl) {
                const receiptModal = new bootstrap.Modal(receiptModalEl);
                receiptModal.show();
            }
    </script>
        @endif
@endpush
