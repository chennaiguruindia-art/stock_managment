@extends('layouts.app')

@section('page-title', 'Return product')
@section('page-subtitle', 'Search an invoice and return purchased items back to stock.')

@section('content')
    <style>
        .order-chip {
            font-family: ui-monospace, "Cascadia Code", Consolas, monospace;
            font-weight: 700;
            font-size: .78rem;
            color: var(--accent-strong, #1043a8);
            background: var(--accent-soft, #e7eefe);
            border: 1px solid #cdddfb;
            border-radius: 6px;
            padding: .2rem .5rem;
        }

        .btn-return {
            background: var(--accent, #1554d1);
            color: #fff;
            border: 0;
            border-radius: 8px;
            padding: .4rem .9rem;
            font-weight: 600;
            font-size: .85rem;
        }
        .btn-return:hover { color: #fff; background: var(--accent-strong, #1043a8); }

        .bill-head {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem 1.6rem;
            background: var(--surface-3, #f6f8fc);
            border: 1px solid var(--border, #dfe4ee);
            border-radius: 10px;
            padding: .7rem .9rem;
            margin-bottom: 1rem;
        }
        .bh-item { display: flex; flex-direction: column; gap: .1rem; }
        .bh-label {
            font-size: .64rem;
            text-transform: uppercase;
            letter-spacing: .09em;
            font-weight: 700;
            color: var(--muted, #66748b);
        }
        .bh-value { font-size: .9rem; font-weight: 700; color: var(--text, #0e1726); }

        .pick-title { font-weight: 700; font-size: .95rem; color: var(--text, #0e1726); }
        .pick-sub { font-size: .84rem; color: var(--muted, #66748b); margin-top: .15rem; }
        .pick-chip {
            display: inline-flex;
            flex-direction: column;
            gap: .1rem;
            text-decoration: none;
            background: #fff;
            border: 1px solid var(--border, #dfe4ee);
            border-radius: 10px;
            padding: .5rem .75rem;
            min-width: 150px;
            transition: border-color .15s, box-shadow .15s;
        }
        .pick-chip:hover {
            border-color: var(--accent, #1554d1);
            box-shadow: 0 2px 8px rgba(21, 84, 209, .14);
        }
        .pick-chip .pc-id {
            font-family: ui-monospace, Consolas, monospace;
            font-weight: 700;
            font-size: .82rem;
            color: var(--accent-strong, #1043a8);
        }
        .pick-chip .pc-meta { font-size: .74rem; color: var(--muted, #66748b); }
    </style>

    @include('layouts.alerts')

    @if ($notFound)
        <div class="alert alert-danger py-2 px-3 mb-3" style="border-radius:10px;">
            <i class="bi bi-search me-2"></i>No bill found for "{{ $invoice }}".
            <span class="d-block mt-1" style="font-size:.84rem;">
                Enter the bill number exactly as printed on the invoice, e.g. <strong>ORD-0001</strong>.
            </span>
        </div>
    @endif

    <div class="panel mb-4">
        <div class="panel-title">
            <i class="bi bi-arrow-return-left"></i> Return purchased items back to stock
            <span class="ms-auto text-muted fw-normal" style="font-size:.82rem;">
                @if ($order)
                    {{ $items->count() }} item(s) in bill {{ $order->order_id }}
                @else
                    Search a bill number to see its products
                @endif
            </span>
        </div>

        <form method="GET" action="{{ route('return_product') }}" class="row g-2 align-items-center mb-3">
            <div class="col-md-5">
                <input type="text" name="invoice" value="{{ $invoice }}"
                       class="search-box-input" autofocus placeholder="Bill number, e.g. ORD-0001">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-return w-100"><i class="bi bi-search me-1"></i>Show items</button>
            </div>
            <div class="col-md-5">
                @if ($invoice !== '')
                    <a href="{{ route('return_product') }}" class="text-muted" style="font-size:.85rem;">
                        <i class="bi bi-x-circle me-1"></i>Clear "{{ $invoice }}"
                    </a>
                @else
                    <span class="text-muted" style="font-size:.82rem;">Type a bill number, then click Show items.</span>
                @endif
            </div>
        </form>

        @if ($order)
            @php
                $returnedAmount = $order->returnedTotal();
                $netAmount = $order->netTotal();
                $fullyReturned = $items->count() > 0 && $items->every(fn ($i) => ((int) $i->qty - (int) $i->returned_qty) <= 0);
            @endphp

            <div class="bill-head">
                <div class="bh-item">
                    <span class="bh-label">Bill</span>
                    <span class="bh-value"><span class="order-chip">{{ $order->order_id }}</span></span>
                </div>
                <div class="bh-item">
                    <span class="bh-label">Date</span>
                    <span class="bh-value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="bh-item">
                    <span class="bh-label">Customer</span>
                    <span class="bh-value">
                        {{ $order->customer_name ?: 'Walk-in' }}
                        <span class="text-muted fw-normal">{{ $order->customer_mobile ?: '' }}</span>
                    </span>
                </div>
                <div class="bh-item">
                    <span class="bh-label">Bill total</span>
                    <span class="bh-value">₹{{ number_format($order->total, 2) }}</span>
                </div>
                @if ($returnedAmount > 0)
                    <div class="bh-item">
                        <span class="bh-label">Returned</span>
                        <span class="bh-value text-danger">− ₹{{ number_format($returnedAmount, 2) }}</span>
                    </div>
                    <div class="bh-item">
                        <span class="bh-label">Net after returns</span>
                        <span class="bh-value">₹{{ number_format($netAmount, 2) }}</span>
                    </div>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Barcode</th>
                            <th>Qty</th>
                            <th>Returned</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            @php
                                $remaining = (int) $item->qty - (int) $item->returned_qty;
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->product_name }}</div>
                                    <div class="text-muted" style="font-size:.78rem;">₹{{ number_format((float) $item->price, 2) }} each</div>
                                </td>
                                <td class="text-muted" style="font-size:.8rem; font-family:ui-monospace,Consolas,monospace;">
                                    {{ $item->barcode }}
                                </td>
                                <td>{{ $item->qty }}</td>
                                <td>
                                    @if ($remaining <= 0)
                                        <span class="badge bg-success rounded-pill"><i class="bi bi-check-lg me-1"></i>Fully returned</span>
                                    @elseif ((int) $item->returned_qty > 0)
                                        <span class="badge bg-warning text-dark rounded-pill">{{ $item->returned_qty }}/{{ $item->qty }} returned</span>
                                    @else
                                        <span class="text-muted" style="font-size:.8rem;">0/{{ $item->qty }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($remaining > 0)
                                        <button type="button" class="btn-return open-return"
                                                data-item-id="{{ $item->id }}"
                                                data-name="{{ $item->product_name }}"
                                                data-barcode="{{ $item->barcode }}"
                                                data-max="{{ $remaining }}"
                                                data-remaining="{{ $remaining }}">
                                            <i class="bi bi-arrow-return-left me-1"></i>Return
                                        </button>
                                    @else
                                        <span class="text-muted" style="font-size:.8rem;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">This bill has no products.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($fullyReturned)
                <div class="alert alert-success py-2 px-3 mb-0" style="border-radius:10px;">
                    <i class="bi bi-check-circle me-2"></i>Everything on this bill has already been returned to stock.
                </div>
            @endif
        @elseif ($notFound)
            <div class="text-center text-muted py-4 mb-0">
                <i class="bi bi-x-octagon fs-3 d-block mb-2"></i>
                No products to show for that bill number.
            </div>
        @else
            <div class="mb-3">
                <div class="pick-title"><i class="bi bi-receipt me-2"></i>Recent bills</div>
                <div class="pick-sub">Pick a bill below, or type its number above to load its products.</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @forelse ($recentOrders as $ro)
                    <a class="pick-chip" href="{{ route('return_product', ['invoice' => $ro->order_id]) }}">
                        <span class="pc-id">{{ $ro->order_id }}</span>
                        <span class="pc-meta">{{ $ro->items_count }} item(s) &middot; {{ $ro->created_at->format('d M Y') }}</span>
                    </a>
                @empty
                    <span class="text-muted" style="font-size:.85rem;">
                        No bills yet — make a sale at the <a href="{{ route('sell_pos') }}">POS terminal</a> first.
                    </span>
                @endforelse
            </div>
        @endif
    </div>

    @if ($recentReturns->isNotEmpty())
        <div class="panel">
            <div class="panel-title"><i class="bi bi-clock-history"></i> Recent return log</div>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.9rem;">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Barcode</th>
                            <th>Qty</th>
                            <th>Reason</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentReturns as $ret)
                            <tr>
                                <td><span class="order-chip" style="cursor:default;">{{ $ret->order_id }}</span></td>
                                <td>{{ $ret->customer_name ?: 'Walk-in' }}</td>
                                <td class="fw-semibold">{{ $ret->product_name }}</td>
                                <td class="text-muted" style="font-family:ui-monospace,Consolas,monospace;">{{ $ret->barcode }}</td>
                                <td>{{ $ret->quantity }}</td>
                                <td>{{ $ret->reason }}</td>
                                <td class="text-muted" style="font-size:.8rem;">{{ \Carbon\Carbon::parse($ret->returned_at)->format('d M Y, h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Return Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-arrow-return-left me-2"></i>Return item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('return_process') }}" id="returnForm">
                    @csrf
                    <input type="hidden" name="item_id" id="retItemId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="fw-bold fs-6" id="retName"></div>
                            <div class="text-muted" style="font-size:.82rem;" id="retBarcode"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted" style="font-size:.8rem;">Return quantity</label>
                            <input type="number" id="retQty" name="quantity" class="form-control" value="1" min="1" required style="border-radius:10px;">
                            <div class="text-muted mt-1" style="font-size:.78rem;" id="retMaxHint"></div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted" style="font-size:.8rem;">Reason</label>
                            <select id="retReasonSelect" class="form-select mb-2" style="border-radius:10px;">
                                <option value="">Select a reason...</option>
                                <option value="Wrong product">Wrong product</option>
                                <option value="Damaged / defective">Damaged / defective</option>
                                <option value="Size / fit issue">Size / fit issue</option>
                                <option value="Customer changed mind">Customer changed mind</option>
                                <option value="Other">Other...</option>
                            </select>
                            <textarea id="retReason" name="reason" class="form-control" rows="2" placeholder="Describe the return reason..." required style="border-radius:10px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border:0;">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-return px-4"><i class="bi bi-check-lg me-1"></i>Confirm return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Return Modal Logic
        const modalEl = document.getElementById('returnModal');
        const returnModal = modalEl ? new bootstrap.Modal(modalEl) : null;

        document.querySelectorAll('.open-return').forEach(btn => {
            btn.addEventListener('click', () => {
                const max = parseInt(btn.dataset.max);
                document.getElementById('retItemId').value = btn.dataset.itemId;
                document.getElementById('retName').textContent = btn.dataset.name;
                document.getElementById('retBarcode').textContent = 'Barcode: ' + btn.dataset.barcode;
                document.getElementById('retQty').value = 1;
                document.getElementById('retQty').max = max;
                document.getElementById('retMaxHint').textContent =
                    'Available to return: ' + btn.dataset.remaining + ' unit(s) remaining';
                document.getElementById('retReason').value = '';
                document.getElementById('retReasonSelect').value = '';
                if (returnModal) returnModal.show();
            });
        });

        const retReasonSelect = document.getElementById('retReasonSelect');
        if (retReasonSelect) {
            retReasonSelect.addEventListener('change', e => {
                document.getElementById('retReason').value = e.target.value === 'Other' ? '' : e.target.value;
            });
        }
    </script>
@endpush
