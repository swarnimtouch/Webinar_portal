<!DOCTYPE html>
<html lang="en">

@include('partials.Website.header')

<body>

@yield('body')

@include('partials.Website.footer')

@stack('scripts')
<script>
    window.sliderData = [
            @foreach($banners as $banner)
        {
            type: "{{ $banner->type }}",
            src: "{{ asset('storage/banners/' . $banner->filename) }}"
        }@if(!$loop->last),@endif
        @endforeach
    ];
</script>

<script src="{{ asset('assets/js/FormValidator.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ✅ Initialize Form Validators
        const loginValidator = new FormValidator('loginForm');
        const registerValidator = new FormValidator('registerForm');

        // ✅ Enable Real-time Validation
        enableRealTimeValidation(loginValidator);
        enableRealTimeValidation(registerValidator);

        // ✅ Modal Handlers
        const loginBtn = document.getElementById('openLoginModal');
        const registerBtn = document.getElementById('openRegisterModal');
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');

        // Open Login Modal
        if (loginBtn) {
            loginBtn.addEventListener('click', () => {
                loginModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        }

        // Open Register Modal
        if (registerBtn) {
            registerBtn.addEventListener('click', () => {
                registerModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        }

        // Close Login Modal
        document.querySelectorAll('.close-modal-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                loginModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        });

        // Close Register Modal
        document.querySelectorAll('.close-register-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                registerModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        });

        // Close on outside click
        [loginModal, registerModal].forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="{{ asset('assets/js/Website/script.js') }}"></script>
</body>
</html>
