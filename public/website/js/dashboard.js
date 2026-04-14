let Toast;
let pollLoaded = false;
let pollInterval = null;
let lastPollHash = null;
let chatInterval = null;
let attendanceInterval = null;
let lastPing = Date.now();

function startAttendanceTracking() {
    if (!window.trackingEnabled) return;
    if (attendanceInterval) return;

    console.log('Attendance tracking started');
    updateSessionTime();
    attendanceInterval = setInterval(updateSessionTime, 30000);
}

function stopAttendanceTracking() {
    if (attendanceInterval) {
        clearInterval(attendanceInterval);
        attendanceInterval = null;
        console.log('Attendance tracking stopped');
    }
}

function updateSessionTime() {
    const now = Date.now();
    const diff = Math.floor((now - lastPing) / 1000);
    lastPing = now;

    fetch(window.attendanceUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({time_diff: diff})
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('Session updated:', data.session_time, 'sec | diff:', diff);
            } else {
                console.warn('Attendance conditions not met — stopping tracker:', data.message);
                stopAttendanceTracking();
            }
        })
        .catch(err => console.error('Fetch error:', err));
}

document.addEventListener('DOMContentLoaded', () => {
    startAttendanceTracking();
});

document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        stopAttendanceTracking();
    } else {
        lastPing = Date.now();
        startAttendanceTracking();
    }
});

