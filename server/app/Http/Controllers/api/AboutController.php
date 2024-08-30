<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $about = About::latest()->get();
            return ResponseFormatter::success($about, 'List Data Of about');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get About Data Failed', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'description' => 'required',
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $image = $request->file('image');
            $image->storeAs('public/portfolio', $image->hashName());

            $data = $request->all();
            $data['image'] = $image->hashName();

            $about = About::create($data);

            return ResponseFormatter::success($about, 'About Successfully Created');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Store About', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $about = About::findOrFail($id);
            return ResponseFormatter::success($about, 'About Data');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get Data About Failed', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'description' => 'required',
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $about = About::findOrFail($id);

            $data = $request->all();

            if ($request->file('image') == '') {
                $about->update($data);
            } else {
                Storage::disk('local')->delete('public/portfolio/' . basename($about->image));

                $image = $request->file('image');
                $image->storeAs('public/portfolio', $image->hashName());
                $data['image'] = $image->hashName();

                $about->update($data);
            }

            return ResponseFormatter::success($about, 'About Data Has Been Successfully Updated');
        } catch (\Exception $th) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $th,
            ], 'Failed To Store About', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $about = About::findOrFail($id);

            Storage::disk('local')->delete('public/portfolio/' . basename($about->image));

            $about->delete();

            return ResponseFormatter::success(null, 'About Data Has Been Successfully Deleted');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Delete Data');
        }
    }
}
