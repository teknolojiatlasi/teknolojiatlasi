@php
    $depth = (int) ($depth ?? 0);
    $indentRem = min($depth, 4) * 1.25;
@endphp

<div id="comment-{{ $comment->id }}" class="mt-3 js-comment" style="margin-left: {{ $indentRem }}rem;">
    <div class="d-flex gap-3">
        <div class="flex-shrink-0">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                 style="width: 44px; height: 44px; font-weight: 700;">
                {{ mb_strtoupper(mb_substr($comment->author_name, 0, 1)) }}
            </div>
        </div>
        <div class="flex-grow-1">
            <div class="bg-white border rounded-4 p-3 shadow-sm">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <div class="fw-semibold">{{ $comment->author_name }}</div>
                        <div class="text-muted small">
                            {{ optional($comment->created_at)->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}
                        </div>
                    </div>
                    @if($depth < 5)
                        <button class="btn btn-sm btn-outline-secondary"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#reply-{{ $comment->id }}"
                                aria-expanded="false"
                                aria-controls="reply-{{ $comment->id }}">
                            Yanitla
                        </button>
                    @endif
                </div>

                <div class="mt-2" style="white-space: pre-wrap;">{{ $comment->body }}</div>

                @if($depth < 5)
                    <div id="reply-{{ $comment->id }}" class="collapse mt-3">
                        <div class="alert alert-danger d-none rounded-3 mb-2" data-comment-form-error></div>
                        <div class="alert alert-success d-none rounded-3 mb-2" data-comment-form-success></div>
                        <form method="POST"
                              action="{{ route('blog.comments.store', $blog) }}#comments"
                              class="bg-light border rounded-4 p-3 blog-comment-form js-blog-comment-form"
                              data-parent-id="{{ $comment->id }}">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="d-none" aria-hidden="true">
                            @include('partials.bot-protection')

                            <div class="row g-2">
                                <div class="col-12 col-md-4">
                                    <label class="form-label small mb-1">Ad</label>
                                    <input type="text"
                                           name="author_name"
                                           class="form-control js-comment-author"
                                           maxlength="80"
                                           required
                                           placeholder="Adiniz">
                                    <div class="text-danger small mt-1" data-error="author_name"></div>
                                </div>
                                <div class="col-12 col-md-8">
                                    <label class="form-label small mb-1">Yanit</label>
                                    <div class="blog-comment-textarea-wrap">
                                        <textarea name="body"
                                                  class="form-control js-emoji-target js-comment-body blog-comment-textarea"
                                                  rows="2"
                                                  maxlength="2000"
                                                  required
                                                  placeholder="Yanitinizi yazin..."></textarea>
                                        <button type="button" class="btn btn-outline-secondary js-emoji-button blog-comment-emoji-btn" aria-label="Emoji">
                                            🙂
                                        </button>
                                    </div>
                                    <div class="border rounded-3 p-2 mt-2 bg-white d-none js-emoji-panel"></div>
                                    <div class="text-danger small mt-1" data-error="body"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-2">
                                <button class="btn btn-sm btn-primary" type="submit" data-submit-button>Gonder</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="js-comment-children" data-replies="{{ $comment->id }}">
                @if($depth < 5 && $comment->childrenRecursive->isNotEmpty())
                    @foreach($comment->childrenRecursive as $child)
                        @include('blog::partials.comment', ['comment' => $child, 'depth' => $depth + 1, 'blog' => $blog])
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
