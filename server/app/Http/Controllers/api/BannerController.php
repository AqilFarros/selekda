<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        try {
            $banner = Banner::latest()->get();
            return ResponseFormatter::success($banner, 'List Data Of banner');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get banner Data Failed', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $image = $request->file('image');
            $image->storeAs('public/banner', $image->hashName());

            $data = $request->all();
            $data['image'] = $image->hashName();

            $banner = Banner::create($data);

            return ResponseFormatter::success($banner, 'banner Successfully Created');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Store banner', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $banner = Banner::findOrFail($id);
            return ResponseFormatter::success($banner, 'banner Data');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get Data banner Failed', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'image' => 'image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $banner = Banner::findOrFail($id);

            $data = $request->all();

            if ($request->file('image') == '') {
                $banner->update($data);
            } else {
                Storage::disk('local')->delete('public/banner/' . basename($banner->image));

                $image = $request->file('image');
                $image->storeAs('public/banner', $image->hashName());
                $data['image'] = $image->hashName();

                $banner->update($data);
            }

            return ResponseFormatter::success($banner, 'banner Data Has Been Successfully Updated');
        } catch (\Exception $th) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $th,
            ], 'Failed To Store banner', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $banner = Banner::findOrFail($id);

            Storage::disk('local')->delete('public/banner/' . basename($banner->image));

            $banner->delete();

            return ResponseFormatter::success(null, 'banner Data Has Been Successfully Deleted');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Delete Data');
        }
    }
}
