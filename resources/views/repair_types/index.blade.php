@extends('layouts.app')
@section('title','Repair Types')
@section('content')
<div class="page-header"><div><h2>🔩 Repair Types</h2><p>Define types of repairs</p></div></div>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-bold">All Repair Types</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary"><tr><th>Type</th><th>Used in Repairs</th><th>Actions</th></tr></thead>
                    <tbody>
                    @forelse($repairTypes as $rt)
                    <tr>
                        <td><span style="font-size:18px;">{{ $rt->icon }}</span> <span class="fw-semibold">{{ $rt->name }}</span></td>
                        <td><span class="badge bg-secondary">{{ $rt->repairs_count ?? 0 }}</span></td>
                        <td>
                            <button onclick="openEdit({{ $rt->id }},'{{ addslashes($rt->name) }}','{{ addslashes($rt->icon) }}')" class="btn btn-sm btn-outline-secondary">✏️ Edit</button>
                            <form method="POST" action="{{ route('repair-types.destroy',$rt) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-secondary py-4">No repair types yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header fw-bold" id="form-title">➕ Add Repair Type</div>
            <div class="card-body">
                <form id="rt-form" method="POST" action="{{ route('repair-types.store') }}">
                    @csrf<input type="hidden" name="_method" id="rt-method" value="POST">
                    <div class="mb-3"><label class="form-label">Icon (emoji)</label><input type="text" name="icon" id="rt-icon" class="form-control text-center" placeholder="🔧" maxlength="5" style="font-size:22px;"></div>
                    <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" id="rt-name" class="form-control" required placeholder="e.g. Screen Replacement"></div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="rt-submit">➕ Add</button>
                        <button type="button" onclick="resetRtForm()" class="btn btn-outline-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openEdit(id,name,icon){
    document.getElementById('form-title').textContent='✏️ Edit Repair Type';
    document.getElementById('rt-submit').textContent='💾 Update';
    document.getElementById('rt-method').value='PUT';
    document.getElementById('rt-form').action='/repair-types/'+id;
    document.getElementById('rt-name').value=name;
    document.getElementById('rt-icon').value=icon;
}
function resetRtForm(){
    document.getElementById('form-title').textContent='➕ Add Repair Type';
    document.getElementById('rt-submit').textContent='➕ Add';
    document.getElementById('rt-method').value='POST';
    document.getElementById('rt-form').action='{{ route("repair-types.store") }}';
    document.getElementById('rt-form').reset();
}
</script>
@endpush