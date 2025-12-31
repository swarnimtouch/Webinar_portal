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
   1. MODAL & LOGIN LOGIC (No Changes Here)
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
   2. DYNAMIC SLIDER LOGIC (New Industry Standard)
   ========================================= */

// --- CONFIGURATION (Backend se data yaha aayega) ---
// Note: Video ke liye 'poster' zaroor dalein, wo background blur ke liye use hoga.
const sliderData = window.sliderData || [];


// --- ELEMENTS SELECTION ---
const track = document.getElementById("sliderTrack");
const heroBanner = document.querySelector(".hero-banner");

// --- INITIALIZATION FUNCTION ---
function initSlider() {
  // Agar HTML me track nahi mila ya data empty hai, to run mat karo
  if (!track || sliderData.length === 0) return;

  // 1. Purana content clear karein
  track.innerHTML = "";

  // 2. Loop chala kar Slides Create karein
  sliderData.forEach((item) => {
    const slideDiv = document.createElement("div");
    slideDiv.classList.add("slide");

    if (item.type === "image") {
      // Image Element Create
      const img = document.createElement("img");
      img.src = item.src;
      img.alt = "Event Banner";
      slideDiv.appendChild(img);
    } else if (item.type === "video") {
      // Video Element Create
      const video = document.createElement("video");
      video.src = item.src;
      video.poster = item.poster || ""; // Fallback image
      video.muted = true; // Browser policy: Auto-play ke liye mute zaroori hai
      video.loop = false;
      video.autoplay = true;
      video.playsInline = true; // Mobile fix
      // Initially pause rakhenge, active hone par play karenge
      video.pause();
      slideDiv.appendChild(video);
    }

    track.appendChild(slideDiv);
  });

  // 3. Slider Logic Start karein
  startSliderAnimation();
}

// --- SLIDER ANIMATION & CONTROL ---
function startSliderAnimation() {
  const slides = document.querySelectorAll(".slide");
  const totalSlides = slides.length;
  let slideIndex = 0;

  // Timing Settings
  const slideDuration = 4000; // 4 Seconds per slide
  let slideInterval;

  // Function: Update Slide & Background
  const updateSlider = () => {
    // A. Track ko move karo
    track.style.transform = `translateX(-${slideIndex * 100}%)`;

    // B. Background Blur Image Update karo
    updateBackground(slideIndex);

    // C. Video Handling (Performance Optimization)
    // Jo slide active hai sirf wahi play ho, baki pause rahein
    slides.forEach((slide, index) => {
      const video = slide.querySelector("video");
      if (video) {
        if (index === slideIndex) {
          video.currentTime = 0; // Shuru se start karein
          video.play().catch(e => console.log("Auto-play blocked:", e));
        } else {
          video.pause();
        }
      }
    });
  };

  // Function: Background Blur Logic
  const updateBackground = (index) => {
    if (!heroBanner) return;

    const data = sliderData[index];
    let bgUrl = "";

    if (data.type === "image") {
      bgUrl = data.src;
    } else if (data.type === "video") {
      // Video ka background uska 'poster' image hoga
      bgUrl = data.poster || "images/banner-image.jpg";
    }

    heroBanner.style.setProperty("--banner-bg", `url('${bgUrl}')`);
  };

  // Function: Move to Next Slide
  const nextSlide = () => {
    slideIndex++;
    if (slideIndex >= totalSlides) {
      slideIndex = 0;
    }
    updateSlider();
  };

  // --- INITIAL START ---
  // Pehli baar manually run karein taaki first image set ho jaye
  updateSlider();

  // Automatic Timer Start
  slideInterval = setInterval(nextSlide, slideDuration);
}

// DOM load hone par slider init karein
document.addEventListener("DOMContentLoaded", initSlider);
