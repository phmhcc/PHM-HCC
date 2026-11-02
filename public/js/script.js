document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.querySelector('.menu');
    const navigation = document.querySelector('.nav-links');
    const navbar = document.querySelector('.navbar');

    if (!(menuButton instanceof HTMLButtonElement)
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
        menuButton.innerHTML = `<i class="fas ${isOpen ? 'fa-xmark' : 'fa-bars'}" aria-hidden="true"></i>`;
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
});