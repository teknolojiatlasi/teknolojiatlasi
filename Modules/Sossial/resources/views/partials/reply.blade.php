@php
    /** @var \Modules\Sossial\Models\Comment $reply */
@endphp

<div class="py-2">
    <div class="d-flex align-items-center gap-2">
        <img
            src="{{ $reply->user?->avatarUrl() }}"
            alt="{{ $reply->user->name ?? 'Kullanıcı' }}"
            class="rounded-circle flex-shrink-0"
            style="width: 29px; height: 29px; object-fit: cover;"
        >
        <div class="fw-semibold">{{ $reply->user->name ?? 'Kullanıcı' }}</div>
        <div class="text-muted small">{{ $reply->created_at?->diffForHumans() }}</div>
    </div>
    <div class="mt-1" style="white-space: pre-wrap;">{{ $reply->body }}</div>
</div>
