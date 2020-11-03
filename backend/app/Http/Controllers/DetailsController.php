<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Product;
use Spatie\Searchable\Search;

class DetailsController extends Controller
{
    public function search(Request $request){
        $result = (new Search())->registerModel(User::class, ['firstName', 'lastName'])->search($request->input('query'));

        return response()->json($result);
    }
}
