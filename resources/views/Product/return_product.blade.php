@extends('layouts.app')

@section('page-title', 'Return product')
@section('page-subtitle', 'Search an invoice and return purchased items back to stock.')

@section('content')
    <style>
        .order-chip {
            font-family: ui-monospace, "Cascadia Code", Consolas, monospace;
            font-weight: 700;
            color: #6b1f2a;
            background: #f3e2e3;
            border-radius: 8px;
            padding: .25rem .55rem;
        }

        .btn-return {
            background: linear-gradient(135deg, #6b1f2a, #a44454);
            color: #fff;
            border: 0;
            border-radius: 10px;
            padding: .4rem .9rem;
            font-weight: 600;
            font-size: .85rem;
        }
        .btn-return:hover { color: #fff; filter: brightness(1.08); }
    </style>

    @include('layouts.alerts')

    @if ($notFound)
        <div class="alert alert-danger py-2 px-3 mb-3" style="border-radius:14px;">
            <i class="bi bi-search me-2"></i>No order found for invoice "{{ $invoice }}".
        </div>
    @endif

    <div class="panel mb-4">
        <div class="panel-title">
            <i class="bi bi-arrow-return-left"></i> Return purchased items back to stock
            <span class="ms-auto text-muted fw-normal" style="font-size:.82rem;">
                {{ $items->count() }} item(s) across {{ $orders->count() }} invoice(s)
            </span>
        </div>

        <form method="GET" action="{{ route('return_product') }}" class="row g-2 align-items-center mb-3">
            <div class="col-md-5">
                <input type="text" name="invoice" value="{{ $invoice }}"
                       class="search-box-input" placeholder="Search by invoice number, e.g. ORD-0001">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-return w-100"><i class="bi bi-search me-1"></i>Search</button>
            </div>
            <div class="col-md-5">
                @if ($invoice !== '')
                    <a href="{{ route('return_product') }}" class="text-muted" style="font-size:.85rem;">
                        <i class="bi bi-x-circle me-1"></i>Showing items for {{ $invoice }} — clear search
                    </a>
                @else
                    <span class="text-muted" style="font-size:.82rem;">Enter an invoice number to filter items by order.</span>
                @endif
            </div>
        </form>

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
                            $remaining = $item->qty - (int) $item->returned_qty;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                <div class="text-muted" style="font-size:.78rem;">{{ $item->customer_name ?: 'Walk-in' }}</div>
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
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <span class="order-chip" style="cursor:default;">{{ $item->order_id }}</span>
                                        <button type="button" class="btn-return open-return"
                                                data-item-id="{{ $item->id }}"
                                                data-name="{{ $item->product_name }}"
                                                data-barcode="{{ $item->barcode }}"
                                                data-max="{{ $remaining }}"
                                                data-remaining="{{ $remaining }}">
                                            <i class="bi bi-arrow-return-left me-1"></i>Return
                                        </button>
                                    </div>
                                @else
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <span class="order-chip" style="cursor:default;">{{ $item->order_id }}</span>
                                        <span class="text-muted" style="font-size:.8rem;">—</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No items to return yet. Place an order at the <a href="{{ route('sell_pos') }}">POS terminal</a> first.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
            <div class="modal-content" style="border-radius:18px; border:0; box-shadow:0 24px 60px rgba(36,25,35,.25);">
                <div class="modal-header" style="background:linear-gradient(120deg,#2a000a,#6b1f2a); color:#fff; border-radius:18px 18px 0 0;">
                    <h5 class="modal-title"><i class="bi bi-arrow-return-left me-2"></i>Return item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
