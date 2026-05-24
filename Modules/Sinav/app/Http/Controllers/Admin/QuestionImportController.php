<?php

namespace Modules\Sinav\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Sinav\Models\Lesson;
use Modules\Sinav\Models\Question;
use Modules\Sinav\Models\Test;
use Modules\Sinav\Models\Topic;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuestionImportController extends Controller
{
    public function create(Request $request)
    {
        $lessons = Lesson::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $prefill = $this->prefill($request);

        return view('sinav::admin.questions.import', compact('lessons', 'prefill'));
    }

    public function createJson(Request $request)
    {
        $lessons = Lesson::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $prefill = $this->prefill($request);

        return view('sinav::admin.questions.import-json', compact('lessons', 'prefill'));
    }

    public function template()
    {
        $this->ensureSpreadsheet();

        $headers = $this->questionColumns();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');

        $example = [
            'Ornek soru metni',
            'A sikki',
            'B sikki',
            'C sikki',
            'D sikki',
            'E sikki',
            'A',
            'Kisa aciklama (opsiyonel)',
            0,
            1,
        ];
        $sheet->fromArray($example, null, 'A2');

        foreach (range(1, count($headers)) as $index) {
            $columnLetter = Coordinate::stringFromColumnIndex($index);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'sinav_soru_sablonu.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function jsonTemplate()
    {
        $example = [
            'questions' => [
                [
                    'question_text' => 'Ornek soru metni',
                    'option_a' => 'A sikki',
                    'option_b' => 'B sikki',
                    'option_c' => 'C sikki',
                    'option_d' => 'D sikki',
                    'option_e' => 'E sikki',
                    'correct_option' => 'A',
                    'explanation' => 'Kisa aciklama (opsiyonel)',
                    'sort_order' => 0,
                    'is_active' => true,
                ],
            ],
        ];

        return response()->streamDownload(function () use ($example) {
            echo json_encode($example, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, 'sinav_soru_sablonu.json', [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    public function topics(Request $request)
    {
        $lessonId = (int) $request->query('lesson_id');

        if ($lessonId <= 0 || ! Lesson::query()->whereKey($lessonId)->exists()) {
            return response()->json([
                'topics' => [],
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
              ->header('Pragma', 'no-cache')
              ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        }

        $topics = Topic::query()
            ->where('lesson_id', $lessonId)
            ->orderBy('sort_order')
            ->get(['id', 'parent_id', 'title', 'sort_order']);

        return response()->json([
            'topics' => $this->topicOptions($topics),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
          ->header('Pragma', 'no-cache')
          ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function tests(Request $request)
    {
        $topicId = (int) $request->query('topic_id');

        if ($topicId <= 0 || ! Topic::query()->whereKey($topicId)->exists()) {
            return response()->json([
                'tests' => [],
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
              ->header('Pragma', 'no-cache')
              ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        }

        $tests = Test::query()
            ->where('topic_id', $topicId)
            ->orderByDesc('created_at')
            ->get(['id', 'title']);

        return response()->json([
            'tests' => $tests,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
          ->header('Pragma', 'no-cache')
          ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function store(Request $request)
    {
        $this->ensureSpreadsheet();

        $data = $request->validate([
            'lesson_id' => ['required', 'integer', 'exists:sinav_lessons,id'],
            'topic_id' => ['required', 'integer', 'exists:sinav_topics,id'],
            'test_id' => ['required', 'integer', 'exists:sinav_tests,id'],
            'file' => ['required', 'file', 'max:10240', 'mimes:xlsx,xls'],
        ]);

        return $this->processImport($data, $this->readRows($request->file('file')));
    }

    public function storeJson(Request $request)
    {
        $data = $request->validate([
            'lesson_id' => ['required', 'integer', 'exists:sinav_lessons,id'],
            'topic_id' => ['required', 'integer', 'exists:sinav_topics,id'],
            'test_id' => ['required', 'integer', 'exists:sinav_tests,id'],
            'file' => ['required', 'file', 'max:10240', 'mimes:json,txt'],
        ]);

        return $this->processImport($data, $this->readJsonRows($request->file('file')));
    }

    private function processImport(array $data, array $rows)
    {
        $test = Test::query()->with('topic.lesson')->findOrFail($data['test_id']);

        if ((int) $test->topic_id !== (int) $data['topic_id'] || (int) $test->topic->lesson_id !== (int) $data['lesson_id']) {
            return back()
                ->withInput()
                ->with('error', 'Secilen Ders/Konu/Test eslesmiyor. Lutfen tekrar secin.');
        }

        if (!$rows) {
            return back()
                ->withInput()
                ->with('error', 'Dosyada aktarilacak soru bulunamadi.');
        }

        [$prepared, $errors] = $this->prepareRows($rows, $test);

        if ($errors) {
            return back()
                ->withInput()
                ->with('import_errors', $errors)
                ->with('error', 'Bazi sorular dogrulanamadi. Lutfen hatalari duzeltip tekrar deneyin.');
        }

        if (!$prepared) {
            return back()
                ->withInput()
                ->with('error', 'Dosyada aktarilacak soru bulunamadi.');
        }

        $created = 0;
        DB::transaction(function () use ($prepared, &$created) {
            foreach ($prepared as $payload) {
                Question::create($payload);
                $created++;
            }
        });

        return back()->with('success', "{$created} soru basariyla yuklendi.");
    }

    /**
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, array{row:int|string, messages:array<int, string>}>}
     */
    private function prepareRows(array $rows, Test $test): array
    {
        $nextSortOrder = ((int) Question::query()->where('test_id', $test->id)->max('sort_order')) + 1;

        $errors = [];
        $prepared = [];
        foreach ($rows as $rowIndex => $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $validator = Validator::make($row, [
                'question_text' => ['required', 'string'],
                'option_a' => ['required', 'string'],
                'option_b' => ['required', 'string'],
                'option_c' => ['required', 'string'],
                'option_d' => ['required', 'string'],
                'option_e' => ['required', 'string'],
                'correct_option' => ['required', Rule::in(['A', 'B', 'C', 'D', 'E'])],
                'explanation' => ['nullable', 'string'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'is_active' => ['nullable'],
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowIndex,
                    'messages' => $validator->errors()->all(),
                ];
                continue;
            }

            $sortOrder = $row['sort_order'] !== null && $row['sort_order'] !== ''
                ? (int) $row['sort_order']
                : $nextSortOrder++;

            $prepared[] = [
                'topic_id' => $test->topic_id,
                'test_id' => $test->id,
                'question_text' => $row['question_text'],
                'option_a' => $row['option_a'],
                'option_b' => $row['option_b'],
                'option_c' => $row['option_c'],
                'option_d' => $row['option_d'],
                'option_e' => $row['option_e'],
                'correct_option' => $row['correct_option'],
                'explanation' => $row['explanation'] ?: null,
                'sort_order' => $sortOrder,
                'is_active' => $this->toBool($row['is_active'], true),
            ];
        }

        return [$prepared, $errors];
    }

    private function ensureSpreadsheet(): void
    {
        if (!class_exists(IOFactory::class)) {
            throw ValidationException::withMessages([
                'file' => 'Excel destegi icin phpoffice/phpspreadsheet paketi gerekli.',
            ]);
        }
    }

    /**
     * @return array<int, array<string, mixed>> key: excel row number
     */
    private function readRows(UploadedFile $file): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();
        $highestCol = $sheet->getHighestDataColumn();
        $highestColIndex = Coordinate::columnIndexFromString($highestCol);

        $header = [];
        for ($col = 1; $col <= $highestColIndex; $col++) {
            $value = (string) $sheet->getCell([$col, 1])->getValue();
            $header[$col] = trim($value);
        }

        $mapped = [];
        foreach ($this->questionColumns() as $name) {
            $colIndex = array_search($name, $header, true);
            if ($colIndex === false) {
                throw ValidationException::withMessages([
                    'file' => "Excel baslik satirinda eksik kolon: {$name}",
                ]);
            }
            $mapped[$name] = $colIndex;
        }

        $rows = [];
        for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
            $row = [];
            foreach ($mapped as $key => $colIndex) {
                $cellValue = $sheet->getCell([$colIndex, $rowIndex])->getCalculatedValue();
                if ($cellValue instanceof RichText) {
                    $cellValue = $cellValue->getPlainText();
                }
                $row[$key] = is_string($cellValue) ? trim($cellValue) : $cellValue;
            }

            if (isset($row['correct_option']) && is_string($row['correct_option'])) {
                $row['correct_option'] = strtoupper(trim($row['correct_option']));
            }

            if (array_key_exists('sort_order', $row) && is_numeric($row['sort_order'])) {
                $row['sort_order'] = (int) $row['sort_order'];
            }
            if (array_key_exists('is_active', $row) && is_numeric($row['is_active'])) {
                $row['is_active'] = (int) $row['is_active'];
            }

            $rows[$rowIndex] = $row;
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>> key: question number
     */
    private function readJsonRows(UploadedFile $file): array
    {
        $contents = file_get_contents($file->getRealPath());
        if ($contents === false || trim($contents) === '') {
            return [];
        }

        try {
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ValidationException::withMessages([
                'file' => 'JSON dosyasi okunamadi: ' . $e->getMessage(),
            ]);
        }

        $items = $decoded['questions'] ?? $decoded;
        if (!is_array($items) || !$this->isList($items)) {
            throw ValidationException::withMessages([
                'file' => 'JSON kok alani soru listesi olmali veya questions anahtari soru listesi icermeli.',
            ]);
        }

        $rows = [];
        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                throw ValidationException::withMessages([
                    'file' => 'JSON icindeki her soru nesne formatinda olmali.',
                ]);
            }

            $row = [];
            foreach ($this->questionColumns() as $column) {
                $row[$column] = $item[$column] ?? null;
            }

            foreach ($row as $key => $value) {
                if (is_string($value)) {
                    $row[$key] = trim($value);
                }
            }

            if (isset($row['correct_option']) && is_string($row['correct_option'])) {
                $row['correct_option'] = strtoupper($row['correct_option']);
            }

            if (array_key_exists('sort_order', $row) && is_numeric($row['sort_order'])) {
                $row['sort_order'] = (int) $row['sort_order'];
            }

            $rows[$index + 1] = $row;
        }

        return $rows;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value === null) {
                continue;
            }
            if (is_string($value) && trim($value) === '') {
                continue;
            }
            return false;
        }
        return true;
    }

    private function toBool(mixed $value, bool $default): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int) $value === 1;
        }
        if (is_string($value)) {
            $v = mb_strtolower(trim($value));
            if (in_array($v, ['1', 'true', 'evet', 'yes', 'aktif'], true)) {
                return true;
            }
            if (in_array($v, ['0', 'false', 'hayir', 'no', 'pasif'], true)) {
                return false;
            }
        }
        return $default;
    }

    /**
     * @return array<int, array{id:int, title:string}>
     */
    private function topicOptions($topics): array
    {
        $grouped = $topics->groupBy(fn ($t) => $t->parent_id ?: 0);

        $out = [];
        $walk = function (int $parentId, string $prefix) use (&$walk, &$out, $grouped) {
            foreach ($grouped->get($parentId, collect()) as $topic) {
                $out[] = ['id' => $topic->id, 'title' => $prefix . $topic->title];
                $walk((int) $topic->id, $prefix . '-- ');
            }
        };

        $walk(0, '');

        return $out;
    }

    /**
     * @return array<string, string|null>
     */
    private function prefill(Request $request): array
    {
        $testId = (int) $request->query('test_id');
        if ($testId > 0) {
            $test = Test::query()
                ->with('topic.lesson')
                ->find($testId);

            if ($test && $test->topic) {
                return [
                    'lesson_id' => (string) $test->topic->lesson_id,
                    'topic_id' => (string) $test->topic_id,
                    'test_id' => (string) $test->id,
                    'topic_title' => (string) $test->topic->title,
                    'test_title' => (string) $test->title,
                ];
            }
        }

        $topicId = (int) $request->query('topic_id');
        if ($topicId > 0) {
            $topic = Topic::query()->find($topicId);

            if ($topic) {
                return [
                    'lesson_id' => (string) $topic->lesson_id,
                    'topic_id' => (string) $topic->id,
                    'test_id' => $request->query('test_id'),
                    'topic_title' => (string) $topic->title,
                    'test_title' => null,
                ];
            }
        }

        return [
            'lesson_id' => $request->query('lesson_id'),
            'topic_id' => $request->query('topic_id'),
            'test_id' => $request->query('test_id'),
            'topic_title' => null,
            'test_title' => null,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function questionColumns(): array
    {
        return [
            'question_text',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'option_e',
            'correct_option',
            'explanation',
            'sort_order',
            'is_active',
        ];
    }

    private function isList(array $value): bool
    {
        if ($value === []) {
            return true;
        }

        return array_keys($value) === range(0, count($value) - 1);
    }
}
