<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\Auth\SocialLoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {


    Route::get('/meus-anuncios', [MainController::class, 'meusAnuncios'])->name('anuncios.meus');

    Route::get('/anuncios/criar', [MainController::class, 'createAnuncio'])->name('anuncios.create');
    Route::post('/anuncios/criar-submit', [MainController::class, 'storeAnuncio'])->name('anuncios.store');

    Route::get('/anuncios/editar/{id}', [MainController::class, 'editAnuncio'])->name('anuncios.edit');
    Route::post('/anuncios/editar-submit', [MainController::class, 'updateAnuncio'])->name('anuncios.update');

    Route::post('/anuncios/deletar-submit', [MainController::class, 'deleteAnuncio'])->name('anuncios.delete');

    Route::post('/anuncios/proposta-submit', [PropostaController::class, 'store'])->name('propostas.store');
    Route::get('/propostas/enviadas', [PropostaController::class, 'enviadas'])->name('propostas.enviadas');
    Route::post('/propostas/atualizar-submit', [PropostaController::class, 'atualizar'])->name('propostas.atualizar');
    Route::post('/propostas/excluir-submit', [PropostaController::class, 'excluir'])->name('propostas.excluir');
    Route::post('/propostas/responder-submit', [PropostaController::class, 'responder'])->name('propostas.responder');
    Route::post('/anuncios/publicar-submit', [MainController::class, 'publicarAnuncio'])->name('anuncios.publicar');
    Route::get('/propostas/recebidas', [PropostaController::class, 'recebidas'])->name('propostas.recebidas');

    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::post('/perfil/atualizar', [PerfilController::class, 'update'])->name('perfil.update');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/anuncios/detalhes/{id}', [MainController::class, 'mostrar'])->name('anuncios.mostrar');
});

Route::get('/', [MainController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {


    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login-submit', [AuthController::class, 'loginSubmit'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register-submit', [AuthController::class, 'registerSubmit'])->name('register.submit');
});



Route::get('/auth/{provider}', [SocialLoginController::class, 'redirect']);

Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback']);
