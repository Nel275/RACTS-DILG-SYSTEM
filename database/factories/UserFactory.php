    <?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 *
 * Rewritten in Phase 12. The default Laravel scaffold's UserFactory
 * generates email/email_verified_at columns that no longer exist on the
 * users table since Phase 2 replaced them with username/role/barangay_id.
 * The old factory was never actually exercised by any RACTS code path, so
 * this went unnoticed until this final testing pass - it would have
 * failed with an "unknown column: email" database error the moment
 * anything called User::factory()->create().
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            // The User model's 'hashed' cast hashes this automatically on save.
            'password' => static::$password ??= 'password',
            'role' => User::ROLE_STAFF,
            'barangay_id' => null,
        ];
    }

    /**
     * Indicate that the user should be a DILG Administrator instead of
     * the default DILG Monitoring Staff role.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
