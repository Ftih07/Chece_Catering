   <style>
       /* Navbar Wrapper */
       .custom-navbar {
           position: fixed;
           top: 0;
           width: 100%;
           background: rgba(255, 255, 255, 0.95);
           box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
           z-index: 999;
       }

       .navbar-container {
           max-width: 1200px;
           margin: 0 auto;
           padding: 0.5rem 1rem;
           display: flex;
           align-items: center;
           justify-content: space-between;
       }

       .logo {
           height: 50px;
       }

       /* Navigation Links */
       .nav-links {
           display: flex;
           gap: 1.5rem;
       }

       .nav-link {
           text-decoration: none;
           color: #000;
           font-family: 'Satisfy', cursive;
           font-size: 1.2rem;
           transition: color 0.3s ease;
       }

       .nav-link:hover,
       .nav-link.active {
           color: #e74c3c;
           font-weight: bold;
       }

       /* Hamburger Menu */
       .menu-toggle {
           display: none;
           flex-direction: column;
           gap: 5px;
           border: none;
           background: none;
           cursor: pointer;
       }

       .menu-toggle span {
           width: 25px;
           height: 3px;
           background-color: #333;
           display: block;
           border-radius: 3px;
       }

       /* Responsive */
       @media (max-width: 768px) {
           .menu-toggle {
               display: flex;
           }

           .nav-links {
               position: absolute;
               top: 100%;
               right: 0;
               background: white;
               flex-direction: column;
               /* Jadi vertikal */
               align-items: center;
               /* Tengah horizontal */
               text-align: center;
               /* Tengah teks */
               width: 100%;
               padding: 1rem;
               gap: 1rem;
               display: none;
           }

           .nav-links.show {
               display: flex;
           }
       }
   </style>

   <header class="custom-navbar">
       <div class="navbar-container">
           <a href="{{ route('home') }}">
               <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo Cheche" class="logo" />
           </a>
           <button class="menu-toggle" id="menuToggle">
               <span></span><span></span><span></span>
           </button>

           <nav class="nav-links" id="navLinks">
               <a href="{{ route('home') }}#home" class="nav-link nav-link-custom">Home</a>
               <a href="{{ route('menu') }}" class="nav-link nav-link-custom">Menu</a>
               <a href="{{ route('gallery') }}" class="nav-link nav-link-custom">Gallery</a>
               <a href="{{ route('home') }}#order" class="nav-link nav-link-custom">Order</a>
           </nav>
       </div>
   </header>

   <script>
       // Toggle menu untuk mobile
       const menuToggle = document.getElementById('menuToggle');
       const navLinks = document.getElementById('navLinks');

       if (menuToggle && navLinks) {
           menuToggle.addEventListener('click', () => {
               navLinks.classList.toggle('show');
           });
       }
   </script>