window.addEventListener('beforeunload', () => {
    stopAttendanceTracking();
    navigator.sendBeacon(
        window.attendanceUrl,
        JSON.stringify({time_diff: Math.floor((Date.now() - lastPing) / 1000)})
    );
});
document.addEventListener("DOMContentLoaded", () => {
    const tabLinks = document.querySelectorAll(".tab-link");
    const tabContents = document.querySelectorAll(".tab-content");

    Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });


    tabLinks.forEach((link) => {
        link.addEventListener("click", () => {
            const tabId = link.getAttribute("data-tab");

            tabLinks.forEach((btn) => btn.classList.remove("active"));
            link.classList.add("active");

            tabContents.forEach((content) => {
                content.style.display = "none";
            });

            const activeTab = document.getElementById(tabId);
            if (activeTab) activeTab.style.display = "block";

            const chatInputArea = document.querySelector(".chat-input-area");

            if (chatInputArea) {
                if (tabId === "polls" || tabId === "feedback") {
                    chatInputArea.style.display = "none";

                    if (tabId === "polls") {
                        loadPoll(true);

                        if (!pollInterval) {
                            pollInterval = setInterval(() => {
                                loadPoll(true);
                            }, 10000);
                        }
                    }

                } else {
                    chatInputArea.style.display = "block";
                }
            }
        });
    });

    function loadChatMessages() {
        fetch(window.chatMessagesUrl)

            .then(res => res.json())
            .then(messages => {
                const box = document.getElementById('chatMessages');
                if (!box) return;

                box.innerHTML = '';

                messages.forEach(msg => {
                    box.innerHTML += `
                      <div class="message">
                          <img src="/website/images/user.png" class="chat-avatar">
                          <div class="message-content">
                              <span class="username">${msg.user}</span>
                              <p>${msg.message}</p>
                          </div>
                      </div>
                  `;
                });

                box.scrollTop = box.scrollHeight;
            });
    }


    document.getElementById('sendChatBtn')?.addEventListener('click', sendChat);
    document.getElementById('chatInput')?.addEventListener('keypress', e => {
        if (e.key === 'Enter') sendChat();
    });

    function sendChat() {
        const input = document.getElementById('chatInput');
        const msg = input.value.trim();
        if (!msg) return;

        fetch(window.chatSendUrl, {

            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({message: msg})
        }).then(() => {
            input.value = '';
            loadChatMessages();
        });
    }

    document.querySelector('.tab-link[data-tab="chat"]')
        ?.addEventListener('click', () => {
            loadChatMessages();

            if (!chatInterval) {
                chatInterval = setInterval(loadChatMessages, 3000);
            }
        });

    function loadPoll(force = false) {
        if (pollLoaded && !force) return;
        pollLoaded = true;

        fetch(window.pollUrl)

            .then(res => res.json())
            .then(data => {

                const pollQuestionEl = document.getElementById("pollQuestion");
                const pollOptionsEl = document.getElementById("pollOptions");
                const pollMessageEl = document.getElementById("pollMessage");

                if (!data.poll) {
                    pollQuestionEl.innerText = "";
                    pollOptionsEl.innerHTML = "";
                    pollMessageEl.style.display = "block";
                    pollMessageEl.innerText = "Poll is not active right now.";
                    return;
                }

                pollMessageEl.style.display = "none";

                const poll = data.poll;
                const voted = data.voted;

                const pollHash = JSON.stringify(poll);
                if (lastPollHash === pollHash && !force) return;
                lastPollHash = pollHash;

                pollQuestionEl.innerText = poll.question;
                pollOptionsEl.innerHTML = "";

                let answers = poll.answers;
                if (typeof answers === "string") {
                    try {
                        answers = JSON.parse(answers);
                    } catch {
                        answers = [];
                    }
                }

                if (!Array.isArray(answers)) answers = [];

                answers.forEach((ans, index) => {
                    const label = String.fromCharCode(65 + index);

                    const btn = document.createElement("button");
                    btn.className = "poll-option";
                    btn.innerHTML = `<strong>${label}.</strong> ${ans}`;

                    if (voted && voted.answer === ans) {
                        btn.classList.add("selected");
                        btn.innerHTML +=
                            ' <span><i class="fa-solid fa-check"></i> Voted</span>';
                        btn.disabled = true;
                    }

                    btn.onclick = () => submitPollVote(poll.id, ans, btn);
                    pollOptionsEl.appendChild(btn);
                });
            })
            .catch(() => {
                console.warn("Poll fetch failed");
            });
    }

    function submitPollVote(pollId, answer, btn) {
        fetch(window.pollVoteUrl, {

            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({poll_id: pollId, answer}),
        })
            .then((res) => res.json())
            .then((data) => {
                if (!data.status) {
                    Toast.fire({icon: "info", title: data.message});
                    return;
                }

                document.querySelectorAll(".poll-option").forEach((b) => {
                    b.classList.remove("selected");
                    const s = b.querySelector("span");
                    if (s) s.remove();
                    b.disabled = true;
                });

                btn.classList.add("selected");
                btn.insertAdjacentHTML(
                    "beforeend",
                    '<span><i class="fa-solid fa-check"></i> Voted</span>'
                );

                Toast.fire({icon: "success", title: "Vote submitted"});
            })
            .catch(() => {
                Toast.fire({icon: "error", title: "Vote failed"});
            });
    }

    function initMobileChatButton() {
        const mobileChatBtn = document.getElementById("mobileChatBtn");
        const chatSidebar = document.querySelector(".chat-sidebar");

        if (mobileChatBtn && chatSidebar) {
            mobileChatBtn.addEventListener("click", () => {
                chatSidebar.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });

                const chatTab = document.querySelector('.tab-link[data-tab="chat"]');
                if (chatTab) {
                    chatTab.click();
                }

                mobileChatBtn.style.transform = "scale(0.95)";
                setTimeout(() => {
                    mobileChatBtn.style.transform = "";
                }, 150);
            });
        }
    }

    function initMobileScrolling() {
        if (window.innerWidth > 767) return;

        const videoSection = document.querySelector(".video-section");
        const chatSidebar = document.querySelector(".chat-sidebar");
        const chatScrollable = document.querySelector(
            ".sidebar-scrollable-content"
        );

        if (!videoSection || !chatSidebar || !chatScrollable) return;

        let isScrolling = false;
        let scrollTimer = null;
        let isChatSidebarVisible = false;

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    isChatSidebarVisible = entry.isIntersecting;
                });
            },
            {threshold: 1}
        );

        observer.observe(chatSidebar);

        function isVideoSectionAtBottom() {
            return (
                videoSection.scrollHeight - videoSection.scrollTop <=
                videoSection.clientHeight + 1
            );
        }

        function isChatSidebarAtTop() {
            return chatScrollable.scrollTop === 0;
        }

        function isChatSidebarAtBottom() {
            return (
                chatScrollable.scrollHeight - chatScrollable.scrollTop <=
                chatScrollable.clientHeight + 1
            );
        }

        function isVideoSectionAtTop() {
            return videoSection.scrollTop === 0;
        }

        function handleVideoSectionScroll(event) {
            if (isScrolling) return;

            if (isVideoSectionAtBottom() && event.deltaY > 0) {
                if (isChatSidebarVisible) {
                    isScrolling = true;
                    chatScrollable.scrollTop += event.deltaY * 2;
                    clearTimeout(scrollTimer);
                    scrollTimer = setTimeout(() => {
                        isScrolling = false;
                    }, 50);
                    event.preventDefault();
                }
            } else if (isVideoSectionAtTop() && event.deltaY < 0) {
                return true;
            }
        }

        function handleChatSidebarScroll(event) {
            if (isScrolling) return;

            if (isChatSidebarAtTop() && event.deltaY < 0) {
                isScrolling = true;
                videoSection.scrollTop += event.deltaY * 2;
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(() => {
                    isScrolling = false;
                }, 50);
                event.preventDefault();
            } else if (isChatSidebarAtBottom() && event.deltaY > 0) {
                return true;
            }
        }

        videoSection.addEventListener("wheel", handleVideoSectionScroll, {passive: false});
        chatScrollable.addEventListener("wheel", handleChatSidebarScroll, {passive: false});

        let startY = 0;

        videoSection.addEventListener("touchstart", (e) => {
            startY = e.touches[0].clientY;
        }, {passive: true});

        videoSection.addEventListener("touchmove", (e) => {
            if (isScrolling) return;
            const currentY = e.touches[0].clientY;
            const deltaY = startY - currentY;

            if (isVideoSectionAtBottom() && deltaY < -5) {
                if (isChatSidebarVisible) {
                    isScrolling = true;
                    chatScrollable.scrollTop -= deltaY * 0.5;
                    clearTimeout(scrollTimer);
                    scrollTimer = setTimeout(() => {
                        isScrolling = false;
                    }, 50);
                    e.preventDefault();
                }
            }
            startY = currentY;
        }, {passive: false});

        chatScrollable.addEventListener("touchstart", (e) => {
            startY = e.touches[0].clientY;
        }, {passive: true});

        chatScrollable.addEventListener("touchmove", (e) => {
            if (isScrolling) return;
            const currentY = e.touches[0].clientY;
            const deltaY = startY - currentY;

            if (isChatSidebarAtTop() && deltaY > 5) {
                isScrolling = true;
                videoSection.scrollTop -= deltaY * 0.5;
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(() => {
                    isScrolling = false;
                }, 50);
                e.preventDefault();
            }
            startY = currentY;
        }, {passive: false});
    }

    function initScrollToBottom() {
        const bottomBtn = document.getElementById("scrollToBottomBtn");
        if (!bottomBtn) return;

        bottomBtn.addEventListener("click", () => {
            window.scrollTo({top: document.documentElement.scrollHeight, behavior: "smooth"});
        });

        window.addEventListener("scroll", () => {
            const scrollPosition = window.scrollY + window.innerHeight;
            const totalHeight = document.documentElement.scrollHeight;

            if (scrollPosition >= totalHeight - 100) {
                bottomBtn.classList.add("btn-hidden");
            } else {
                bottomBtn.classList.remove("btn-hidden");
            }
        });
    }

    function initDesktopScrollIsolation() {
        if (window.innerWidth <= 767) return;

        const videoSection = document.querySelector(".video-section");
        const chatScrollable = document.querySelector(".sidebar-scrollable-content");

        if (!videoSection || !chatScrollable) return;

        videoSection.addEventListener("wheel", (e) => {
            if (videoSection.scrollHeight > videoSection.clientHeight) {
                const isAtTop = videoSection.scrollTop === 0;
                const isAtBottom =
                    videoSection.scrollTop + videoSection.clientHeight >=
                    videoSection.scrollHeight - 1;

                if ((isAtTop && e.deltaY < 0) || (isAtBottom && e.deltaY > 0)) return true;
                e.stopPropagation();
            }
        });

        chatScrollable.addEventListener("wheel", (e) => {
            if (chatScrollable.scrollHeight > chatScrollable.clientHeight) {
                const isAtTop = chatScrollable.scrollTop === 0;
                const isAtBottom =
                    chatScrollable.scrollTop + chatScrollable.clientHeight >=
                    chatScrollable.scrollHeight - 1;

                if ((isAtTop && e.deltaY < 0) || (isAtBottom && e.deltaY > 0)) return true;
                e.stopPropagation();
            }
        });
    }

    function initializeScrollFeatures() {
        if (window.innerWidth <= 767) {
            initMobileScrolling();
        } else {
            initDesktopScrollIsolation();
        }
    }

    function handleResize() {
        initializeScrollFeatures();
    }

    function initSmoothScrolling() {
        const internalLinks = document.querySelectorAll('a[href^="#"]');

        internalLinks.forEach((link) => {
            link.addEventListener("click", (e) => {
                const targetId = link.getAttribute("href");

                if (!targetId || targetId === "#") return;

                e.preventDefault();
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    targetElement.scrollIntoView({behavior: "smooth", block: "start"});
                }
            });
        });
    }

    function initFollowButton() {
        const followBtn = document.querySelector(".follow-btn");
        if (followBtn) {
            followBtn.addEventListener("click", () => {
                const isFollowing = followBtn.innerHTML.includes("Following");

                if (isFollowing) {
                    followBtn.innerHTML = '<i class="fa-solid fa-user-plus"></i> Follow';
                    followBtn.style.backgroundColor = "";
                } else {
                    followBtn.innerHTML = '<i class="fa-solid fa-user-check"></i> Following';
                    followBtn.style.backgroundColor = "#22c55e";
                }
            });
        }
    }

    function initActionButtons() {
        const likeBtn = document.querySelector(".action-btn:nth-child(1)");
        const commentBtn = document.querySelector(".action-btn:nth-child(2)");

        if (likeBtn) {
            likeBtn.addEventListener("click", () => {
                const currentText = likeBtn.innerHTML;
                const likeCount = parseInt(currentText.match(/\d+/)?.[0]) || 1234;

                if (currentText.includes("fa-thumbs-up")) {
                    likeBtn.innerHTML = `<i class="fa-solid fa-thumbs-up" style="color: #3b82f6;"></i> ${likeCount + 1}`;
                } else {
                    likeBtn.innerHTML = `<i class="fa-solid fa-thumbs-up"></i> ${likeCount - 1}`;
                }
            });
        }

        if (commentBtn) {
            commentBtn.addEventListener("click", () => {
                const qaTab = document.querySelector('.tab-link[data-tab="qa"]');
                if (qaTab) {
                    qaTab.click();

                    if (window.innerWidth <= 767) {
                        const chatSidebar = document.querySelector(".chat-sidebar");
                        if (chatSidebar) {
                            chatSidebar.scrollIntoView({behavior: "smooth", block: "start"});
                        }
                    }
                }
            });
        }
    }

    function initFeedbackSystem() {
        const stars = document.querySelectorAll(".star-rating-widget .star");
        const ratingText = document.querySelector(".rating-text");
        const submitBtn = document.getElementById("submitFeedbackBtn");
        let currentRating = 0;

        const messages = ["Poor", "Fair", "Good", "Very Good", "Excellent"];

        stars.forEach((star) => {
            star.addEventListener("click", () => {
                currentRating = parseInt(star.getAttribute("data-value"));

                stars.forEach((s) => {
                    const value = parseInt(s.getAttribute("data-value"));
                    if (value <= currentRating) {
                        s.classList.add("active");
                    } else {
                        s.classList.remove("active");
                    }
                });

                if (currentRating > 0) {
                    ratingText.textContent = messages[currentRating - 1];
                    ratingText.style.color = "var(--text-primary)";
                }
            });
        });

        if (submitBtn) {
            submitBtn.addEventListener("click", () => {
                const textVal = document.getElementById("feedbackText").value.trim();

                if (currentRating === 0) {
                    Toast.fire({icon: "warning", title: "Please select a star rating"});
                    return;
                }

                fetch(window.feedbackStoreUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify({
                        rating: currentRating,
                        comment: textVal !== "" ? textVal : null,
                    }),
                })
                    .then((res) => res.json())
                    .then((data) => {
                        if (data.status) {
                            Toast.fire({icon: "success", title: "Thank you! Feedback saved"});

                            currentRating = 0;
                            document.getElementById("feedbackText").value = "";
                            document
                                .querySelectorAll(".star-rating-widget .star")
                                .forEach((s) => s.classList.remove("active"));
                            ratingText.textContent = "Select stars to rate";
                        }
                    })
                    .catch(() => {
                        Toast.fire({icon: "error", title: "Server error"});
                    });
            });
        }
    }

    function initializeAllFeatures() {
        initializeScrollFeatures();
        initSmoothScrolling();
        initMobileChatButton();
        initFollowButton();
        initActionButtons();
        initScrollToBottom();
        initFeedbackSystem();
    }

    initializeAllFeatures();
    if (window._toastSuccess) {
        Toast.fire({
            icon: "success",
            title: window._toastSuccess
        });
        window._toastSuccess = null;
    }

    const activeTabLink = document.querySelector(".tab-link.active");
    const defaultTabId = activeTabLink ? activeTabLink.getAttribute("data-tab") : null;

    if (defaultTabId === "chat") {
        loadChatMessages();
        if (!chatInterval) {
            chatInterval = setInterval(loadChatMessages, 3000);
        }
    } else if (defaultTabId === "polls") {
        loadPoll(true);
        if (!pollInterval) {
            pollInterval = setInterval(() => {
                loadPoll(true);
            }, 10000);
        }
    }

    const chatInputArea = document.querySelector(".chat-input-area");
    if (chatInputArea && (defaultTabId === "polls" || defaultTabId === "feedback")) {
        chatInputArea.style.display = "none";
    }

    const debouncedResize = debounce(handleResize, 250);
    window.addEventListener("resize", debouncedResize);

    window.addEventListener("beforeunload", () => {
        window.removeEventListener("resize", debouncedResize);
        if (chatInterval) clearInterval(chatInterval);
        if (pollInterval) clearInterval(pollInterval);
    });

        $('.profile-info').on('click', function(e) {
            e.stopPropagation();
            $(this).toggleClass('active'); 
        });

        $(document).on('click', function() {
            $('.profile-info').removeClass('active');
        });

        $('.profile-dropdown').on('click', function(e) {
            e.stopPropagation();
        });
});

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function isMobileDevice() {
    return window.innerWidth <= 767;
}

console.log("LiveStream Pro initialized successfully!");
