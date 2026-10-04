<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="save" class="space-y-6">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                    Bobot Penilaian Papan Peringkat (Total 100%)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Atur perbandingan bobot antara aktivitas belajar mandiri (roadmap, cek pemahaman, latihan coding) dan modul / bank soal.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Bobot Poin Aktivitas Belajar (%)
                        </label>
                        <input
                            type="number"
                            min="0"
                            max="100"
                            wire:model="weight_activity"
                            class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
                            required
                        >
                        <p class="text-[11px] text-gray-500 mt-1">
                            Default: 30%. Dihitung dari pemahaman konsep, latihan coding, dan durasi aktif belajar.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Bobot Poin Modul & Bank Soal (%)
                        </label>
                        <input
                            type="number"
                            min="0"
                            max="100"
                            wire:model="weight_modules"
                            class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
                            required
                        >
                        <p class="text-[11px] text-gray-500 mt-1">
                            Default: 70%. Dihitung dari skor penilaian mentor pada Modul LKS dan bank soal.
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Batas Maksimum Waktu Belajar Aktif per Hari (Menit)
                    </label>
                    <input
                        type="number"
                        min="30"
                        max="720"
                        wire:model="daily_active_cap_minutes"
                        class="w-full md:w-1/2 text-sm border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
                        required
                    >
                    <p class="text-[11px] text-gray-500 mt-1">
                        Mencegah farming waktu aktif. Durasi lebih dari batas ini tidak akan dihitung menjadi poin tambahan di hari yang sama.
                    </p>
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-5 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-medium text-sm rounded-lg shadow transition-colors inline-flex items-center gap-2"
                >
                    <span>Simpan Pengaturan & Hitung Ulang Peringkat</span>
                </button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
