        </main>

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <a href="index.php" class="nav-item <?= $active_page == 'index' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Beranda</span>
            </a>
            <a href="profil.php" class="nav-item <?= $active_page == 'profil' ? 'active' : '' ?>">
                <i class="fas fa-user-circle"></i>
                <span>Profil</span>
            </a>
        </nav>
    </div>
</body>
</html>
