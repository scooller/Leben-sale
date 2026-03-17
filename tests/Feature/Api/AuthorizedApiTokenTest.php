<?php

namespace Tests\Feature\Api;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizedApiTokenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{token: string, user: User, accessToken: PersonalAccessToken}
     */
    private function createApiToken(?string $authorizedUrl = null): array
    {
        $user = User::factory()->create();
        $newToken = $user->createToken('external-api-token');

        /** @var PersonalAccessToken $accessToken */
        $accessToken = $newToken->accessToken;
        $accessToken->forceFill([
            'authorized_url' => $authorizedUrl,
        ])->save();

        return [
            'token' => $newToken->plainTextToken,
            'user' => $user,
            'accessToken' => $accessToken,
        ];
    }

    public function test_api_token_allows_requests_from_authorized_origin(): void
    {
        $tokenData = $this->createApiToken('https://cliente.example.com');

        $response = $this
            ->withHeaders([
                'Authorization' => 'Bearer '.$tokenData['token'],
                'Origin' => 'https://cliente.example.com',
            ])
            ->getJson('/api/v1/me');

        $response
            ->assertOk()
            ->assertJsonPath('id', $tokenData['user']->id)
            ->assertJsonPath('email', $tokenData['user']->email);
    }

    public function test_api_token_rejects_requests_from_non_authorized_origin(): void
    {
        $tokenData = $this->createApiToken('https://cliente.example.com');

        $response = $this
            ->withHeaders([
                'Authorization' => 'Bearer '.$tokenData['token'],
                'Origin' => 'https://otro.example.com',
            ])
            ->getJson('/api/v1/me');

        $response
            ->assertForbidden()
            ->assertJson([
                'message' => 'La URL de origen no está autorizada para este token.',
            ]);
    }

    public function test_api_token_without_authorized_url_keeps_existing_behavior(): void
    {
        $tokenData = $this->createApiToken();

        $response = $this
            ->withHeaders([
                'Authorization' => 'Bearer '.$tokenData['token'],
            ])
            ->getJson('/api/v1/me');

        $response
            ->assertOk()
            ->assertJsonPath('id', $tokenData['user']->id);
    }
}
