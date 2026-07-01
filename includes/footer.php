<?php
// Include settings if not already included
if (!class_exists('Settings')) {
    require_once __DIR__ . '/settings.php';
}

// Get school and contact info
$school_info = getSchoolInfo();
$contact_info = getContactInfo();
$social_media = getSocialMedia();
?>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <?php if (!empty($school_info['logo'])): ?>
                            <img src="admin/uploads/<?php echo htmlspecialchars($school_info['logo']); ?>" alt="Logo" style="height: 40px; width: auto; margin-right: 10px; background: white; padding: 2px; border-radius: 4px;">
                        <?php else: ?>
                            <i class="fas fa-graduation-cap"></i>
                        <?php endif; ?>
                        <span><?php echo htmlspecialchars($school_info['name']); ?></span>
                    </div>
                    <p><?php echo htmlspecialchars($school_info['description']); ?></p>
                    <div class="social-links">
                        <a href="<?php echo htmlspecialchars($social_media['facebook']); ?>" target="_blank" rel="noopener">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="<?php echo htmlspecialchars($social_media['instagram']); ?>" target="_blank" rel="noopener">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="<?php echo htmlspecialchars($social_media['youtube']); ?>" target="_blank" rel="noopener">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="<?php echo htmlspecialchars($social_media['twitter']); ?>" target="_blank" rel="noopener">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Menu Utama</h3>
                    <ul>
                        <li><a href="profil.php">Profil</a></li>
                        <li><a href="berita.php">Berita</a></li>
                        <li><a href="academic.php">Akademik</a></li>
                        <li><a href="inovasi.php">Inovasi</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Informasi</h3>
                    <ul>
                        <li><a href="info.php">Informasi Umum</a></li>
                        <li><a href="transparansi.php">Transparansi</a></li>
                        <li><a href="contact.php">Kontak</a></li>
                        <li><a href="index.php#integrity-values">Pendidikan Karakter</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Kontak</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($contact_info['address']); ?></li>
                        <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($contact_info['phone']); ?></li>
                        <li><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($contact_info['email']); ?></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-divider"></div>
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($school_info['name']); ?>. All rights reserved.</p>
                    <p>NPSN: <?php echo htmlspecialchars($school_info['npsn']); ?></p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
        // Also support class-based hamburger (legacy)
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');
        
        if (hamburger && navMenu) {
            hamburger.addEventListener('click', function() {
                navMenu.classList.toggle('active');
                this.classList.toggle('active');
            });
        }

        // Navbar Scroll Effect (Standardized)
        const navbar = document.getElementById('navbar');
        if (navbar) {
            const navTheme = navbar.getAttribute('data-theme') || 'dark';
            const glassEffect = navbar.querySelector('.glass-effect');
            const navLinks = navbar.querySelectorAll('.nav-link:not(.bg-primary-500)');
            const logoText = navbar.querySelector('h1');
            const logoSubtext = navbar.querySelector('p');
            const mobileMenuBtnIcon = navbar.querySelector('#mobile-menu-button i');

            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    // Scrolled state
                    if (glassEffect) {
                        glassEffect.style.background = 'rgba(255, 255, 255, 0.95)';
                        glassEffect.style.borderBottom = '1px solid rgba(0, 0, 0, 0.05)';
                        glassEffect.classList.add('shadow-md');
                    }
                    
                    // Change text colors to dark (only if theme was dark)
                    if (navTheme !== 'light') {
                        if (logoText) {
                            logoText.classList.remove('text-white');
                            logoText.classList.add('text-gray-800');
                        }
                        
                        if (logoSubtext) {
                            logoSubtext.classList.remove('text-gray-200');
                            logoSubtext.classList.add('text-gray-500');
                        }
                        
                        if (mobileMenuBtnIcon) {
                            mobileMenuBtnIcon.classList.remove('text-white');
                            mobileMenuBtnIcon.classList.add('text-gray-800');
                        }

                        if (navLinks) {
                            navLinks.forEach(link => {
                                link.classList.remove('text-gray-100', 'hover:bg-white/20', 'hover:text-white');
                                link.classList.add('text-gray-700', 'hover:bg-primary-50', 'hover:text-primary-600');
                            });
                        }
                    }
                } else {
                    // Top state
                    if (glassEffect && navTheme !== 'light') {
                        glassEffect.style.background = 'rgba(255, 255, 255, 0.1)';
                        glassEffect.style.borderBottom = '1px solid rgba(255, 255, 255, 0.2)';
                        glassEffect.classList.remove('shadow-md');
                    }
                    
                    // Change text colors back to light (only if theme was dark)
                    if (navTheme !== 'light') {
                        if (logoText) {
                            logoText.classList.remove('text-gray-800');
                            logoText.classList.add('text-white');
                        }
                        
                        if (logoSubtext) {
                            logoSubtext.classList.remove('text-gray-500');
                            logoSubtext.classList.add('text-gray-200');
                        }
                        
                        if (mobileMenuBtnIcon) {
                            mobileMenuBtnIcon.classList.remove('text-gray-800');
                            mobileMenuBtnIcon.classList.add('text-white');
                        }

                        if (navLinks) {
                            navLinks.forEach(link => {
                                link.classList.remove('text-gray-700', 'hover:bg-primary-50', 'hover:text-primary-600');
                                link.classList.add('text-gray-100', 'hover:bg-white/20', 'hover:text-white');
                            });
                        }
                    }
                }
            });
        }

        // Dropdown toggle for touch devices
        const dropdowns = document.querySelectorAll('.nav-item.dropdown');
        dropdowns.forEach(dd => {
            const toggle = dd.querySelector('.dropdown-toggle');
            if (toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Close other dropdowns
                    dropdowns.forEach(other => { if (other !== dd) other.classList.remove('open'); });
                    dd.classList.toggle('open');
                });
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const anyDropdown = e.target.closest('.nav-item.dropdown');
            if (!anyDropdown) {
                dropdowns.forEach(dd => dd.classList.remove('open'));
            }
        });
    </script>
