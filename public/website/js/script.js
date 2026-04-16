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

if (loginBtn) loginBtn.addEventListener("click", openLoginModal);
if (registerBtn) registerBtn.addEventListener("click", openRegisterModal);
if (closeLoginBtn) closeLoginBtn.addEventListener("click", closeLoginModal);
if (closeRegisterBtn) closeRegisterBtn.addEventListener("click", closeRegisterModal);

window.addEventListener("click", function (event) {
    if (event.target === loginModal) closeLoginModal();
    if (event.target === registerModal) closeRegisterModal();
});

if (window._openLoginModal) {
    openLoginModal();
    window._openLoginModal = false;
}
if (window._openRegisterModal) {
    openRegisterModal();
    window._openRegisterModal = false;
}


$(document).ready(function () {
    const $track = $("#sliderTrack");
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
                mediaElement = `<video poster="${item.poster || ''}" muted playsinline loop>
                                    <source src="${item.src}" type="video/mp4">
                                </video>`;
            }
            $track.append(`<div class="slide">${mediaElement}</div>`);
        });
        updateSlider();
        startAutoSlide();
    }

    function updateSlider() {
        $track.css("transform", `translateX(-${slideIndex * 100}%)`);
        updateBackground(slideIndex);
        $(".slide video").each(function () {
            this.pause();
            this.currentTime = 0;
        });
        const $activeVideo = $(".slide").eq(slideIndex).find("video");
        if ($activeVideo.length > 0) {
            const p = $activeVideo.get(0).play();
            if (p !== undefined) p.catch(e => console.log("Auto-play blocked:", e));
        }
    }

    function updateBackground(index) {
        const data = sliderData[index];
        const $bgImg = $("#bgImage");
        const $bgVid = $("#bgVideo");
        $(".bg-media").removeClass("active");
        if (data.type === 'image') {
            $bgVid.trigger('pause');
            $bgImg.attr("src", data.src).addClass("active");
        } else if (data.type === 'video') {
            $bgVid.attr("src", data.src).addClass("active");
            const v = $bgVid.get(0);
            v.load();
            const p = v.play();
            if (p !== undefined) p.catch(e => console.log("Bg Video Auto-play blocked:", e));
        }
    }

    function nextSlide() {
        slideIndex = (slideIndex + 1) % sliderData.length;
        updateSlider();
    }

  function startAutoSlide() {
    if (slideInterval) clearInterval(slideInterval);
    slideInterval = setInterval(nextSlide, slideDuration);
  }

  initSlider();
});

