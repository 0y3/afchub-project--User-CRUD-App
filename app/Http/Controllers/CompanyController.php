<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\CompanyDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->filled('search')){
            $companies = Company::search($request->search)
                            ->where('user_id', Auth::id())->get();
        }
        else{
        $companies = Company::whereBelongsTo(Auth::user())->get(); //Auth::id(); // or Auth::user()->id
        }
        //dd(Company::whereBelongsTo(Auth::user())->get());
        // dd(Auth::user()->company());
        // dd(User::find(Auth::user()->id)->company());
        return view('company.index')->with('companies', $companies)->with('count', $companies->count());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Company::whereBelongsTo(Auth::user())->get()->count() >= 3)
        {
            return redirect()->route('company')
                        ->with('warning','You can only Add 3 Companies ');
        }
        return view('company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required','string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:companies'],
        ]);
        if(Company::whereBelongsTo(Auth::user())->get()->count() >= 3)
        {
            return redirect()->route('company')
                        ->with('warning','You can only Add 3 Companies ');
        }
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        Company::create($input);
      
        return redirect()->route('company')
                        ->with('success','Company created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $companies = Company::where('id', $id)->where('user_id', Auth::id())->first();
        return view('company.create')->with('companies', $companies);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $companies = Company::where('id', $id)->where('user_id', Auth::id())->first();
        return view('company.edit')->with('companies', $companies);
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
            'location' => ['required','string']
        ]);
   
        $input = $request->all();
        $Company = Company::where('id', $id)->where('user_id', Auth::id())->first();
        $Company->update($input);
     
        return redirect()->route('company')
                        ->with('success','Company updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Company::where('id', $id)->where('user_id', Auth::id())->delete();
        return redirect()->route('company')
                        ->with('success','Company deleted');
    }

    public function nestedrel(Request $request)
    {
        $relCompany = Company::with([
            'users',
            'companies_depatment'
        ])->get();

        $relUser = User::with([
            'users',
            'company'=>[
                'companies_depatment'  => fn ($query) => $query->latest(), 
            ],
        ])->get();

        $cb = function ($query){
            $query->where('is_deleted', 'N');
        };

        $books = User::with([
                    'author'            => fn ($query) => $query->where('is_deleted', 'N'), 
                    'author.contacts'   => fn ($query2) => $query2->where('is_deleted', 'N'),
                ])->get();
        


           
        $books = User::with(['author.contact' => $cb])
                       ->whereHas('author', function($query) use ($cb) { $query->where('is_online', 'Y')->whereHas('contacts', $cb);})
                ->get();

        $books  = User::with(['author' => function ($query) { 
                                    $query->where('is_active', 0); // auther condition
                                        $query->with(['contacts' => function ($q2) {
                                            $q2->where('is_active', 1); // contacts condition
                                         }
                                        ]);
                                    }
                ])->get();

        $books = User::with([
                    'Queue' => function($query) { 
                        $query->select('PRIVATE','queue_naam')
                        ->withDefault(['PRIVATE' => '','queue_naam' => '']); 
                    },
                    'AgentStatus' => function($query) { 
                        $query->select('PRIVATE','status','status_colour')
                        ->withDefault(['PRIVATE' => '','status' => '','status_colour' => '']); 
                    },
                    'author' => function($query) { 
                        $query->select('PRIVATE','naam')
                        ->where('is_active', 0)->withDefault(['PRIVATE' => '','naam' => '']); // auther condition
                    },
                    'author.contacts' => function($query) { 
                        $query->select('PRIVATE','naam')->withDefault(['PRIVATE' => '','naam' => '']); 
                    },
                    'authorTbl' => function($query) { 
                            $query->select('PRIVATE','naam')
                            ->where('is_active', 0)->withDefault(['PRIVATE' => '','naam' => '']) // auther condition
                            ->with([
                                'contacts' => fn($query) => $query->select('PRIVATE','naam')->withDefault(['PRIVATE' => '','naam' => '']),
                            
                            ]);
                    },
                ])->get();
                
        dd($relCompany);
        //dd(Company::whereBelongsTo(Auth::user())->get());
        // dd(Auth::user()->company());
        // dd(User::find(Auth::user()->id)->company());
    }
}