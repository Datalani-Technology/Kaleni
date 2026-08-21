<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormNotification;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function index()
    {
        $mathA = random_int(1, 12);
        $mathB = random_int(1, 12);
        session(['contact_math' => $mathA + $mathB]);

        return view('contact', compact('mathA', 'mathB'));
    }

    public function store(Request $request)
    {
        // Rate limiting - 3 messages per hour per IP
        $key = 'contact-form:' . $request->ip();
        try {
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                return back()
                    ->withInput()
                    ->withErrors(['rate_limit' => "Too many attempts. Please try again in " . ceil($seconds / 60) . " minutes."]);
            }
        } catch (\Throwable $e) {
            Log::warning('Contact form rate limiter check failed', ['error' => $e->getMessage()]);
            // proceed without rate limiting
        }

        // Honeypot field - if filled, it's a bot
        if ($request->filled('website')) {
            Log::warning('Bot detected on contact form', ['ip' => $request->ip()]);
            return redirect()->route('contact')
                ->with('success', 'Thank you for your message. We will get back to you soon.');
        }

        // Human verification - math check
        $expected = session('contact_math');
        session()->forget('contact_math');
        if ($expected === null || (string) $request->input('human_check') !== (string) $expected) {
            return back()
                ->withInput($request->except('human_check'))
                ->withErrors(['human_check' => 'Please solve the simple math problem correctly to verify you\'re human.']);
        }

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255|min:3',
            'message' => 'required|string|min:10|max:5000',
        ], [
            'name.required' => 'Please enter your name.',
            'name.min' => 'Name must be at least 2 characters.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'subject.required' => 'Please enter a subject.',
            'subject.min' => 'Subject must be at least 3 characters.',
            'message.required' => 'Please enter your message.',
            'message.min' => 'Message must be at least 10 characters.',
            'message.max' => 'Message is too long (maximum 5000 characters).',
        ]);

        // Additional security checks
        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/http[s]?:\/\//i',
            '/www\./i',
            '/\[url\]/i',
            '/\[link\]/i',
            '/<script/i',
            '/javascript:/i',
        ];

        $combinedText = $validated['name'] . ' ' . $validated['subject'] . ' ' . $validated['message'];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $combinedText)) {
                Log::warning('Suspicious content detected in contact form', [
                    'ip' => $request->ip(),
                    'pattern' => $pattern
                ]);
                return back()
                    ->withInput()
                    ->withErrors(['message' => 'Your message contains invalid content. Please try again.']);
            }
        }

        // Check for spam keywords
        $spamKeywords = ['viagra', 'casino', 'lottery', 'winner', 'click here', 'buy now', 'limited time'];
        $lowerText = strtolower($combinedText);
        
        foreach ($spamKeywords as $keyword) {
            if (strpos($lowerText, $keyword) !== false) {
                Log::warning('Spam keyword detected in contact form', [
                    'ip' => $request->ip(),
                    'keyword' => $keyword
                ]);
                RateLimiter::hit($key, 3600); // Penalty hit
                return back()
                    ->withInput()
                    ->withErrors(['message' => 'Your message could not be sent. Please contact us directly.']);
            }
        }

        try {
            // Save the contact message
            Contact::create([
                'name' => strip_tags($validated['name']),
                'email' => filter_var($validated['email'], FILTER_SANITIZE_EMAIL),
                'subject' => strip_tags($validated['subject']),
                'message' => strip_tags($validated['message']),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'is_read' => false,
            ]);
        } catch (\Throwable $e) {
            Log::error('Contact form save failed', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            return back()->withInput($request->except('human_check'))
                ->withErrors(['message' => 'We could not save your message. Please try again or contact us by phone.']);
        }

        // Send email to both info and enquiries (dedupe, validate)
        $candidates = array_filter([
            config('contact.email_info'),
            config('contact.email_enquiries'),
            config('mail.from.address'),
        ]);
        $recipients = [];
        foreach ($candidates as $addr) {
            $addr = is_string($addr) ? trim($addr) : '';
            if ($addr !== '' && filter_var($addr, FILTER_VALIDATE_EMAIL) && !in_array($addr, $recipients, true)) {
                $recipients[] = $addr;
            }
        }
        if ($recipients !== []) {
            try {
                Mail::to($recipients)->send(new ContactFormNotification(
                    $validated['name'],
                    $validated['email'],
                    $validated['subject'],
                    $validated['message'],
                ));
            } catch (\Throwable $e) {
                Log::error('Contact form email failed', [
                    'to' => $recipients,
                    'error' => $e->getMessage(),
                    'exception' => get_class($e),
                ]);
            }
        } else {
            Log::warning('Contact form: no valid email configured (contact.email_info / contact.email_enquiries / mail.from.address). Message saved to DB only.');
        }

        try {
            RateLimiter::hit($key, 3600);
        } catch (\Throwable $e) {
            Log::warning('Contact form rate limiter hit failed', ['error' => $e->getMessage()]);
        }

        Log::info('Contact form submitted', [
            'email' => $validated['email'],
            'ip' => $request->ip(),
        ]);

        return redirect()->route('contact')
            ->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
