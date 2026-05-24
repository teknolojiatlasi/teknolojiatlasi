@extends('exam::components.layouts.master')

@section('title', 'Soruyu Düzenle')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sınav Sorusunu Düzenle: {{ $exam->title }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('exam.exams.questions.update', [$exam->id, $question->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="question_text" class="form-label">Soru Metni *</label>
                            <textarea class="form-control @error('question_text') is-invalid @enderror" 
                                      id="question_text" name="question_text" rows="3" required>{{ old('question_text', $question->question_text) }}</textarea>
                            @error('question_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="image_path" class="form-label">Görsel Yolu (isteğe bağlı)</label>
                            <input type="text" class="form-control @error('image_path') is-invalid @enderror" 
                                   id="image_path" name="image_path" value="{{ old('image_path', $question->image_path) }}">
                            @error('image_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="option_a" class="form-label">A Şıkkı *</label>
                                    <textarea class="form-control @error('option_a') is-invalid @enderror" 
                                              id="option_a" name="option_a" rows="2" required>{{ old('option_a', $question->option_a) }}</textarea>
                                    @error('option_a')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="option_b" class="form-label">B Şıkkı *</label>
                                    <textarea class="form-control @error('option_b') is-invalid @enderror" 
                                              id="option_b" name="option_b" rows="2" required>{{ old('option_b', $question->option_b) }}</textarea>
                                    @error('option_b')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="option_c" class="form-label">C Şıkkı *</label>
                                    <textarea class="form-control @error('option_c') is-invalid @enderror" 
                                              id="option_c" name="option_c" rows="2" required>{{ old('option_c', $question->option_c) }}</textarea>
                                    @error('option_c')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="option_d" class="form-label">D Şıkkı *</label>
                                    <textarea class="form-control @error('option_d') is-invalid @enderror" 
                                              id="option_d" name="option_d" rows="2" required>{{ old('option_d', $question->option_d) }}</textarea>
                                    @error('option_d')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="option_e" class="form-label">E Şıkkı *</label>
                                    <textarea class="form-control @error('option_e') is-invalid @enderror" 
                                              id="option_e" name="option_e" rows="2" required>{{ old('option_e', $question->option_e) }}</textarea>
                                    @error('option_e')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="correct_answer" class="form-label">Doğru Cevap *</label>
                                    <select class="form-select @error('correct_answer') is-invalid @enderror" 
                                            id="correct_answer" name="correct_answer" required>
                                        <option value="">Doğru cevabı seçin</option>
                                        <option value="A" {{ old('correct_answer', $question->correct_answer) == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('correct_answer', $question->correct_answer) == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ old('correct_answer', $question->correct_answer) == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="D" {{ old('correct_answer', $question->correct_answer) == 'D' ? 'selected' : '' }}>D</option>
                                        <option value="E" {{ old('correct_answer', $question->correct_answer) == 'E' ? 'selected' : '' }}>E</option>
                                    </select>
                                    @error('correct_answer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="explanation" class="form-label">Açıklama (isteğe bağlı)</label>
                            <textarea class="form-control @error('explanation') is-invalid @enderror" 
                                      id="explanation" name="explanation" rows="3">{{ old('explanation', $question->explanation) }}</textarea>
                            @error('explanation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="order" class="form-label">Sıra</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" value="{{ old('order', $question->order) }}" min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('exam.exams.questions.index', $exam->id) }}" class="btn btn-secondary">İptal</a>
                            <button type="submit" class="btn btn-primary">Soruyu Güncelle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
