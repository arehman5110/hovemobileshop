@extends('layouts.app')
@section('title','Terms & Conditions')
@section('content')
<div class="page-header"><div><h2>📄 Terms & Conditions</h2><p>Manage buy/sell agreement terms</p></div></div>
<div class="row g-3">
    @foreach([['buy','📥 Buy Terms','Shown when buying devices from customers',$buyTerms],['sell','📤 Sell Terms','Shown when selling devices to customers',$sellTerms]] as [$type,$title,$desc,$terms])
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold">{{ $title }} <small class="text-secondary fw-normal ms-2" style="font-size:11px;">{{ $desc }}</small></div>
            <div class="card-body">
                @forelse($terms as $t)
                <div class="border rounded p-3 mb-3 {{ $t->is_active ? 'border-success' : '' }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold small">{{ $t->title }}</div>
                        @if($t->is_active)<span class="badge bg-success">✅ Active</span>@endif
                    </div>
                    <div class="text-secondary mb-2" style="font-size:12px;white-space:pre-wrap;max-height:120px;overflow-y:auto;">{{ $t->content }}</div>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('terms.update',$t) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="title" value="{{ $t->title }}">
                            <input type="hidden" name="content" value="{{ $t->content }}">
                            <input type="hidden" name="is_active" value="{{ $t->is_active?'0':'1' }}">
                            <button class="btn btn-sm {{ $t->is_active?'btn-outline-secondary':'btn-success' }}">{{ $t->is_active?'Deactivate':'Set Active' }}</button>
                        </form>
                        <form method="POST" action="{{ route('terms.destroy',$t) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-secondary small">No {{ $type }} terms yet.</p>
                @endforelse
                <hr>
                <form method="POST" action="{{ route('terms.store') }}">
                    @csrf<input type="hidden" name="type" value="{{ $type }}">
                    <div class="mb-2"><label class="form-label">Title</label><input type="text" name="title" class="form-control form-control-sm" required placeholder="e.g. Standard {{ ucfirst($type) }} Terms v1"></div>
                    <div class="mb-2"><label class="form-label">Content</label><textarea name="content" class="form-control form-control-sm" rows="8" required style="font-family:monospace;font-size:12px;"></textarea></div>
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="active-{{ $type }}"><label class="form-check-label small" for="active-{{ $type }}">Set as active</label></div>
                    <button class="btn btn-primary btn-sm">💾 Save {{ ucfirst($type) }} Terms</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
