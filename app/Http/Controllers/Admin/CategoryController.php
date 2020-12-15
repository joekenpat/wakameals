<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $categories = Category::get();
    $response['status'] = 'success';
    $response['categories'] = $categories;
    return response()->json($response, Response::HTTP_OK);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|string|min:3|max:245',
      'icon' => 'sometimes|nullable|image|mimes:png,jpg,svg,jpeg,gif',
    ]);
    $category = Category::updateOrCreate([
      'name' => $request->name,
    ]);

    //adding images
    if ($request->hasFile('icon') && $request->file('icon') != null) {
      $image = Image::make($request->file('icon'))->encode('jpg', 1);
      if ($category->icon != null) {
        if (File::exists(public_path("images/categories/") . $category->icon)) {
          File::delete(public_path("images/categories/") . $category->icon);
        }
      }
      $img_name = sprintf("%s%s.jpg", md5($request->name . now()->format('y-m-d H:i:s.u')));
      $image->save(public_path("images/categories/") . $img_name, 70, 'jpg');
      $category->icon = $img_name;
    }
    $response['status'] = 'success';
    $response['message'] = 'Category Created';
    return response()->json($response, Response::HTTP_CREATED);
  }


  /**
   * update  resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param Str $subcat_slug
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $subcat_slug)
  {
    $this->validate($request, [
      'name' => 'sometimes|nullable|string|min:3|max:245',
      'icon' => 'sometimes|nullable|image|mimes:png,jpg,svg,jpeg,gif',
    ]);

    $category = Category::whereSlug($subcat_slug)->firstOrFail();
    if (isset($request->category)) {
      $category->name = $request->name;
    }
    $category->update();

    //adding images
    if ($request->hasFile('icon') && $request->file('icon') != null) {
      $image = Image::make($request->file('icon'))->encode('jpg', 1);
      if ($category->icon != null) {
        if (File::exists("images/subcategories/" . $category->icon)) {
          File::delete("images/subcategories/" . $category->icon);
        }
      }
      $img_name = sprintf("SUBCAT%s%s.jpg", md5($request->name . now()->format('y-m-d H:i:s.u')));
      $image->save(public_path("images/categories/") . $img_name, 70, 'jpg');
      $category->icon = $img_name;
      $category->update();
    }
    $response['status'] = 'success';
    $response['message'] = 'Category Updated';
    return response()->json($response, Response::HTTP_OK);
  }
}
