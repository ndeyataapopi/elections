<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Candidate Profile - {{ $candidate->election->name ?? 'Election' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .profile-form {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header-section {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2cabe3;
        }
        .deadline-alert {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
        }
        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #2cabe3;
        }
        .success-message {
            background-color: #d4edda;
            border-left: 5px solid #28a745;
            padding: 20px;
            margin-bottom: 20px;
        }
        .error-message {
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-form">
            @if(isset($success))
                <div class="success-message">
                    <h4 class="text-success"><i class="bi bi-check-circle"></i> Success!</h4>
                    <p class="mb-0">{{ $success }}</p>
                    
                </div>
            @elseif(isset($error))
                <div class="error-message">
                    <h4 class="text-danger"><i class="bi bi-exclamation-triangle"></i> Error</h4>
                    <p class="mb-0">{{ $error }}</p>
                    
                </div>
            @else
                <div class="header-section">
                    <h2>Update Your Profile</h2>
                    <p class="text-muted">{{ $election->name ?? 'Upcoming Election' }}</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if($candidate->profile_edit_deadline)
                <div class="alert deadline-alert">
                    <strong>Deadline:</strong> Please complete your profile update before 
                    <strong>{{ \Carbon\Carbon::parse($candidate->profile_edit_deadline)->format('F j, Y \a\t g:i A') }}</strong>.
                    After this date, you will no longer be able to edit your profile.
                </div>
                @endif

                <form action="{{ route('candidate.update.profile', $token) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" 
                                   value="{{ old('first_name', $candidate->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" 
                                   value="{{ old('last_name', $candidate->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="staff_number" class="form-label">Staff Number</label>
                            <input type="text" class="form-control" id="staff_number" 
                                   value="{{ $candidate->staff_number }}" disabled>
                            <small class="text-muted">Staff number cannot be changed</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select @error('gender') is-invalid @enderror" 
                                    id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender', $candidate->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $candidate->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $candidate->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" 
                                   value="{{ old('email', $candidate->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" 
                                   value="{{ old('phone', $candidate->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="job_title" class="form-label">Job Title / Position</label>
                        <input type="text" class="form-control @error('job_title') is-invalid @enderror" 
                               id="job_title" name="job_title" 
                               value="{{ old('job_title', $candidate->job_title) }}">
                        @error('job_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="portfolio_id" class="form-label">Portfolio/Position Running For <span class="text-danger">*</span></label>
                        <select class="form-select @error('portfolio_id') is-invalid @enderror" 
                                id="portfolio_id" name="portfolio_id" required>
                            <option value="">Select Portfolio</option>
                            @foreach($portfolios as $portfolio)
                                <option value="{{ $portfolio->id }}" 
                                        {{ old('portfolio_id', $candidate->portfolio_id) == $portfolio->id ? 'selected' : '' }}>
                                    {{ $portfolio->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('portfolio_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="manifesto" class="form-label">Manifesto / Vision Statement</label>
                        <textarea class="form-control @error('manifesto') is-invalid @enderror" 
                                  id="manifesto" name="manifesto" rows="4" 
                                  placeholder="Share your vision and what you hope to achieve if elected...">{{ old('manifesto', $candidate->manifesto) }}</textarea>
                        <small class="text-muted">Maximum 2000 characters</small>
                        @error('manifesto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="photo" class="form-label">Profile Photo</label>
                        <div class="mb-2">
                            @if($candidate->photo)
                                <img src="{{ asset('storage/' . $candidate->photo) }}" 
                                     alt="Current Photo" class="photo-preview mb-2">
                            @else
                                <div class="text-muted mb-2">No photo uploaded yet</div>
                            @endif
                        </div>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                               id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Accepted formats: JPEG, PNG, GIF. Max size: 2MB</small>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning">
                        <strong>Important:</strong> This is a one-time submission. Once you submit your profile, 
                        you will not be able to make further changes. Please review all information carefully.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Submit Profile
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
