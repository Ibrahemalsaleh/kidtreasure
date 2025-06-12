<?php

namespace App\Http\Controllers;

use App\Models\WorkshopEvent; 
use App\Models\WorkshopRegistration; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\ValidationException; 

class WorkshopController extends Controller
{
    
    public function index()
    {
        // مثال: جلب فعاليات الورش القادمة المتاحة للتسجيل
        $upcomingEvents = WorkshopEvent::where('event_date', '>=', now())->where('is_open_for_registration', true)->orderBy('event_date')->get();

        return view('pages.workshop', compact('upcomingEvents'));
    }

 
    public function showRegistrationForm(WorkshopEvent $event)
    {
        // // مثال: التحقق مما إذا كانت الفعالية متاحة للتسجيل
        if (!$event->is_open_for_registration || $event->event_date < now()) {
            return redirect()->route('workshops.index')->with('error', 'هذه الورشة غير متاحة للتسجيل حالياً.');
        }
        return view('pages.workshops.register', compact('event'));
    }

   
  
    public function register(Request $request, WorkshopEvent $event)
    {
        // مثال: التحقق من صحة بيانات النموذج
        $request->validate([
            'attendee_name' => 'required|string|max:255',
            'parent_name' => 'nullable|string|max:255', 
            'parent_contact' => 'nullable|string|max:255',
           
        ]);

        // مثال: التحقق مرة أخرى من حالة الفعالية قبل التسجيل النهائي
        if (!$event->is_open_for_registration || $event->event_date < now()) {
             return back()->withInput()->with('error', 'تعذر التسجيل. الورشة غير متاحة.');
        }

        // مثال: حفظ بيانات التسجيل في قاعدة البيانات
        $registration = WorkshopRegistration::create([
            'user_id' => Auth::id(), 
            'event_id' => $event->id,
            'attendee_name' => $request->attendee_name,
            'parent_name' => $request->parent_name ?? (Auth::user()->name ?? null), 
            'parent_contact' => $request->parent_contact ?? (Auth::user()->email ?? Auth::user()->phone ?? null), 
            'registration_date' => now(),
            'status' => 'pending', 
            'payment_status' => ($event->price_jod > 0) ? 'pending' : 'not_applicable', 
        ]);
        $event->increment('current_attendees');
        return redirect()->route('workshops.index')->with('success', 'تم تسجيلك في الورشة بنجاح!');

       
    }

    /**
     * Handle the workshop interest registration form submission
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerInterest(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'parent_name' => 'required|string|max:255',
            'parent_email' => 'required|email|max:255',
            'parent_phone' => 'required|string|max:20',
            'child_name' => 'required|string|max:255',
            'child_age' => 'required|integer|min:3|max:18',
            'preferred_day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'special_requirements' => 'nullable|string',
            'privacy_policy' => 'required|accepted',
        ]);
        
        // Store interest registration in database
        // If you have a WorkshopInterest model, use it here
        // Otherwise, you can create a simple DB entry
        DB::table('workshop_interests')->insert([
            'parent_name' => $validated['parent_name'],
            'parent_email' => $validated['parent_email'],
            'parent_phone' => $validated['parent_phone'],
            'child_name' => $validated['child_name'],
            'child_age' => $validated['child_age'],
            'preferred_day' => $validated['preferred_day'],
            'special_requirements' => $validated['special_requirements'] ?? null,
            'created_at' => now(),
        ]);
        
        // Redirect with success message
        return redirect()->route('workshop')->with('success', 'Thank you for your interest! We will contact you about upcoming workshops.');
    }
}