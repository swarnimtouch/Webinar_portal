<aside class="chat-sidebar">
    <div class="chat-sidebar-container">

        <div class="tab-navigation">
            <button class="tab-link active" data-tab="chat">
                <i class="fa-solid fa-comments"></i> Live Chat
            </button>
            <button class="tab-link" data-tab="qa">
                <i class="fa-solid fa-circle-question"></i> Q&A
            </button>
            <button class="tab-link" data-tab="polls">
                <i class="fa-solid fa-poll"></i> Polls
            </button>
            <button class="tab-link" data-tab="feedback">
                <i class="fa-solid fa-star"></i> Feedback
            </button>
        </div>

        <div class="sidebar-scrollable-content">



            <div class="tab-content" id="chat">
                <div class="chat-messages">
                    <div class="message">
                        <img src="{{asset('assets/Website/images/user.png')}}" alt="Jane Doe" class="chat-avatar">
                        <div class="message-content">
                            <span class="username">Jane Doe</span>
                            <p>This is an amazing presentation! So insightful.</p>
                        </div>
                    </div>
                    <div class="message">
                        <img src="{{asset('assets/Website/images/user.png')}}" alt="John Smith" class="chat-avatar">
                        <div class="message-content">
                            <span class="username">John Smith</span>
                            <p>Could you elaborate on the multi-modal capabilities?</p>
                        </div>
                    </div>
                    <div class="message">
                        <img src="{{asset('assets/Website/images/user.png')}}" alt="Sarah Lee" class="chat-avatar">
                        <div class="message-content">
                            <span class="username">Sarah Lee</span>
                            <p>Loving the examples. Really helps clarify the concepts.</p>
                        </div>
                    </div>
                    <div class="message">
                        <img src="{{asset('assets/Website/images/user.png')}}" alt="Mike Chen" class="chat-avatar">
                        <div class="message-content">
                            <span class="username">Mike Chen</span>
                            <p>Will the slides be available for download after the session?</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="qa" style="display: none;">
                <div class="qa-list">
                    <div class="qa-item">
                        <img src="{{asset('assets/Website/images/user.png')}}" alt="David Kim" class="chat-avatar"> <div class="qa-content">
                            <span class="username">David Kim</span>
                            <p>What are the primary ethical considerations when deploying these models at scale?</p>
                            <div class="qa-meta">
                                <button class="upvote-btn"><i class="fa-solid fa-arrow-up"></i> Upvote (12)</button>
                                <span>2 minutes ago</span>
                            </div>
                        </div>
                    </div>
                    <div class="qa-item">
                        <img src="{{asset('assets/Website/images/user.png')}}" alt="Emily Rogers" class="chat-avatar"> <div class="qa-content">
                            <span class="username">Emily Rogers</span>
                            <p>How does the cost of training compare to fine-tuning existing foundation models?</p>
                            <div class="qa-meta">
                                <button class="upvote-btn"><i class="fa-solid fa-arrow-up"></i> Upvote (8)</button>
                                <span>5 minutes ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="polls" style="display: none;">
                <div class="poll-container">
                    <p class="poll-question">What is your experience level with generative AI?</p>
                    <div class="poll-options">
                        <button class="poll-option">Beginner</button>
                        <button class="poll-option">Intermediate</button>
                        <button class="poll-option">Expert</button>
                    </div>
                    <p class="poll-footer">Poll closes in 2 minutes</p>
                </div>
            </div>

            <div class="tab-content" id="feedback" style="display: none;">
                <div class="feedback-container">
                    <h3>Rate this Session</h3>
                    <div class="star-rating-widget">
                        <i class="fa-solid fa-star star" data-value="1"></i>
                        <i class="fa-solid fa-star star" data-value="2"></i>
                        <i class="fa-solid fa-star star" data-value="3"></i>
                        <i class="fa-solid fa-star star" data-value="4"></i>
                        <i class="fa-solid fa-star star" data-value="5"></i>
                    </div>
                    <p class="rating-text">Select stars to rate</p>

                    <textarea id="feedbackText" placeholder="Write your feedback here..."></textarea>

                    <button id="submitFeedbackBtn" class="btn-primary-full">Submit Feedback</button>
                </div>
                <div class="feedback-success" style="display: none; text-align: center; padding: 2rem;">
                    <i class="fa-solid fa-circle-check" style="font-size: 3rem; color: #22c55e; margin-bottom: 1rem;"></i>
                    <h3>Thank You!</h3>
                    <p style="color: var(--text-secondary);">Your feedback has been recorded.</p>
                </div>
            </div>

            <div class="chat-input-area">
                <div class="input-wrapper">
                    <input type="text" placeholder="Type your message...">
                    <button class="send-btn">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>

            <div class="sidebar-extra-content">

                <div class="quick-actions-panel">
                    <h3>Quick Actions</h3>
                    <div class="actions-grid">
                        <button class="action-box">
                            <i class="fa-solid fa-hand"></i>
                            <span>Raise Hand</span>
                        </button>
                        <button class="action-box">
                            <i class="fa-solid fa-share-from-square"></i>
                            <span>Share Screen</span>
                        </button>
                        <button class="action-box">
                            <i class="fa-solid fa-file-lines"></i>
                            <span>Resources</span>
                        </button>
                        <button class="action-box">
                            <i class="fa-solid fa-certificate"></i>
                            <span>Certificate</span>
                        </button>
                    </div>
                </div>

                <div class="participants-panel">
                    <div class="participants-header">
                        <h3>Participants</h3>
                        <span class="online-count">2,847 online</span>
                    </div>
                    <ul class="participants-list">
                        <li class="participant-item">
                            <img src="{{asset('assets/Website/images/user.png')}}" alt="Dr. Marcus Chen" class="participant-avatar">
                            <div class="participant-info">
                                <span class="name">Dr. Marcus Chen</span>
                                <span class="role">Host</span>
                            </div>
                            <i class="fa-solid fa-microphone participant-icon"></i>
                        </li>
                        <li class="participant-item">
                            <img src="{{asset('assets/Website/images/user.png')}}" alt="James Rodriguez" class="participant-avatar">
                            <div class="participant-info">
                                <span class="name">James Rodriguez</span>
                                <span class="role">Moderator</span>
                            </div>
                            <i class="fa-solid fa-microphone-slash participant-icon muted"></i>
                        </li>
                        <li class="participant-item">
                            <img src="{{asset('assets/Website/images/user.png')}}" alt="Michael Torres" class="participant-avatar">
                            <div class="participant-info">
                                <span class="name">Michael Torres</span>
                                <span class="role">Participant</span>
                            </div>
                            <i class="fa-solid fa-hand participant-icon raised"></i>
                        </li>
                    </ul>
                    <a href="#" class="view-all-link">View all participants <i class="fa-solid fa-arrow-right"></i></a>
                </div>

            </div>
        </div>
</aside>
