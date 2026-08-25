@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:14px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:14px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger py-2 px-3 mb-3" style="border-radius:14px;">{{ $errors->first() }}</div>
@endif
