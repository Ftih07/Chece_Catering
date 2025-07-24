<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>503 - Layanan Tidak Tersedia | Cheche Catering</title>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;600;700&family=Satisfy:wght@400&display=swap"
        rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --primary-red: #e74c3c;
            --primary-red-hover: #f79f95;
            --primary-red-active: #c0392b;
            --text-gray: #374151;
            --light-gray: #f8fafc;
        }

        body {
            font-family: "Poppins", sans-serif;
        }

        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .error-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
        }

        .floating-food {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .floating-food:nth-child(2n) {
            animation-delay: -3s;
            animation-direction: reverse;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            max-width: 500px;
            margin: 0 auto;
            animation: slideInUp 0.8s ease-out;
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

        .error-code {
            font-size: 8rem;
            font-weight: 700;
            background: linear-gradient(135deg,
                    var(--primary-red),
                    #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            text-shadow: 0 0 30px rgba(231, 76, 60, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .error-title {
            color: var(--text-gray);
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .error-title span {
            color: var(--primary-red);
            font-family: "Pacifico", cursive;
        }

        .error-description {
            color: #6b7280;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .btn-home {
            background: linear-gradient(135deg,
                    var(--primary-red),
                    #ff6b6b);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }

        .btn-home:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.6);
            color: white;
        }

        .btn-home:active {
            transform: translateY(0) scale(0.98);
        }

        .food-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: block;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            53%,
            80%,
            100% {
                transform: translate3d(0, 0, 0);
            }

            40%,
            43% {
                transform: translate3d(0, -10px, 0);
            }

            70% {
                transform: translate3d(0, -5px, 0);
            }

            90% {
                transform: translate3d(0, -2px, 0);
            }
        }

        .page-selector {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .page-selector select {
            border: none;
            background: none;
            color: var(--primary-red);
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }

        /* Error-specific backgrounds */
        .error-404 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .error-403 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .error-500 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .error-503 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .error-card {
                padding: 2rem;
                margin: 1rem;
                max-width: 90%;
            }

            .error-code {
                font-size: 6rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-description {
                font-size: 1rem;
            }

            .food-icon {
                font-size: 3rem;
            }
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 4rem;
            }

            .error-title {
                font-size: 1.3rem;
            }

            .page-selector {
                position: relative;
                top: auto;
                left: auto;
                margin-bottom: 2rem;
                text-align: center;
            }
        }

        .hidden {
            display: none !important;
        }
    </style>
</head>

<body>
    <!-- Page Selector -->
    <div class="page-selector">
        <select id="pageSelector" onchange="showErrorPage(this.value)">
            <option value="503">503 - Service Unavailable</option>
        </select>
    </div>

    <!-- 503 Error Page -->
    <div id="error-503" class="error-container error-503 hidden">
        <div class="error-bg">
            <div
                class="floating-food"
                style="top: 18%; left: 10%; font-size: 3rem">
                🛠️
            </div>
            <div
                class="floating-food"
                style="top: 28%; right: 14%; font-size: 2.5rem">
                ⏰
            </div>
            <div
                class="floating-food"
                style="bottom: 32%; left: 16%; font-size: 2rem">
                🔄
            </div>
            <div
                class="floating-food"
                style="bottom: 22%; right: 18%; font-size: 3.5rem">
                ⏳
            </div>
            <div
                class="floating-food"
                style="top: 52%; left: 4%; font-size: 2.2rem">
                🚧
            </div>
            <div
                class="floating-food"
                style="top: 72%; right: 6%; font-size: 2.8rem">
                📋
            </div>
        </div>

        <div class="error-card">
            <div class="food-icon">🔄</div>
            <div class="error-code">503</div>
            <h1 class="error-title">Layanan Tidak <span>Tersedia</span></h1>
            <p class="error-description">
                Maaf, layanan sedang dalam pemeliharaan untuk memberikan
                pengalaman yang lebih baik. Kami akan segera kembali
                melayani Anda!
            </p>
        </div>w
    </div>

    <script>
        // Fungsi untuk menampilkan halaman error sesuai value select
        function showErrorPage(value) {
            // Sembunyikan semua error container
            const pages = document.querySelectorAll(".error-container");
            pages.forEach(page => page.classList.add("hidden"));

            // Tampilkan yang dipilih
            const target = document.getElementById("error-" + value);
            if (target) target.classList.remove("hidden");
        }

        // Fungsi tombol kembali ke home (ubah URL sesuai kebutuhan)
        function goHome() {
            window.location.href = "/";
        }

        // Jika hanya satu error page aktif saat load, jalankan ini
        document.addEventListener("DOMContentLoaded", () => {
            const selector = document.getElementById("pageSelector");
            if (selector) {
                showErrorPage(selector.value); // inisialisasi default saat pertama load
            }
        });
    </script>

</body>

</html>