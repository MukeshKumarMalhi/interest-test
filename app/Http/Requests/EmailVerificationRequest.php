<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Events\EmailVerificationEvent;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class EmailVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = User::findOrFail($this->route('id'));
        if (!hash_equals((string)$this->route('hash'),
            sha1($user->getEmailForVerification()))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $user = User::findOrFail($this->route('id'));
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            $user = User::findOrFail($this->route('id'));
            event(new Verified($user));
            broadcast(new EmailVerificationEvent($user,$user->id))->toOthers();
            echo "Account Is Verified Now";
            die;
        }
    }
}
