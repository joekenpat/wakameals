<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Dispatcher;
use App\Models\PasswordReset;
use App\Models\State;
use App\Notifications\PasswordResetCodeSent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class DispatchController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($state_slug)
  {
    $state =  State::whereSlug($state_slug)->firstOrFail();
    $pickups = Dispatcher::whereStateId($state->id)->whereType("pickup")->get();
    $response['status'] = 'success';
    $response['pickups'] = $pickups;
    return response()->json($response, Response::HTTP_OK);
  }
}
