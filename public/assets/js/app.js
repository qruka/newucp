/**
 * Main Application Script
 * Handles common UI interactions and initializations
 */
(function() {
    'use strict';
    
    /**
     * Initialize the application
     */
    function init() {
        initSidebar();
        initDropdowns();
        initModals();
        initAlerts();
        initTooltips();
        initAnimations();
        initFormValidation();
        
        // Initialize flash message if exists
        const flashMessage = document.getElementById('flashMessage');
        if (flashMessage && flashMessage.textContent.trim() !== '') {
            Toast.show(flashMessage.textContent, {
                type: flashMessage.dataset.type || 'info'
            });
        }
    }
    
    /**
     * Initialize mobile sidebar functionality
     */
    function initSidebar() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const sidebarPanel = document.getElementById('sidebarPanel');
        const closeSidebar = document.getElementById('closeSidebar');
        
        if (!sidebarToggle || !mobileSidebar) return;
        
        function openSidebar() {
            mobileSidebar.classList.remove('opacity-0', 'pointer-events-none');
            if (sidebarBackdrop) sidebarBackdrop.classList.remove('opacity-0');
            if (sidebarPanel) sidebarPanel.classList.remove('-translate-x-full');
            document.body.classList.add('overflow-hidden');
        }
        
        function closeSidebarMenu() {
            if (sidebarPanel) sidebarPanel.classList.add('-translate-x-full');
            if (sidebarBackdrop) sidebarBackdrop.classList.add('opacity-0');
            
            // Wait for animation to complete
            setTimeout(() => {
                mobileSidebar.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('overflow-hidden');
            }, 300);
        }
        
        // Add event listeners
        sidebarToggle.addEventListener('click', openSidebar);
        
        if (closeSidebar) {
            closeSidebar.addEventListener('click', closeSidebarMenu);
        }
        
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', closeSidebarMenu);
        }
        
        // Close sidebar on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !mobileSidebar.classList.contains('opacity-0')) {
                closeSidebarMenu();
            }
        });
    }
    
    /**
     * Initialize dropdown functionality
     */
    function initDropdowns() {
        // Get all dropdown toggles
        const dropdownToggles = document.querySelectorAll('[data-toggle="dropdown"]');
        
        dropdownToggles.forEach(toggle => {
            const target = document.getElementById(toggle.dataset.target);
            if (!target) return;
            
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Close all dropdowns first
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu !== target) {
                        menu.classList.add('hidden');
                    }
                });
                
                // Toggle this dropdown
                target.classList.toggle('hidden');
            });
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown-menu') && !e.target.closest('[data-toggle="dropdown"]')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
        
        // Close dropdowns on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
    }
    
    /**
     * Initialize modal functionality
     */
    function initModals() {
        // Get all modal triggers
        const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
        const modalCloses = document.querySelectorAll('[data-dismiss="modal"]');
        
        modalTriggers.forEach(trigger => {
            const targetId = trigger.dataset.target;
            const modal = document.getElementById(targetId);
            
            if (!modal) return;
            
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(modal);
            });
        });
        
        modalCloses.forEach(closeBtn => {
            const modal = closeBtn.closest('.modal');
            
            if (!modal) return;
            
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeModal(modal);
            });
        });
        
        // Close modal when clicking backdrop
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal(e.target);
            }
        });
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal:not(.hidden)');
                if (openModal) {
                    closeModal(openModal);
                }
            }
        });
        
        function openModal(modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            // Animate modal
            setTimeout(() => {
                const dialog = modal.querySelector('.modal-dialog');
                if (dialog) {
                    dialog.classList.add('scale-100', 'opacity-100');
                    dialog.classList.remove('scale-95', 'opacity-0');
                }
            }, 10);
        }
        
        function closeModal(modal) {
            const dialog = modal.querySelector('.modal-dialog');
            
            if (dialog) {
                dialog.classList.remove('scale-100', 'opacity-100');
                dialog.classList.add('scale-95', 'opacity-0');
                
                // Wait for animation to complete
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }, 300);
            } else {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
        
        // Expose methods globally
        window.Modal = {
            open: openModal,
            close: closeModal
        };
    }
    
    /**
     * Initialize alert dismissal functionality
     */
    function initAlerts() {
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.alert-close');
            
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    alert.classList.add('fade-out');
                    
                    // Remove after animation completes
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 300);
                });
            }
            
            // Auto-dismiss alerts with data-autodismiss attribute
            if (alert.dataset.autodismiss) {
                const timeout = parseInt(alert.dataset.autodismiss, 10) || 5000;
                
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.classList.add('fade-out');
                        
                        setTimeout(() => {
                            if (alert.parentNode) {
                                alert.parentNode.removeChild(alert);
                            }
                        }, 300);
                    }
                }, timeout);
            }
        });
    }
    
    /**
     * Initialize tooltips
     */
    function initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            const tooltipText = element.dataset.tooltip;
            
            if (!tooltipText) return;
            
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip-text';
            tooltip.textContent = tooltipText;
            
            element.classList.add('tooltip');
            element.appendChild(tooltip);
        });
    }
    
    /**
     * Initialize animations for elements with data-animate attribute
     */
    function initAnimations() {
        const animatedElements = document.querySelectorAll('[data-animate]');
        
        animatedElements.forEach(element => {
            const animation = element.dataset.animate;
            const delay = element.dataset.delay || 0;
            const duration = element.dataset.duration || 500;
            
            // Add animation classes
            element.style.animationDelay = `${delay}ms`;
            element.style.animationDuration = `${duration}ms`;
            
            // If not using "on-visible" trigger, animate immediately
            if (!element.dataset.animateOn || element.dataset.animateOn !== 'visible') {
                element.classList.add(animation);
                return;
            }
            
            // Otherwise, use Intersection Observer
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add(animation);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            observer.observe(element);
        });
    }
    
    /**
     * Initialize form validation
     */
    function initFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!validateForm(form)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
            
            // Live validation on field change
            const fields = form.querySelectorAll('input, select, textarea');
            
            fields.forEach(field => {
                field.addEventListener('change', function() {
                    validateField(field);
                });
                
                // More responsive validation for text inputs
                if (field.tagName === 'INPUT' && ['text', 'email', 'password', 'tel', 'url'].includes(field.type)) {
                    field.addEventListener('blur', function() {
                        validateField(field);
                    });
                }
            });
        });
        
        function validateForm(form) {
            let isValid = true;
            const fields = form.querySelectorAll('input, select, textarea');
            
            fields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            
            return isValid;
        }
        
        function validateField(field) {
            // Skip disabled fields
            if (field.disabled) return true;
            
            // Skip fields without validation rules
            if (!field.required && !field.pattern && !field.getAttribute('data-validate-min') && !field.getAttribute('data-validate-max')) {
                return true;
            }
            
            let isValid = true;
            const errorElement = field.nextElementSibling?.classList.contains('form-error') 
                ? field.nextElementSibling 
                : null;
            
            // Check for required
            if (field.required && !field.value.trim()) {
                isValid = false;
                showFieldError(field, errorElement, "Ce champ est requis");
                return false;
            }
            
            // Skip remaining validation if field is empty and not required
            if (!field.required && !field.value.trim()) {
                clearFieldError(field, errorElement);
                return true;
            }
            
            // Check for pattern
            if (field.pattern && !new RegExp(field.pattern).test(field.value)) {
                isValid = false;
                showFieldError(field, errorElement, field.dataset.errorPattern || "Format invalide");
                return false;
            }
            
            // Check for min/max for number inputs
            if (field.type === 'number') {
                const value = parseFloat(field.value);
                
                if (field.min && value < parseFloat(field.min)) {
                    isValid = false;
                    showFieldError(field, errorElement, `La valeur minimale est ${field.min}`);
                    return false;
                }
                
                if (field.max && value > parseFloat(field.max)) {
                    isValid = false;
                    showFieldError(field, errorElement, `La valeur maximale est ${field.max}`);
                    return false;
                }
            }
            
            // Check for minlength/maxlength
            if (field.minLength && field.value.length < parseInt(field.minLength, 10)) {
                isValid = false;
                showFieldError(field, errorElement, `Minimum ${field.minLength} caractères requis`);
                return false;
            }
            
            if (field.maxLength && field.value.length > parseInt(field.maxLength, 10)) {
                isValid = false;
                showFieldError(field, errorElement, `Maximum ${field.maxLength} caractères autorisés`);
                return false;
            }
            
            // Check for data-validate-min/data-validate-max (string length)
            if (field.dataset.validateMin && field.value.length < parseInt(field.dataset.validateMin, 10)) {
                isValid = false;
                showFieldError(field, errorElement, `Minimum ${field.dataset.validateMin} caractères requis`);
                return false;
            }
            
            if (field.dataset.validateMax && field.value.length > parseInt(field.dataset.validateMax, 10)) {
                isValid = false;
                showFieldError(field, errorElement, `Maximum ${field.dataset.validateMax} caractères autorisés`);
                return false;
            }
            
            // Check for email format
            if (field.type === 'email' && !validateEmail(field.value)) {
                isValid = false;
                showFieldError(field, errorElement, "Format d'email invalide");
                return false;
            }
            
            // Check for custom validation function
            if (field.dataset.validateFn && window[field.dataset.validateFn]) {
                const customValid = window[field.dataset.validateFn](field.value, field);
                
                if (!customValid) {
                    isValid = false;
                    showFieldError(field, errorElement, field.dataset.errorCustom || "Valeur invalide");
                    return false;
                }
            }
            
            // Check for matching field
            if (field.dataset.match) {
                const matchField = document.getElementById(field.dataset.match);
                
                if (matchField && field.value !== matchField.value) {
                    isValid = false;
                    showFieldError(field, errorElement, field.dataset.errorMatch || "Les valeurs ne correspondent pas");
                    return false;
                }
            }
            
            // Field is valid
            if (isValid) {
                clearFieldError(field, errorElement);
            }
            
            return isValid;
        }
        
        function showFieldError(field, errorElement, message) {
            field.classList.add('is-invalid');
            
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
            } else {
                // Create error element if it doesn't exist
                const newErrorElement = document.createElement('span');
                newErrorElement.className = 'form-error text-sm text-red-600 dark:text-red-400 mt-1';
                newErrorElement.textContent = message;
                
                field.parentNode.insertBefore(newErrorElement, field.nextSibling);
            }
        }
        
        function clearFieldError(field, errorElement) {
            field.classList.remove('is-invalid');
            
            if (errorElement) {
                errorElement.textContent = '';
                errorElement.classList.add('hidden');
            }
        }
        
        function validateEmail(email) {
            const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }
    }
    
    // Initialize the application when DOM is fully loaded
    document.addEventListener('DOMContentLoaded', init);
})();