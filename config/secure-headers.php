<?php

/**
 * Konfigurasi Security Headers (bepsvpt/secure-headers)
 *
 * Mitigasi: Clickjacking (X-Frame-Options), MIME sniffing (X-Content-Type-Options),
 * info disclosure (X-Powered-By), XSS via CSP, dan lainnya.
 */
return [

    // Sembunyikan informasi server (information disclosure)
    'server' => '',

    // Cegah MIME sniffing (CWE-430)
    'x-content-type-options' => 'nosniff',

    'x-dns-prefetch-control' => '',

    'x-download-options' => 'noopen',

    // Cegah clickjacking — gunakan DENY agar halaman tidak bisa di-embed
    'x-frame-options' => 'deny',

    'x-permitted-cross-domain-policies' => 'none',

    // Sembunyikan X-Powered-By agar tidak bocorkan versi PHP/Laravel
    'x-powered-by' => '',

    // Nonaktifkan X-XSS-Protection (deprecated, andalkan CSP)
    'x-xss-protection' => '',

    // Referrer hanya dikirim ke origin yang sama (privasi)
    'referrer-policy' => 'strict-origin-when-cross-origin',

    'cross-origin-embedder-policy' => 'unsafe-none',

    'cross-origin-opener-policy' => 'unsafe-none',

    'cross-origin-resource-policy' => 'cross-origin',

    'clear-site-data' => [
        'enable' => false,
        'all' => false,
        'cache' => true,
        'clientHints' => true,
        'cookies' => true,
        'storage' => true,
        'executionContexts' => true,
    ],

    // HSTS: aktifkan saat production dengan HTTPS
    // Di lokal, nonaktifkan agar tidak memaksa HTTPS pada HTTP
    'hsts' => [
        'enable' => env('APP_ENV') === 'production',
        'max-age' => 31536000,
        'include-sub-domains' => false,
        'preload' => false,
    ],

    'reporting' => [],

    'nel' => [
        'enable' => false,
        'report-to' => '',
        'max-age' => 86400,
        'include-subdomains' => false,
        'success-fraction' => 0.0,
        'failure-fraction' => 1.0,
    ],

    'expect-ct' => [
        'enable' => false,
        'max-age' => 2147483648,
        'enforce' => false,
        'report-uri' => null,
    ],

    'permissions-policy' => [
        'enable' => true,

        'accelerometer' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'ambient-light-sensor' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'attribution-reporting' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'autoplay' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'bluetooth' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'browsing-topics' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        // Izinkan kamera untuk fitur scan QR
        'camera' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'compute-pressure' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'cross-origin-isolated' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'display-capture' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'document-domain' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'encrypted-media' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'fullscreen' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'gamepad' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'geolocation' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'gyroscope' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'hid' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'identity-credentials-get' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'idle-detection' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'local-fonts' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'magnetometer' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'microphone' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'midi' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'otp-credentials' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'payment' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'picture-in-picture' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'publickey-credentials-create' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'publickey-credentials-get' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'screen-wake-lock' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'serial' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'speaker-selection' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'storage-access' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'usb' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'web-share' => ['none' => false, '*' => false, 'self' => true, 'origins' => []],

        'window-management' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],

        'xr-spatial-tracking' => ['none' => false, '*' => false, 'self' => false, 'origins' => []],
    ],

    /**
     * Content Security Policy (CSP)
     *
     * Dibuat ketat namun kompatibel dengan:
     * - Alpine.js (inline script/event handler)
     * - html5-qrcode (membutuhkan blob: dan kamera)
     * - Font dari fonts.bunny.net (digunakan Breeze)
     * - Vite-compiled assets (self)
     */
    'csp' => [
        'enable' => true,
        'report-only' => false,
        'report-to' => '',
        'report-uri' => [],
        'block-all-mixed-content' => false,
        'upgrade-insecure-requests' => false,

        // base-uri: hanya izinkan 'self' (cegah base tag injection)
        'base-uri' => [
            'self' => true,
        ],

        'child-src' => [
            'self' => true,
            'schemes' => ['blob:'],
        ],

        // connect-src: izinkan blob: untuk html5-qrcode worker
        'connect-src' => [
            'self' => true,
            'schemes' => ['blob:'],
        ],

        // default-src: fallback ke 'self'
        'default-src' => [
            'self' => true,
        ],

        'fenced-frame-src' => [],

        // font-src: izinkan fonts.bunny.net (digunakan Breeze/Tailwind default)
        'font-src' => [
            'self' => true,
            'allow' => [
                'https://fonts.bunny.net',
            ],
        ],

        // form-action: hanya izinkan submit ke origin sendiri
        'form-action' => [
            'self' => true,
        ],

        // frame-ancestors: none = cegah embedding di iframe manapun (clickjacking)
        'frame-ancestors' => [
            'none' => true,
        ],

        'frame-src' => [],

        // img-src: izinkan data: untuk QR code SVG inline dan blob: untuk preview kamera
        'img-src' => [
            'self' => true,
            'schemes' => ['data:', 'blob:'],
        ],

        'manifest-src' => [
            'self' => true,
        ],

        'media-src' => [
            'self' => true,
            'schemes' => ['blob:'],
        ],

        // object-src: none (cegah plugin Flash, dsb)
        'object-src' => [
            'none' => true,
        ],

        'prefetch-src' => [],

        'require-trusted-types-for' => [
            'script' => false,
        ],

        'sandbox' => [
            'enable' => false,
            'allow-downloads' => false,
            'allow-forms' => false,
            'allow-modals' => false,
            'allow-orientation-lock' => false,
            'allow-pointer-lock' => false,
            'allow-popups' => false,
            'allow-popups-to-escape-sandbox' => false,
            'allow-presentation' => false,
            'allow-same-origin' => false,
            'allow-scripts' => false,
            'allow-storage-access-by-user-activation' => false,
            'allow-top-navigation' => false,
            'allow-top-navigation-by-user-activation' => false,
            'allow-top-navigation-to-custom-protocols' => false,
        ],

        // script-src: izinkan 'self' + 'unsafe-inline' untuk Alpine.js
        // unsafe-inline diperlukan karena Alpine.js menggunakan x-on: / @click inline
        // Untuk produksi ideal, gunakan nonce; ini adalah tradeoff untuk Breeze + Alpine
        'script-src' => [
            'none' => false,
            'self' => true,
            'report-sample' => false,
            'allow' => [],
            'schemes' => ['blob:'],
            'unsafe-inline' => true,   // diperlukan Alpine.js & event handler
            'unsafe-eval' => true,     // diperlukan Alpine.js untuk evaluasi ekspresi reaktif (x-data, @click)
            'wasm-unsafe-eval' => false,
            'unsafe-hashes' => false,
            'strict-dynamic' => false,
            'hashes' => [
                'sha256' => [],
                'sha384' => [],
                'sha512' => [],
            ],
        ],

        'script-src-attr' => [],

        'script-src-elem' => [],

        // style-src: izinkan 'unsafe-inline' untuk Tailwind utility class inline & Alpine
        'style-src' => [
            'self' => true,
            'allow' => [
                'https://fonts.bunny.net',
            ],
            'unsafe-inline' => true,   // diperlukan Tailwind CSS inline & Alpine transitions
        ],

        'style-src-attr' => [],

        'style-src-elem' => [],

        'trusted-types' => [
            'enable' => false,
            'none' => false,
            'allow-duplicates' => false,
            'policies' => [],
        ],

        // worker-src: izinkan blob: untuk html5-qrcode web worker
        'worker-src' => [
            'self' => true,
            'schemes' => ['blob:'],
        ],
    ],
];
