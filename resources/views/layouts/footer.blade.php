<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Tz...saf" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="container-fluid custom-footer py-5">
    <div class="row">
        <!-- Logo -->
        <div class="col-md-3 text-center mb-4 logo">
            <img src="{{ asset('images/nav_bar_logo.png') }}" alt="Logo" height="125" width="126">
        </div>

        <!-- Company Links -->
        <div class="col-md-3">
            <h5 class="footer-title">Company</h5>
            <ul class="list-unstyled">
                <li><a href="/features" class="footer-link">Features</a></li>
                <li><a href="/price" class="footer-link">Pricing</a></li>
                <li><a href="/who_we_are" class="footer-link">Who We Are</a></li>
                <li><a href="/blogs" class="footer-link">Blog</a></li>
            </ul>
        </div>

        <!-- Help & Contact Info -->
        <div class="col-md-3">
            <h5 class="footer-title">Help</h5>
            <ul class="list-unstyled">
                <li><span class="footer-link">📧 hello@onhandsolution.com</span></li>
                <li><span class="footer-link">📞 402-378-9083 (call or text)</span></li>
                <li><a href="/terms" target="_blank" class="footer-link">Terms and Conditions</a></li>
                <li><a href="/privacy" target="_blank" class="footer-link">Privacy Policy</a></li>
            </ul>
        </div>

        <!-- Social Media -->
        <div class="col-md-3 text-center text-md-left">
            <h5 class="footer-title">Follow Us</h5>
            <div class="d-flex justify-content-center justify-content-md-start social-icons mt-2">
                <a href="https://www.instagram.com/onhandsolution" target="_blank" class="footer-link mr-3">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>
                <a href="https://www.facebook.com/onhandsolution" target="_blank" class="footer-link">
                    <i class="fab fa-facebook fa-lg"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="text-center footer-copyright mt-4">
        <hr class="line-above">
        <p class="footer-copyright">© Copyright {{ date('Y') }}, All Rights Reserved by ON HAND SOLUTION</p>
    </div>
</div>
