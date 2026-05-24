<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sinav API Routes
|--------------------------------------------------------------------------
|
| Bu modülde yönetim tarafı AJAX istekleri web rotaları üzerinden çalışır.
| İhtiyaç olursa ileride `api/v1/sinav/*` altında ayrı bir API tasarlanabilir.
|
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // ...
});
