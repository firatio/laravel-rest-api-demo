<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Hash;

class ItemsTest extends TestCase
{
    use RefreshDatabase;
    
    private $user1 = null;
    private $user2 = null;
    private $user1_token = null;
    private $user2_token = null;
    private $user1_items = [];
    private $user2_items = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsers();
        $this->logUsersIn();
        $this->createItems();
    }
    
    private function createUsers()
    {
        $this->user1 = User::create([
            'name' => "user1@example.com",
            'email' => "user1@example.com",
            'password' => Hash::make("user1password")
        ]);

        $this->user2 = User::create([
            'name' => "user2@example.com",
            'email' => "user2@example.com",
            'password' => Hash::make("user2password")
        ]);
    }

    private function logUsersIn()
    {
        $this->user1_token = $this->user1->createToken('authToken')->plainTextToken;
        $this->user2_token = $this->user2->createToken('authToken')->plainTextToken;
    }

    /**
     * Create a few items for user1 and user2
     */
    private function createItems()
    {
        // 2 items for user 1
        $this->user1_items[] = Item::create([
            'user_id' => $this->user1->id,
            'name' => "Milk",
            'notes' => "2 bottles"
        ]);

        $this->user1_items[] = Item::create([
            'user_id' => $this->user1->id,
            'name' => "Apple",
            'notes' => "1 kg"
        ]);

        // 1 item for user 2
        $this->user2_items[] = Item::create([
            'user_id' => $this->user2->id,
            'name' => "Olive oil",
            'notes' => "1 bottle. 2 bottles if on discount."
        ]);
    }

    /**
     * Unauthenticated users cannot create items
     */
    public function test_unauthenticated_users_cannot_create_items()
    {
        $payload = [
            'name' => 'Milk',
            'notes' => '2 bottles'
        ];

        $this->postJson('api/items', $payload)
            ->assertStatus(401);
    }

    /**
     * Requests with invalid tokens cannot create items
     */
    public function test_requests_with_invalid_tokens_cannot_create_items()
    {
        $payload = [
            'name' => 'Potato',
            'notes' => '2 kg'
        ];

        $token = "invalidtoken";
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('api/items', $payload)
            ->assertStatus(401);
    }

    /**
     * Authenticated users can create items
     */
    public function test_authenticated_users_can_create_items()
    {
        $payload = [
            'name' => 'Eggs',
            'notes' => 'Free range, 12pcs'
        ];

        $token = $this->user1_token;
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('api/items', $payload)
            ->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'notes']);

        $responseContent = json_decode($response->getContent());
        $id = $responseContent->id;

        $items = Item::where('user_id', $this->user1->id)->get();
        $this->assertEquals(count($this->user1_items) + 1, count($items));

        $item = Item::findOrFail($id);
        $this->assertEquals($this->user1->id, $item->user_id);
        $this->assertEquals($payload['name'], $item->name);
    }

    /**
     * Authenticated users can get a list of their items
     */
    public function test_authenticated_users_can_get_a_list_of_their_items()
    {
        $token = $this->user1_token;
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('api/items')
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'notes', 'created_at', 'updated_at'],
            ])
            ->assertJsonCount(count($this->user1_items))
            ->assertJson([
                ['id' => $this->user1_items[0]->id]
            ])
            ->assertJsonMissing(
                ['id' => $this->user2_items[0]->id]
            );
    }

    /**
     * Authenticated users can view their items
     */
    public function test_authenticated_users_can_view_their_items()
    {
        $token = $this->user1_token;
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('api/items/' . $this->user1_items[0]->id)
            ->assertStatus(200)
            ->assertJson(['id' => $this->user1_items[0]->id]);
    }

    /**
     * Authenticated users cannot view items that belong to other users
     */
    public function test_authenticated_users_cannot_view_items_belonging_to_other_users()
    {
        // user2 should not be able to view user1's items
        $token = $this->user2_token;
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('api/items/' . $this->user1_items[0]->id)
            ->assertStatus(403)
            ->assertJsonMissing(['id' => $this->user1_items[0]->id]);
    }

    /**
     * Authenticated users can delete their items
     */
    public function test_authenticated_users_can_delete_their_items()
    {
        $token = $this->user1_token;
        $itemId = $this->user1_items[0]->id;
        $itemCountBeforeDelete = count($this->user1_items);
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson("api/items/$itemId")
            ->assertStatus(204);
        
        // user1 should have one less item
        $items = Item::where('user_id', $this->user1->id)->get();
        $this->assertEquals($itemCountBeforeDelete - 1, count($items));
    }

    /**
     * Authenticated users cannot delete items that belong to other users
     */
    public function test_authenticated_users_cannot_delete_items_belonging_to_other_users()
    {
        // user2 should not be able to delete user1's items
        $token = $this->user2_token;
        $itemId = $this->user1_items[0]->id;
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson("api/items/$itemId")
            ->assertStatus(403);
    }

    /**
     * Authenticated users can update their items
     */
    public function test_authenticated_users_can_update_their_items()
    {
        $payload = [
            'name' => $this->user1_items[0]->name,
            'notes' => 'Updated notes'
        ];

        $token = $this->user1_token;
        $itemId = $this->user1_items[0]->id;
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->putJson("api/items/$itemId", $payload)
            ->assertStatus(204);

        $this->assertEquals($payload['notes'], Item::find($itemId)->notes);
    }

    /**
     * Authenticated users cannot update items that belong to other users
     */
    public function test_authenticated_users_cannot_update_items_belonging_to_other_users()
    {
        $payload = [
            'name' => $this->user1_items[0]->name,
            'notes' => 'Updated notes'
        ];

        // user2 should not be able to update user1's items
        $token = $this->user2_token;
        $itemId = $this->user1_items[0]->id;
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->putJson("api/items/$itemId", $payload)
            ->assertStatus(403);
    }
}
