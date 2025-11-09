document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll('.nav-link');
    const currentPath = window.location.pathname;

    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }

        link.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');

            if (url.startsWith('#')) {
                const sectionId = url.substring(1);
                const section = document.getElementById(sectionId);

                if (section) {
                    window.scrollTo({
                        top: section.offsetTop,
                        behavior: 'smooth'
                    });
                }
            } else {
                window.location.href = url;
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll('.nav-link');
    const currentPath = window.location.pathname;

    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }

        link.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');

            if (url.startsWith('#')) {
                const sectionId = url.substring(1);
                const section = document.getElementById(sectionId);

                if (section) {
                    window.scrollTo({
                        top: section.offsetTop,
                        behavior: 'smooth'
                    });
                }
            } else {
                window.location.href = url;
            }
        });
    });
});