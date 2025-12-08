<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    // ... (otras propiedades: $dontReport, $dontFlash, $signature)

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // ✅ CORRECCIÓN CLAVE: Si la solicitud espera JSON (es AJAX/Fetch), 
        // devuelve un 401 en JSON. De lo contrario, redirige al login.
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'No autenticado. Por favor, inicia sesión de nuevo.'], 401);
        }

        return redirect()->guest(route('login'));
    }
    
    // ... (resto de la clase)
}