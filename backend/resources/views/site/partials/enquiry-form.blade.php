{{--
    Ports components/marketing/enquiry-form.tsx. Required: $fields (list of
    ['name','label','type' => 'text'|'email'|'textarea', 'required' => bool]),
    $submitLabel, $action (form POST URL). Optional: $extra (hidden fields
    submitted alongside, e.g. contact's category).
--}}
@php($extra = $extra ?? [])

@if (session('submitted'))
    <p class="alert alert-success" style="max-width: 32rem;">
        Thanks — we've received your submission and will be in touch.
    </p>
@else
    <form method="POST" action="{{ $action }}" class="d-flex flex-column gap-3" style="max-width: 32rem;">
        @csrf
        @foreach ($extra as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
        @endforeach

        @foreach ($fields as $field)
            @php($required = $field['required'] ?? true)
            <label class="d-flex flex-column gap-1 small fw-medium">
                {{ $field['label'] }}
                @if (($field['type'] ?? 'text') === 'textarea')
                    <textarea name="{{ $field['name'] }}" rows="5" @if($required) required @endif
                        class="form-control fw-normal @error($field['name']) is-invalid @enderror">{{ old($field['name']) }}</textarea>
                @else
                    <input type="{{ $field['type'] ?? 'text' }}" name="{{ $field['name'] }}" @if($required) required @endif
                        value="{{ old($field['name']) }}"
                        class="form-control fw-normal @error($field['name']) is-invalid @enderror" />
                @endif
                @error($field['name'])<span class="invalid-feedback">{{ $message }}</span>@enderror
            </label>
        @endforeach

        <div>
            <button type="submit" class="btn btn-primary rounded-pill">{{ $submitLabel }}</button>
        </div>
    </form>
@endif
