@extends('exam::components.layouts.master')

@section('title', 'Sınava Gir: ' . $exam->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $exam->title }}</h3>
                </div>
                <div class="card-body">
                    <!-- Progress Bar -->
                    <div class="progress mb-3">
                        <div id="progress-bar" class="progress-bar" role="progressbar" 
                             style="width: {{ ($answeredQuestions / $totalQuestions) * 100 }}%" 
                             aria-valuenow="{{ $answeredQuestions }}" 
                             aria-valuemin="0" 
                             aria-valuemax="{{ $totalQuestions }}">
                            {{ $answeredQuestions }} / {{ $totalQuestions }}
                        </div>
                    </div>
                    
                    <!-- Timer -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong>Kalan Süre:</strong> 
                            <span id="timer" class="badge bg-danger">--:--</span>
                        </div>
                        <div>
                            <strong>{{ $totalQuestions }} sorudan {{ $answeredQuestions + 1 }}. soru</strong>
                        </div>
                    </div>
                    
                    <!-- Question -->
                    <div id="question-container">
                        <form id="answer-form">
                            @csrf
                            <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                            
                            <div class="mb-3">
                                <h5>{{ $currentQuestion->question_text }}</h5>
                            </div>
                            
                            @if($currentQuestion->image_path)
                                <div class="mb-3 text-center">
                                    <img src="{{ asset($currentQuestion->image_path) }}" 
                                         alt="Soru Görseli" 
                                         class="img-fluid rounded">
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                @foreach($currentQuestion->getOptionsArray() as $key => $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answer" id="option_{{ $key }}" 
                                               value="{{ $key }}" required>
                                        <label class="form-check-label" for="option_{{ $key }}">
                                            <strong>{{ $key }}.</strong> {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">Cevabı Gönder</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Timer functionality
    let timeLimitMinutes = {{ $session->time_limit_minutes ?? 60 }};
    let startTime = new Date("{{ $session->started_at->toIso8601String() }}").getTime();
    let endTime = startTime + (timeLimitMinutes * 60 * 1000);
    
    function updateTimer() {
        let now = new Date().getTime();
        let distance = endTime - now;
        
        if (distance <= 0) {
            clearInterval(timerInterval);
            document.getElementById('timer').innerHTML = "SÜRE DOLDU!";
            document.getElementById('answer-form').style.display = 'none';
            alert("Süre doldu. Sınavınız otomatik olarak tamamlanacak.");
            window.location.href = "{{ route('exam.exam-taking.finish', $exam->id) }}";
            return;
        }
        
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('timer').innerHTML = minutes + "m " + seconds + "s";
    }
    
    let timerInterval = setInterval(updateTimer, 1000);
    updateTimer(); // Initial call
    
    // Form submission with AJAX
    document.getElementById('answer-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let selectedAnswer = document.querySelector('input[name="answer"]:checked');
        
        if (!selectedAnswer) {
            alert('Lütfen bir cevap seçin.');
            return;
        }
        
        formData.append('answer', selectedAnswer.value);
        
        fetch("{{ route('exam.exam-taking.submit-answer', [$exam->id, $currentQuestion->id]) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update progress bar
                let progressBar = document.getElementById('progress-bar');
                let currentWidth = parseFloat(progressBar.style.width);
                let newWidth = currentWidth + (100 / {{ $totalQuestions }});
                progressBar.style.width = newWidth + '%';
                progressBar.innerHTML = parseInt(progressBar.getAttribute('aria-valuenow')) + 1 + ' / {{ $totalQuestions }}';
                progressBar.setAttribute('aria-valuenow', parseInt(progressBar.getAttribute('aria-valuenow')) + 1);
                
                // Load next question or finish exam
                if (data.next_question_url) {
                    window.location.href = data.next_question_url;
                } else {
                    window.location.href = data.redirect_url;
                }
            } else if (data.status === 'completed') {
                window.location.href = data.redirect_url;
            } else {
                alert('Cevap gönderilirken hata oluştu. Lütfen tekrar deneyin.');
            }
        })
        .catch(error => {
            console.error('Hata:', error);
            alert('Cevap gönderilirken hata oluştu. Lütfen tekrar deneyin.');
        });
    });
</script>
@endsection
