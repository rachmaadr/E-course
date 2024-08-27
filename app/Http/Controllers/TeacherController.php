<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $teachers = Teacher::orderBy('id','desc')->get();
        return view('admin.teachers.index', ['teachers' => $teachers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request)
    {
        //
        
            $validated = $request->validated();
            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                # code...
                return back()->with([
                    'email' => 'Data tidak ditemukan'
                ]);
            }

            if ($user->hasRole('teacher')) {
                # code...
                return back()->withErrors([
                    'email' => 'Email sudah ditambahkan'
                ]);
            }

            DB::transaction(function () use($user, $validated){
                $validated['user_id'] = $user->id;
                $validated['is_active'] = true;

                Teacher::create($validated);
                if ($user->hasRole('student')) {
                    # code...
                    $user->removeRole('student');
                }
                // Tambahkan role 'teacher'
                $user->assignRole('teacher');
            });
            return redirect()->route('admin.teachers.index')->with('success', 'Teacher added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
        try {
            //code...
            $teacher->delete();

            $user = \App\Models\User::find($teacher->user_id);
            $user->removeRole('teacher');
            $user->assignRole('student');

            return redirect()->back();
        } catch (\Exception $th) {
            //throw $th;
            DB::rollBack();
            $error = ValidationException::withMessages([
                "system_error" => ['System Error!' . $th->getMessage()],
            ]);
            throw $error;
            
        }
    }
}
