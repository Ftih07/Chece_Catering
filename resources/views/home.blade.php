<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cheche Catering - Jasa Katering Pernikahan & Acara di Purwokerto</title>
    <meta name="description" content="Cheche Catering menyediakan jasa katering pernikahan, ulang tahun, meeting, dan event lainnya di Purwokerto. Sajian lezat, higienis, dan pelayanan profesional sejak 2006.">
    <meta name="keywords" content="katering Purwokerto, catering pernikahan Purwokerto, catering ulang tahun, catering event, catering nasi box, snack box Purwokerto, jasa boga Purwokerto, Cheche Catering">
    <meta name="author" content="Cheche Catering" />

    <link rel="icon" href="{{ asset('assets/images/logo/logo.png') }}" type="image/x-icon" />
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:title" content="Cheche Catering: Katering Terbaik untuk Pernikahan & Acara Anda di Purwokerto" />
    <meta property="og:description" content="Cheche Catering menyediakan jasa katering pernikahan, ulang tahun, meeting, dan event lainnya di Purwokerto. Sajian lezat, higienis, dan pelayanan profesional sejak 2006." />
    <meta property="og:image" content="{{ asset('assets/images/home/meta/tumbnail-home.png') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Cheche Catering: Katering Terbaik untuk Pernikahan & Acara Anda di Purwokerto" />
    <meta name="twitter:description" content="Cari katering di Purwokerto? Cheche Catering hadir dengan menu lezat, higienis, dan pelayanan profesional untuk segala acara Anda. Pesan sekarang!" />
    <meta name="twitter:image" content="{{ asset('assets/images/home/meta/tumbnail-home.png') }}" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS Animation Library -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet" />

    <!-- Custom Styles -->
    <style>
        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        .font-satisfy {
            font-family: 'Satisfy', cursive;
        }

        .hero-text-shadow {
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(231, 76, 60, 0.3);
        }

        .card-hover:active {
            transform: translateY(-2px) scale(0.98);
            box-shadow: 0 8px 20px rgba(231, 76, 60, 0.4);
        }

        .pulse-glow {
            animation: pulse-glow 2s infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(231, 76, 60, 0.3);
            }

            50% {
                box-shadow: 0 0 30px rgba(231, 76, 60, 0.6);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bounce-in {
            animation: bounceIn 0.6s ease-out;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }

            50% {
                transform: scale(1.05);
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .social-icon {
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .social-icon:hover {
            transform: scale(1.15) rotate(8deg);
            background: linear-gradient(135deg, #f77162, #e74c3c);
            box-shadow: 0 8px 25px rgba(248, 113, 29, 0.6);
        }

        .social-icon:active {
            transform: scale(0.95);
            filter: brightness(0) invert(1);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #ff6b6b, #e74c3c);
        }

        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        html {
            scroll-behavior: smooth;
        }

        section[id] {
            scroll-margin-top: 100px;
        }

        .hero-overlay {
            background: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3));
            position: absolute;
            inset: 0;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-white text-gray-800">

    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Hero -->
    <section class="hero mt-24 mb-16 px-4" id="home">
        <div class="position-relative text-center overflow-hidden rounded-xl">
            <img src="assets/images/home/hero/hero-images.png" alt="Enjoy the Food" class="w-100 h-auto rounded-xl" />
            <div class="hero-overlay"></div>
            <h1 class="position-absolute top-1/3 w-100 text-white font-satisfy hero-text-shadow bounce-in"
                style="font-size: clamp(2rem, 5vw, 3rem);" data-aos="fade-down" data-aos-delay="300">
                Enjoy The Food
            </h1>
        </div>
    </section>

    <!-- Menu -->
    <section class="menu mb-16 px-4" id="menu">
        <div class="text-center" data-aos="fade-up">
            <h2 class="text-center text-red-500 font-satisfy mb-12 pulse-glow rounded-full mx-auto py-2 px-6 inline-block"
                style="font-size: 2rem;" data-aos="fade-up">Menu</h2>
        </div>
        <div class="container-fluid">
            <div class="row g-4 justify-content-center">
                @php
                use Illuminate\Support\Facades\Storage;
                @endphp

                @foreach ($menuCategories as $category)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                    <a href="{{ route('menu', ['category' => $category->slug]) }}" class="text-decoration-none text-inherit">
                        <div class="card border-0 shadow-lg card-hover h-100 overflow-hidden">
                            <img src="{{ $category->thumbnail?->image ? Storage::url($category->thumbnail->image) : asset('assets/image/default.jpg') }}"
                                alt="{{ $category->name }}"
                                class="card-img-top object-cover"
                                style="height: 180px;" />
                            <div class="card-body text-center py-3">
                                <p class="font-satisfy text-xl mb-0 transition-colors duration-300">{{ $category->name }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Gallery -->
    <section class="gallery mb-16 px-4" id="gallery">
        <div class="text-center" data-aos="fade-up">
            <h2 class="text-center text-red-500 font-satisfy mb-12 pulse-glow rounded-full mx-auto py-2 px-6 inline-block"
                style="font-size: 2rem;" data-aos="fade-up">Gallery</h2>
        </div>

        <div class="container">
            <div class="row g-4 justify-content-center">
                @foreach($galleryCategories as $category)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3" data-aos="flip-left" data-aos-delay="{{ $loop->index * 150 }}">
                    <a href="{{ route('gallery', ['category' => $category->slug]) }}" class="text-decoration-none text-inherit">
                        <div class="card border-0 shadow-lg card-hover h-100 overflow-hidden">
                            <img src="{{ $category->galleries->first()?->image ? asset('storage/' . $category->galleries->first()->image) : asset('assets/image/default.jpg') }}"
                                alt="{{ $category->name }}"
                                class="card-img-top object-cover"
                                style="height: 180px;" />
                            <div class="card-body text-center py-3">
                                <p class="font-satisfy text-xl mb-0 transition-colors duration-300">{{ $category->name }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Order Section -->
    <section class="order py-16 px-4 bg-gray-50" id="order">
        <div class="text-center" data-aos="fade-up">
            <h2 class="text-center text-red-500 font-satisfy mb-12 pulse-glow rounded-full mx-auto py-2 px-6 inline-block"
                style="font-size: 2rem;" data-aos="fade-up">Order</h2>
        </div>

        <div class="container">
            <div class="row g-4 align-items-stretch">

                <!-- Left Column: Map + About -->
                <div class="col-12 col-lg-6" data-aos="fade-right">
                    <div class="d-flex flex-column gap-4 h-100">

                        <!-- Map -->
                        <div class="map-frame bg-white rounded-xl border-2 border-gray-300 overflow-hidden shadow-lg card-hover"
                            style="height: 320px;">
                            <iframe src="https://www.google.com/maps?q=cheche+catering&output=embed"
                                width="100%" height="100%" loading="lazy" allowfullscreen class="border-0">
                            </iframe>
                        </div>

                        <!-- About Us -->
                        <div class="card border-2 border-gray-300 shadow-lg card-hover h-30">
                            <div class="card-body">
                                <h3 class="text-red-500 font-satisfy mb-3 text-xl">About Us</h3>
                                <p class="text-sm text-gray-700 mb-0 leading-relaxed">
                                    Berdiri sejak 2006, Cheche Catering siap untuk memberikan pelayanan catering yang bermutu dengan standar cita rasa yang tinggi. Mengedepankan rasa yang berkualitas, makanan higienis, dan estetika makanan. Kami melayani acara pernikahan, khitan, ulang tahun, meeting, acara seminar, workshop dan lain lain. Kami juga melayani catering untuk anak sekolahan dan karyawan perkantoran melewati nasibox dan snackbox. Pemesanan lebih lanjut dapat menghubungi kontak yang tertera, dan media sosial kami.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Contact Info -->
                <div class="col-12 col-lg-6" data-aos="fade-left">
                    <div class="d-flex flex-column gap-4 h-100">

                        <!-- Office -->
                        <div class="card border-2 border-gray-300 shadow-lg card-hover">
                            <a href="https://maps.app.goo.gl/krGEXjrCUDrGT6iz5" target="_blank" class="text-decoration-none">
                                <div class="card-body">
                                    <strong class="text-red-500 font-weight-bold">Office:</strong><br />
                                    <span class="text-gray-700">CHECHE CATERING, Perum griya satria indah blok e 1, Karangmiri, Sumampir, Kec. Purwokerto Utara, Kabupaten Banyumas, Jawa Tengah</span>
                                </div>
                            </a>
                        </div>

                        <!-- Open Hours -->
                        <div class="card border-2 border-gray-300 shadow-lg card-hover">
                            <div class="card-body">
                                <strong class="text-red-500 font-weight-bold">Open Hours:</strong><br />
                                <span class="text-gray-700">Setiap hari, Pukul 08.00 - 18.00 WIB</span>
                            </div>
                        </div>

                        <!-- WhatsApp -->
                        <div class="card border-2 border-gray-300 shadow-lg card-hover">
                            <a href="https://wa.me/6285956777138" target="_blank" class="text-decoration-none">
                                <div class="card-body">
                                    <strong class="text-red-500 font-weight-bold">Telepon:</strong><br />
                                    <span class="text-gray-700">+62 859-5677-7138</span>
                                </div>
                            </a>
                        </div>

                        <!-- Email -->
                        <div class="card border-2 border-gray-300 shadow-lg card-hover">
                            <a href="mailto:chechecatering@gmail.com" class="text-decoration-none">
                                <div class="card-body">
                                    <strong class="text-red-500 font-weight-bold">Email:</strong><br />
                                    <span class="text-gray-700">chechecatering@gmail.com</span>
                                </div>
                            </a>
                        </div>

                        <!-- Social Media -->
                        <div class="card border-2 border-gray-300 shadow-lg card-hover">
                            <a href="https://lynk.id/cheche_catering" target="_blank" class="text-decoration-none">
                                <div class="card-body d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                                    <div class="text-center text-sm-start">
                                        <span class="text-gray-700 font-weight-medium">&copy; 2025 Cheche Catering. All rights reserved.</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/icon/website-icon.svg" alt="Website"
                                            class="social-icon rounded-circle p-2 bg-white shadow-md"
                                            style="height: 42px; width: 42px;" />
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Active nav link on scroll
        const sections = document.querySelectorAll('section[id]');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const top = window.scrollY;
                const offset = section.offsetTop - 150;
                const height = section.offsetHeight;
                const id = section.getAttribute('id');
                if (top >= offset && top < offset + height) {
                    current = id;
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });

        // Gallery select dropdown (if exists)
        const gallerySelect = document.getElementById("gallerySelect");
        if (gallerySelect) {
            gallerySelect.addEventListener("change", function() {
                const page = this.value;
                if (page) {
                    window.location.href = page;
                }
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add subtle parallax effect to hero
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero img');
            if (hero) {
                hero.style.transform = `translateY(${scrolled * 0.3}px)`;
            }
        });

        // Add loading animation
        window.addEventListener('load', () => {
            document.body.classList.add('fade-in-up');
        });
    </script>

    @include('partials.floating-order-button')

</body>

</html>