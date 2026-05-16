<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Museum')</title>

    @vite('resources/css/app.css')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .museum-title {
            font-family: 'Cormorant Garamond', serif;
        }
    </style>
</head>

<body class="bg-[#f8f6f2] text-black">

    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    {{-- Upgrade Alert Menggunakan SA2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))

        <script>

            Swal.fire({

                toast: true,
                position: 'top-end',

                icon: 'success',
                title: '{{ session('success') }}',

                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,

                background: '#111',
                color: '#fff',

            });

        </script>

    @endif

    @if(session('success'))

        <script>

            Swal.fire({

                icon: 'success',

                title: 'Reservation Confirmed',

                text: '{{ session('success') }}',

                confirmButtonColor: '#000',

                background: '#ffffff',

            });

        </script>

    @endif



</body>

</html>