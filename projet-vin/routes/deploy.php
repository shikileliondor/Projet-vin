<?php

use App\Http\Controllers\DeployController;
use Illuminate\Support\Facades\Route;

Route::post('deploy/run', DeployController::class)->name('deploy.run');
