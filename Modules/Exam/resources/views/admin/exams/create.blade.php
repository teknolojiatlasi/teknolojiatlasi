@extends('exam::components.layouts.master')

@section('title', 'Sınav Oluştur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sınav Oluştur</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('exam.exams.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Başlık *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="menu_id" class="form-label">Menü *</label>
                            <select class="form-select @error('menu_id') is-invalid @enderror" 
                                    id="menu_id" name="menu_id" required>
                                <option value="">Bir menü seçin</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}" {{ old('menu_id') == $menu->id ? 'selected' : '' }}>
                                        {{ $menu->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('menu_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', 0) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('exam.exams.index') }}" class="btn btn-secondary">İptal</a>
                            <button type="submit" class="btn btn-primary">Sınav Oluştur</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
