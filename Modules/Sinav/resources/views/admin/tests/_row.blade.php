<tr data-id="{{ $test->id }}" data-title="{{ $test->title }}" data-duration="{{ $test->duration_minutes }}" data-active="{{ $test->is_active ? '1' : '0' }}">
    <td>{{ $test->title }}</td>
    <td>{{ $test->duration_minutes }}</td>
    <td>
        @if ($test->is_active)
            <span class="badge bg-success">Aktif</span>
        @else
            <span class="badge bg-secondary">Pasif</span>
        @endif
    </td>
    <td class="text-end">
        <a href="{{ route('sinav.admin.tests.questions.index', $test) }}" class="btn btn-sm btn-info">
            <i class="fa fa-question-circle"></i> Sorular
        </a>
        <button type="button" class="btn btn-sm btn-warning" data-action="edit-test">
            <i class="fa fa-edit"></i> Düzenle
        </button>
        <button type="button" class="btn btn-sm btn-danger" data-action="delete-test">
            <i class="fa fa-trash"></i> Sil
        </button>
    </td>
</tr>

