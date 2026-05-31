<?php

return [
    'common' => [
        'access_denied' => 'Access denied.',
        'series_not_unlocked_for_child' => 'This series is not unlocked for this child.',
    ],
    'auth' => [
        'invalid_credentials' => 'Invalid credentials.',
        'account_disabled' => 'Your account is disabled.',
        'logout_success' => 'Successfully logged out.',
        'reset_link_sent' => 'If this email exists, a reset link has been sent.',
        'invalid_token' => 'Invalid or expired token.',
        'password_reset_success' => 'Password successfully reset.',
    ],
    'child_series' => [
        'series_not_found' => 'Series not found.',
        'already_unlocked' => 'This series is already unlocked for this child.',
        'unlocked_success' => 'Series successfully unlocked.',
        'already_completed' => 'This series is already completed.',
        'completed_success' => 'Series marked as completed.',
    ],
    'notification' => [
        'not_found' => 'Notification not found.',
        'all_read_success' => 'All notifications have been marked as read.',
    ],
    'profile' => [
        'wrong_password' => 'Current password is incorrect.',
        'password_updated' => 'Password successfully updated.',
    ],
    'subscription' => [
        'already_active' => 'This child already has an active subscription.',
        'no_price_configured' => 'No price configured.',
        'creation_error' => 'An error occurred while creating the subscription.',
        'no_active_subscription' => 'No active subscription for this child.',
        'cancel_error' => 'An error occurred while canceling the subscription.',
        'cancel_success' => 'Subscription successfully canceled.',
        'not_found' => 'No subscription found.',
    ],
    'therapist_invitation' => [
        'invalid_code' => 'Invalid invitation code.',
        'already_linked' => 'This child is already being followed by this speech therapist.',
        'attach_success' => 'Speech therapist successfully linked.',
    ],
];
