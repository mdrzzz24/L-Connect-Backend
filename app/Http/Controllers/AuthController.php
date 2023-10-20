    <?php

    namespace App\Http\Controllers;

    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Support\Facades\Hash;

    class AuthController extends Controller
    {
        public function Register(Request $request) {
            $request->validate([
                "name" => "required|string",
                "phone" => "required|string",
                "email" => "required|email|unique:users,email",
                "password" => "required|string|min:6",
            ]);

            $user = User::create([
                "name" => $request->name,
                "phone" => $request->phone,
                "email" => $request->email,
                "password" => Hash::make($request->password),
            ]);

            $token = $user->createToken("Auth_Token")->plainTextToken;

            return response()->json([
                "message" => "Registration successful",
                "token" => $token
            ], Response::HTTP_CREATED);
        }
        function Login(Request $request) {
            $request->validate([
                "email" => "required|string",
                "password" => "required|string",
            ]);

            $user = User::firstWhere("email", $request->email);

            if(!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    "message" => "Bad Credentials!"
                ], Response::HTTP_NOT_FOUND);
            }

            $token = $user->createToken("Auth_Token")->plainTextToken;

            return response()->json([
                "message" => "Success Login",
                "token" => $token
            ], Response::HTTP_OK);
        }
        public function getUserInfo() {
            $user = auth()->user();
            // dd($user);

            return response()->json([
                "user" => $user
            ], Response::HTTP_OK);
        }
        public function updateUser(Request $request) {
            $user = auth()->user(); // Get the currently authenticated user

            $request->validate([
                "name" => "string", // Name should be a string
                "phone" => "string", // Phone should be a string
                "email" => "email|unique:users,email," . $user->id, // Email should be a valid email format and unique in the 'users' table, except for the current user
                "password" => "string|min:6", // Password should be a string with a minimum length of 6 characters
            ]);

            // Update user data based on the request
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('password')) {
                $user->password = Hash::make($request->password); // Hash the password before saving it
            }

            // Save the changes to the user
            $user->save();

            return response()->json([
                "message" => "User information updated successfully",
                "user" => $user
            ], Response::HTTP_OK);
        }

        function Logout() {
            auth()->user()->tokens()->delete();

            return response()->json([
                "message" => "Success Logout"
            ], Response::HTTP_OK);
        }
    }
