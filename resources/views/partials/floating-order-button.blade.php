{{-- resources/views/partials/floating-order-button.blade.php --}}
<style>
    .floating-order-btn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 9999;
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
        color: white;
        font-size: 28px;
        width: 65px;
        height: 65px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow:
            0 8px 25px rgba(229, 62, 62, 0.4),
            0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        border: 3px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        animation: pulse 2s infinite;
    }

    .floating-order-btn::before {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        background: linear-gradient(45deg, #ff6b6b, #ee5a24, #ff6b6b);
        border-radius: 50%;
        z-index: -1;
        opacity: 0;
        transition: opacity 0.3s ease;
        animation: rotate 3s linear infinite;
    }

    .floating-order-btn:hover::before {
        opacity: 1;
    }

    .floating-order-btn:hover {
        transform: scale(1.15) rotate(5deg);
        box-shadow:
            0 12px 35px rgba(229, 62, 62, 0.6),
            0 6px 25px rgba(0, 0, 0, 0.3);
        background: linear-gradient(135deg, var(--primary-red-dark) 0%, #a91f1f 100%);
    }

    .floating-order-btn:active {
        transform: scale(1.05);
        transition: transform 0.1s ease;
    }

    .floating-order-btn .btn-text {
        position: absolute;
        right: 75px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 15px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .floating-order-btn .btn-text::after {
        content: '';
        position: absolute;
        right: -8px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid rgba(0, 0, 0, 0.8);
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
    }

    .floating-order-btn:hover .btn-text {
        opacity: 1;
        visibility: visible;
        right: 85px;
    }

    @keyframes pulse {
        0% {
            box-shadow:
                0 8px 25px rgba(229, 62, 62, 0.4),
                0 4px 15px rgba(0, 0, 0, 0.2),
                0 0 0 0 rgba(229, 62, 62, 0.7);
        }

        70% {
            box-shadow:
                0 8px 25px rgba(229, 62, 62, 0.4),
                0 4px 15px rgba(0, 0, 0, 0.2),
                0 0 0 15px rgba(229, 62, 62, 0);
        }

        100% {
            box-shadow:
                0 8px 25px rgba(229, 62, 62, 0.4),
                0 4px 15px rgba(0, 0, 0, 0.2),
                0 0 0 0 rgba(229, 62, 62, 0);
        }
    }

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Mobile optimization */
    @media (max-width: 768px) {
        .floating-order-btn {
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            font-size: 24px;
        }

        .floating-order-btn .btn-text {
            display: none;
        }
    }

    /* Enhanced version with cart icon animation */
    .cart-icon {
        position: relative;
        display: inline-block;
        transition: transform 0.2s ease;
    }

    .floating-order-btn:hover .cart-icon {
        animation: bounce 0.6s ease;
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-8px);
        }

        60% {
            transform: translateY(-4px);
        }
    }

    /* Alternative design with notification badge */
    .floating-order-btn.with-badge::after {
        content: 'NEW';
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff4757;
        color: white;
        font-size: 10px;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 10px;
        animation: wiggle 1s ease-in-out infinite;
    }

    @keyframes wiggle {

        0%,
        7% {
            transform: rotateZ(0);
        }

        15% {
            transform: rotateZ(-15deg);
        }

        20% {
            transform: rotateZ(10deg);
        }

        25% {
            transform: rotateZ(-10deg);
        }

        30% {
            transform: rotateZ(6deg);
        }

        35% {
            transform: rotateZ(-4deg);
        }

        40%,
        100% {
            transform: rotateZ(0);
        }
    }
</style>

<a href="{{ $orderUrl ?? 'https://lynk.id/cheche_catering' }}"
    target="_blank"
    class="floating-order-btn"
    aria-label="{{ $buttonText ?? 'Pesan Sekarang' }}">
    <span class="btn-text">{{ $buttonText ?? 'Pesan Sekarang!' }}</span>
    <span class="cart-icon">🛒</span>
</a>

<script>
    // Optional: Add click tracking or additional interactions
    document.querySelector('.floating-order-btn').addEventListener('click', function(e) {
        // Add a small click animation
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = '';
        }, 150);

        // Optional: Add analytics tracking here
        console.log('Order button clicked!');
    });

    // Optional: Hide/show based on scroll position
    let lastScrollTop = 0;
    const btn = document.querySelector('.floating-order-btn');

    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scrolling down - could hide if needed
            // btn.style.transform = 'translateY(100px)';
        } else {
            // Scrolling up - show
            btn.style.transform = 'translateY(0)';
        }

        lastScrollTop = scrollTop;
    });
</script>