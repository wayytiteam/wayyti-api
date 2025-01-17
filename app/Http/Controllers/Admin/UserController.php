<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersonaUser;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $status = $request->query('status');
        $users = User::where('is_admin', false)
            ->when($keyword, function (Builder $query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->whereRaw('LOWER(email) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereRaw('LOWER(username) LIKE ?', ['%' . strtolower($keyword) . '%']);
                });
            })
            ->when($status === 'active', function (Builder $query) {
                $query->whereHas('attendances', function ($subQuery) {
                    $subQuery->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()]);
                });
            })
            ->when($status === 'inactive', function (Builder $query) {
                $query->whereDoesntHave('attendances', function ($subQuery) {
                    $subQuery->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()]);
                });
            })
            ->paginate(10);
        return response()->json($users, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => $request->password,
                'username' => $request->username,
                'country' => $request->country,
                'age_group' => $request->age_group,
            ]);
            if($request->personas && $user) {
                foreach($request->personas as $persona) {
                    PersonaUser::create([
                        'user_id' => $user->id,
                        'persona_id' => $persona
                    ]);
                }
            }
            return response()->json($user->load('personas'), 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('personas');
        return response()->json($user, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            foreach ($request->all() as $key => $value) {
                if (array_key_exists($key, $user->getAttributes())) {
                    $user->$key = $value;
                }
            }
            if($request->new_password) {
                $user->password = Hash::make($request->new_password);
            }
            if($request->new_persona) {
                PersonaUser::create([
                    'user_id' => $user->id,
                    'persona_id' => $request->new_persona
                ]);
            }
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'message' => 'User deleted successfully'
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function report() {
        $age_group_1 = User::where('age_group', '16 to 19 years')->count();
        $age_group_2 = User::where('age_group', '20 to 29 years')->count();
        $age_group_3 = User::where('age_group', '30 to 39 years')->count();
        $age_group_4 = User::where('age_group', '40 to 49 years')->count();
        $age_group_5 = User::where('age_group', '50 to 59 years')->count();
        $age_group_6 = User::where('age_group', '60 to 69 years')->count();
        $age_group_7 = User::where('age_group', '70 years and above')->count();;
        $top_countries = User::select('country', DB::raw('COUNT(*) as user_count'))
            ->where('is_admin', false)
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('user_count')
            ->limit(10)
            ->get();
        $report = array(
            'age_groups' => [
                '16_to_19_years' => $age_group_1,
                '20_to_29_years' => $age_group_2,
                '30_to_39_years' => $age_group_3,
                '40_to_49_years' => $age_group_4,
                '50_to_59_years' => $age_group_5,
                '60_to_69_years' => $age_group_6,
                '70_years_and_above' => $age_group_7
            ],
            'top_countries' => $top_countries
        );
        return response()->json($report, 200);
    }

    public function admin_auth(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        try {
            if ($validator->fails()) {
                throw new Exception("Something went wrong", 400);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = User::where('email', $request->email)->first();
                // $token = $user->createToken('Email Sign-in')->accessToken;
                if($user->is_admin) {
                    $token = $user->createToken('Admin Sign-in', ['admin'])->accessToken;
                } else {
                    throw new Exception("User not allowed", 403);
                }
                return response()->json([
                    "message" => "Signed-in successfully",
                    "token" => $token,
                    "user" => $user
                ], 200);
            } else {
                throw new Exception("User not found", 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
