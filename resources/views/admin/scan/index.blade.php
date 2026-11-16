<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pemindai Presensi QR Code') }}
            </h2>

            <!-- Form Pilih Event yang Sedang Dipindai -->
            <form method="GET" action="{{ route('admin.scan.index') }}" class="flex items-center gap-2">
                <label for="event_id" class="text-xs font-semibold text-gray-600">Event:</label>
                <select name="event_id" id="event_id" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @forelse ($events as $ev)
                        <option value="{{ $ev->id }}" {{ $selectedEvent && $selectedEvent->id === $ev->id ? 'selected' : '' }}>
                            {{ $ev->title }} ({{ $ev->start_at->format('d/m/Y') }})
                        </option>
                    @empty
                        <option value="">Belum ada event</option>
                    @endforelse
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (!$selectedEvent)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 text-yellow-800 rounded">
                    Silakan buat event terlebih dahulu sebelum memulai pemindaian presensi.
                </div>
            @else
                <!-- Banner Event Terpilih -->
                <div class="bg-indigo-900 text-white p-4 sm:p-6 rounded-xl shadow-md mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-indigo-300">Target Event Presensi</div>
                        <h3 class="text-xl font-black mt-1">{{ $selectedEvent->title }}</h3>
                        <p class="text-xs text-indigo-200 mt-1">
                            📍 {{ $selectedEvent->location }} | 📅 {{ $selectedEvent->start_at->format('d F Y, H:i') }} WIB
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-mono">
                            ID Event: #{{ $selectedEvent->id }}
                        </span>
                    </div>
                </div>

                <!-- Notifikasi Flash Manual -->
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Panel Scanner & Feedback -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Kolom 1: Scanner Kamera Browser (html5-qrcode) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Pemindai Kamera Langsung
                                </h4>
                                <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded font-medium">html5-qrcode</span>
                            </div>

                            <!-- Area Render Kamera -->
                            <div id="reader" class="rounded-lg overflow-hidden border border-gray-200 bg-black min-h-[300px] flex items-center justify-center text-white text-xs">
                                Memuat modul kamera...
                            </div>
                            <p class="text-xs text-gray-400 mt-2 text-center">
                                Arahkan kode QR tiket peserta ke depan kamera browser.
                            </p>
                        </div>

                        <!-- Form Fallback Manual Input Token (Untuk Kemudahan Uji Coba Demo) -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h5 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Simulasi / Input Token Manual:</h5>
                            <form id="manual-verify-form" method="POST" action="{{ route('admin.scan.verify') }}" class="flex gap-2">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
                                <input
                                    type="text"
                                    name="qr_token"
                                    id="manual_token_input"
                                    placeholder="Paste 64 karakter token QR disini..."
                                    class="text-xs font-mono rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 flex-1"
                                    required
                                />
                                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold uppercase tracking-wider rounded-md transition shadow">
                                    Verifikasi
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Kolom 2: Realtime Verification Status & Log -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="flex h-3 w-3 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                </span>
                                Status Hasil Pemindaian
                            </h4>

                            <!-- Kotak Hasil Interaktif -->
                            <div id="scan-feedback-box" class="p-6 rounded-xl border border-gray-200 bg-gray-50 text-center transition-all duration-200">
                                <div id="feedback-icon" class="text-4xl mb-2">📷</div>
                                <h5 id="feedback-title" class="font-bold text-gray-700 text-base">Menunggu Pemindaian</h5>
                                <p id="feedback-desc" class="text-xs text-gray-500 mt-1">Arahkan kamera ke tiket QR atau input token di bawah.</p>
                                <div id="feedback-details" class="mt-3 text-xs hidden text-left bg-white p-3 rounded-lg border border-gray-200 font-mono"></div>
                            </div>
                        </div>

                        <!-- Riwayat 10 Kehadiran Terakhir untuk Event Ini -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h5 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">
                                Kehadiran Terbaru (Event Ini)
                            </h5>

                            <div class="overflow-y-auto max-h-56 divide-y divide-gray-100 text-xs">
                                @forelse ($recentAttendances as $att)
                                    <div class="py-2 flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $att->registration->user->name }}</div>
                                            <div class="text-gray-400 text-[11px]">{{ $att->registration->user->email }}</div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-green-700 font-semibold bg-green-50 px-2 py-0.5 rounded text-[11px]">
                                                {{ $att->scanned_at->format('H:i:s') }}
                                            </span>
                                            <div class="text-[10px] text-gray-400">Oleh: {{ $att->scanner->name }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-gray-400 italic">
                                        Belum ada presensi yang tercatat untuk event ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Script Pemindai Kamera Menggunakan html5-qrcode & Integrasi AJAX -->
    @if ($selectedEvent)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const eventId = {{ $selectedEvent->id }};
                const verifyUrl = "{{ route('admin.scan.verify') }}";
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const feedbackBox = document.getElementById('scan-feedback-box');
                const feedbackIcon = document.getElementById('feedback-icon');
                const feedbackTitle = document.getElementById('feedback-title');
                const feedbackDesc = document.getElementById('feedback-desc');
                const feedbackDetails = document.getElementById('feedback-details');

                let isProcessing = false;

                function updateFeedback(success, title, message, details = null) {
                    if (success) {
                        feedbackBox.className = "p-6 rounded-xl border border-green-300 bg-green-50 text-center transition-all duration-200";
                        feedbackIcon.innerText = "✅";
                        feedbackTitle.className = "font-bold text-green-800 text-lg";
                        feedbackDesc.className = "text-xs text-green-700 mt-1";
                    } else {
                        feedbackBox.className = "p-6 rounded-xl border border-red-300 bg-red-50 text-center transition-all duration-200";
                        feedbackIcon.innerText = "❌";
                        feedbackTitle.className = "font-bold text-red-800 text-lg";
                        feedbackDesc.className = "text-xs text-red-700 mt-1";
                    }

                    feedbackTitle.innerText = title;
                    feedbackDesc.innerText = message;

                    if (details) {
                        feedbackDetails.classList.remove('hidden');
                        feedbackDetails.innerHTML = details;
                    } else {
                        feedbackDetails.classList.add('hidden');
                    }
                }

                // Fungsi kirim verifikasi ke server via AJAX
                async function verifyToken(token) {
                    if (isProcessing) return;
                    isProcessing = true;

                    updateFeedback(true, "Memverifikasi...", "Mengecek keabsahan token presensi ke database...", null);

                    try {
                        const response = await fetch(verifyUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                event_id: eventId,
                                qr_token: token.trim(),
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            let detailsHtml = `<strong>Peserta:</strong> ${data.data.participant_name}<br>` +
                                              `<strong>Email:</strong> ${data.data.participant_email}<br>` +
                                              `<strong>Waktu Presensi:</strong> ${data.data.scanned_at} WIB`;
                            updateFeedback(true, "PRESENSI BERHASIL", data.message, detailsHtml);

                            // Refresh halaman setelah 2 detik agar riwayat presensi terupdate
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            updateFeedback(false, "PRESENSI DITOLAK", data.message || "Verifikasi gagal.");
                            // Izinkan scan kembali setelah 3 detik
                            setTimeout(() => {
                                isProcessing = false;
                            }, 3000);
                        }
                    } catch (error) {
                        updateFeedback(false, "ERROR JARINGAN", "Gagal menghubungi server untuk memvalidasi QR.");
                        setTimeout(() => {
                            isProcessing = false;
                        }, 3000);
                    }
                }

                // Inisialisasi html5-qrcode Scanner
                if (window.Html5QrcodeScanner) {
                    const html5QrcodeScanner = new window.Html5QrcodeScanner(
                        "reader",
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                            aspectRatio: 1.0,
                        },
                        /* verbose= */ false
                    );

                    html5QrcodeScanner.render((decodedText) => {
                        verifyToken(decodedText);
                    }, (errorMessage) => {
                        // Scan berjalan, abaikan frame tanpa QR
                    });
                } else {
                    document.getElementById('reader').innerHTML = `
                        <div class="p-4 text-center">
                            <p class="text-yellow-400 font-semibold">Kamera siap digunakan.</p>
                            <p class="text-xs text-gray-300 mt-1">Gunakan form input manual di bawah jika kamera browser dinonaktifkan.</p>
                        </div>
                    `;
                }

                // Handler form manual
                const manualForm = document.getElementById('manual-verify-form');
                if (manualForm) {
                    manualForm.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const token = document.getElementById('manual_token_input').value;
                        if (token) {
                            verifyToken(token);
                        }
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
