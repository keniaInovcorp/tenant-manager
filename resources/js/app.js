import './bootstrap';
import { createApp } from 'vue';
import TenantSwitcher from './components/TenantSwitcher.vue';
import UserMenu from './components/UserMenu.vue';

// Function to mount components
function mountComponents() {
    // Find all tenant-switcher elements in the DOM
    const switcherElements = document.querySelectorAll('tenant-switcher');

    switcherElements.forEach((element) => {
        if (!element.__vue_app__) {
            const app = createApp(TenantSwitcher);
            app.mount(element);
        }
    });

    // Create and mount user menu dynamically
    function mountUserMenu() {
        const nav = document.querySelector('nav');
        if (!nav) {
            setTimeout(mountUserMenu, 100);
            return;
        }

        // Check if user menu already exists
        let userMenuElement = document.getElementById('user-menu-dropdown');
        if (userMenuElement && userMenuElement.__vue_app__) {
            return;
        }

        // Try to find existing container first
        const container = document.getElementById('user-menu-container');
        let userName = window.currentUserName || 'Utilizador';

        // Try to get from container data attribute
        if (container) {
            const dataUserName = container.getAttribute('data-user-name');
            if (dataUserName) {
                userName = dataUserName;
            }

            // Create the dropdown element inside the container
            userMenuElement = document.createElement('div');
            userMenuElement.id = 'user-menu-dropdown';
            container.appendChild(userMenuElement);
        } else {
            const tenantSwitcher = document.querySelector('tenant-switcher');
            if (tenantSwitcher) {
                const containerRetry = document.getElementById('user-menu-container');
                if (containerRetry) {
                    userName = containerRetry.getAttribute('data-user-name') || window.currentUserName || 'Utilizador';
                    userMenuElement = document.createElement('div');
                    userMenuElement.id = 'user-menu-dropdown';
                    containerRetry.appendChild(userMenuElement);
                } else {
                    const allElementsWithUserName = document.querySelectorAll('[data-user-name]');
                    if (allElementsWithUserName.length > 0) {
                        userName = allElementsWithUserName[0].getAttribute('data-user-name') || window.currentUserName || 'Utilizador';
                    }

                    userMenuElement = document.createElement('div');
                    userMenuElement.id = 'user-menu-dropdown';

                    tenantSwitcher.parentNode.insertBefore(userMenuElement, tenantSwitcher.nextSibling);
                }
            } else {
                const tenantsLink = nav.querySelector('a[href*="tenants"]');
                if (tenantsLink) {
                    const containerRetry = document.getElementById('user-menu-container');
                    if (containerRetry) {
                        userName = containerRetry.getAttribute('data-user-name') || window.currentUserName || 'Utilizador';
                        userMenuElement = document.createElement('div');
                        userMenuElement.id = 'user-menu-dropdown';
                        containerRetry.appendChild(userMenuElement);
                    } else {
                        const allElementsWithUserName = document.querySelectorAll('[data-user-name]');
                        if (allElementsWithUserName.length > 0) {
                            userName = allElementsWithUserName[0].getAttribute('data-user-name') || window.currentUserName || 'Utilizador';
                        }

                        userMenuElement = document.createElement('div');
                        userMenuElement.id = 'user-menu-dropdown';
                        tenantsLink.parentNode.insertBefore(userMenuElement, tenantsLink.nextSibling);
                    }
                } else {
                    setTimeout(mountUserMenu, 100);
                    return;
                }
            }
        }
        // Final check for userName
        if (userName === 'Utilizador') {
            if (window.currentUserName) {
                userName = window.currentUserName;
            } else {
                const finalContainer = document.getElementById('user-menu-container');
                if (finalContainer) {
                    userName = finalContainer.getAttribute('data-user-name') || 'Utilizador';
                }
            }
        }

        // Mount Vue component
        if (!userMenuElement.__vue_app__) {
            try {
                const app = createApp(UserMenu, { userName });
                app.mount(userMenuElement);
            } catch (error) {
                console.error('Error mounting UserMenu:', error);
            }
        }
    }

    mountUserMenu();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountComponents);
} else {
    mountComponents();
}

setTimeout(mountComponents, 100);
