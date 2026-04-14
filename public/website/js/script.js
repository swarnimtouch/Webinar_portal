const loginModal = document.getElementById("loginModal");
const registerModal = document.getElementById("registerModal");

const loginBtn = document.getElementById("openLoginModal");
const registerBtn = document.getElementById("openRegisterModal");

const closeLoginBtn = document.querySelector(".close-modal-btn");
const closeRegisterBtn = document.querySelector(".close-register-btn");

const body = document.body;

function openLoginModal() {
    if (loginModal) {
        loginModal.style.display = "flex";
        body.style.overflow = "hidden";
    }
}

function closeLoginModal() {
    if (loginModal) {
        loginModal.style.display = "none";
        body.style.overflow = "auto";
    }
}

function openRegisterModal() {
    if (registerModal) {
        registerModal.style.display = "flex";
        body.style.overflow = "hidden";
    }
}

function closeRegisterModal() {
    if (registerModal) {
        registerModal.style.display = "none";
        body.style.overflow = "auto";
    }
}

if (loginBtn) {
    loginBtn.addEventListener("click", openLoginModal);
}

if (registerBtn) {
    registerBtn.addEventListener("click", openRegisterModal);
}

if (closeLoginBtn) {
    closeLoginBtn.addEventListener("click", closeLoginModal);
}

if (closeRegisterBtn) {
    closeRegisterBtn.addEventListener("click", closeRegisterModal);
}

window.addEventListener("click", function (event) {
    if (event.target === loginModal) {
        closeLoginModal();
    }
    if (event.target === registerModal) {
        closeRegisterModal();
    }
});


$(document).ready(function () {

    const $track = $("#sliderTrack");
    const $heroBanner = $(".hero-banner");
    const sliderData = window.sliderData || [];
    let slideIndex = 0;
    let slideInterval;
    const slideDuration = 3000;

    if ($track.length === 0 || sliderData.length === 0) return;

    function initSlider() {
        $track.empty();

        $.each(sliderData, function (index, item) {
            let mediaElement = '';

            if (item.type === 'image') {
                mediaElement = `<img src="${item.src}" alt="Event Banner">`;
            } else if (item.type === 'video') {
                mediaElement = `
                    <video poster="${item.poster || ''}" muted playsinline loop>
                        <source src="${item.src}" type="video/mp4">
                    </video>`;
            }

            const slideHtml = `<div class="slide">${mediaElement}</div>`;
            $track.append(slideHtml);
        });

        updateSlider();
        startAutoSlide();
    }

    function updateSlider() {
        $track.css("transform", `translateX(-${slideIndex * 100}%)`);
        updateBackground(slideIndex);
        const $allVideos = $(".slide video");
        $allVideos.each(function () {
            $(this).get(0).pause();
            $(this).get(0).currentTime = 0;
        });

        const $currentSlide = $(".slide").eq(slideIndex);
        const $activeVideo = $currentSlide.find("video");

        if ($activeVideo.length > 0) {
            const playPromise = $activeVideo.get(0).play();
            if (playPromise !== undefined) {
                playPromise.catch(error => console.log("Auto-play blocked:", error));
            }
        }
    }

    function updateBackground(index) {
        const data = sliderData[index];
        const $bgImage = $("#bgImage");
        const $bgVideo = $("#bgVideo");

        $(".bg-media").removeClass("active");

        if (data.type === 'image') {
            $bgVideo.trigger('pause');
            $bgImage.attr("src", data.src);
            $bgImage.addClass("active");

        } else if (data.type === 'video') {
            $bgVideo.attr("src", data.src);
            $bgVideo.addClass("active");
            const videoEl = $bgVideo.get(0);
            videoEl.load();
            const playPromise = videoEl.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => console.log("Bg Video Auto-play blocked:", error));
            }
        }
    }

    function nextSlide() {
        slideIndex++;
        if (slideIndex >= sliderData.length) {
            slideIndex = 0;
        }
        updateSlider();
    }

    function startAutoSlide() {
        if (slideInterval) clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, slideDuration);
    }

    initSlider();
});
