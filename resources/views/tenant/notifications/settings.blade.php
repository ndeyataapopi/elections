@extends('layouts.app')

@section('title', 'Notification Settings')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Notification Settings</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notification Settings</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- Container fluid  -->
<!-- ============================================================== -->
<div class="page-content container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- Email Notification Settings  -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-uppercase mb-3">Email Notification Settings</h4>
                    <h6 class="card-subtitle mb-4 text-muted">Configure email notifications for candidates and voters</h6>

                    <form action="{{ route('tenant.notifications.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="enable_candidate_email_notifications" value="0">
                                        <input type="checkbox" class="custom-control-input" id="enable_candidate_email_notifications"
                                            name="enable_candidate_email_notifications" value="1"
                                            {{ $tenant->enable_candidate_email_notifications ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_candidate_email_notifications">
                                            <strong>Enable Candidate Email Notifications</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ml-4">
                                        Send profile update emails to candidates when they are registered.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="enable_voter_email_notifications" value="0">
                                        <input type="checkbox" class="custom-control-input" id="enable_voter_email_notifications"
                                            name="enable_voter_email_notifications" value="1"
                                            {{ $tenant->enable_voter_email_notifications ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_voter_email_notifications">
                                            <strong>Enable Voter Email Notifications</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ml-4">
                                        Send voting link emails to voters when elections are active.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- ============================================================== -->
                        <!-- SMS Notification Settings  -->
                        <!-- ============================================================== -->
                        <h4 class="card-title text-uppercase mb-3">SMS Notification Settings</h4>
                        <h6 class="card-subtitle mb-4 text-muted">Configure SMS notifications for candidates and voters</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="enable_candidate_sms_notifications" value="0">
                                        <input type="checkbox" class="custom-control-input" id="enable_candidate_sms_notifications"
                                            name="enable_candidate_sms_notifications" value="1"
                                            {{ $tenant->enable_candidate_sms_notifications ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_candidate_sms_notifications">
                                            <strong>Enable Candidate SMS Notifications</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ml-4">
                                        Send profile update SMS to candidates when they are registered.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="enable_voter_sms_notifications" value="0">
                                        <input type="checkbox" class="custom-control-input" id="enable_voter_sms_notifications"
                                            name="enable_voter_sms_notifications" value="1"
                                            {{ $tenant->enable_voter_sms_notifications ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_voter_sms_notifications">
                                            <strong>Enable Voter SMS Notifications</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ml-4">
                                        Send voting link SMS to voters when elections are active.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Save Notification Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Test Notifications  -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-uppercase mb-3">Test Email</h4>
                    <h6 class="card-subtitle mb-4 text-muted">Send a test email to verify your email configuration</h6>

                    <form action="{{ route('tenant.notifications.test-email') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="test_email">Test Email Address</label>
                            <input type="email" class="form-control" id="test_email" name="test_email"
                                placeholder="Enter email address" required>
                            <small class="form-text text-muted">
                                We'll send a test email to this address to verify your Mailtrap/SMTP configuration.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-info">Send Test Email</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-uppercase mb-3">Test SMS</h4>
                    <h6 class="card-subtitle mb-4 text-muted">Send a test SMS to verify your SMS configuration</h6>

                    <form action="{{ route('tenant.notifications.test-sms') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="test_phone">Test Phone Number</label>
                            <input type="text" class="form-control" id="test_phone" name="test_phone"
                                placeholder="+1234567890" required>
                            <small class="form-text text-muted">
                                Include country code (e.g., +254712345678). We'll send a test SMS to verify your Twilio configuration.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-info">Send Test SMS</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Configuration Info  -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-uppercase mb-3">Configuration Guide</h4>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Email Configuration (Mailtrap)</h5>
                            <p>To configure email notifications with Mailtrap:</p>
                            <ol>
                                <li>Sign up at <a href="https://mailtrap.io" target="_blank">mailtrap.io</a></li>
                                <li>Go to your inbox and copy the SMTP settings</li>
                                <li>Add these to your <code>.env</code> file:
                                    <pre class="bg-light p-2 mt-2">
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=noreply@elections.test
MAIL_FROM_NAME="Election System"</pre>
                                </li>
                            </ol>
                        </div>

                        <div class="col-md-6">
                            <h5>SMS Configuration (Twilio)</h5>
                            <p>To configure SMS notifications with Twilio:</p>
                            <ol>
                                <li>Sign up at <a href="https://twilio.com" target="_blank">twilio.com</a></li>
                                <li>Get your Account SID and Auth Token from the console</li>
                                <li>Buy a phone number or use your trial number</li>
                                <li>Add these to your <code>.env</code> file:
                                    <pre class="bg-light p-2 mt-2">
SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_FROM_NUMBER=your_twilio_phone_number
SMS_DEFAULT_COUNTRY_CODE=254</pre>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <strong>Note:</strong> These settings need to be configured by your system administrator in the <code>.env</code> file. The settings above are for testing purposes.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->
@endsection