$(document).ready(function () {

    $("#loginForm").validate({
        errorElement: "div",
        errorClass: "error-text",
        errorPlacement: function (error, element) {
            error.insertAfter(element.closest(".email-input-group"));
        },
        highlight: function (el) {
            $(el).addClass("is-invalid");
        },
        unhighlight: function (el) {
            $(el).removeClass("is-invalid");
        }
    });

    $("#loginForm input").each(function () {
        const $input = $(this);
        const label = $input.data("label") || "This field";
        const type = $input.attr("type");
        const name = $input.attr("name");

        if ($input.data("is-required") == 1) {
            $input.rules("add", {required: true, messages: {required: label + " is required"}});
        }
        if (type === "email" || name === "email") {
            $input.rules("add", {email: true, messages: {email: "Please enter a valid email address"}});
        }
        if (type === "tel" || name === "mobile_number" || name === "phone") {
            $input.rules("add", {
                digits: true, minlength: 10, maxlength: 10,
                messages: {
                    digits: "Only numbers are allowed",
                    minlength: "Mobile number must be 10 digits",
                    maxlength: "Mobile number must be 10 digits"
                }
            });
        }
    });

    $("#registerForm").validate({
        errorElement: "div",
        errorClass: "error-text",
        errorPlacement: function (error, element) {
            error.insertAfter(element.closest(".email-input-group"));
        },
        highlight: function (el) {
            $(el).addClass("is-invalid");
        },
        unhighlight: function (el) {
            $(el).removeClass("is-invalid");
        }
    });

    $("#registerForm").find("input, select, textarea").each(function () {
        const $input = $(this);
        const label = $input.data("label") || "This field";
        const type = $input.attr("type");
        const name = $input.attr("name");
        let rules = {}, messages = {};

        if ($input.data("is-required") == 1) {
            rules.required = true;
            messages.required = label + " is required";
        }
        if (name === "email") {
            rules.email = true;
            messages.email = "Please enter a valid email address";
        }
        if (type === "tel" || name === "mobile_number") {
            rules.digits = true;
            rules.minlength = 10;
            rules.maxlength = 10;
            messages.digits = "Only numbers are allowed";
            messages.minlength = "Mobile number must be 10 digits";
            messages.maxlength = "Mobile number must be 10 digits";
        }
        if (name === "password") {
            rules.minlength = 6;
            messages.minlength = label + " must be at least 6 characters";
        }
        if (Object.keys(rules).length > 0) {
            $input.rules("add", {...rules, messages});
        }
    });
});


    $(document).ready(function () {
        $('.select2').select2({
            width: '100%',
            allowClear: false,
            dropdownParent: $('#registerModal'),
            minimumResultsForSearch: 0
    });


    $(document).on('select2:open', (e) => {
        setTimeout(() => {
            const searchInput = document.querySelector('.select2-search__field');
            if (searchInput) searchInput.focus();

            const $select = $(e.target);
            const $parentGroup = $select.closest('.email-input-group');

            if ($parentGroup.length) {
            const fullWidth = $parentGroup.outerWidth();

            const innerSelectLeft = $select.next('.select2-container').offset().left;
            const outerGroupLeft = $parentGroup.offset().left;
            const offsetDiff = innerSelectLeft - outerGroupLeft;

            document.documentElement.style.setProperty('--dynamic-width', fullWidth + 'px');
            document.documentElement.style.setProperty('--dynamic-margin', `-${offsetDiff}px`);
            }
        }, 10);
    });

    $.get('/get-countries', function (countries) {
        $('#country').append(
            countries.map(c => `<option value="${c.name}" data-id="${c.id}">${c.name}</option>`)
        );
        const india = countries.find(c => c.name.toLowerCase() === 'india');
        if (india) {
            $('#country').is(':visible')
                ? $('#country').val(india.name).trigger('change')
                : loadStates(india.id);
        }
    });

    function loadStates(countryId) {
        $('#state').empty().append('<option value="">Select State</option>').trigger('change');
        $('#city').empty().append('<option value="">Select City</option>').trigger('change');
        $.get(`/get-states/${countryId}`, function (states) {
            $('#state').append(
                states.map(s => `<option value="${s.name}" data-id="${s.id}">${s.name}</option>`)
            );
            const gujarat = states.find(s => s.name.toLowerCase() === 'gujarat');
            if (gujarat) {
                $('#state').is(':visible')
                    ? $('#state').val(gujarat.name).trigger('change')
                    : loadCities(gujarat.id);
            }
        });
    }

    function loadCities(stateId) {
        $('#city').empty().append('<option value="">Select City</option>').trigger('change');
        $.get(`/get-cities/${stateId}`, function (cities) {
            $('#city').append(
                cities.map(c => `<option value="${c.name}" data-id="${c.id}">${c.name}</option>`)
            );
        });
    }

    $('#country').on('change', function () {
        const countryId = $('#country option:selected').data('id');
        $('#state').empty().append('<option value="">Select State</option>').trigger('change');
        $('#city').empty().append('<option value="">Select City</option>').trigger('change');
        if (!countryId) return;
        $.get(`/get-states/${countryId}`, function (states) {
            $('#state').append(
                states.map(s => `<option value="${s.name}" data-id="${s.id}">${s.name}</option>`)
            );
            if (window.oldState) $('#state').val(window.oldState).trigger('change');
        });
    });

    $('#state').on('change', function () {
        const stateId = $('#state option:selected').data('id');
        $('#city').empty().append('<option value="">Select City</option>').trigger('change');
        if (!stateId) return;
        $.get(`/get-cities/${stateId}`, function (cities) {
            $('#city').append(
                cities.map(c => `<option value="${c.name}" data-id="${c.id}">${c.name}</option>`)
            );
            if (window.oldCity) $('#city').val(window.oldCity).trigger('change');
        });
    });
});
