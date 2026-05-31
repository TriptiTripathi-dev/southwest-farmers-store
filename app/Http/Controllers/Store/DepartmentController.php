<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $query = Department::where(function($q) use ($storeId) {
            $q->whereNull('store_id') // Global
              ->orWhere('store_id', $storeId); // Local
        });

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%")
                  ->orWhere('code', 'ilike', "%{$request->search}%");
            });
        }

        if ($request->status !== null) {
            $query->where('is_active', $request->status);
        }

        $departments = $query->latest()->paginate(10);

        return view('store.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('store.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
        ]);

        Department::create([
            'store_id' => Auth::user()->store_id ?? Auth::user()->id,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'is_active' => true
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully');
    }

    public function edit(Department $department)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        if ($department->store_id != $storeId) {
            abort(403, 'Unauthorized action.');
        }

        return view('store.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        if ($department->store_id != $storeId) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
        ]);

        $department->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        if ($department->store_id != $storeId) {
            abort(403, 'Unauthorized action.');
        }

        // Check if products exist in this department
        if ($department->products()->exists()) {
            return back()->with('error', 'Cannot delete department. It has associated products.');
        }

        $department->delete();
        return back()->with('success', 'Department deleted successfully');
    }

    public function changeStatus(Request $request)
    {
        try {
            $user = Auth::user();
            $storeId = $user->store_id ?? $user->id;

            $dept = Department::where('id', $request->id)
                ->where('store_id', $storeId)
                ->first();

            if (!$dept) {
                return response()->json(['message' => 'Unauthorized or not found'], 403);
            }

            $dept->update(['is_active' => $request->status]);
            return response()->json(['message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating status'], 500);
        }
    }
}
