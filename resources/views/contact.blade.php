<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Museum</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0d0d0d;
            color: white;
        }

        .title-font {
            font-family: 'Cormorant Garamond', serif;
        }
    </style>
</head>
<body>
    @include('components.navbar')

    <section class="min-h-screen flex items-center justify-center px-6 py-12">

        <div class="w-full max-w-5xl grid md:grid-cols-2 bg-[#151515] border border-white/10 rounded-3xl overflow-hidden shadow-2xl">

            <!-- LEFT -->
            <div class="bg-[#111111] p-10 md:p-14 flex flex-col justify-between">

                <div>
                    <p class="uppercase tracking-[0.3em] text-sm text-gray-500 mb-5">
                        Kontak Museum
                    </p>

                    <h1 class="title-font text-5xl md:text-6xl leading-tight mb-6">
                        Biar Karya<br>
                        Berbicara.
                    </h1>

                    <p class="text-gray-400 leading-relaxed">
                        Punya pertanyaan tentang pameran, tiket, atau kolaborasi? Hubungi tim museum kami. Umat manusia terus menciptakan bentuk untuk segala hal. Perilaku yang benar-benar menginspirasi.
                    </p>
                </div>

                <div class="mt-10 text-sm text-gray-500 space-y-2">
                    <p>Email : alphaseum@email.com</p>
                    <p>Nomor Telepon : +62 812 3456 7890</p>
                    <p>Lokasi : Medan, Indonesia</p>
                </div>

            </div>

            <!-- RIGHT -->
            <div class="p-10 md:p-14">

                <form
                    class="space-y-6"
                    onsubmit="showAlert(event)"
                >

                    <!-- NAME -->
                    <div>
                        <label class="block mb-2 text-sm text-gray-300">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            placeholder="Nama Lengkapmu"
                            required
                            class="w-full bg-transparent border border-white/10 px-5 py-4 rounded-xl focus:outline-none focus:border-white/40 transition"
                        >
                    </div>

                    <!-- EMAIL -->
                    <div>
                        <label class="block mb-2 text-sm text-gray-300">
                            Email
                        </label>

                        <input
                            type="email"
                            placeholder="contohemail@email.com"
                            required
                            class="w-full bg-transparent border border-white/10 px-5 py-4 rounded-xl focus:outline-none focus:border-white/40 transition"
                        >
                    </div>

                    <!-- MESSAGE -->
                    <div>
                        <label class="block mb-2 text-sm text-gray-300">
                            Pesan
                        </label>

                        <textarea
                            rows="5"
                            placeholder="Tulis pesanmu..."
                            required
                            class="w-full bg-transparent border border-white/10 px-5 py-4 rounded-xl focus:outline-none focus:border-white/40 transition resize-none"
                        ></textarea>
                    </div>

                    <!-- BUTTON -->
                    <button
                        type="submit"
                        class="w-full bg-white text-black py-4 rounded-xl font-medium hover:bg-gray-200 transition duration-300"
                    >
                        Kirim Pesan
                    </button>

                </form>

            </div>

        </div>

    </section>

    <script>
    function showAlert(event) {
        event.preventDefault();

        alert("Pesanmu Sudah Kami Terima, Terimakasih Sudah Menghubungi Kami");

        event.target.reset();
    }
</script>

</body>
</html>