<?php

/**
 * IDE Helper for Laravel & Antigravity / Intelephense
 * File ini menyediakan type-hinting dan deklarasi static untuk IDE/Language Server
 * sehingga method-method magic Laravel seperti PDF::loadView(), Auth::user()->hasRole(), dll.
 * dikenali dengan baik tanpa garis merah.
 *
 * File ini TIDAK dieksekusi saat runtime Laravel.
 */

namespace {
    /**
     * @see \Barryvdh\DomPDF\PDF
     */
    class PDF extends \Barryvdh\DomPDF\Facade\Pdf {}

    /**
     * @see \Barryvdh\DomPDF\PDF
     */
    class Pdf extends \Barryvdh\DomPDF\Facade\Pdf {}

    if (!function_exists('auth')) {
        /**
         * Get the available auth instance.
         *
         * @param string|null $guard
         * @return \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|\App\User
         */
        function auth($guard = null)
        {
            return new \App\User();
        }
    }
}

namespace Illuminate\Support\Facades {
    /**
     * @see \Illuminate\Auth\AuthManager
     * @see \Illuminate\Contracts\Auth\Factory
     * @see \Illuminate\Contracts\Auth\Guard
     * @see \Illuminate\Contracts\Auth\StatefulGuard
     */
    class Auth {
        /**
         * Get the currently authenticated user.
         *
         * @return \App\User|null
         */
        public static function user()
        {
            return new \App\User();
        }
    }
}

namespace Illuminate\Contracts\Auth {
    interface Guard {
        /**
         * Get the currently authenticated user.
         *
         * @return \App\User|null
         */
        public function user();
    }

    interface StatefulGuard {
        /**
         * Get the currently authenticated user.
         *
         * @return \App\User|null
         */
        public function user();
    }
}

