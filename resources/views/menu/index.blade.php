<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($selectedSubcategory)
        Menu {{ $selectedSubcategory->name }} | {{ $selectedCategory->name }} - Cheche Catering
        @elseif($selectedCategory)
        Menu {{ $selectedCategory->name }} | Cheche Catering
        @else
        Daftar Menu Katering Lengkap | Cheche Catering
        @endif
    </title>
    <meta name="description" content="
        @if($selectedSubcategory)
            Lihat semua pilihan menu {{ $selectedSubcategory->name }} dari kategori {{ $selectedCategory->name }} Cheche Catering di Purwokerto. Temukan sajian lezat untuk acara Anda.
        @elseif($selectedCategory)
            Jelajahi pilihan menu {{ $selectedCategory->name }} Cheche Catering yang bervariasi. Sajian katering premium untuk segala kebutuhan acara Anda di Purwokerto.
        @else
            Jelajahi daftar menu katering lengkap Cheche Catering di Purwokerto. Pilihan menu bervariasi untuk pernikahan, ulang tahun, meeting, dan event lainnya.
        @endif
    ">
    <meta name="keywords" content="
        @if($selectedSubcategory)
            menu {{ strtolower($selectedSubcategory->name) }}, {{ strtolower($selectedCategory->name) }} Cheche Catering, katering Purwokerto, daftar menu katering
        @elseif($selectedCategory)
            menu {{ strtolower($selectedCategory->name) }} Cheche Catering, katering Purwokerto, daftar menu katering
        @else
            daftar menu katering, menu Cheche Catering, katering Purwokerto, pilihan menu catering, harga catering
        @endif
    ">
    <meta name="author" content="Cheche Catering" />

    <link rel="icon" href="{{ asset('assets/images/logo/logo.png') }}" type="image/x-icon" />
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:title" content="
        @if($selectedSubcategory)
            Menu {{ $selectedSubcategory->name }} | {{ $selectedCategory->name }} - Cheche Catering
        @elseif($selectedCategory)
            Menu {{ $selectedCategory->name }} | Cheche Catering
        @else
            Daftar Menu Katering Lengkap | Cheche Catering
        @endif
    " />
    <meta property="og:description" content="
        @if($selectedSubcategory)
            Lihat semua pilihan menu {{ $selectedSubcategory->name }} dari kategori {{ $selectedCategory->name }} Cheche Catering di Purwokerto. Temukan sajian lezat untuk acara Anda.
        @elseif($selectedCategory)
            Jelajahi pilihan menu {{ $selectedCategory->name }} Cheche Catering yang bervariasi. Sajian katering premium untuk segala kebutuhan acara Anda di Purwokerto.
        @else
            Jelajahi daftar menu katering lengkap Cheche Catering di Purwokerto. Pilihan menu bervariasi untuk pernikahan, ulang tahun, meeting, dan event lainnya.
        @endif
    " />
    <meta property="og:image" content="{{ asset('assets/images/og-menu-image.jpg') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="
        @if($selectedSubcategory)
            Menu {{ $selectedSubcategory->name }} | {{ $selectedCategory->name }} - Cheche Catering
        @elseif($selectedCategory)
            Menu {{ $selectedCategory->name }} | Cheche Catering
        @else
            Daftar Menu Katering Lengkap | Cheche Catering
        @endif
    " />
    <meta name="twitter:description" content="
        @if($selectedSubcategory)
            Cari menu {{ strtolower($selectedSubcategory->name) }} katering di Purwokerto? Cek pilihan lezat dari Cheche Catering sekarang!
        @elseif($selectedCategory)
            Temukan semua menu {{ strtolower($selectedCategory->name) }} terbaik untuk katering Anda dari Cheche Catering.
        @else
            Lihat daftar menu katering Cheche Catering yang lengkap dan siap memenuhi kebutuhan acara Anda di Purwokerto.
        @endif
    " />
    <meta name="twitter:image" content="{{ asset('assets/images/og-menu-image.jpg') }}" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-red: #e74c3c;
            --primary-red-light: #ff6b6b;
            --primary-red-dark: #c0392b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--gray-700);
        }

        .font-satisfy {
            font-family: 'Satisfy', cursive;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-bottom: 1px solid var(--gray-200);
            padding: 2rem 0;
            margin-top: 80px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-red), var(--primary-red-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-size: 1.1rem;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1.5rem 0;
        }

        .filter-select {
            border: 2px solid var(--gray-200);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
        }

        .filter-select:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
            outline: none;
        }

        .filter-select:hover {
            border-color: var(--primary-red-light);
        }

        /* Content Layout */
        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 500px;
            gap: 2rem;
            padding: 2rem 0;
        }

        @media (max-width: 1024px) {
            .content-wrapper {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        /* Menu Cards */
        .menu-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .menu-card:hover {
            border-color: var(--primary-red-light);
            box-shadow: 0 10px 25px rgba(231, 76, 60, 0.1);
            transform: translateY(-2px);
        }

        .menu-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 0.75rem;
            flex-shrink: 0;
        }

        .menu-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-red);
            margin-bottom: 0.5rem;
        }

        .menu-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 1rem;
        }

        .menu-variants {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu-variants li {
            color: var(--gray-600);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
        }

        .menu-variants li:before {
            content: "•";
            color: var(--primary-red);
            margin-right: 0.5rem;
            font-weight: bold;
        }

        /* PDF Container */
        .pdf-container {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 1rem;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .pdf-container iframe {
            width: 100%;
            height: 600px;
            border: none;
        }

        /* Sidebar */
        .sidebar-card {
            background: linear-gradient(135deg, #fff8f7 0%, #ffe6e4 100%);
            border: 1px solid rgba(231, 76, 60, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            position: sticky;
            top: 120px;
        }

        .sidebar-title {
            font-size: 1.5rem;
            color: var(--primary-red);
            margin-bottom: 1rem;
        }

        .sidebar-content {
            color: var(--gray-700);
            line-height: 1.7;
        }

        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, #fff8f7 0%, #ffe6e4 100%);
            border-bottom: 1px solid var(--gray-200);
            border-radius: 1rem 1rem 0 0;
            padding: 1.5rem;
        }

        .modal-title {
            color: var(--primary-red);
            font-size: 2rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .info-card {
            background: var(--gray-50);
            padding: 1rem;
            border-radius: 0.75rem;
            border-left: 4px solid var(--primary-red);
        }

        .info-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-weight: 600;
            color: var(--gray-800);
        }

        .variants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.5rem;
            background: var(--gray-50);
            padding: 1rem;
            border-radius: 0.75rem;
            margin-top: 1rem;
        }

        .variant-item {
            display: flex;
            align-items: center;
            color: var(--gray-700);
        }

        .variant-item:before {
            content: "✓";
            color: var(--primary-red);
            margin-right: 0.5rem;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--gray-800) 0%, var(--gray-700) 100%);
            color: white;
            padding: 3rem 0;
            text-align: center;
            margin-top: 3rem;
        }

        .footer img {
            height: 60px;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stagger-1 {
            animation-delay: 0.1s;
        }

        .stagger-2 {
            animation-delay: 0.2s;
        }

        .stagger-3 {
            animation-delay: 0.3s;
        }

        .stagger-4 {
            animation-delay: 0.4s;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }

            .menu-card {
                padding: 1rem;
            }

            .menu-card .d-flex {
                flex-direction: column;
                text-align: center;
            }

            .menu-image {
                width: 100%;
                height: 200px;
                margin-bottom: 1rem;
            }

            .filter-section .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.5;
            pointer-events: none;
        }

        /* Smooth Transitions */
        * {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-view-pdf {
            background-color: #e65c00;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(230, 92, 0, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-view-pdf:hover {
            background-color: #cc5200;
            box-shadow: 0 6px 16px rgba(230, 92, 0, 0.4);
            text-decoration: none;
            color: white;
        }
    </style>
</head>

<body class="bg-gray-50">

    @include('partials.navbar')

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="text-center">
                <h1 class="page-title font-satisfy">Our Menu</h1>
                @if($selectedCategory || $selectedSubcategory)
                <p class="page-subtitle">
                    @if($selectedCategory)
                    {{ $selectedCategory->name }}
                    @if($selectedSubcategory) - {{ $selectedSubcategory->name }} @endif
                    @endif
                </p>
                @else
                <p class=" page-subtitle">Discover our delicious food selections</p>
                @endif
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                <select id="categorySelect" class="filter-select flex-fill" style="max-width: 300px;">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" {{ $selectedCategory?->id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>

                <select id="subcategorySelect" class="filter-select flex-fill" style="max-width: 300px;">
                    <option value="">All Subcategories</option>
                    @foreach ($subcategories as $subcategory)
                    <option value="{{ $subcategory->slug }}" {{ $selectedSubcategory?->id == $subcategory->id ? 'selected' : '' }}>
                        {{ $subcategory->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        {{-- PDF Embed --}}
        @if ($pdfPath)
        <div class="pdf-container fade-in d-none d-md-block">
            <iframe src="{{ asset('storage/' . $pdfPath) }}"></iframe>
        </div>

        <!-- Fallback untuk mobile -->
        <div class="d-block d-md-none text-center my-4">
            <a href="{{ asset('storage/' . $pdfPath) }}" target="_blank" class="btn btn-view-pdf">
                📄 Lihat PDF Menu
            </a>
        </div>
        @endif


        <div class="content-wrapper">
            <!-- Menu List -->
            <div class="menu-list">
                @foreach ($menus as $menu)
                <div class="menu-card fade-in stagger-{{ ($loop->index % 4) + 1 }}"
                    data-bs-toggle="modal" data-bs-target="#menuModal{{ $menu->id }}">
                    <div class="d-flex gap-3 align-items-start">
                        @if ($menu->image)
                        <img src="{{ asset('storage/' . $menu->image) }}"
                            alt="{{ $menu->name }}" class="menu-image">
                        @endif

                        <div class="flex-grow-1">
                            <h3 class="menu-title font-satisfy">{{ $menu->name }}</h3>
                            <p class="menu-price">Rp {{ number_format($menu->price, 2, ',', '.') }}</p>
                            @if ($menu->variants->isNotEmpty())
                            <ul class="menu-variants">
                                @foreach ($menu->variants->take(3) as $variant)
                                <li>{{ $variant->name }}</li>
                                @endforeach
                                @if($menu->variants->count() > 3)
                                <li class="text-muted">+{{ $menu->variants->count() - 3 }} more...</li>
                                @endif
                            </ul>
                            @elseif ($menu->description)
                            <p class="mb-3">{{ Str::words(strip_tags($menu->description), 50, '...') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="menuModal{{ $menu->id }}" tabindex="-1"
                    aria-labelledby="menuModalLabel{{ $menu->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-satisfy" id="menuModalLabel{{ $menu->id }}">
                                    {{ $menu->name }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @if ($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}"
                                    alt="{{ $menu->name }}" class="modal-image">
                                @endif

                                @if($menu->description)
                                <p class="mb-3">{{ $menu->description }}</p>
                                @endif

                                <div class="info-grid">
                                    <div class="info-card">
                                        <div class="info-label">Menu Subcategory</div>
                                        <div class="info-value">{{ $menu->subcategory?->name }}</div>
                                    </div>
                                    <div class="info-card">
                                        <div class="info-label">Harga</div>
                                        <p class="menu-price">Rp {{ number_format($menu->price, 2, ',', '.') }}</p>
                                    </div>
                                </div>

                                @if ($menu->variants->isNotEmpty())
                                <div class="mt-4">
                                    <h6 class="fw-bold mb-3">Isian Menu:</h6>
                                    <div class="variants-grid">
                                        @foreach ($menu->variants as $variant)
                                        <div class="variant-item">{{ $variant->name }}</div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Sidebar -->
            <aside class="sidebar d-none d-lg-block">
                @if($addon)
                <div class="sidebar-card fade-in stagger-4">
                    <h3 class="sidebar-title font-satisfy">{{ $addon->title }}</h3>
                    <div class="sidebar-content">
                        {!! $addon->description !!}
                    </div>
                </div>
                @else
                <div class="sidebar-placeholder">
                    <img src="{{ asset('assets/images/menu/sidebar-placeholder.png') }}"
                        alt="Sidebar Placeholder"
                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;" />
                </div>
                @endif
            </aside>
        </div>

        <!-- Mobile Sidebar -->
        @if($addon)
        <div class="d-lg-none mt-4">
            <div class="sidebar-card fade-in">
                <h3 class="sidebar-title font-satisfy text-center">{{ $addon->title }}</h3>
                <div class="sidebar-content">
                    {!! $addon->description !!}
                </div>
            </div>
        </div>
        @endif
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Category and Subcategory Selection
        document.getElementById('categorySelect').addEventListener('change', function() {
            this.classList.add('loading');
            const category = this.value;
            window.location.href = `{{ route('menu') }}?category=${category}`;
        });

        document.getElementById('subcategorySelect').addEventListener('change', function() {
            this.classList.add('loading');
            const category = document.getElementById('categorySelect').value;
            const subcategory = this.value;
            window.location.href = `{{ route('menu') }}?category=${category}&subcategory=${subcategory}`;
        });

        // Initialize page animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add entrance animations
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';

                setTimeout(() => {
                    el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Enhanced modal animations
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('show.bs.modal', function() {
                    const content = this.querySelector('.modal-content');
                    content.style.transform = 'scale(0.8)';
                    content.style.opacity = '0';

                    setTimeout(() => {
                        content.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                        content.style.transform = 'scale(1)';
                        content.style.opacity = '1';
                    }, 50);
                });
            });
        });

        // Performance optimization - lazy load images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Active nav link on scroll
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link-custom');

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