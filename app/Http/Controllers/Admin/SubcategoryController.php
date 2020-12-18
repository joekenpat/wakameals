<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class SubcategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $subcategories = Subcategory::get();
    $response['status'] = 'success';
    $response['subcategories'] = $subcategories;
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
      'category' => 'required|alpha_dash|exists:categories,slug',
      'icon' => 'sometimes|nullable|image|mimes:png,jpg,svg,jpeg,gif',
    ]);

    $cat = Category::whereSlug($request->category)->firstOrFail();
    $subcategory = Subcategory::updateOrCreate([
      'name' => $request->name,
      'category_id' => $cat->id,
    ]);

    //adding images
    if ($request->hasFile('icon') && $request->file('icon') != null) {
      $image = Image::make($request->file('icon'))->encode('jpg', 1);
      if ($subcategory->icon != null) {
        if (File::exists(public_path("images/categories/") . $subcategory->icon)) {
          File::delete(public_path("images/categories/") . $subcategory->icon);
        }
      }
      $img_name = sprintf("SUBCAT%s%s.jpg", md5($request->name . now()->format('y-m-d H:i:s.u')));
      $image->save(public_path("images/categories/") . $img_name, 70, 'jpg');
      $subcategory->icon = $img_name;
    }

    $response['status'] = 'success';
    $response['message'] = 'Subcategory Created';
    return response()->json($response, Response::HTTP_CREATED);
  }

  /**
   * update resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param Str $subcat_slug
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $subcategory_slug)
  {
    $this->validate($request, [
      'name' => 'sometimes|nullable|string|min:3|max:245',
      'category' => 'sometimes|nullable|intger|exists:categories,slug',
      'icon' => 'sometimes|nullable|image|mimes:png,jpg,svg,jpeg,gif',
    ]);
    try {
      $subcategory = Subcategory::whereSlug($subcategory_slug)->firstOrFail();
      if (isset($request->category)) {
        $cat = Category::whereSlug($request->category)->firstOrFail();
        $subcategory->category_id = $cat->id;
      }
      if (isset($request->name)) {
        $subcategory->name = $request->name;
      }
      $subcategory->update();

      //adding images
      if ($request->hasFile('icon') && $request->file('icon') != null) {
        $image = Image::make($request->file('icon'))->encode('jpg', 1);
        if ($subcategory->icon != null) {
          if (File::exists("images/subcategories/" . $subcategory->icon)) {
            File::delete("images/subcategories/" . $subcategory->icon);
          }
        }
        $img_name = sprintf("SUBCAT%s%s.jpg", md5($request->name . now()->format('y-m-d H:i:s.u')));
        $image->save(public_path("images/categories/") . $img_name, 70, 'jpg');
        $subcategory->icon = $img_name;
        $subcategory->update();
      }

      $response['status'] = 'success';
      $response['message'] = 'Subcategory Updated';
      return response()->json($response, Response::HTTP_OK);
    } catch (ModelNotFoundException $mnf) {
      $response['status'] = 'error';
      $response['message'] = 'Category not found';
      return response()->json($response, Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      $response['status'] = 'error';
      $response['message'] = $e->getMessage();
      return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
