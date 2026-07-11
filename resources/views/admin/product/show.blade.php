@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none">Products</a></li>
    <li class="breadcrumb-item active">Product Details</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-box-seam me-2"></i>{{ $product->name }}</h4>
        <p class="text-muted mb-0">Product details and information</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Product Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-3 text-center">
                        @if($product->getFirstMedia('product-images'))
                            <img src="{{ $product->getFirstMediaUrl('product-images') }}" class="rounded mb-2" style="max-height: 150px; width: 100%; object-fit: cover;" alt="{{ $product->name }}">
                        @else
                            <div class="bg-light rounded d-inline-flex align-items-center justify-content-center mb-2" style="width: 150px; height: 150px;">
                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted small">Product Name</label>
                            <div class="fw-semibold">{{ $product->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted small">SKU</label>
                            <div>
                                @if($product->sku)
                                    <span class="badge bg-light text-dark">{{ $product->sku }}</span>
                                @else
                                    <span class="text-muted fst-italic">Not assigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted small">Barcode</label>
                            <div>
                                @if($product->barcode)
                                    <span class="badge bg-light text-dark">{{ $product->barcode }}</span>
                                @else
                                    <span class="text-muted fst-italic">Not assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted small">Brand</label>
                            <div>
                                @if($product->brand)
                                    <span class="badge bg-primary-subtle text-primary">
                                        <i class="bi bi-bookmark me-1"></i>{{ $product->brand->name }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">No brand</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted small">Category</label>
                            <div>
                                @if($product->category)
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="bi bi-tag me-1"></i>{{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">No category</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted small">Status</label>
                            <div>
                                @if($product->is_active)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-x-circle me-1"></i>Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($product->description)
                        <div class="col-12">
                            <label class="form-label fw-medium text-muted small">Description</label>
                            <div class="text-muted">{{ $product->description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($product->variants && $product->variants->count() > 0)
            <div class="card glass mt-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="bi bi-diagram-3 me-2"></i>Variants
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                    <tr>
                                        <td class="fw-medium">{{ $variant->name }}</td>
                                        <td>
                                            @if($variant->sku)
                                                <span class="badge bg-light text-dark">{{ $variant->sku }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($variant->price ?? 0, 2) }}</td>
                                        <td>
                                            @if($variant->is_active ?? true)
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card glass">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="card-title fw-bold mb-0">Pricing</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-medium text-muted small">Purchase Price</label>
                    <div class="fs-4 fw-bold text-success">${{ number_format($product->purchase_price, 2) }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium text-muted small">Selling Price</label>
                    <div class="fs-4 fw-bold text-primary">${{ number_format($product->selling_price, 2) }}</div>
                </div>
                <hr>
                <div>
                    <label class="form-label fw-medium text-muted small">Profit Margin</label>
                    <div class="fs-5 fw-bold {{ $product->profit_margin > 0 ? 'text-success' : 'text-danger' }}">
                        {{ $product->profit_margin }}%
                    </div>
                </div>
            </div>
        </div>

        <div class="card glass mt-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="card-title fw-bold mb-0">
                    <i class="bi bi-clock-history me-2"></i>Meta
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Created:</small>
                    <div class="small">{{ $product->created_at->format('M d, Y h:i A') }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Last Updated:</small>
                    <div class="small">{{ $product->updated_at->format('M d, Y h:i A') }}</div>
                </div>
                <div>
                    <small class="text-muted">Product ID:</small>
                    <div class="small font-monospace text-muted">{{ $product->id }}</div>
                </div>
            </div>
        </div>

        <div class="card glass mt-4">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Product
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i>Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
