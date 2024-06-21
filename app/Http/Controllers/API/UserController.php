<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Laravel\Passport\Token;
use Mockery\Exception;
use function Laravel\Prompts\error;


class UserController extends Controller
{
    //Fonction de connexion
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4'
        ]);
        try {
            $credentials = $request->only(['email', 'password']);
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                return $this->responseJson([
                    'token' => $user->createToken('Amdcompany')->accessToken,
                    'user' => $user,
                ], 'Utilisateur connecté avec succès !!',  200);
            }else {
                return $this->responseJson([], 'Identifiants incorrects', 401);
            }
        }catch (\Exception $exception){
            return $this->responseJson([
                'error' => $exception->getMessage(),
            ], 'Erreur !!', 500);
        }

    }

    //Fonction de déconnexion de l'utilisateur
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $accessToken = $user->token();

            Token::where('id', $accessToken->id)->update(['revoked' => true]);

            $accessToken->revoke();
            return $this->responseJson([
                'message' => 'Utilisateur déconnecté avec succès !!',
                'status' => 200
            ], 200);

        }catch (Exception $exception){
            return $this->responseJson([
                'message' => $exception->getMessage()
            ], 500);
        }

    }

    //Lister les utilisateurs
    public function index()
    {
        try {
            $user = Auth::user();
            if ($user){
                if ($user->role === 'admin'){
                    return $this->responseJson([User::all()],
                        'Liste des utilisateurs',
                        200
                    );
                }else{
                    return $this->responseJson([
                        User::findOrFail($user->id)
                    ],
                        'Données de l\'utilisateur');
                }
            }
        }catch (Exception $e){
            return $this->responseJson([
                'error' => $e->getMessage(),
            ], 'Erreur', 500);
        }


    }

    //Création des utilisateurs
    public function store(UserRequest $request)
    {
        try {
            $validated = $request->validated();

                $users = User::create($validated);

                return $this->responseJson([
                   'token' => $users->createToken('Amdcompany')->accessToken,
                   'user' => $users,
                ], 'Utilisateur créé avec succès !!',  200);

        }catch (\Exception $exception){
            return $this->responseJson('Erreur', 'Une erreur s\'est produite lors de la création
            de l\'utilisateur !!', 500);
        }
    }

    //Fonction de détails de l'utilisateur
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return $this->responseJson([
                'data' => $user,
            ], 'Détail de l\'utilisateur', 200);
        } catch (Exception $exception) {
            return $this->responseJson([
                'Erreur' => 'Erreur !!',
            ], 'Aucun utilisateur trouvé !!', 404);
        }
    }

    //Fonction de modification
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'nom'=>['required', 'min:2'],
                'prenom'=>['required', 'min:2'],
                'telephone'=>['required'],
                'email'=>['required', 'email'],
                'adresse'=>['required', 'min:2'],
                'role' => ['required', Rule::in(['admin', 'client'])],
                'password' => ['required', 'min:4'],
                'c_password' => ['required', 'same:password'],
            ]);

            $user = User::findOrFail($id);
            if($validated && $user){
                return $this->responseJson([
                    'data' => $user->update($validated),
                ], 'Utilisateur modifié !!', 200);
            }
        }catch (Exception $exception){
            return $this->responseJson([
                'error' => $exception->getMessage(),
            ], 'Erreur de sauvegarde', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            User::destroy($id);
            return $this->responseJson([
                'data' => null,
            ], 'Utilisateur supprimé avec succès !!', 200);
        }catch (Exception $exception){
            return $this->responseJson([
                'error' => $exception->getMessage(),
            ], 'Erreur', 500);
        }
    }
}
