<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cast Your Vote - {{ $election->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .ballot-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .ballot-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 30px;
            background: linear-gradient(135deg, #0B1F3A 0%, #1a3a5c 100%);
            color: white;
            border-radius: 10px;
        }
        .portfolio-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .portfolio-title {
            color: #0B1F3A;
            border-bottom: 3px solid #2cabe3;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .candidate-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .candidate-card:hover {
            border-color: #2cabe3;
            box-shadow: 0 2px 8px rgba(44, 171, 227, 0.2);
        }
        .candidate-card.selected {
            border-color: #28a745;
            background-color: #f8fff8;
        }
        .candidate-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e9ecef;
        }
        .submit-btn {
            background: linear-gradient(135deg, #0B1F3A 0%, #1a3a5c 100%);
            border: none;
            padding: 15px 50px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .submit-btn:hover {
            background: linear-gradient(135deg, #1a3a5c 0%, #0B1F3A 100%);
        }
        .election-info {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
        }
        .candidate-radio {
            display: none;
        }
        .manifesto-text {
            font-size: 0.9rem;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container ballot-container">
        <div class="ballot-header">
            <h1 class="mb-2">{{ $election->name }}</h1>
            <p class="mb-0">Secure Electronic Ballot</p>
            <div class="election-info">
                <small>
                    <strong>Voting Period:</strong> 
                    {{ \Carbon\Carbon::parse($election->start_time)->format('M j, Y g:i A') }} - 
                    {{ \Carbon\Carbon::parse($election->end_time)->format('M j, Y g:i A') }}
                </small>
            </div>
        </div>

        <div class="warning-box">
            <strong>Important Voting Instructions:</strong>
            <ul class="mb-0 mt-2">
                <li>You can cast only {{ $election->allowed_votes ?? 1 }} ballot(s) in total for this election. Please review all candidates before submitting.</li>
                <li>Your vote is completely anonymous and cannot be changed after submission.</li>
                @foreach($election->portfolios as $portfolio)
                <li>For <strong>{{ $portfolio->name }}</strong>: Select up to {{ $portfolio->max_votes ?? 1 }} candidate(s).</li>
                @endforeach
            </ul>
        </div>

        <form method="POST" action="{{ route('vote.submit', request()->token) }}" id="votingForm">
            @csrf

            @if($election->portfolios && $election->portfolios->count() > 0)
                @foreach($election->portfolios as $portfolio)
                    <div class="portfolio-section">
                        <h3 class="portfolio-title">{{ $portfolio->name }}</h3>
                        <p class="text-muted mb-3">{{ $portfolio->description }}</p>

                        @php
                            $candidates = $portfolio->candidates;
                        @endphp

                        @php
                            $maxVotes = $portfolio->max_votes ?? 1;
                        @endphp
                        @if($candidates->count() > 0)
                            <div class="portfolio-vote-info mb-2">
                                <span class="badge badge-info">Select up to {{ $maxVotes }} candidate(s)</span>
                                <span class="badge badge-secondary portfolio-count" data-portfolio="{{ $portfolio->id }}">Selected: 0/{{ $maxVotes }}</span>
                            </div>
                            @foreach($candidates as $candidate)
                                <label class="candidate-card d-block" data-portfolio="{{ $portfolio->id }}" onclick="selectCandidate(this, {{ intval($portfolio->id) }}, {{ intval($candidate->id) }}, {{ intval($maxVotes) }})">
                                    <div class="d-flex align-items-start">
                                        <input type="checkbox"
                                               name="votes[{{ $portfolio->id }}][]"
                                               value="{{ intval($candidate->id) }}"
                                               class="candidate-checkbox"
                                               id="candidate_{{ $portfolio->id }}_{{ $candidate->id }}"
                                               data-portfolio="{{ $portfolio->id }}">
                                        <div class="me-3">
                                            @if($candidate->photo)
                                                <img src="{{ asset('storage/' . $candidate->photo) }}"
                                                     alt="{{ $candidate->first_name }}"
                                                     class="candidate-photo">
                                            @else
                                                <div class="candidate-photo d-flex align-items-center justify-content-center bg-light">
                                                    <span class="text-muted">No Photo</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $candidate->first_name }} {{ $candidate->last_name }}</h5>
                                            @if($candidate->job_title)
                                                <p class="text-muted mb-1"><small>{{ $candidate->job_title }}</small></p>
                                            @endif
                                            @if($candidate->manifesto)
                                                <p class="manifesto-text mb-0">{{ Str::limit($candidate->manifesto, 150) }}</p>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            <div class="selection-indicator">
                                                <i class="bi bi-square fs-4 text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                No candidates available for this portfolio yet.
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="alert alert-warning">
                    No portfolios have been configured for this election.
                </div>
            @endif

            @php
                $totalRequiredVotes = $election->portfolios->sum('max_votes') ?? $election->portfolios->count();
            @endphp
            <div class="alert alert-info text-center">
                <strong>Total Votes Selected:</strong> <span id="totalVotesSelected">0</span> / <span id="totalRequiredVotes">{{ $totalRequiredVotes }}</span>
                <br><small class="text-muted">({{ $election->portfolios->count() }} portfolio(s) - Total votes required)</small>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary submit-btn">
                    Submit My Vote
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const totalRequiredVotes = {{ $totalRequiredVotes }};

        function updatePortfolioCount(portfolioId) {
            const checkboxes = document.querySelectorAll(`input[data-portfolio="${portfolioId}"]:checked`);
            const countBadge = document.querySelector(`.portfolio-count[data-portfolio="${portfolioId}"]`);
            const maxVotes = parseInt(document.querySelector(`label[data-portfolio="${portfolioId}"]`).closest('.portfolio-section').querySelector('.badge-info').textContent.match(/\d+/)[0]);
            if (countBadge) {
                countBadge.textContent = `Selected: ${checkboxes.length}/${maxVotes}`;
                if (checkboxes.length >= maxVotes) {
                    countBadge.classList.remove('badge-secondary');
                    countBadge.classList.add('badge-success');
                } else {
                    countBadge.classList.remove('badge-success');
                    countBadge.classList.add('badge-secondary');
                }
            }
        }

        function updateTotalCount() {
            const allChecked = document.querySelectorAll('.candidate-checkbox:checked');
            const totalSpan = document.getElementById('totalVotesSelected');
            const requiredSpan = document.getElementById('totalRequiredVotes');
            totalSpan.textContent = allChecked.length;

            if (allChecked.length >= totalRequiredVotes) {
                totalSpan.parentElement.classList.remove('alert-info');
                totalSpan.parentElement.classList.add('alert-success');
            } else {
                totalSpan.parentElement.classList.remove('alert-success');
                totalSpan.parentElement.classList.add('alert-info');
            }
        }

        function selectCandidate(element, portfolioId, candidateId, maxVotes) {
            const checkbox = document.getElementById('candidate_' + portfolioId + '_' + candidateId);
            const portfolioSection = element.closest('.portfolio-section');
            const currentSelected = portfolioSection.querySelectorAll('input[type="checkbox"]:checked').length;

            if (!checkbox.checked && currentSelected >= maxVotes) {
                alert(`You can only select up to ${maxVotes} candidate(s) for this portfolio.`);
                return;
            }

            // Toggle checkbox
            checkbox.checked = !checkbox.checked;

            // Toggle visual selection
            if (checkbox.checked) {
                element.classList.add('selected');
            } else {
                element.classList.remove('selected');
            }

            updatePortfolioCount(portfolioId);
            updateTotalCount();
        }

        // Form validation
        document.getElementById('votingForm').addEventListener('submit', function(e) {
            const allChecked = document.querySelectorAll('.candidate-checkbox:checked');
            const totalSelected = allChecked.length;

            if (totalSelected === 0) {
                e.preventDefault();
                alert('Please select at least one candidate before submitting.');
                return;
            }

            // Validate total required votes matches portfolio rules sum
            if (totalSelected < totalRequiredVotes) {
                e.preventDefault();
                alert(`You must select exactly ${totalRequiredVotes} votes in total. You have selected ${totalSelected}. Please review the portfolio rules.`);
                return;
            }

            if (totalSelected > totalRequiredVotes) {
                e.preventDefault();
                alert(`You have selected too many candidates. The total should be ${totalRequiredVotes} based on portfolio rules. You have selected ${totalSelected}.`);
                return;
            }

            // Validate per-portfolio limits and sanitize values
            const portfolios = document.querySelectorAll('.portfolio-section');
            let portfolioError = false;
            let portfolioErrors = [];

            portfolios.forEach(section => {
                const portfolioName = section.querySelector('.portfolio-title')?.textContent;
                const portfolioId = section.querySelector('.candidate-checkbox')?.dataset.portfolio;
                if (portfolioId) {
                    const selected = section.querySelectorAll('input[type="checkbox"]:checked').length;
                    const maxVotes = parseInt(section.querySelector('.badge-info').textContent.match(/\d+/)[0]);

                    if (selected > maxVotes) {
                        portfolioError = true;
                        section.style.border = '2px solid #dc3545';
                        portfolioErrors.push(`${portfolioName}: max ${maxVotes}, selected ${selected}`);
                    } else if (selected < maxVotes) {
                        portfolioError = true;
                        section.style.border = '2px solid #ffc107';
                        portfolioErrors.push(`${portfolioName}: requires ${maxVotes}, selected ${selected}`);
                    } else {
                        section.style.border = '';
                    }

                    // Sanitize checkbox values - ensure they're clean integers
                    section.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
                        const cleanValue = parseInt(cb.value, 10);
                        if (isNaN(cleanValue) || cleanValue <= 0) {
                            portfolioError = true;
                            portfolioErrors.push(`${portfolioName}: Invalid candidate value`);
                        } else {
                            cb.value = cleanValue; // Set clean value
                        }
                    });
                }
            });

            if (portfolioError) {
                e.preventDefault();
                alert('Portfolio selection errors:\n' + portfolioErrors.join('\n'));
                return;
            }

            if (!confirm('Are you sure you want to submit your vote? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>