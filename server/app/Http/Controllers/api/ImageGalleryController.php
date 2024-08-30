<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ImageGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageGalleryController extends Controller
{
    public function index()
    {
        try {
            $imageGallery = ImageGallery::latest()->get();
            return ResponseFormatter::success($imageGallery, 'List Data Of imageGallery');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get imageGallery Data Failed', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'text' => 'required',
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $image = $request->file('image');
            $image->storeAs('public/portfolio', $image->hashName());

            $data = $request->all();
            $data['image'] = $image->hashName();

            $imageGallery = ImageGallery::create($data);

            return ResponseFormatter::success($imageGallery, 'imageGallery Successfully Created');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Store imageGallery', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $imageGallery = ImageGallery::findOrFail($id);
            return ResponseFormatter::success($imageGallery, 'imageGallery Data');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get Data imageGallery Failed', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'text' => 'required',
                'image' => 'image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $imageGallery = ImageGallery::findOrFail($id);

            $data = $request->all();

            if ($request->file('image') == '') {
                $imageGallery->update($data);
            } else {
                Storage::disk('local')->delete('public/portfolio/' . basename($imageGallery->image));

                $image = $request->file('image');
                $image->storeAs('public/portfolio', $image->hashName());
                $data['image'] = $image->hashName();

                $imageGallery->update($data);
            }

            return ResponseFormatter::success($imageGallery, 'imageGallery Data Has Been Successfully Updated');
        } catch (\Exception $th) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $th,
            ], 'Failed To Store imageGallery', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $imageGallery = ImageGallery::findOrFail($id);

            Storage::disk('local')->delete('public/portfolio/' . basename($imageGallery->image));

            $imageGallery->delete();

            return ResponseFormatter::success(null, 'imageGallery Data Has Been Successfully Deleted');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Delete Data');
        }
    }
}
