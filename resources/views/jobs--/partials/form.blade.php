@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div>
        <label for="customer_name">Customer Name</label>
        <input type="text" name="customer_name" id="customer_name"
               value="{{ old('customer_name', $job->customer_name ?? '') }}"
               class="form-control">
    </div>

    <div>
        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone"
               value="{{ old('phone', $job->phone ?? '') }}"
               class="form-control">
    </div>

    <div>
        <label for="device">Device</label>
        <input type="text" name="device" id="device"
               value="{{ old('device', $job->device ?? '') }}"
               class="form-control">
    </div>

    <div>
        <label for="issue">Issue</label>
        <input type="text" name="issue" id="issue"
               value="{{ old('issue', $job->issue ?? '') }}"
               class="form-control">
    </div>

    <div>
        <label for="price">Price</label>
        <input type="number" name="price" id="price"
               value="{{ old('price', $job->price ?? '') }}"
               class="form-control">
    </div>

    <div>
        <label for="status">Status</label>
        <select name="status" id="status" class="form-control">
            @foreach(['pending', 'in_progress', 'completed', 'delivered'] as $status)
                <option value="{{ $status }}"
                    {{ old('status', $job->status ?? '') == $status ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>
    </div>

</div>

<div class="mt-4">
    <label for="notes">Notes</label>
    <textarea name="notes" id="notes" class="form-control">{{ old('notes', $job->notes ?? '') }}</textarea>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        {{ $buttonText }}
    </button>
</div>
