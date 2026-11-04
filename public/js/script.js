const initializePublicNavigation = () => {
    const menuButton = document.querySelector('.menu');
    const menuIcon = menuButton?.querySelector('i');
    const navigation = document.querySelector('.nav-links');
    const navbar = document.querySelector('.navbar');

    if (!(menuButton instanceof HTMLButtonElement)
        || !(menuIcon instanceof HTMLElement)
        || !(navigation instanceof HTMLElement)
        || !(navbar instanceof HTMLElement)) {
        return;
    }

    const setMenuOpen = (isOpen) => {
        navigation.classList.toggle('active', isOpen);
        menuButton.setAttribute('aria-expanded', String(isOpen));
        menuButton.setAttribute(
            'aria-label',
            isOpen ? 'Close navigation menu' : 'Open navigation menu'
        );
        menuIcon.classList.toggle('fa-bars', !isOpen);
        menuIcon.classList.toggle('fa-xmark', isOpen);
    };

    menuButton.addEventListener('click', () => {
        setMenuOpen(menuButton.getAttribute('aria-expanded') !== 'true');
    });

    navigation.addEventListener('click', (event) => {
        if (event.target instanceof Element && event.target.closest('a')) {
            setMenuOpen(false);
        }
    });

    document.addEventListener('click', (event) => {
        if (event.target instanceof Node && !navbar.contains(event.target)) {
            setMenuOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setMenuOpen(false);
            menuButton.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 900) {
            setMenuOpen(false);
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePublicNavigation, { once: true });
} else {
    initializePublicNavigation();
}