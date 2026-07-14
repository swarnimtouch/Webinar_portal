<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>Contact Tech Team</h3>
            <div class="contact-row">
                <i class="fa-solid fa-phone"></i>
                <span>{{ app('event')->phone??'N/A' }}</span>
            </div>
            <div class="contact-row">
                <i class="fa-solid fa-envelope"></i>
                <span>{{ app('event')->email??'N/A' }}</span>
            </div>
        </div>

        <div class="footer-section links-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Content</a></li>
            </ul>
        </div>

    </div>
    <div class="footer-bottom">
        {{ app('event')->footer_text??'N/A' }}
    </div>

</footer>
