<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\apiresponse;
use App\Models\Car;
use App\Models\Quick_spec;


class CardController extends Controller
{
    use apiresponse;

public function index()
{
    // Fetch cars with quick_spec relation
    $cards = Car::with('quick_spec')->get();

    return $this->success(
        ['cards' => $cards],
        'Card fetched successfully',
        200
    );
}






}
