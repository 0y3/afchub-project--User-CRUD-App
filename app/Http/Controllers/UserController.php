<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('user.edit');
        //return view('students.index')->with('user', $users);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required'],
            'country' => ['required','string']
        ]);
   
        $input = $request->all();
        $user = User::find(Auth::id());
        $user->update($input);
     
        return redirect()->route('dashboard')
                        ->with('success','User Detail updated successfully');
    }

}