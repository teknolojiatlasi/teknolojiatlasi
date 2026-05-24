<div class="mb-3">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        @if ($message->contact_is_read)
            <span class="badge bg-secondary">Okundu</span>
        @else
            <span class="badge bg-danger">Okunmadı</span>
        @endif

        @if ($message->contact_is_replied)
            <span class="badge bg-success">Yanıtlandı</span>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-md-6">
        <div class="small text-muted">Ad Soyad</div>
        <div class="fw-semibold">{{ $message->contact_full_name }}</div>
    </div>
    <div class="col-12 col-md-6">
        <div class="small text-muted">E-posta</div>
        <div class="fw-semibold">{{ $message->contact_email }}</div>
    </div>
    <div class="col-12">
        <div class="small text-muted">Konu</div>
        <div class="fw-semibold">{{ $message->contact_subject }}</div>
    </div>
    <div class="col-12">
        <div class="small text-muted">Mesaj</div>
        <div class="border rounded p-3 bg-light" style="white-space: pre-wrap">{{ $message->contact_message }}</div>
    </div>
</div>

<hr>

<h6>Yanıtla</h6>

<div class="alert alert-success d-none" data-role="form-ok"></div>
<div class="alert alert-danger d-none" data-role="form-error"></div>

<form data-action="reply" method="POST" action="{{ route('contact_admin_messages_reply', $message) }}">
    @csrf
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">Konu</label>
            <input class="form-control" name="contact_reply_subject" value="{{ old('contact_reply_subject', $message->contact_reply_subject ?: ('RE: ' . $message->contact_subject)) }}" required>
            <div class="text-danger small" data-error="contact_reply_subject"></div>
        </div>
        <div class="col-12">
            <label class="form-label">Mesaj</label>
            <textarea class="form-control" name="contact_reply_message" rows="6" required>{{ old('contact_reply_message', $message->contact_reply_message) }}</textarea>
            <div class="text-danger small" data-error="contact_reply_message"></div>
        </div>
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-success" type="submit">
                <i class="fa fa-paper-plane"></i> Yanıt Gönder
            </button>
        </div>
    </div>
</form>

