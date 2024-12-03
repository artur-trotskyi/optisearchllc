<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum ResourceMessagesEnum: string
{
    use EnumTrait;

    case DefaultSuccessfully = 'Request processed successfully.';
    case DefaultFailed = 'Request failed.';
    case DataRetrievedSuccessfully = 'Data retrieved successfully.';
    case DataCreatedSuccessfully = 'Data created successfully.';
    case DataUpdatedSuccessfully = 'Data updated successfully.';
    case DataDeletedSuccessfully = 'Data deleted successfully.';
    case RegisterSuccessful = 'Register successful.';
    case LoginSuccessful = 'Login successful.';
    case YouAreLoggedOut = 'You are logged out.';
    case AlreadyLoggedOut = 'Already logged out.';
    case VerificationEmailSent = 'A verification email has been sent to your email address. Please check your inbox and complete the verification process.';
    case UserNotFound = 'User not found.';
    case InvalidHash = 'Invalid hash.';
    case EmailAlreadyVerified = 'Email already verified.';
    case EmailVerifiedSuccessfully = 'Email verified successfully.';
    case PasswordResetSuccessful = 'Your password has been successfully reset.';
}
