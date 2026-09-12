{{--
    Ports components/ui/image-upload-field.tsx. Required props: $name (the
    form field name the hidden input submits under), $label, $context (one
    of the MediaUploadController::CONTEXTS — artists/releases/products/news).
    Optional: $value (existing URL, when editing).
--}}
@php($value = $value ?? null)
<div class="mb-3">
    <label class="form-label small fw-medium">{{ $label }}</label>
    <div class="image-upload d-flex align-items-center gap-3"
         data-upload-url="{{ route('admin.uploads') }}"
         data-context="{{ $context }}">
        <img
            src="{{ $value }}"
            alt=""
            class="image-upload__preview rounded border {{ $value ? '' : 'd-none' }}"
            style="width: 3rem; height: 3rem; object-fit: cover; border-color: var(--color-border-default) !important;"
        />
        <input type="file" accept="image/*" class="image-upload__file form-control form-control-sm" style="max-width: 220px;" />
        <span class="image-upload__status small" style="color: var(--color-text-muted);"></span>
        <input type="hidden" name="{{ $name }}" class="image-upload__hidden" value="{{ $value }}" />
    </div>
</div>
