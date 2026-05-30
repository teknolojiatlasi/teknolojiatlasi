<tr
    data-id="{{ $question->id }}"
    data-question-text="{{ $question->question_text }}"
    data-image-path="{{ $question->image_path }}"
    data-image-url="{{ $question->image_url }}"
    data-option-a="{{ $question->option_a }}"
    data-option-b="{{ $question->option_b }}"
    data-option-c="{{ $question->option_c }}"
    data-option-d="{{ $question->option_d }}"
    data-option-e="{{ $question->option_e }}"
    data-correct-option="{{ $question->correct_option }}"
    data-explanation="{{ $question->explanation }}"
    data-sort-order="{{ $question->sort_order }}"
    data-active="{{ $question->is_active ? '1' : '0' }}"
>
    <td style="max-width: 520px">
        @if ($question->image_url)
            <div class="mb-2">
                <img src="{{ $question->image_url }}" alt="Soru gorseli" style="max-width: 140px; max-height: 90px; border-radius: 8px; object-fit: cover;">
            </div>
        @endif
        <div class="fw-semibold">{{ \Illuminate\Support\Str::limit(strip_tags($question->question_text), 120) }}</div>
        <div class="text-muted small">A: {{ \Illuminate\Support\Str::limit($question->option_a, 60) }}</div>
    </td>
    <td class="text-center"><span class="badge bg-dark">{{ $question->correct_option }}</span></td>
    <td class="text-center">{{ $question->sort_order }}</td>
    <td class="text-center">
        @if ($question->is_active)
            <span class="badge bg-success">Aktif</span>
        @else
            <span class="badge bg-secondary">Pasif</span>
        @endif
    </td>
    <td class="text-end">
        <button type="button" class="btn btn-sm btn-warning" data-action="edit-question">
            <i class="fa fa-edit"></i> Düzenle
        </button>
        <button type="button" class="btn btn-sm btn-danger" data-action="delete-question">
            <i class="fa fa-trash"></i> Sil
        </button>
    </td>
</tr>
