/**
 * Toast Notification System
 * Displays temporary notification messages to the user
 */
(function() {
    // Default options
    const defaults = {
        position: 'bottom-right',
        duration: 4000,  // 4 seconds
        closeable: true,
        pauseOnHover: true,
        type: 'info'     // info, success, error, warning
    };
    
    // Toast container element
    let container = null;
    
    // Create container for toast notifications
    function createContainer(position) {
        // Remove existing container if any
        if (container) {
            document.body.removeChild(container);
        }
        
        // Create new container
        container = document.createElement('div');
        container.className = 'toast-container';
        
        // Apply positioning
        container.style.position = 'fixed';
        container.style.zIndex = '9999';
        
        switch (position) {
            case 'top-left':
                container.style.top = '1rem';
                container.style.left = '1rem';
                break;
            case 'top-center':
                container.style.top = '1rem';
                container.style.left = '50%';
                container.style.transform = 'translateX(-50%)';
                break;
            case 'top-right':
                container.style.top = '1rem';
                container.style.right = '1rem';
                break;
            case 'bottom-left':
                container.style.bottom = '1rem';
                container.style.left = '1rem';
                break;
            case 'bottom-center':
                container.style.bottom = '1rem';
                container.style.left = '50%';
                container.style.transform = 'translateX(-50%)';
                break;
            case 'bottom-right':
            default:
                container.style.bottom = '1rem';
                container.style.right = '1rem';
                break;
        }
        
        // Add the container to the body
        document.body.appendChild(container);
        
        return container;
    }
    
    /**
     * Show a toast notification
     * @param {string} message - The message to display
     * @param {object} options - Custom options
     */
    function showToast(message, options = {}) {
        // Merge default options with custom options
        const settings = Object.assign({}, defaults, options);
        
        // Create container if it doesn't exist or if position changed
        if (!container || container.dataset.position !== settings.position) {
            container = createContainer(settings.position);
            container.dataset.position = settings.position;
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${settings.type} fade-in`;
        toast.style.marginTop = '0.5rem';
        toast.style.marginBottom = '0.5rem';
        toast.style.minWidth = '18rem';
        toast.style.maxWidth = '24rem';
        
        // Create message element
        const messageElement = document.createElement('div');
        messageElement.className = 'toast-message';
        messageElement.innerHTML = message;
        toast.appendChild(messageElement);
        
        // Add close button if closeable is true
        if (settings.closeable) {
            const closeButton = document.createElement('button');
            closeButton.className = 'toast-close';
            closeButton.innerHTML = '×';
            closeButton.style.marginLeft = '0.5rem';
            closeButton.style.background = 'transparent';
            closeButton.style.border = 'none';
            closeButton.style.color = 'inherit';
            closeButton.style.fontSize = '1.25rem';
            closeButton.style.cursor = 'pointer';
            closeButton.style.float = 'right';
            closeButton.addEventListener('click', function() {
                closeToast(toast);
            });
            toast.appendChild(closeButton);
        }
        
        // Add the toast to the container
        container.appendChild(toast);
        
        // Set a timeout to remove the toast
        let timeoutId;
        let remainingTime = settings.duration;
        const startTime = Date.now();
        
        function startTimer() {
            timeoutId = setTimeout(function() {
                closeToast(toast);
            }, remainingTime);
        }
        
        function pauseTimer() {
            clearTimeout(timeoutId);
            remainingTime -= (Date.now() - startTime);
        }
        
        if (settings.duration > 0) {
            startTimer();
            
            if (settings.pauseOnHover) {
                toast.addEventListener('mouseenter', pauseTimer);
                toast.addEventListener('mouseleave', startTimer);
            }
        }
        
        return toast;
    }
    
    /**
     * Close and remove a toast notification
     * @param {HTMLElement} toast - The toast element to remove
     */
    function closeToast(toast) {
        // Animation to fade out
        toast.classList.remove('fade-in');
        toast.classList.add('fade-out');
        
        // Remove after animation completes
        setTimeout(function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
                
                // Remove container if empty
                if (container && !container.hasChildNodes()) {
                    document.body.removeChild(container);
                    container = null;
                }
            }
        }, 500); // Match with the CSS animation duration
    }
    
    /**
     * Close all toast notifications
     */
    function closeAll() {
        if (container) {
            const toasts = container.querySelectorAll('.toast');
            toasts.forEach(closeToast);
        }
    }
    
    // Expose public methods
    window.Toast = {
        show: showToast,
        close: closeToast,
        closeAll: closeAll,
        
        /**
         * Show a success toast notification
         * @param {string} message - The message to display
         * @param {object} options - Custom options
         */
        success: function(message, options = {}) {
            return showToast(message, Object.assign({}, options, { type: 'success' }));
        },
        
        /**
         * Show an error toast notification
         * @param {string} message - The message to display
         * @param {object} options - Custom options
         */
        error: function(message, options = {}) {
            return showToast(message, Object.assign({}, options, { type: 'error' }));
        },
        
        /**
         * Show a warning toast notification
         * @param {string} message - The message to display
         * @param {object} options - Custom options
         */
        warning: function(message, options = {}) {
            return showToast(message, Object.assign({}, options, { type: 'warning' }));
        },
        
        /**
         * Show an info toast notification
         * @param {string} message - The message to display
         * @param {object} options - Custom options
         */
        info: function(message, options = {}) {
            return showToast(message, Object.assign({}, options, { type: 'info' }));
        }
    };
})();