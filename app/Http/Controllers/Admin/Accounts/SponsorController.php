<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Users\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SponsorController extends Controller
{
    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        $users = User::where('user_type_id',3)->get();
        return view('admin.accounts.sponsors.index', compact('users'));
    }

}
