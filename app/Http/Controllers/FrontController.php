<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscribeTransactionRequest;
use App\Models\Category;
use App\Models\course;
use App\Models\SubscribeTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    //
    public function index(){
        $courses = course::with([
            'category',
            'teacher',
            'students'
        ])->orderByDesc('id')->get();

        return view('front.index', compact('courses'));
    }

    

    public function learning(course $course, $courseVideoid){

        $user = Auth::user();

        if (!$user->hasActiveSubcription()) {
            return redirect()->route('front.pricing');
        }

        $video = $course->course_videos->firstWhere('id', $courseVideoid);

        $user->courses()->syncWithoutDetaching($course->id);

        return view('front.learning', compact('course', 'video'));
    }

    public function details(course $course){
        
        return view('front.details', compact('course'));
    }

    public function category(Category $category){
        
        $courses = $category->courses()->get();
        return view('front.category', compact('courses'));
    }

    public function pricing(){
        return view('front.pricing');
    }

    public function checkout(){
        return view('front.checkout');
    }

    public function checkout_store(StoreSubscribeTransactionRequest $request){
        $user = Auth::user();

        if (Auth::user()->hasActiveSubcription()) {
            return redirect()->route('front.index');
        }

        DB::transaction(function() use($request, $user){

            $validated = $request->validated();
            
            if ($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }

            $validated['user_id'] = $user->id;
            $validated['total_amount'] = 429000;
            $validated['is_paid'] = false;

            $transaction = SubscribeTransaction::create($validated);
        });

        return redirect()->route('dashboard');

    }
}
