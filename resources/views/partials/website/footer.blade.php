<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>Contact Tech Team</h3>
            <div class="contact-row">
                <i class="fa-solid fa-phone"></i>
                <span>{{ siteSetting('ADMIN_phone', config('app.name')) }}</span>
            </div>
            <div class="contact-row">
                <i class="fa-solid fa-envelope"></i>
                <span>{{ siteSetting('ADMIN_EMAIL', config('app.name')) }}</span>
            </div>
        </div>

        <div class="footer-section links-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="#">Hackathon Rules</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Code of Conduct</a></li>
            </ul>
        </div>

    </div> <div class="footer-bottom">
        {{ siteSetting('footer_text', config('app.name')) }}
    </div>

</footer>
