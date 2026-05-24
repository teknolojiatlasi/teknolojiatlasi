@php
    /** @var \App\Models\User $profileUser */
    $isFollowing = (bool) ($isFollowing ?? false);
@endphp

@auth
    @if (auth()->id() !== $profileUser->id)
        <form
            method="POST"
            action="{{ $isFollowing ? route('sosial.follow.destroy', $profileUser) : route('sosial.follow.store', $profileUser) }}"
            class="d-inline-block w-100"
            data-follow-form
            data-follow-user="{{ $profileUser->id }}"
            data-following="{{ $isFollowing ? '1' : '0' }}"
            data-follow-store-url="{{ route('sosial.follow.store', $profileUser) }}"
            data-follow-destroy-url="{{ route('sosial.follow.destroy', $profileUser) }}"
        >
            @csrf
            @if ($isFollowing)
                @method('DELETE')
            @endif
            <button
                class="btn {{ $isFollowing ? 'btn-outline-danger' : 'sosial-btn-primary' }} rounded-pill px-4 w-100"
                type="submit"
                data-follow-button
            >
                {{ $isFollowing ? 'Takibi Bırak' : 'Takip Et' }}
            </button>
        </form>
    @endif
@endauth
