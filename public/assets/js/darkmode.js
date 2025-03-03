/**
 * Dark Mode Manager
 * Handles dark mode preferences and transitions
 */
(function() {
    // Prevent FOUC (Flash Of Unstyled Content) by applying preference immediately
    // This script is deliberately inlined in the head of the document
    const htmlElement = document.documentElement;
    
    // Check for dark mode preference
    function isDarkMode() {
        // First check localStorage
        const darkModeStored = localStorage.getItem('darkMode');
        
        if (darkModeStored !== null) {
            return darkModeStored === 'true';
        }
        
        // Then check system preference
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    
    // Apply dark mode immediately to avoid flash
    if (isDarkMode()) {
        htmlElement.classList.add('dark');
    } else {
        htmlElement.classList.remove('dark');
    }
    
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        
        if (!darkModeToggle) {
            return; // Exit if toggle doesn't exist
        }
        
        // Initialize toggle state
        darkModeToggle.checked = isDarkMode();
        
        // Handle toggle changes
        darkModeToggle.addEventListener('change', function() {
            if (this.checked) {
                htmlElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
                if (window.dispatchEvent) {
                    window.dispatchEvent(new CustomEvent('darkModeChange', { detail: { darkMode: true } }));
                }
            } else {
                htmlElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
                if (window.dispatchEvent) {
                    window.dispatchEvent(new CustomEvent('darkModeChange', { detail: { darkMode: false } }));
                }
            }
        });
        
        // Listen for system preference changes
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        // Not all browsers support this
        if (mediaQuery.addEventListener) {
            mediaQuery.addEventListener('change', function(e) {
                if (localStorage.getItem('darkMode') === null) {
                    // Only auto-switch if user hasn't set a preference
                    if (e.matches) {
                        htmlElement.classList.add('dark');
                        if (darkModeToggle) darkModeToggle.checked = true;
                    } else {
                        htmlElement.classList.remove('dark');
                        if (darkModeToggle) darkModeToggle.checked = false;
                    }
                    
                    if (window.dispatchEvent) {
                        window.dispatchEvent(new CustomEvent('darkModeChange', { detail: { darkMode: e.matches } }));
                    }
                }
            });
        }
    });
    
    // Expose public methods
    window.DarkMode = {
        /**
         * Check if dark mode is active
         * @return {boolean} True if dark mode is active
         */
        isDarkMode: function() {
            return isDarkMode();
        },
        
        /**
         * Toggle dark mode
         * @return {boolean} New dark mode state
         */
        toggle: function() {
            const newState = !isDarkMode();
            localStorage.setItem('darkMode', newState.toString());
            
            if (newState) {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }
            
            // Update checkbox if it exists
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (darkModeToggle) {
                darkModeToggle.checked = newState;
            }
            
            if (window.dispatchEvent) {
                window.dispatchEvent(new CustomEvent('darkModeChange', { detail: { darkMode: newState } }));
            }
            
            return newState;
        },
        
        /**
         * Set dark mode state
         * @param {boolean} state - True to enable dark mode, false to disable
         */
        setDarkMode: function(state) {
            localStorage.setItem('darkMode', state.toString());
            
            if (state) {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }
            
            // Update checkbox if it exists
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (darkModeToggle) {
                darkModeToggle.checked = state;
            }
            
            if (window.dispatchEvent) {
                window.dispatchEvent(new CustomEvent('darkModeChange', { detail: { darkMode: state } }));
            }
        }
    };
})();