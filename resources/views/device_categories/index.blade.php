@extends('layouts.app')
@section('title','Device Categories')
@section('content')
<div class="page-header"><div><h2>🏷️ Device Categories</h2><p>Manage categories for buying & selling</p></div></div>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-bold">{{ $categories->count() }} Categories</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary"><tr><th>Icon</th><th>Name</th><th>Deals</th><th>Actions</th></tr></thead>
                    <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td style="font-size:22px;">{{ $cat->icon }}</td>
                        <td class="fw-semibold">{{ $cat->name }}</td>
                        <td><span class="badge bg-secondary">{{ $cat->deal_items_count }}</span></td>
                        <td>
                            <button onclick="openEdit({{ $cat->id }},'{{ addslashes($cat->name) }}','{{ addslashes($cat->icon) }}')" class="btn btn-sm btn-outline-secondary">✏️ Edit</button>
                            <form method="POST" action="{{ route('device-categories.destroy',$cat) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-secondary py-4">No categories yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header fw-bold" id="form-title">➕ Add Category</div>
            <div class="card-body">
                <form id="cat-form" method="POST" action="{{ route('device-categories.store') }}">
                    @csrf<input type="hidden" name="_method" id="cat-method" value="POST">
                    <div class="mb-3"><label class="form-label">Icon (emoji)</label><input type="text" name="icon" id="cat-icon" class="form-control text-center" placeholder="📱" maxlength="10" style="font-size:24px;"></div>
                    <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" id="cat-name" class="form-control" required placeholder="e.g. Laptop, Tablet"></div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="cat-submit">➕ Add</button>
                        <button type="button" onclick="resetForm()" class="btn btn-outline-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openEdit(id,name,icon){document.getElementById('form-title').textContent='✏️ Edit';document.getElementById('cat-submit').textContent='💾 Update';document.getElementById('cat-method').value='PUT';document.getElementById('cat-form').action='/device-categories/'+id;document.getElementById('cat-name').value=name;document.getElementById('cat-icon').value=icon;}
function resetForm(){document.getElementById('form-title').textContent='➕ Add Category';document.getElementById('cat-submit').textContent='➕ Add';document.getElementById('cat-method').value='POST';document.getElementById('cat-form').action='{{ route("device-categories.store") }}';document.getElementById('cat-form').reset();}
</script>
@endpush
