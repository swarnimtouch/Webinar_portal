<aside class="chat-sidebar">
    <div class="chat-sidebar-container">
        <div class="tab-navigation">
            @if($enable_live_chat)<button class="tab-link active" data-tab="chat">
                <i class="fa-solid fa-comments"></i> Live Chat
            </button>@endif
            @if($enable_comments)<button class="tab-link {{ !$enable_live_chat ? 'active' : '' }}" data-tab="comments"><i class="fa-solid fa-message"></i> Comments</button>@endif
            <button class="tab-link d-none" data-tab="qa">
                <i class="fa-solid fa-circle-question"></i> Q&A
            </button>
            @if($enable_polls)<button class="tab-link {{ !$enable_live_chat && !$enable_comments ? 'active' : '' }}" data-tab="polls">
                <i class="fa-solid fa-poll"></i> Polls
            </button>@endif
            @if($enable_feedback)<button class="tab-link {{ !$enable_live_chat && !$enable_comments && !$enable_polls ? 'active' : '' }}" data-tab="feedback">
                <i class="fa-solid fa-star"></i> Feedback
            </button>@endif
        </div>
        <div class="sidebar-scrollable-content">
            @if($enable_live_chat)<div class="tab-content" id="chat">
                <div class="chat-messages" id="chatMessages">
                    <div class="chat-skeleton" id="chatSkeleton">
                        <div class="skeleton-row">
                            <div class="skeleton-avatar"></div>
                            <div class="skeleton-bubble" style="width:55%"></div>
                        </div>
                        <div class="skeleton-row right">
                            <div class="skeleton-avatar"></div>
                            <div class="skeleton-bubble" style="width:40%"></div>
                        </div>
                        <div class="skeleton-row">
                            <div class="skeleton-avatar"></div>
                            <div class="skeleton-bubble" style="width:65%"></div>
                        </div>
                    </div>
                </div>

                <div class="chat-typing" id="chatTyping">
                    <div class="typing-dots">
                        <span></span><span></span><span></span>
                    </div>
                    <span id="typingText">Someone is typing…</span>
                </div>
            </div>@endif

            @if($enable_comments)
                <div class="tab-content" id="comments" style="display:none">
                    <div class="event-comments-panel">
                        <div class="event-comments-list" id="eventCommentsList">
                            <div class="chat-empty-state comments-empty-state">
                                <i class="fa-regular fa-comments"></i>
                                <p>No comments yet.<br>Be the first to add a comment!</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="tab-content" id="qa" style="display:none">
                <div class="qa-list" id="qaList">
                </div>
                <div class="qna-input-area">
                    <div class="chat-input-wrapper">
                        <input type="text" id="qnaInput" placeholder="Ask a question…" maxlength="500">
                        <button class="chat-send-btn" id="submitQnaBtn">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if($enable_polls)<div class="tab-content" id="polls" style="display:none">
                <div class="poll-container" id="pollBox">
                    <p class="poll-question" id="pollQuestion"></p>
                    <div class="poll-options" id="pollOptions"></div>
                    <p class="poll-message" id="pollMessage">Poll is not active right now.</p>
                    <p class="poll-footer" id="pollFooter"></p>
                </div>
            </div>@endif

            @if($enable_feedback)<div class="tab-content" id="feedback" style="display:none">
                <div class="feedback-container">
                    <div class="feedback-header">
                        <h3>Rate this Session</h3>
                        <p class="feedback-subtitle" id="feedbackSubtitle">Share a quick rating and comment about this webinar.</p>
                    </div>
                    <div class="star-rating-widget" id="feedbackStars">
                        <i class="fa-solid fa-star star" data-value="1"></i>
                        <i class="fa-solid fa-star star" data-value="2"></i>
                        <i class="fa-solid fa-star star" data-value="3"></i>
                        <i class="fa-solid fa-star star" data-value="4"></i>
                        <i class="fa-solid fa-star star" data-value="5"></i>
                    </div>
                    <p class="rating-text" id="feedbackRatingText">Select stars to rate</p>
                    <textarea id="feedbackText" placeholder="Write your feedback here..." maxlength="1000"></textarea>
                    <div class="feedback-actions">
                        <button id="submitFeedbackBtn" class="btn-primary-full">Submit Feedback</button>
                    </div>
                    <div class="feedback-success-state" id="feedbackSuccessState" style="display:none">
                        <div class="feedback-success-icon">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="feedback-success-copy">
                            <strong id="feedbackSuccessTitle">Feedback saved</strong>
                            <p id="feedbackSuccessText">Thanks for sharing your thoughts.</p>
                        </div>
                    </div>
                </div>
            </div>@endif

            @if($enable_live_chat)<div class="chat-input-area" id="chatInputArea">
                <button class="chat-scroll-chip" id="chatScrollChip">
                    <i class="fa-solid fa-arrow-down"></i> New messages
                </button>

                <div class="chat-input-wrapper">
                    <input type="text" id="chatInput" placeholder="Type a message…" maxlength="500" autocomplete="off">
                    <button class="chat-send-btn" id="sendChatBtn">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
                <div class="chat-char-count" id="chatCharCount">0 / 500</div>
            </div>@endif
            @if($enable_comments)<div class="chat-input-area event-comment-input-area" id="commentInputArea" style="display:none">
                <div class="chat-input-wrapper">
                    <input id="eventCommentText" type="text" placeholder="Write a comment..." maxlength="2000" autocomplete="off">
                    <button id="submitEventCommentBtn" class="chat-send-btn" type="button" aria-label="Send comment">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
                <div class="event-comment-note">Comments appear after admin approval.</div>
            </div>@endif
            <div class="sidebar-extra-content">

                <div class="quick-actions-panel">
                    <h3>Quick Actions</h3>
                    <div class="actions-grid">
                        <button class="action-box" id="raiseHandActionBtn">
                            <i class="fa-solid fa-hand"></i>
                            <span>Raise Hand</span>
                        </button>
                        @if($resources->isNotEmpty())
                            <a href="#session-resources" class="action-box">
                                <i class="fa-solid fa-file-lines"></i>
                                <span>Resources</span>
                            </a>
                        @endif
                        @if($active_certificate)
                            <a href="{{ event_route('certificate.generate', ['certificateId' => $active_certificate->id, 'userId' => auth()->id()]) }}"
                               class="action-box">
                                <i class="fa-solid fa-certificate"></i>
                                <span>Certificate</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="participants-panel">
                    <div class="participants-header">
                        <h3>Participants</h3>
                        <span class="online-count" id="onlineCount">
            <i class="fa-solid fa-circle" style="font-size:0.5rem;color:#22c55e"></i>
            <span id="onlineCountNum">0</span> online
        </span>
                    </div>

                    {{-- Raise Hand Button --}}
                    <button class="raise-hand-btn" id="raiseHandBtn">
                        <span class="hand-icon"><i class="fa-solid fa-hand"></i></span>
                        <span class="hand-label">Raise Hand</span>
                        <span class="hand-badge" id="handBadge" style="display:none"></span>
                    </button>

                    {{-- Hand Raised Notification Toast --}}
                    <div class="hand-toast" id="handToast" style="display:none">
                        <i class="fa-solid fa-hand"></i>
                        <span id="handToastText"></span>
                    </div>

                    {{-- Online Users List --}}
                    <ul class="participants-list" id="participantsList">
                        <li class="participants-loading">
                            <i class="fa-solid fa-spinner fa-spin"></i> Connecting…
                        </li>
                    </ul>

                    <div class="hands-raised-section" id="handsRaisedSection" style="display:none">
                        <div class="hands-raised-header">
                            <i class="fa-solid fa-hand" style="color:#eab308"></i>
                            Hands Raised
                            <span class="hands-count-badge" id="handsCountBadge">0</span>
                        </div>
                        <ul class="hands-list" id="handsList"></ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</aside>
