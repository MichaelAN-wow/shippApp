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


// Data representing services (replace the paths with actual image paths)
let services = [
    { id: 1, title: 'COGS Calculation', description: 'Automatically calculate COGS, set retail and wholesale values.', image: '../icons/settingsIcon.svg' },
    { id: 2, title: 'Shop Integration', description: 'Sync with multiple platforms to ensure that product inventory never runs out.', image: '../icons/cartIcon.svg' },
    { id: 3, title: 'Raw Material Tracking', description: 'Always know what raw materials you have on hand, as products are made.', image: '../icons/cubeIcon.svg' },
    { id: 4, title: 'Centralized Info', description: 'View all of your sales and customers, from multiple channels, in one spot.', image: '../icons/cloudIcon.svg' },
    { id: 5, title: 'Business Reports', description: 'Understand your business on a deeper level with inventory and sales reports.', image: '../icons/heartFolderIcon.svg' },
    { id: 6, title: 'Effective Solutions', description: 'Document supply orders, understand unit costs, perform audits, and more.', image: '../icons/pieIcon.svg' },
];

// Function to generate service items
function renderServices() {
    console.log('renderServices;')
    const servicesGrid = document.querySelector('.services-grid');
    console.log(servicesGrid);
    services.forEach(service => {
        const serviceItem = document.createElement('div');
        serviceItem.className = 'service-item';

        const serviceImage = document.createElement('img');
        serviceImage.src = service.image;
        serviceImage.alt = service.title;

        const serviceTitle = document.createElement('div');
        serviceTitle.className = 'service-title';
        serviceTitle.innerText = service.title;

        const serviceDescription = document.createElement('div');
        serviceDescription.className = 'service-description';
        serviceDescription.innerText = service.description;

        serviceItem.appendChild(serviceImage);
        serviceItem.appendChild(serviceTitle);
        serviceItem.appendChild(serviceDescription);

        servicesGrid.appendChild(serviceItem);
    });
} 

// Call the function to render services on page load
window.onload = renderServices;