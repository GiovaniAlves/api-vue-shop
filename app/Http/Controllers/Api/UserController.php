<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $repository;

    public function __construct(User $user)
    {
        $this->repository = $user;
    }

    public function search(Request $request)
    {
        $data = $request->all();

        $data['month'] = substr($data['date'], 0, 2);
        $data['year'] = substr($data['date'], 3, 5);

       $qtyUsers = $this->repository->whereMonth('created_at', $data['month'])->whereYear('created_at', $data['year'])->count();

       return response(['amount' => $qtyUsers]);
    }
}
