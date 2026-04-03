@extends('layouts.app')
@section('title','Categories')
@section('content')
<div class="page-header"><div><h2>🏷️ Categories</h2><p>Organise parts by brand</p></div></div>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-bold">All Categories</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary"><tr><th>Brand</th><th>Parts</th><th>Actions</th></tr></thead>
                    <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td class="fw-semibold">{{ $cat->name }}</td>
                        <td><span class="badge bg-secondary">{{ $cat->parts_count ?? 0 }}</span></td>
                        <td>
                            <button onclick="openEdit({{ $cat->id }},'{{ addslashes($cat->name) }}')" class="btn btn-sm btn-outline-secondary">✏️ Edit</button>
                            <form method="POST" action="{{ route('categories.destroy',$cat) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-secondary py-4">No categories yet</td></tr>
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
                <form id="cat-form" method="POST" action="{{ route('categories.store') }}">
                    @csrf<input type="hidden" name="_method" id="cat-method" value="POST">
                    <div class="mb-3"><label class="form-label">Brand Name *</label><input type="text" name="name" id="cat-name" class="form-control" required placeholder="e.g. Apple, Samsung"></div>
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
function openEdit(id,name){
    document.getElementById('form-title').textContent='✏️ Edit Category';
    document.getElementById('cat-submit').textContent='💾 Update';
    document.getElementById('cat-method').value='PUT';
    document.getElementById('cat-form').action='/categories/'+id;
    document.getElementById('cat-name').value=name;
}
function resetForm(){
    document.getElementById('form-title').textContent='➕ Add Category';
    document.getElementById('cat-submit').textContent='➕ Add';
    document.getElementById('cat-method').value='POST';
    document.getElementById('cat-form').action='{{ route("categories.store") }}';
    document.getElementById('cat-form').reset();
}
</script>
@endpush
