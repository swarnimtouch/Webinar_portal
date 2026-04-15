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
                <div class="chat-messages" id="chatMessages">

                </div>
            </div>
            <div class="tab-content" id="qa">
                <div class="qa-list">
                    <div class="qa-item">
                        <img src="{{asset('website/images/user.png')}}" alt="David Kim" class="chat-avatar">
                        <div class="qa-content">
                            <span class="username">David Kim</span>
                            <p>What are the primary ethical considerations when deploying these models at scale?</p>
                            <div class="qa-meta">
                                <button class="upvote-btn"><i class="fa-solid fa-arrow-up"></i> Upvote (12)</button>
                                <span>2 minutes ago</span>
                            </div>
                        </div>
                    </div>
                    <div class="qa-item">
                        <img src="{{asset('website/images/user.png')}}" alt="Emily Rogers" class="chat-avatar">
                        <div class="qa-content">
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

            <div class="tab-content" id="polls">
                <div class="poll-container" id="pollBox">
                    <p class="poll-question" id="pollQuestion">
                        <!-- Question loaded from DB -->
                    </p>

                    <div class="poll-options" id="pollOptions">
                        <!-- Options loaded dynamically -->
                    </div>
                    <p class="poll-message" id="pollMessage">
                        Poll is not active right now.
                    </p>

                    <p class="poll-footer" id="pollFooter">
                        <!-- Optional footer -->
                    </p>
                </div>
            </div>


            <div class="tab-content" id="feedback">
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
            </div>

            <div class="chat-input-area">
                <div class="input-wrapper">
                    <input type="text" id="chatInput" placeholder="Type your message...">
                    <button class="send-btn" id="sendChatBtn">
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
                        @php
                            $file = siteSetting('resources');
                        @endphp

                        @if(!empty($file))
                            <a href="{{ asset('storage/site_settings/'.$file) }}"
                               class="action-box"
                               target="_blank"
                               download>
                                <i class="fa-solid fa-file-lines"></i>
                                <span>Resources</span>
                            </a>
                        @endif
                        @if($activeCertificate)
                            <a href="{{ route('admin.certificate.generate', ['certificateId' => $activeCertificate->id, 'userId' => auth()->id()]) }}"
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
                        <span class="online-count">2,847 online</span>
                    </div>
                    <ul class="participants-list">
                        <li class="participant-item">
                            <img src="{{asset('assets/website/images/user.png')}}" alt="Dr. Marcus Chen"
                                 class="participant-avatar">
                            <div class="participant-info">
                                <span class="name">Dr. Marcus Chen</span>
                                <span class="role">Host</span>
                            </div>
                            <i class="fa-solid fa-microphone participant-icon"></i>
                        </li>
                        <li class="participant-item">
                            <img src="{{asset('assets/website/images/user.png')}}" alt="James Rodriguez"
                                 class="participant-avatar">
                            <div class="participant-info">
                                <span class="name">James Rodriguez</span>
                                <span class="role">Moderator</span>
                            </div>
                            <i class="fa-solid fa-microphone-slash participant-icon muted"></i>
                        </li>
                        <li class="participant-item">
                            <img src="{{asset('assets/website/images/user.png')}}" alt="Michael Torres"
                                 class="participant-avatar">
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
