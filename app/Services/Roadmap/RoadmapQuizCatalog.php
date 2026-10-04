<?php

namespace App\Services\Roadmap;

class RoadmapQuizCatalog
{
    /**
     * Get quiz options, correct answer, and explanation for a given checkpoint prompt.
     *
     * @return array{options: array<array{id: string, text: string}>, correct_answer: string, explanation: string}
     */
    public static function getQuizForPrompt(string $prompt): array
    {
        $catalog = self::catalog();

        foreach ($catalog as $key => $quiz) {
            if (str_contains(mb_strtolower($prompt), mb_strtolower($key))) {
                return $quiz;
            }
        }

        // Fallback default quiz if no exact match
        return [
            'options' => [
                ['id' => 'a', 'text' => 'Memahami dan menerapkan konsep sesuai panduan modul.'],
                ['id' => 'b', 'text' => 'Mengabaikan konsep dan menyalin kode dari modul lain.'],
                ['id' => 'c', 'text' => 'Menghafal sintaks tanpa memahami alur kerja di browser.'],
                ['id' => 'd', 'text' => 'Tidak relevan dengan modul ini.'],
            ],
            'correct_answer' => 'a',
            'explanation' => 'Pemahaman konsep adalah fondasi utama dalam menyelesaikan permasalahan modul client-side LKS.',
        ];
    }

