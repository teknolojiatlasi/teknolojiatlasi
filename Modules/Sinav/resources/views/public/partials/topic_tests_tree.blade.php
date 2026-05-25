@php
    /** @var array $nodes */
    /** @var \Modules\Sinav\Models\Lesson $lesson */
    /** @var int|null $activeTestId */
    $depth ??= 0;
@endphp

<style>
    .exam-topic-tree,
    .exam-topic-tree ul {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .exam-topic-item + .exam-topic-item {
        margin-top: 1rem;
    }

    .exam-topic-branch {
        position: relative;
    }

    .exam-topic-branch.is-child {
        margin-top: 0.95rem;
        margin-left: 0.9rem;
        padding-left: 1rem;
        border-left: 2px solid rgba(59, 130, 246, 0.14);
    }

    .exam-topic-card {
        padding: 1rem;
        border-radius: 1.15rem;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.05);
    }

    .exam-topic-card.is-parent {
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, 0.1), transparent 34%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(239, 246, 255, 0.86));
        border-color: rgba(59, 130, 246, 0.16);
    }

    .exam-topic-card.is-child {
        border-radius: 1rem;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.96));
        border-color: rgba(148, 163, 184, 0.14);
    }

    .exam-topic-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.85rem;
        margin-bottom: 0.35rem;
    }

    .exam-topic-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.32rem 0.62rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.08);
        color: #00fdf6;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .exam-topic-title {
        color: #0f172a;
        font-weight: 800;
        line-height: 1.35;
    }

    .exam-topic-card.is-parent .exam-topic-title {
        font-size: 1rem;
    }

    .exam-topic-card.is-child .exam-topic-title {
        font-size: 0.95rem;
    }

    .exam-topic-count {
        flex: 0 0 auto;
        min-width: 2.2rem;
        height: 2.2rem;
        padding: 0 0.65rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #00fdf6, #06b6d4);
        color: #fff;
        font-size: 0.82rem;
        font-weight: 800;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.18);
    }

    .exam-topic-description {
        color: #64748b;
        font-size: 0.85rem;
        line-height: 1.55;
        margin-bottom: 0.9rem;
    }

    .exam-topic-tests {
        display: grid;
        gap: 0.65rem;
    }

    .exam-subtopic-stack {
        margin-top: 0.9rem;
    }

    .exam-test-link {
        display: block;
        padding: 0.95rem 1rem;
        border-radius: 1rem;
        background:
            linear-gradient(135deg, rgba(59, 130, 246, 0.04), rgba(16, 185, 129, 0.02)),
            #f8fafc;
        border: 1px solid rgba(148, 163, 184, 0.18);
        color: #0f172a;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .exam-test-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        border-color: rgba(37, 99, 235, 0.25);
    }

    .exam-test-link.active {
        background: linear-gradient(135deg, #00fdf6, #2563eb);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 20px 34px rgba(37, 99, 235, 0.2);
    }

    .exam-test-meta {
        color: #64748b;
        font-size: 0.82rem;
    }

    .exam-test-link.active .exam-test-meta {
        color: rgba(255, 255, 255, 0.74);
    }

    .exam-test-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.6rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.1);
        color: #00fdf6;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .exam-test-link.active .exam-test-pill {
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .exam-topic-branch.is-child {
            margin-left: 0.35rem;
            padding-left: 0.75rem;
        }

        .exam-topic-card {
            padding: 0.9rem;
        }

        .exam-topic-head {
            gap: 0.65rem;
        }

        .exam-topic-count {
            min-width: 2rem;
            height: 2rem;
            font-size: 0.78rem;
        }
    }
</style>

<ul class="exam-topic-tree">
    @foreach ($nodes as $node)
        @php
            /** @var \Modules\Sinav\Models\Topic $topic */
            $topic = $node['topic'];
            $isChild = $depth > 0;
            $topicTests = $topic->tests->count();
        @endphp

        <li class="exam-topic-item">
            <div class="exam-topic-branch {{ $isChild ? 'is-child' : 'is-root' }}">
            <div class="exam-topic-card {{ $isChild ? 'is-child' : 'is-parent' }}">
                <div class="exam-topic-head">
                    <div class="flex-grow-1">
                        @unless($isChild)
                            <div class="exam-topic-kicker mb-2">
                                <i class="fa fa-folder-open"></i>
                                Ana konu
                            </div>
                        @endunless
                        <div class="exam-topic-title">{{ $topic->title }}</div>
                    </div>
                    <span class="exam-topic-count" title="Aktif test sayısı">{{ $topicTests }}</span>
                </div>

                @if ($topic->description)
                    <div class="exam-topic-description">{{ $topic->description }}</div>
                @endif

                @if ($topic->tests->isNotEmpty())
                    <div class="exam-topic-tests">
                        @foreach ($topic->tests as $test)
                            @php
                                $isActive = $activeTestId && (int) $activeTestId === (int) $test->id;
                            @endphp
                            <a
                                class="exam-test-link {{ $isActive ? 'active' : '' }}"
                                href="{{ route('sinav.lessons.show', $lesson) }}?test_id={{ $test->id }}#solve"
                            >
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-semibold mb-1">{{ $test->title }}</div>
                                        <div class="exam-test-meta">
                                            Süre: {{ $test->duration_minutes }} dk · Soru: {{ $test->questions_count }}
                                        </div>
                                    </div>
                                    <span class="exam-test-pill">
                                        <i class="fa fa-play"></i>
                                        {{ $isActive ? 'Açık' : 'Başla' }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted small">Bu konuda aktif test yok.</div>
                @endif

                @if (!empty($node['children']))
                    <div class="exam-subtopic-stack">
                        @include('sinav::public.partials.topic_tests_tree', ['nodes' => $node['children'], 'lesson' => $lesson, 'activeTestId' => $activeTestId, 'depth' => $depth + 1])
                    </div>
                @endif
            </div>
            </div>
        </li>
    @endforeach
</ul>
