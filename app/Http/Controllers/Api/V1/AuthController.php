<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Patron;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use RuntimeException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        try {
            $this->ensurePassportReady();
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'Authentication server not ready',
                'details' => $exception->getMessage(),
            ], 500);
        }

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();

        if ($user->role === 'patron') {
            $this->ensurePatronProfile($user);
        }

        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role ?? null,
            ],
        ], 200);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'patron',
        ]);

        $this->ensurePatronProfile($user);

        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Dev helper to reset the admin password to "password".
     * Only active in local environment.
     */
    public function resetAdmin(): JsonResponse
    {
        if (!app()->environment('local')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = User::firstOrCreate(
            ['email' => 'admin@domus.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        $user->update(['password' => Hash::make('password')]);

        return response()->json([
            'message' => 'Admin password reset to "password".',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Dev helper to (re)install Passport keys/clients.
     * Only active in local environment.
     */
    public function installPassport(): JsonResponse
    {
        if (!app()->environment('local')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $status = $this->ensurePassportReady();
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'Authentication server not ready',
                'details' => $exception->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Passport installed',
            'status' => $status,
        ]);
    }

    /**
     * Ensure keys + personal access client exist so createToken() can succeed.
     *
     * @return array<string, mixed>
     */
    private function ensurePassportReady(): array
    {
        if (!Schema::hasTable('oauth_clients')) {
            throw new RuntimeException('Passport tables have not been migrated. Run `php artisan migrate`.');
        }

        $keysGenerated = false;

        $publicKey = Passport::keyPath('oauth-public.key');
        $privateKey = Passport::keyPath('oauth-private.key');

        if (! file_exists($publicKey) || ! file_exists($privateKey)) {
            // Generate keys inline (similar to passport:keys --force)
            \Artisan::call('passport:keys', [
                '--force' => true,
            ]);
            $keysGenerated = true;
        }

        $clients = app(ClientRepository::class);
        $provider = config('auth.guards.api.provider');
        $clientCreated = false;

        try {
            $client = $clients->personalAccessClient($provider);
        } catch (RuntimeException $exception) {
            $client = $clients->createPersonalAccessGrantClient(
                config('app.name').' Personal Access Client',
                $provider
            );
            $clientCreated = true;
        }

        return [
            'keys_generated' => $keysGenerated,
            'client_created' => $clientCreated,
            'client_id' => $client->getKey(),
            'provider' => $provider,
        ];
    }

    private function ensurePatronProfile(User $user): Patron
    {
        $patron = $user->patron;

        if ($patron) {
            return $patron;
        }

        $existingPatron = Patron::where('email', $user->email)->first();

        if ($existingPatron) {
            $existingPatron->update([
                'name' => $user->name,
                'user_id' => $user->id,
            ]);

            return $existingPatron;
        }

        return $user->patron()->create([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
