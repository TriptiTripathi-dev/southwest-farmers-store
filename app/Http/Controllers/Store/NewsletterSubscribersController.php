<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterSubscribersController extends Controller
{
    public function index()
    {
        $subscribers = NewsletterSubscriber::with('store')->latest()->paginate(20);

        return view('settings.newsletter_subscribers', compact('subscribers'));
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'subscriber_id' => 'nullable|integer|exists:newsletter_subscribers,id',
        ]);

        $subject = $request->subject;
        $body = $request->body;
        $subscriberId = $request->subscriber_id;

        if ($subscriberId) {
            $subscriber = NewsletterSubscriber::findOrFail($subscriberId);
            try {
                Mail::to($subscriber->email)->send(new \App\Mail\NewsletterMail($subject, $body));
                return back()->with('success', 'Email sent successfully to ' . $subscriber->email);
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to send email: ' . $e->getMessage());
            }
        } else {
            $subscribers = NewsletterSubscriber::all();

            if ($subscribers->isEmpty()) {
                return back()->with('error', 'No subscribers found to send email to.');
            }

            try {
                foreach ($subscribers as $subscriber) {
                    Mail::to($subscriber->email)->send(new \App\Mail\NewsletterMail($subject, $body));
                }
                return back()->with('success', 'Email sent successfully to all (' . $subscribers->count() . ') subscribers.');
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to send some/all emails: ' . $e->getMessage());
            }
        }
    }

    public function destroy($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        return back()->with('success', 'Subscriber removed successfully.');
    }
}
