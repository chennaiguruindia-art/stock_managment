<!-- Edit Discount Modal -->
<div class="modal fade no-print" id="editDiscountModal" tabindex="-1" aria-labelledby="editDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px; border: 1px solid #dfe4ee; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.12);">
            <form method="POST" action="{{ route('invoice_update_discount') }}" id="editDiscountForm">
                @csrf
                <input type="hidden" name="order_id" id="modalOrderId" value="">

                <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1.1rem 1.3rem;">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-1" id="editDiscountModalLabel" style="font-size: 1.1rem;">
                            <i class="bi bi-percent text-warning me-1"></i> Edit Bill Discount
                        </h5>
                        <div class="text-muted" style="font-size: .8rem;" id="modalOrderSubtitle">
                            Invoice: <span class="badge bg-primary-subtle text-primary fw-bold" id="modalOrderDisplay">—</span> 
                            &middot; <span id="modalCustomerDisplay" class="fw-semibold text-secondary">—</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-3 p-md-4">
                    <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size: .82rem; border-radius: 8px;">
                        <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                        <div>This changes the discount and recalculates the grand total. <b>Stock will NOT be affected.</b></div>
                    </div>

                    <div class="p-3 rounded mb-3 border" style="background:#f8fafc;">
                        <div class="d-flex justify-content-between mb-1" style="font-size: .85rem;">
                            <span class="text-muted">Items Subtotal:</span>
                            <span class="fw-bold text-dark" id="modalSubtotalDisplay">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1" style="font-size: .85rem;">
                            <span class="text-muted">Current Discount:</span>
                            <span class="text-danger fw-semibold" id="modalCurrentDiscountDisplay">0% (₹0.00)</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mt-1" style="font-size: .9rem;">
                            <span class="fw-bold text-dark">Current Bill Total:</span>
                            <span class="fw-bold text-dark" id="modalCurrentTotalDisplay">₹0.00</span>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size: .84rem;">
                                Discount Percentage (%)
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" class="form-control fw-bold" name="discount_percent" id="modalDiscountPercent" placeholder="e.g. 30" style="font-size:1rem;">
                                <span class="input-group-text fw-bold">%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark mb-1" style="font-size: .84rem;">
                                Discount Amount (₹)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control fw-bold" name="discount_amount" id="modalDiscountAmount" placeholder="e.g. 300" style="font-size:1rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Quick Preset Buttons -->
                    <div class="mb-3">
                        <label class="form-label text-muted d-block mb-1" style="font-size: .75rem; text-transform:uppercase; letter-spacing: .05em; font-weight:600;">
                            Quick Preset Discounts:
                        </label>
                        <div class="d-flex gap-1 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 preset-discount-btn" data-pct="0" style="font-size:.76rem;">0% (No discount)</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 preset-discount-btn" data-pct="10" style="font-size:.76rem;">10%</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 preset-discount-btn" data-pct="15" style="font-size:.76rem;">15%</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 preset-discount-btn" data-pct="20" style="font-size:.76rem;">20%</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 preset-discount-btn" data-pct="25" style="font-size:.76rem;">25%</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 fw-bold preset-discount-btn" data-pct="30" style="font-size:.76rem; border-width:2px;">30%</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 preset-discount-btn" data-pct="50" style="font-size:.76rem;">50%</button>
                        </div>
                    </div>

                    <!-- Live Calculation Preview Card -->
                    <div class="card p-3 border-0" style="background:#e8f5e9; border-radius:10px;">
                        <div class="d-flex justify-content-between mb-1" style="font-size: .84rem;">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold text-dark" id="previewSubtotal">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-danger" style="font-size: .84rem;">
                            <span>Discount Deduction:</span>
                            <span class="fw-semibold" id="previewDiscountDeduction">- ₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between border-top border-success-subtle pt-2 mt-1 align-items-center">
                            <span class="fw-bold text-success-emphasis" style="font-size:.9rem;">NEW PAYABLE TOTAL:</span>
                            <span class="fw-bold text-success-emphasis" id="previewNewTotal" style="font-size: 1.25rem;">₹0.00</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light" style="border-top: 1px solid #e2e8f0; padding: .85rem 1.3rem;">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-4" style="background:#1554d1;border-color:#1554d1;">
                        <i class="bi bi-check2-circle me-1"></i> Update &amp; Save Discount
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let currentSubtotal = 0;

        function updateModalCalculations(fromField) {
            const pctInput = document.getElementById('modalDiscountPercent');
            const amtInput = document.getElementById('modalDiscountAmount');

            let pct = parseFloat(pctInput.value);
            let amt = parseFloat(amtInput.value);

            if (fromField === 'percent') {
                if (isNaN(pct) || pct < 0) pct = 0;
                if (pct > 100) pct = 100;
                amt = Math.round((currentSubtotal * pct / 100) * 100) / 100;
                amtInput.value = amt > 0 ? amt.toFixed(2) : (pct === 0 ? '0' : '');
            } else if (fromField === 'amount') {
                if (isNaN(amt) || amt < 0) amt = 0;
                if (amt > currentSubtotal) amt = currentSubtotal;
                pct = currentSubtotal > 0 ? Math.round((amt / currentSubtotal * 100) * 100) / 100 : 0;
                pctInput.value = pct > 0 ? pct : (amt === 0 ? '0' : '');
            }

            const deduction = !isNaN(amt) ? amt : 0;
            const newTotal = Math.max(0, Math.round((currentSubtotal - deduction) * 100) / 100);

            document.getElementById('previewSubtotal').textContent = '₹' + currentSubtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('previewDiscountDeduction').textContent = '- ₹' + deduction.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('previewNewTotal').textContent = '₹' + newTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.edit-discount-btn');
            if (!btn) return;

            const orderId = btn.dataset.orderId || '';
            const customer = btn.dataset.customer || 'Walk-in';
            const mobile = btn.dataset.mobile || '';
            const subtotal = parseFloat(btn.dataset.subtotal) || 0;
            const currentPct = parseFloat(btn.dataset.discountPercent) || 0;
            const currentAmt = parseFloat(btn.dataset.discountAmount) || 0;
            const currentTotal = parseFloat(btn.dataset.total) || 0;

            currentSubtotal = subtotal > 0 ? subtotal : (currentTotal + currentAmt);

            document.getElementById('modalOrderId').value = orderId;
            document.getElementById('modalOrderDisplay').textContent = orderId;
            document.getElementById('modalCustomerDisplay').textContent = customer + (mobile && mobile !== '—' ? ' (' + mobile + ')' : '');
            document.getElementById('modalSubtotalDisplay').textContent = '₹' + currentSubtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('modalCurrentDiscountDisplay').textContent = currentPct + '% (₹' + currentAmt.toFixed(2) + ')';
            document.getElementById('modalCurrentTotalDisplay').textContent = '₹' + currentTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            document.getElementById('modalDiscountPercent').value = currentPct > 0 ? currentPct : '';
            document.getElementById('modalDiscountAmount').value = currentAmt > 0 ? currentAmt.toFixed(2) : '';

            updateModalCalculations('percent');

            const modalEl = document.getElementById('editDiscountModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        });

        document.getElementById('modalDiscountPercent')?.addEventListener('input', function () {
            updateModalCalculations('percent');
        });

        document.getElementById('modalDiscountAmount')?.addEventListener('input', function () {
            updateModalCalculations('amount');
        });

        document.querySelectorAll('.preset-discount-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const pct = parseFloat(this.dataset.pct) || 0;
                const pctInput = document.getElementById('modalDiscountPercent');
                if (pctInput) {
                    pctInput.value = pct > 0 ? pct : (pct === 0 ? '0' : '');
                    updateModalCalculations('percent');
                }
            });
        });
    });
</script>
