function showPopup(type, message) {
    const popup = document.getElementById("statusPopup");
    const text = document.getElementById("popupMessage");

    if (!popup || !text) {
        alert(message); // fallback
        return;
    }

    popup.className = `status-popup ${type}`;
    text.innerText = message;

    popup.style.display = "block";

    setTimeout(() => {
        popup.style.display = "none";
    }, 2000);
}

/* =========================================
   1. MODAL & LOGIN LOGIC
   ========================================= */
const modal = document.getElementById("loginModal");
const loginBtn = document.querySelector(".navbar .btn-gold");
const closeBtn = document.querySelector(".close-modal-btn");
const body = document.body;

function openModal() {
  if (modal) {
    modal.style.display = "flex";
    body.style.overflow = "hidden";
  }
}

function closeModal() {
  if (modal) {
    modal.style.display = "none";
    body.style.overflow = "auto";
  }
}

if (loginBtn) {
  loginBtn.addEventListener("click", openModal);
}

if (closeBtn) {
  closeBtn.addEventListener("click", closeModal);
}

window.addEventListener("click", function (event) {
  if (event.target === modal) {
    closeModal();
  }
});

const loginForm = document.getElementById("loginForm");

/* =========================================
   2. SLIDER LOGIC (OPTIMIZED & STABLE)
   ========================================= */
$(document).ready(function() {
    
    // --- VARIABLES ---
    const $track = $("#sliderTrack");
    const $heroBanner = $(".hero-banner");
    const sliderData = window.sliderData || [];
    let slideIndex = 0;
    let slideInterval;
    const slideDuration = 3000; // ✅ 3 Seconds Timer

    // Agar track ya data nahi hai to return
    if ($track.length === 0 || sliderData.length === 0) return;

    // --- 1. INITIALIZATION ---
    function initSlider() {
        $track.empty(); // Purana content clear

        // Slides create karein
        $.each(sliderData, function(index, item) {
            let mediaElement = '';

            if (item.type === 'image') {
                // Image
                mediaElement = `<img src="${item.src}" alt="Event Banner">`;
            } else if (item.type === 'video') {
                // Video (Muted, PlaysInline important for autoplay)
                mediaElement = `
                    <video poster="${item.poster || ''}" muted playsinline loop>
                        <source src="${item.src}" type="video/mp4">
                    </video>`;
            }

            const slideHtml = `<div class="slide">${mediaElement}</div>`;
            $track.append(slideHtml);
        });

        // Start Slider
        updateSlider();
        startAutoSlide();
    }

    // --- 2. UPDATE SLIDER (Movement & Background) ---
    function updateSlider() {
        // A. Track Move karein
        $track.css("transform", `translateX(-${slideIndex * 100}%)`);

        // B. Background Blur Effect Update karein
        updateBackground(slideIndex);

        // C. Video Handling (Current video play, baaki pause)
        const $allVideos = $(".slide video");
        $allVideos.each(function() {
            $(this).get(0).pause();
            $(this).get(0).currentTime = 0;
        });

        const $currentSlide = $(".slide").eq(slideIndex);
        const $activeVideo = $currentSlide.find("video");

        if ($activeVideo.length > 0) {
            // Video hai to play karein
            const playPromise = $activeVideo.get(0).play();
            if (playPromise !== undefined) {
                playPromise.catch(error => console.log("Auto-play blocked:", error));
            }
        }
    }

    // --- 3. BACKGROUND UPDATER (Blur Effect) ---
    function updateBackground(index) {
        if ($heroBanner.length === 0) return;

        const data = sliderData[index];
        let bgUrl = "";

        if (data.type === 'image') {
            bgUrl = data.src;
        } else if (data.type === 'video') {
            // Video ke liye poster use karein agar available ho
            bgUrl = data.poster || ""; 
        }

        // CSS Variable update
        if(bgUrl) {
             $heroBanner.css("--banner-bg", `url('${bgUrl}')`);
        } else {
             $heroBanner.css("--banner-bg", `none`);
        }
    }

    // --- 4. NEXT SLIDE ---
    function nextSlide() {
        slideIndex++;
        if (slideIndex >= sliderData.length) {
            slideIndex = 0;
        }
        updateSlider();
    }

    // --- 5. TIMER CONTROL ---
    function startAutoSlide() {
        if (slideInterval) clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, slideDuration);
    }

    // Init Call
    initSlider();
});