<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        @if($selectedCategory)
        Galeri Foto {{ $selectedCategory->name }} | Cheche Catering
        @else
        Galeri Foto Makanan & Minuman | Cheche Catering
        @endif
    </title>
    <meta name="description" content="
        @if($selectedCategory)
            Lihat koleksi galeri foto makanan dan minuman {{ strtolower($selectedCategory->name) }} dari Cheche Catering. Inspirasi visual untuk acara Anda di Purwokerto.
        @else
            Jelajahi galeri foto lengkap makanan dan minuman dari Cheche Catering. Dapatkan inspirasi untuk menu katering pernikahan, event, dan pesta di Purwokerto.
        @endif
    ">
    <meta name="keywords" content="
        @if($selectedCategory)
            galeri foto {{ strtolower($selectedCategory->name) }} katering, foto makanan {{ strtolower($selectedCategory->name) }}, Cheche Catering Purwokerto, gambar catering
        @else
            galeri foto makanan, galeri minuman, katering Purwokerto, Cheche Catering, foto katering, inspirasi menu
        @endif
    ">
    <meta name="author" content="Cheche Catering" />

    <link rel="icon" href="{{ asset('assets/images/logo/logo.png') }}" type="image/x-icon" />
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:title" content="
        @if($selectedCategory)
            Galeri Foto {{ $selectedCategory->name }} | Cheche Catering
        @else
            Galeri Foto Makanan & Minuman | Cheche Catering
        @endif
    " />
    <meta property="og:description" content="
        @if($selectedCategory)
            Lihat koleksi galeri foto makanan dan minuman {{ strtolower($selectedCategory->name) }} dari Cheche Catering. Inspirasi visual untuk acara Anda di Purwokerto.
        @else
            Jelajahi galeri foto lengkap makanan dan minuman dari Cheche Catering. Dapatkan inspirasi untuk menu katering pernikahan, event, dan pesta di Purwokerto.
        @endif
    " />
    <meta property="og:image" content="{{ asset('assets/images/og-gallery-image.jpg') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="
        @if($selectedCategory)
            Galeri Foto {{ $selectedCategory->name }} | Cheche Catering
        @else
            Galeri Foto Makanan & Minuman | Cheche Catering
        @endif
    " />
    <meta name="twitter:description" content="
        @if($selectedCategory)
            Temukan inspirasi visual menu katering {{ strtolower($selectedCategory->name) }} terbaik dari Cheche Catering di galeri kami.
        @else
            Lihat koleksi foto makanan dan minuman katering Cheche Catering yang menggugah selera untuk acara Anda di Purwokerto.
        @endif
    " />
    <meta name="twitter:image" content="{{ asset('assets/images/og-gallery-image.jpg') }}" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;600&family=Satisfy:wght@400&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-red: #e74c3c;
            --primary-red-hover: #f79f95;
            --primary-red-active: #c0392b;
            --text-gray: #374151;
            --light-gray: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 80px;
            /* Account for fixed navbar */
        }

        /* Gallery Styles */
        .gallery-header {
            margin-bottom: 2rem;
        }

        .gallery-header h2 span {
            color: var(--primary-red);
            font-family: 'Pacifico', cursive;
        }

        .gallery-filter {
            position: relative;
        }

        .gallery-filter select {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 140 140' width='14' height='14' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolygon points='0,0 140,0 70,140' fill='%23e74c3c'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 12px;
            color: var(--primary-red);
            border: 2px solid #e5e7eb;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .gallery-filter select:hover,
        .gallery-filter select:focus {
            background-color: #fceae8;
            border-color: var(--primary-red-active);
            box-shadow: 0 0 12px rgba(231, 76, 60, 0.4);
            transform: scale(1.02);
            outline: none;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .gallery-item {
            position: relative;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            transform: translateY(20px);
            animation: slideInUp 0.6s ease forwards;
        }

        .gallery-item:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-item::after {
            content: "🔍 Klik untuk perbesar";
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: rgba(231, 76, 60, 0.9);
            color: white;
            font-size: 0.75rem;
            padding: 6px 10px;
            border-radius: 6px;
            opacity: 0;
            transition: all 0.3s ease;
            transform: translateY(10px);
            font-weight: 500;
        }

        .gallery-item:hover::after {
            opacity: 1;
            transform: translateY(0);
        }

        /* Lightbox Styles */
        .lightbox {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(5px);
        }

        .lightbox.show {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .lightbox-img {
            max-width: 90%;
            max-height: 80vh;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .lightbox-img.show {
            opacity: 1;
            transform: scale(1);
        }

        .close-lightbox {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.5);
        }

        .close-lightbox:hover {
            color: var(--primary-red);
            background: rgba(231, 76, 60, 0.2);
            transform: scale(1.1);
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            padding: 15px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-nav:hover {
            background: rgba(231, 76, 60, 0.8);
            transform: translateY(-50%) scale(1.1);
        }

        .prev {
            left: 20px;
        }

        .next {
            right: 20px;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1rem;
            }

            .gallery-header {
                flex-direction: column;
                gap: 1rem;
            }

            .gallery-filter {
                width: 100%;
            }

            .lightbox-nav {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .close-lightbox {
                width: 40px;
                height: 40px;
                font-size: 30px;
                top: 15px;
                right: 15px;
            }
        }

        @media (max-width: 640px) {
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }

            .gallery-item img {
                height: 180px;
            }

            .prev {
                left: 10px;
            }

            .next {
                right: 10px;
            }
        }

        @media (max-width: 480px) {
            .gallery-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .gallery-item img {
                height: 150px;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            opacity: 0.5;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navbar (preserved) -->
    @include('partials.navbar')

    <!-- Gallery Section -->
    <section class="py-8 lg:py-12" id="gallery">
        <div class="container mx-auto px-4 max-w-7xl">
            <!-- Gallery Header -->
            <div class="gallery-header flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="mb-4 lg:mb-0">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-gray-800">
                        <span>Gallery</span> - {{ $selectedCategory?->name ?? 'All Categories' }}
                    </h2>
                    <p class="text-gray-600 mt-2">Koleksi foto makanan dan minuman terbaik kami</p>
                </div>
                <div class="gallery-filter w-full lg:w-auto lg:min-w-[250px]">
                    <form method="GET" action="{{ route('gallery') }}">
                        <select name="category"
                            class="form-select w-full rounded-full px-4 py-3 font-medium transition-all duration-300"
                            onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ $selectedSlug === $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="gallery-grid" id="gallery-grid">
                @forelse($galleries as $index => $gallery)
                <div class="gallery-item"
                    style="animation-delay: {{ $index * 0.1 }}s;"
                    onclick="openLightbox('{{ asset('storage/' . $gallery->image) }}', {{ $index }})">
                    <img src="{{ asset('storage/' . $gallery->image) }}"
                        alt="{{ $gallery->name }}"
                        loading="lazy">
                </div>
                @empty
                <div class="col-span-full">
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium mb-2">Belum ada gambar</h3>
                        <p>Belum ada gambar pada kategori ini.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <span class="close-lightbox" onclick="closeLightbox()">&times;</span>
        <img src="" alt="Preview" class="lightbox-img" id="lightbox-img">
        <div class="lightbox-nav prev" onclick="prevImage()">&#10094;</div>
        <div class="lightbox-nav next" onclick="nextImage()">&#10095;</div>
    </div>

    <!-- Footer (preserved) -->
    @include('partials.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        let currentImageIndex = 0;
        let galleryImages = [];

        // Initialize gallery
        document.addEventListener('DOMContentLoaded', function() {
            initializeGallery();
            setupIntersectionObserver();
        });

        function initializeGallery() {
            const galleryItems = document.querySelectorAll('.gallery-item img');
            galleryImages = Array.from(galleryItems).map(img => img.src);
        }

        function setupIntersectionObserver() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.gallery-item').forEach((item, index) => {
                item.style.animationPlayState = 'paused';
                observer.observe(item);
            });
        }

        // Lightbox functions
        function openLightbox(imageSrc, imageIndex) {
            currentImageIndex = imageIndex;
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightbox-img');

            lightbox.classList.add('show');
            lightboxImg.src = imageSrc;

            setTimeout(() => {
                lightboxImg.classList.add('show');
            }, 50);

            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightbox-img');

            lightboxImg.classList.remove('show');

            setTimeout(() => {
                lightbox.classList.remove('show');
                document.body.style.overflow = 'auto';
            }, 300);
        }

        function prevImage() {
            if (galleryImages.length === 0) return;
            currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : galleryImages.length - 1;
            changeImage();
        }

        function nextImage() {
            if (galleryImages.length === 0) return;
            currentImageIndex = currentImageIndex < galleryImages.length - 1 ? currentImageIndex + 1 : 0;
            changeImage();
        }

        function changeImage() {
            const lightboxImg = document.getElementById('lightbox-img');
            lightboxImg.classList.remove('show');

            setTimeout(() => {
                lightboxImg.src = galleryImages[currentImageIndex];
                lightboxImg.classList.add('show');
            }, 200);
        }

        // Event listeners
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function(e) {
            const lightbox = document.getElementById('lightbox');
            if (lightbox.classList.contains('show')) {
                switch (e.key) {
                    case 'ArrowLeft':
                        prevImage();
                        break;
                    case 'ArrowRight':
                        nextImage();
                        break;
                    case 'Escape':
                        closeLightbox();
                        break;
                }
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (navLinks && navLinks.classList.contains('show') &&
                !navLinks.contains(e.target) &&
                !menuToggle.contains(e.target)) {
                navLinks.classList.remove('show');
            }
        });
    </script>
</body>

</html>