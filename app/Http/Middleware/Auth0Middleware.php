<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Auth0Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $authHeader = $request->header('Authorization');

            if (! $authHeader) {
                return response()->json(['error' => 'Authorization header missing'], 401);
            }

            if (! preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                return response()->json(['error' => 'Invalid authorization format'], 401);
            }

            $token = $matches[1];

            $auth0Domain = config('auth0.domain');
            $auth0Audience = config('auth0.audience');
            $auth0Algorithm = config('auth0.algorithm', 'RS256');

            if (! $auth0Domain || ! $auth0Audience) {
                return response()->json(['error' => 'Auth0 configuration missing'], 500);
            }

            $decoded = JWT::decode($token, new Key($this->getPublicKey($auth0Domain), $auth0Algorithm));

            if (! $this->validateToken($decoded, $auth0Audience, $auth0Domain)) {
                return response()->json(['error' => 'Invalid token'], 401);
            }

            $request->merge(['auth0_user' => $decoded]);

            return $next($request);
        } catch (ExpiredException $e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (SignatureInvalidException $e) {
            return response()->json(['error' => 'Invalid token signature'], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Token validation failed: ' . $e->getMessage()], 401);
        }
    }

    /**
     * Get Auth0 public key for token verification
     */
    private function getPublicKey(string $domain): string
    {
        $jwksUrl = "https://{$domain}/.well-known/jwks.json";

        $cacheKey = "auth0_jwks_{$domain}";
        $jwks = cache()->remember($cacheKey, 3600, function () use ($jwksUrl) {
            $response = file_get_contents($jwksUrl);

            return json_decode($response, true);
        });

        if (isset($jwks['keys'][0])) {
            $key = $jwks['keys'][0];

            return $this->convertJWKToPEM($key);
        }

        throw new Exception('Unable to retrieve Auth0 public key');
    }

    /**
     * Convert JWK to PEM format
     */
    private function convertJWKToPEM(array $jwk): string
    {
        $n = $jwk['n'];
        $e = $jwk['e'];

        $n = strtr($n, '-_', '+/');
        $e = strtr($e, '-_', '+/');

        $n = str_pad($n, strlen($n) + (4 - strlen($n) % 4) % 4, '=', STR_PAD_RIGHT);
        $e = str_pad($e, strlen($e) + (4 - strlen($e) % 4) % 4, '=', STR_PAD_RIGHT);

        $modulus = base64_decode($n);
        $exponent = base64_decode($e);

        $publicKey = pack('H*', '30820122300d06092a864886f70d01010105000382010f003082010a0282010100') .
          $modulus .
          pack('H*', '0203') .
          $exponent;

        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($publicKey), 64, "\n") . "-----END PUBLIC KEY-----\n";
    }

    /**
     * Validate the decoded token
     */
    private function validateToken($decoded, string $audience, string $domain): bool
    {
        if (! isset($decoded->aud) || $decoded->aud !== $audience) {
            return false;
        }

        if (! isset($decoded->iss) || $decoded->iss !== "https://{$domain}/") {
            return false;
        }

        if (! isset($decoded->exp) || $decoded->exp < time()) {
            return false;
        }

        return true;
    }
}