    /**
     * Comprehensive catalog of quizzes for all 38 roadmap checkpoints.
     *
     * @return array<string, array{options: array<array{id: string, text: string}>, correct_answer: string, explanation: string}>
     */
    public static function catalog(): array
    {
        return [
            // Level 0
            'beda perubahan yang kamu buat di devtools' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Perubahan di DevTools hanya bersifat sementara di memori browser dan hilang saat reload, sedangkan perubahan di file bersifat permanen.'],
                    ['id' => 'b', 'text' => 'Perubahan di DevTools langsung otomatis tersimpan ke file proyek di VS Code.'],
                    ['id' => 'c', 'text' => 'Perubahan di DevTools hanya berlaku untuk JavaScript, tidak bisa mengubah HTML atau CSS.'],
                    ['id' => 'd', 'text' => 'Tidak ada perbedaan sama sekali antara keduanya.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'DevTools hanya memodifikasi DOM dan CSSOM yang sedang aktif di memori browser. Refresh halaman akan memuat ulang kode dari file asli di disk.',
            ],
            'dibuka dengan klik ganda' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Karena browser membatasi fitur seperti fetch() pada protokol file:// akibat kebijakan keamanan (CORS/origin policy).'],
                    ['id' => 'b', 'text' => 'Karena browser tidak bisa merender CSS jika file dibuka langsung dari file manager.'],
                    ['id' => 'c', 'text' => 'Karena file HTML harus selalu dikompilasi oleh compiler server terlebih dahulu.'],
                    ['id' => 'd', 'text' => 'Karena JavaScript dinonaktifkan secara permanen pada protokol file://.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Fitur async seperti fetch() membutuhkan HTTP server (misal Live Server di port lokal) agar origin keamanan browser terpenuhi.',
            ],

            // Level 1
            'beda id dan class' => [
                'options' => [
                    ['id' => 'a', 'text' => 'id harus unik untuk satu elemen dalam dokumen, sedangkan class boleh dipakai berulang oleh banyak elemen.'],
                    ['id' => 'b', 'text' => 'id hanya untuk CSS sedangkan class hanya untuk JavaScript.'],
                    ['id' => 'c', 'text' => 'class harus unik per halaman sedangkan id boleh dipakai berulang.'],
                    ['id' => 'd', 'text' => 'id dan class identik dan fungsinya dapat saling menggantikan tanpa batasan.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'id bersifat unik (identitas tunggal), sedangkan class digunakan untuk mengelompokkan elemen yang memiliki karakteristik atau styling serupa.',
            ],
            'atribut for pada label' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Menghubungkan label ke input dengan id terkait sehingga klik pada teks label otomatis memfokuskan kolom input.'],
                    ['id' => 'b', 'text' => 'Menentukan perulangan elemen form menggunakan loop for.'],
                    ['id' => 'c', 'text' => 'Mengirim nilai input secara langsung ke server tanpa tombol submit.'],
                    ['id' => 'd', 'text' => 'Memberi warna merah otomatis pada input jika kosong.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Atribut for pada label meningkatkan UX dan aksesibilitas (WAI-ARIA) dengan mengaitkan klik label langsung ke input id.',
            ],
            'type="number" lebih baik' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Menyediakan validasi native angka, memunculkan keypad angka di perangkat sentuh, dan mendukung atribut min/max/step.'],
                    ['id' => 'b', 'text' => 'Otomatis mengubah angka kilometer menjadi mil secara otomatis.'],
                    ['id' => 'c', 'text' => 'Mencegah browser menggunakan memori berlebih untuk string.'],
                    ['id' => 'd', 'text' => 'Karena JavaScript tidak dapat membaca angka dari type="text".'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'type="number" membatasi input hanya untuk karakter numerik dan memberikan keyboard numpad di mobile.',
            ],

            // Level 2
            'translate(-50%, -100%)' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Persentase translate dihitung dari ukuran elemen itu sendiri, sehingga menggeser titik tengah horizontal (-50%) dan ujung paling bawah (-100%) tepat ke titik koordinat.'],
                    ['id' => 'b', 'text' => 'Persentase dihitung dari lebar seluruh jendela browser.'],
                    ['id' => 'c', 'text' => 'Karena transform otomatis membalikkan arah sumbu koordinat kartu.'],
                    ['id' => 'd', 'text' => 'Agar elemen penanda pindah ke pojok kiri atas viewport.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Ujung bawah penanda pinpoint (jarum) tepat di (x,y) jika penanda digeser sebesar setengah lebarnya ke kiri dan seluruh tingginya ke atas.',
            ],
            'transform-origin: 0 0' => [
                'options' => [
                    ['id' => 'a', 'text' => '0 0 menggunakan pojok kiri atas sebagai titik acuan transformasi, sedangkan default (center) membesarkan elemen ke segala arah dari tengah.'],
                    ['id' => 'b', 'text' => '0 0 menonaktifkan zoom peta.'],
                    ['id' => 'c', 'text' => '0 0 membuat elemen tidak bisa digeser saat drag.'],
                    ['id' => 'd', 'text' => 'Keduanya menghasilkan posisi koordinat yang sama persis.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Peta harus menggunakan transform-origin: 0 0 agar perhitungan koordinat translate(tx, ty) scale(s) sesuai dengan sistem koordinat kartesius layar.',
            ],
            'transisi tidak jalan dari display: none' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Karena display adalah properti diskrit (tidak memiliki nilai perantara kontinu untuk diinterpolasi browser).'],
                    ['id' => 'b', 'text' => 'Karena display: flex tidak mendukung properti transition di browser Chromium.'],
                    ['id' => 'c', 'text' => 'Karena CSS transition hanya bisa dijalankan pada elemen teks.'],
                    ['id' => 'd', 'text' => 'Karena transition memerlukan JavaScript requestAnimationFrame.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Browser tidak bisa membuat animasi bertahap (interpolasi) antara "hilang dari DOM layout" (none) dan "muncul" (flex). Gunakan opacity dan visibility/transform.',
            ],
            'elemen absolute mengacu ke siapa' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Mengacu ke elemen induk terdekat yang memiliki properti position selain static (relative, absolute, atau fixed).'],
                    ['id' => 'b', 'text' => 'Selalu mengacu langsung ke tag body dokumen.'],
                    ['id' => 'c', 'text' => 'Selalu mengacu ke elemen saudara (sibling) di sebelahnya.'],
                    ['id' => 'd', 'text' => 'Mengacu ke posisi kursor mouse saat elemen dibuat.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Elemen absolute mencari containing block terdekat yang non-static. Jika tidak ditemukan, ia akan mengacu ke viewport dokumen.',
            ],

            // Level 3
            'b.push(3) mengubah a juga' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Karena array adalah tipe data referensi; variabel b hanya menyalin alamat memori yang sama dengan a, bukan menyalin isinya.'],
                    ['id' => 'b', 'text' => 'Karena fungsi push() di JavaScript selalu bekerja secara global.'],
                    ['id' => 'c', 'text' => 'Karena variabel a dan b harus selalu memiliki panjang yang sama.'],
                    ['id' => 'd', 'text' => 'Karena JavaScript tidak mendukung pembuatan array baru.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Array dan objek di JavaScript disimpan sebagai referensi memori. Untuk membuat salinan independen, gunakan operator spread: const b = [...a].',
            ],
            'objek berkunci id lebih baik daripada di array' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Pencarian, pembaruan, dan penghapusan data berdasarkan ID bernilai O(1) konstan (misal data[pinId]), tanpa perlu perulangan loop.'],
                    ['id' => 'b', 'text' => 'Karena array tidak bisa menyimpan objek JSON.'],
                    ['id' => 'c', 'text' => 'Karena objek berkunci ID otomatis terurut berdasarkan abjad.'],
                    ['id' => 'd', 'text' => 'Karena array memakan memori sepuluh kali lipat lebih besar.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Mengakses data dengan kunci ID (seperti pinpoint[id]) langsung menunjuk ke lokasi data secara instan tanpa perlu findIndex atau loop.',
            ],
            'json.parse menerima teks yang bukan json' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Akan melempar SyntaxError yang mematikan eksekusi script; dicegah dengan membungkusnya dalam blok try...catch.'],
                    ['id' => 'b', 'text' => 'Otomatis mengembalikan nilai null tanpa error.'],
                    ['id' => 'c', 'text' => 'Otomatis mengubah teks menjadi string biasa.'],
                    ['id' => 'd', 'text' => 'Browser akan me-reload halaman secara otomatis.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'JSON.parse melempar exception fatal bila format string tidak valid (misal data localStorage rusak). Selalu bungkus dalam try { JSON.parse(str) } catch (e) { ... }.',
            ],

            // Level 4
            'tombol di dalam kartu tidak memanggil stoppropagation' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Event klik akan terus naik (bubbling) ke elemen kartu dan memicu event listener milik kartu secara tidak sengaja.'],
                    ['id' => 'b', 'text' => 'Tombol tidak akan bisa diklik sama sekali.'],
                    ['id' => 'c', 'text' => 'Kartu akan otomatis terhapus dari DOM.'],
                    ['id' => 'd', 'text' => 'Browser akan menampilkan dialog konfirmasi error.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Event bubbling membuat event merambat ke induk. stopPropagation() mencegah event diteruskan ke penampung kartu.',
            ],
            'listener drag dipasang di window' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Agar gerakan mouse tetap terlacak meskipun kursor bergerak sangat cepat keluar dari kotak elemen saat proses drag berlangsung.'],
                    ['id' => 'b', 'text' => 'Karena elemen div biasa tidak memiliki event mousemove.'],
                    ['id' => 'c', 'text' => 'Karena window adalah satu-satunya objek yang mendukung pan.'],
                    ['id' => 'd', 'text' => 'Agar event drag bisa dibagikan ke tab browser lain.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Jika dipasang hanya pada elemen, kursor yang meluncur cepat keluar batas kotak akan kehilangan tracking sehingga drag "macet".',
            ],
            'render() menghapus semua lalu menggambar ulang' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Memastikan tampilan UI selalu sinkron 100% dengan satu sumber kebenaran (state data), menghindari inkonsistensi DOM.'],
                    ['id' => 'b', 'text' => 'Karena browser melarang pengubahan satu elemen tunggal.'],
                    ['id' => 'c', 'text' => 'Agar animasi CSS berjalan lebih lambat.'],
                    ['id' => 'd', 'text' => 'Karena menghapus semua elemen mempercepat kerja database.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Pola data dulu, tampilan kemudian menjamin tidak ada glitch atau elemen "hantu" yang tertinggal saat data diperbarui atau dihapus.',
            ],
            'data diubah, tetapi lupa disimpan' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Semua perubahan yang ada di variabel memori akan hilang dan aplikasi kembali ke kondisi data lama saat halaman direfresh.'],
                    ['id' => 'b', 'text' => 'Browser otomatis menyimpan variabel JavaScript ke hard disk.'],
                    ['id' => 'c', 'text' => 'Aplikasi akan menampilkan layar putih error.'],
                    ['id' => 'd', 'text' => 'Data otomatis tersimpan di cache history browser.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Variabel JavaScript hanya hidup di memori tab aktif. Tanpa localStorage.setItem(), reload akan menghapus semua perubahan.',
            ],
            'backspace di kolom isian bisa tanpa sengaja' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Jika pintasan keyboard penghapusan mendengarkan keydown global tanpa memeriksa apakah document.activeElement sedang berada di input.'],
                    ['id' => 'b', 'text' => 'Karena Backspace adalah tombol terlarang di HTML5.'],
                    ['id' => 'c', 'text' => 'Karena form otomatis menghapus pinpoint saat input diedit.'],
                    ['id' => 'd', 'text' => 'Karena browser salah mengenali tombol Backspace sebagai tombol Delete.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Selalu periksa if (["INPUT", "TEXTAREA"].includes(document.activeElement.tagName)) return; sebelum menjalankan shortcut hapus.',
            ],

            // Level 5
            'createelement(\'line\') tidak menampilkan apa-apa' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Elemen SVG harus dibuat dengan document.createElementNS memakai namespace SVG (http://www.w3.org/2000/svg) agar dikenali browser sebagai SVG element.'],
                    ['id' => 'b', 'text' => 'Karena garis SVG tidak boleh dibuat dari JavaScript.'],
                    ['id' => 'c', 'text' => 'Karena elemen line memerlukan CSS display: block.'],
                    ['id' => 'd', 'text' => 'Karena createElement hanya bisa membuat elemen div dan span.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Tanpa namespace XML SVG, browser memperlakukannya sebagai HTMLUnknownElement yang tidak dapat dirender secara grafis.',
            ],
            'beda viewbox dengan width dan height' => [
                'options' => [
                    ['id' => 'a', 'text' => 'width dan height menentukan ukuran fisik tampilan di layar, sedangkan viewBox mendefinisikan sistem koordinat internal di dalam gambar SVG.'],
                    ['id' => 'b', 'text' => 'viewBox hanya mengatur warna background SVG.'],
                    ['id' => 'c', 'text' => 'width dan height mengatur koordinat, viewBox mengatur ukuran pixel layar.'],
                    ['id' => 'd', 'text' => 'viewBox tidak berpengaruh pada penskalaan vektor SVG.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'viewBox="minX minY w h" memungkinkan grafik vektor diskalakan secara presisi dan responsif ke ukuran fisik width/height apa pun.',
            ],
            'fungsi pointer-events: none pada svg tumpukan' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Membuat kanvas SVG tembus klik ke peta di bawahnya, sementara pointer-events: stroke pada garis membuat hanya garisnya saja yang dapat diklik.'],
                    ['id' => 'b', 'text' => 'Menyembunyikan SVG dari pandangan pengguna.'],
                    ['id' => 'c', 'text' => 'Mematikan seluruh animasi SVG di halaman.'],
                    ['id' => 'd', 'text' => 'Mengubah garis SVG menjadi teks transparan.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Overlay SVG transparan yang menumpuk peta akan memblokir klik ke peta jika pointer-events tidak diatur ke none pada container dan stroke pada garis.',
            ],
            'perlu garis transparan yang lebih tebal' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Garis koneksi tipis (3px) sulit diklik dengan kursor; garis transparan tebal (15-20px) bertindak sebagai hit-area yang ramah pengguna.'],
                    ['id' => 'b', 'text' => 'Agar garis tipis tidak terhapus oleh anti-aliasing browser.'],
                    ['id' => 'c', 'text' => 'Untuk menyimpan data JSON rute di dalam garis.'],
                    ['id' => 'd', 'text' => 'Karena browser melarang garis SVG berukuran kurang dari 5px.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Teknik invisible thick hit-box membuat pengguna mudah mengklik atau hover garis tipis tanpa harus mengepaskan kursor pixel demi pixel.',
            ],
            'sumbu y di svg terbalik' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Sistem koordinat grafis komputer standar menempatkan (0,0) di pojok kiri atas dengan nilai y positif mengarah ke bawah layar.'],
                    ['id' => 'b', 'text' => 'Karena adanya bug warisan pada spesifikasi W3C tahun 1999.'],
                    ['id' => 'c', 'text' => 'Karena SVG dikhususkan hanya untuk orientasi teks Arab.'],
                    ['id' => 'd', 'text' => 'Agar garis tidak bertabrakan dengan header dokumen.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Semua sistem koordinat layar komputer (screen space dan SVG) menghitung y positif ke arah bawah layar, berbeda dari kuadran I matematika.',
            ],

            // Level 6
            'transform-origin: 0 0 dipakai' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Agar penskalaan zoom dan pergeseran pan bertumpu pada titik asal (0,0) sehingga rumus konversi matematika sx = tx + mx*s valid dan konsisten.'],
                    ['id' => 'b', 'text' => 'Supaya peta tidak bisa di-scroll oleh keyboard.'],
                    ['id' => 'c', 'text' => 'Agar peta otomatis berputar 90 derajat.'],
                    ['id' => 'd', 'text' => 'Karena browser melarang nilai transform-origin selain 0 0.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Jika transform-origin memakai default (center), titik tengah container ikut bergeser secara non-linier dan merusak kalkulasi koordinat kursor.',
            ],
            'rumus zoom ke arah kursor' => [
                'options' => [
                    ['id' => 'a', 'text' => 'tx2 = sx - (sx - tx) * (sBaru / sLama) dan ty2 = sy - (sy - ty) * (sBaru / sLama).'],
                    ['id' => 'b', 'text' => 'tx2 = tx * sBaru dan ty2 = ty * sBaru.'],
                    ['id' => 'c', 'text' => 'tx2 = sx + tx dan ty2 = sy + ty.'],
                    ['id' => 'd', 'text' => 'tx2 = (sx - tx) / sBaru.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Rumus ini menjamin titik peta (mx, my) yang berada tepat di bawah kursor tetap berada di bawah kursor (sx, sy) setelah zoom berlangsung.',
            ],
            'klik ganda tidak boleh memakai clientx dan clienty langsung' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Karena clientX dan clientY berada di sistem koordinat layar; harus dikonversi ke koordinat peta dengan rumus (sx - tx) / s agar posisi pinpoint akurat.'],
                    ['id' => 'b', 'text' => 'Karena clientX dan clientY bernilai acak di setiap browser.'],
                    ['id' => 'c', 'text' => 'Karena klik ganda hanya menghasilkan koordinat integer negatif.'],
                    ['id' => 'd', 'text' => 'Karena pinpoint harus selalu berada di titik (0,0).'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Ketika peta digeser (pan) atau dizoom, posisi pixel layar bukan lagi posisi sebenarnya di permukaan peta SVG.',
            ],
            'popup ditaruh di body' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Agar popup tidak ikut membesar, mengecil, atau terpotong saat peta di-pan dan di-zoom di dalam wrapper.'],
                    ['id' => 'b', 'text' => 'Karena popup dilarang memiliki styling CSS.'],
                    ['id' => 'c', 'text' => 'Agar popup otomatis menutup jendela browser.'],
                    ['id' => 'd', 'text' => 'Karena elemen SVG tidak bisa memiliki anak tag div.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Elemen UI overlay (popup, panel) harus memiliki ukuran dan posisi tetap relatif terhadap layar (viewport), bukan di dalam elemen yang tertransformasi.',
            ],
            'skala cover dan fit' => [
                'options' => [
                    ['id' => 'a', 'text' => 'cover (Math.max) memenuhi seluruh layar tanpa ruang kosong (peta bisa terpotong); fit (Math.min) menampilkan seluruh peta (bisa ada ruang kosong).'],
                    ['id' => 'b', 'text' => 'cover digunakan untuk zoom out, fit digunakan untuk zoom in.'],
                    ['id' => 'c', 'text' => 'fit hanya bekerja untuk gambar PNG bukan peta SVG.'],
                    ['id' => 'd', 'text' => 'Keduanya menghasilkan perbesaran skala yang identik.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Modul LKS menuntut peta memenuhi layar penuh tanpa celah kosong saat awal muat, sehingga skala awal menggunakan perhitungan cover.',
            ],

            // Level 7
            'beda bfs dan dfs' => [
                'options' => [
                    ['id' => 'a', 'text' => 'BFS menjelajah lapis demi lapis memakai Queue (FIFO) untuk jalur terpendek sisi; DFS menjelajah sedalam mungkin memakai Stack/Rekursi untuk mencari semua kombinasi jalur.'],
                    ['id' => 'b', 'text' => 'BFS hanya untuk graph berarah sedangkan DFS hanya untuk graph tidak berarah.'],
                    ['id' => 'c', 'text' => 'BFS memakai rekursi sedangkan DFS memakai loop while antrean.'],
                    ['id' => 'd', 'text' => 'Tidak ada perbedaan, keduanya menghasilkan urutan simpul yang sama persis.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'BFS cocok untuk mencari satu jalur tersingkat jumlah langkah, sedangkan DFS dengan backtracking digunakan untuk menemukan seluruh alternatif rute.',
            ],
            'kenapa modul ini memakai dfs' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Karena soal meminta daftar beberapa alternatif rute (maksimal 10) yang dapat diurutkan tercepat atau termurah, sehingga semua jalur harus ditemukan terlebih dahulu.'],
                    ['id' => 'b', 'text' => 'Karena algoritma BFS tidak didukung oleh bahasa JavaScript.'],
                    ['id' => 'c', 'text' => 'Karena DFS menjamin durasi waktu tercepat tanpa perlu menghitung kecepatan.'],
                    ['id' => 'd', 'text' => 'Karena graph peta Indonesia tidak memiliki siklus.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Rute tersedikit sisi belum tentu tercepat (karena ada faktor kecepatan moda pesawat vs bus), maka seluruh jalur dikumpulkan dengan DFS sebelum diurutkan.',
            ],
            'fungsi baris backtrack' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Mengeluarkan simpul dari path dan menghapusnya dari visited agar simpul tersebut dapat dilewati kembali oleh cabang jalur penelusuran lain.'],
                    ['id' => 'b', 'text' => 'Menghapus simpul dari database secara permanen.'],
                    ['id' => 'c', 'text' => 'Membalikkan arah graph dari tujuan ke awal.'],
                    ['id' => 'd', 'text' => 'Menghentikan seluruh proses pencarian rute secara mendadak.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Tanpa backtracking (visited.delete(tetangga)), simpul yang sudah dikunjungi di satu jalur akan terkunci selamanya dan cabang rute alternatif lain gagal ditemukan.',
            ],
            'path perlu disalin sebelum disimpan' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Karena path adalah array referensi yang terus dimutasi (push/pop). Tanpa salinan [...path], semua rute yang tersimpan akan mengacu ke array yang sama dan akhirnya kosong.'],
                    ['id' => 'b', 'text' => 'Agar array path otomatis dikonversi menjadi huruf kapital.'],
                    ['id' => 'c', 'text' => 'Karena JavaScript melarang penyimpanan array asli.'],
                    ['id' => 'd', 'text' => 'Untuk mencegah browser crash karena out of memory.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Ini adalah jebakan referensi dari Level 3. Selalu simpan salinan hasil.push([...path]) bukan referensi hasil.push(path).',
            ],
            'sisi dimasukkan ke dua arah' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Karena koneksi antar pinpoint bersifat tidak berarah (undirected); perjalanan dapat dilakukan bolak-balik (A ke B dan B ke A).'],
                    ['id' => 'b', 'text' => 'Untuk menggandakan biaya tarif perjalanan.'],
                    ['id' => 'c', 'text' => 'Karena struktur graph adjacency list hanya bisa dibaca dari kanan ke kiri.'],
                    ['id' => 'd', 'text' => 'Agar rute otomatis berbentuk lingkaran penuh.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Graph tidak berarah membutuhkan pencatatan adjacency timbal balik: graph[from].push(to) dan graph[to].push(from).',
            ],
            'visited pada dfs pencari-semua-jalur berlaku per jalur' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Agar simpul yang sudah dipakai di Jalur 1 bisa digunakan kembali di Jalur 2 yang melewati cabang berbeda tanpa menyebabkan perulangan siklus tak terbatas di jalur yang sama.'],
                    ['id' => 'b', 'text' => 'Karena memori browser hanya mampu menampung satu simpul.'],
                    ['id' => 'c', 'text' => 'Karena variabel visited bersifat konstanta lokal fungsi.'],
                    ['id' => 'd', 'text' => 'Agar hanya simpul tujuan yang ditandai sebagai dikunjungi.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Di BFS visited berlaku global karena hanya mencari 1 jalur terpendek. Di DFS all-paths, visited hanya mencegah siklus dalam 1 lintasan aktif.',
            ],
            'rumus durasi dan biaya satu rute' => [
                'options' => [
                    ['id' => 'a', 'text' => 'durasi = total(jarak_sisi / kecepatan_moda) dan biaya = total(jarak_sisi * tarif_per_km_moda).'],
                    ['id' => 'b', 'text' => 'durasi = total jarak * total kecepatan dan biaya = total jarak / tarif.'],
                    ['id' => 'c', 'text' => 'durasi = jumlah simpul * 60 menit dan biaya = flat Rp50.000.'],
                    ['id' => 'd', 'text' => 'durasi dan biaya dihitung acak oleh fungsi Math.random().'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Setiap segmen rute memiliki jarak dan moda transportasi masing-masing yang diakumulasikan untuk memperoleh total durasi dan biaya.',
            ],

            // Level 8
            'validasi harus dipanggil juga saat data berubah' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Karena penambahan atau penghapusan pinpoint bisa membuat input asal/tujuan yang tadinya valid menjadi invalid (misal pinpoint yang dipilih baru saja dihapus).'],
                    ['id' => 'b', 'text' => 'Agar browser tidak perlu menyimpan data di localStorage.'],
                    ['id' => 'c', 'text' => 'Hanya untuk memperlambat respon tombol submit.'],
                    ['id' => 'd', 'text' => 'Karena event input tidak bisa dipicu oleh keyboard.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'State form harus selalu divalidasi ulang saat koleksi data berubah agar tombol Search tidak dapat diklik dengan data usang.',
            ],
            'backdrop-filter tidak terlihat pada latar yang solid' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Efek blur mengaburkan elemen visual yang ada di belakangnya; jika latarnya 100% solid (tidak tembus pandang), tidak ada elemen belakang yang bisa diburamkan.'],
                    ['id' => 'b', 'text' => 'Karena browser menonaktifkan GPU pada warna solid.'],
                    ['id' => 'c', 'text' => 'Karena CSS backdrop-filter hanya berlaku untuk teks.'],
                    ['id' => 'd', 'text' => 'Karena latar solid otomatis menghapus properti filter.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Efek kaca (glassmorphism) mensyaratkan background semi-transparan (misal rgba(255, 255, 255, 0.75)) agar elemen di belakangnya terlihat diburamkan.',
            ],
            'panel perlu penjaga (closest) di event peta' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Agar klik atau interaksi di dalam panel input tidak merambat dan memicu aksi peta seperti pan geser atau klik ganda tambah pinpoint di bawahnya.'],
                    ['id' => 'b', 'text' => 'Untuk mengunci ukuran tinggi panel.'],
                    ['id' => 'c', 'text' => 'Supaya panel tidak bisa digeser keluar layar.'],
                    ['id' => 'd', 'text' => 'Karena elemen panel tidak memiliki z-index.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'Periksa if (e.target.closest("#panel")) return; pada event mousedown dan dblclick peta agar interaksi di form tidak mengganggu kanvas peta.',
            ],
            'pointer-events: none pada teks petunjuk' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Agar label petunjuk (hint) di bawah layar tembus klik, sehingga pengguna tetap bisa mengklik titik peta yang berada tepat di belakang label tersebut.'],
                    ['id' => 'b', 'text' => 'Agar teks petunjuk tidak bisa dibaca oleh pengguna.'],
                    ['id' => 'c', 'text' => 'Untuk mengubah warna teks menjadi transparan.'],
                    ['id' => 'd', 'text' => 'Karena teks tidak boleh menerima seleksi mouse.'],
                ],
                'correct_answer' => 'a',
                'explanation' => 'pointer-events: none membuat elemen visual informatif tidak menghalangi interaksi mouse pengguna ke objek di bawahnya.',
            ],
        ];
    }
}
