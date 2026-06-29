            </main>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 space-y-2 z-50"></div>

    <script>
        // Toast Notification Function
        function showToast(message, type = 'info', duration = 3000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-green-500'
            };
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            toast.className = `${bgColors[type]} text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 flex items-center`;
            toast.innerHTML = `
                <i class="fas ${icons[type]} mr-3"></i>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }, duration);
        }

        // Confirm Delete Function
        function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
            return confirm(message);
        }

        // Form Validation Helper
        function validateRequired(form) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            return isValid;
        }

        // Auto-resize textarea
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        // Initialize auto-resize for all textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', () => autoResize(textarea));
            autoResize(textarea); // Initial resize
        });

        // Character counter
        function initCharCounter(textareaId, counterId, maxChars) {
            const textarea = document.getElementById(textareaId);
            const counter = document.getElementById(counterId);
            
            if (textarea && counter) {
                textarea.addEventListener('input', function() {
                    const remaining = maxChars - this.value.length;
                    counter.textContent = remaining;
                    
                    if (remaining < 0) {
                        counter.classList.add('text-red-500');
                        counter.classList.remove('text-gray-500');
                    } else {
                        counter.classList.remove('text-red-500');
                        counter.classList.add('text-gray-500');
                    }
                });
                
                // Initial count
                const remaining = maxChars - textarea.value.length;
                counter.textContent = remaining;
            }
        }

        // Loading state untuk buttons
        function setLoading(button, loading = true) {
            if (loading) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            } else {
                button.disabled = false;
                button.innerHTML = button.dataset.originalText || 'Submit';
            }
        }

        // Save original button text
        document.querySelectorAll('button[type="submit"]').forEach(button => {
            button.dataset.originalText = button.innerHTML;
        });

        // Preview image before upload
        function previewImage(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            
            if (file && preview) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        // Copy to clipboard
        function copyToClipboard(text, button = null) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Berhasil disalin ke clipboard!', 'success', 2000);
                if (button) {
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        button.innerHTML = originalText;
                    }, 1000);
                }
            });
        }

        // Format number with thousands separator
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Format currency (IDR)
        function formatCurrency(amount) {
            return 'Rp ' + formatNumber(amount);
        }

        // Search table functionality
        function searchTable(searchInput, tableId) {
            const input = document.getElementById(searchInput);
            const table = document.getElementById(tableId);
            const rows = table.querySelectorAll('tbody tr');
            
            input.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Show "no results" message if needed
                const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
                const noResultsRow = table.querySelector('.no-results');
                
                if (visibleRows.length === 0 && searchTerm !== '') {
                    if (!noResultsRow) {
                        const tbody = table.querySelector('tbody');
                        const colSpan = table.querySelectorAll('thead th').length;
                        const row = document.createElement('tr');
                        row.className = 'no-results';
                        row.innerHTML = `<td colspan="${colSpan}" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang ditemukan</td>`;
                        tbody.appendChild(row);
                    }
                } else if (noResultsRow) {
                    noResultsRow.remove();
                }
            });
        }

        // Initialize tooltips (simple implementation)
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            element.addEventListener('mouseenter', function() {
                const tooltip = document.createElement('div');
                tooltip.className = 'absolute bg-gray-800 text-white text-xs rounded py-1 px-2 z-50';
                tooltip.textContent = this.dataset.tooltip;
                tooltip.style.bottom = '100%';
                tooltip.style.left = '50%';
                tooltip.style.transform = 'translateX(-50%)';
                tooltip.style.marginBottom = '5px';
                this.style.position = 'relative';
                this.appendChild(tooltip);
            });
            
            element.addEventListener('mouseleave', function() {
                const tooltip = this.querySelector('.absolute.bg-gray-800');
                if (tooltip) {
                    tooltip.remove();
                }
            });
        });
    </script>
</body>
</html>
