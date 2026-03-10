<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;

class EnquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enquiries = Enquiry::latest()->paginate(10);
        return view('store.enquiries.index', compact('enquiries'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        // Mark as read when viewed
        if (!$enquiry->is_read) {
            $enquiry->update(['is_read' => true]);
        }
        
        return view('store.enquiries.show', compact('enquiry'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->delete();
        
        return redirect()->route('store.enquiries.index')->with('success', 'Enquiry deleted successfully.');
    }
}
